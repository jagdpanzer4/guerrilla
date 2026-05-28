# Material Web Components (MD3) Integration Design

**Date:** 2026-05-28  
**Project:** Guerrilla — Concrete CMS 9.5 package  
**Status:** Approved  
**Supersedes:** Original MUI v6/React design (abandoned — ConcreteCMS 9.5 uses Vue 2, not React; React blocks render client-side only which is unsuitable for content blocks)

---

## Overview

Integrate Google's official Material Design 3 library — **Material Web** (`@material/web`) — into the `guerrilla` Concrete CMS package as the component foundation for custom blocks.

Material Web ships as **Web Components** (custom elements). PHP renders the component HTML tags server-side; the JS bundle registers the custom elements and handles interactivity. Content is fully visible before JavaScript loads — no client-side rendering problem.

The build toolchain (Vite) runs on the developer's machine only. Shared hosting receives pre-built assets and a deployable zip.

---

## Why Material Web over MUI/React

| Concern | MUI v6 + React | Material Web Components |
|---|---|---|
| ConcreteCMS ships React? | ❌ No (uses Vue 2) | ✅ Not needed |
| Content rendered server-side? | ❌ Client-side only | ✅ PHP renders HTML tags |
| SEO-friendly? | ⚠️ Depends on JS | ✅ Always |
| Framework conflicts? | ⚠️ Vue 2 globals in edit mode | ✅ None (Web Components) |
| Bootstrap 5 CSS conflict? | ⚠️ Requires careful scoping | ✅ Shadow DOM isolates styles |
| Shared hosting compatible? | ✅ Pre-built dist | ✅ Pre-built dist |

---

## Architecture

```
packages/guerrilla/
├── package.json               ← Vite + @material/web
├── vite.config.js             ← Single entry, ES module output
├── src/
│   └── material-web.js        ← Imports all MD3 components used in blocks
└── themes/guerrilla/
    └── js/dist/               ← Vite build output (committed to git)
        └── material-web.js    ← Tree-shaken bundle of used components
```

**Key decisions:**

- **Single bundle** — Material Web registers components via `customElements.define()` globally. Splitting across files would cause "already defined" errors if two blocks used the same component. One bundle, loaded once, covers all blocks.
- **Tree-shaking** — only the components explicitly imported in `src/material-web.js` are included. Unused MD3 components are stripped at build time.
- **Server-side rendering** — PHP writes `<md-filled-button>`, `<md-card>`, etc. directly in `view.php`. Content is in the HTML; JS only enhances it.
- **Style isolation** — Material Web uses Shadow DOM. Its internal styles do not conflict with Bootstrap 5 or ConcreteCMS chrome.
- **`dist/` committed to git** — shared hosting deployments require no Node.js or npm.

---

## Dependencies

**Runtime (bundled into `material-web.js`):**
- `@material/web` ^2 (Google's official MD3 web components)

**Dev only:**
- `vite`

No React, no Vue, no Emotion — no framework dependency at all.

---

## Vite Configuration

`packages/guerrilla/vite.config.js`:

- **Entry point:** `src/material-web.js` (single file)
- **Output directory:** `themes/guerrilla/js/dist/`
- **Format:** `es` (ES module — modern browsers; or `iife` if IE11/legacy support needed)
- **Minify:** `true` for production
- **No externals** — everything bundled; no runtime dependency on ConcreteCMS internals

---

## Asset Registration

Registration happens in the package controller's `on_start()` — this is the correct ConcreteCMS hook for registering assets into `AssetList` before any page rendering occurs.

### In `packages/guerrilla/controller.php` → `on_start()`

```php
public function on_start(): void
{
    $al = \Concrete\Core\Asset\AssetList::getInstance();
    $al->register(
        'javascript',
        'guerrilla/material-web',
        'js/dist/material-web.js',
        [
            'position' => \Concrete\Core\Asset\Asset::ASSET_POSITION_FOOTER,
            'local'    => true,
            'version'  => $this->pkgVersion,
            'combine'  => false,
            'minify'   => false,
        ],
        $this->getPackageHandle()
    );
}
```

### In each block's `controller.php`

```php
public function registerViewAssets($outputContent = ''): void
{
    $this->requireAsset('javascript', 'guerrilla/material-web');
}
```

The asset loads once per page even if multiple blocks require it — ConcreteCMS deduplicates asset requirements.

---

## Block Authoring Pattern

### `view.php` — PHP renders full content using MD3 tags

```php
<?php /** @var string $title */ /** @var string $body */ ?>

<md-card>
    <div slot="headline"><?= h($title) ?></div>
    <div slot="subhead"><?= h($subtitle) ?></div>
    <div slot="supporting-text"><?= $body ?></div>
    <div slot="actions">
        <md-filled-button><?= t('Read more') ?></md-filled-button>
    </div>
</md-card>
```

Content is fully present in the HTML. The `material-web.js` bundle registers the custom elements, which then upgrade the existing DOM nodes — no content is created by JavaScript.

### `src/material-web.js` — import components as you add blocks

```js
// Add an import here each time a new MD3 component is used in any block
import '@material/web/card/filled-card.js';
import '@material/web/button/filled-button.js';
import '@material/web/button/outlined-button.js';
import '@material/web/divider/divider.js';
// ... add more as needed
```

Vite tree-shakes this — only imported components are in the bundle.

---

## Theming

Material Web uses **CSS custom properties** (tokens) for theming. Define your brand colours once in the theme's `main.css`:

```css
:root {
    --md-sys-color-primary: #6750a4;
    --md-sys-color-on-primary: #ffffff;
    --md-sys-color-surface: #fffbfe;
    /* ... full token set generated by Material Theme Builder */
}
```

Tokens are generated for free at [material-foundation.github.io/material-theme-builder](https://material-foundation.github.io/material-theme-builder/). Export as CSS and paste into `main.css`.

---

## On-Demand Installation Package

### Command

```bash
cd packages/guerrilla
npm run package:build
```

### What it does

1. Runs `vite build` to compile fresh assets into `js/dist/`
2. Runs `scripts/package-guerrilla.sh` (project root) which:
   - Reads the version from `packages/guerrilla/controller.php`
   - Creates `guerrilla-v{version}.zip` at the project root
   - Includes: all PHP files, `themes/`, `blocks/` (with built assets)
   - Excludes: `node_modules/`, `src/`, `vite.config.js`, `package.json`, `package-lock.json`

### Resulting zip structure

```
guerrilla-v1.0.0.zip
└── guerrilla/
    ├── controller.php
    ├── blocks/
    │   └── [block-handle]/
    │       ├── controller.php
    │       ├── view.php
    │       └── ...
    └── themes/
        └── guerrilla/
            ├── js/dist/material-web.js
            └── css/main.css
```

### Deployment to shared hosting

1. Run `npm run package:build` on your dev machine
2. Upload `guerrilla-v{version}.zip` to the server's `packages/` directory
3. Extract: `unzip guerrilla-v{version}.zip -d packages/`
4. Install via Concrete CMS dashboard → Extend Concrete CMS → Install

No Node.js, npm, or build tools required on the server.

---

## Git Strategy

- `themes/guerrilla/js/dist/` — **committed** (pre-built assets for shared hosting)
- `node_modules/` — gitignored
- `src/` — committed (source of truth for the component bundle)
- `guerrilla-v*.zip` — gitignored (build artefact)

---

## Developer Workflow

```bash
cd packages/guerrilla

# First time setup
npm install

# Development (watch mode — rebuilds on file change)
npm run dev

# Production build
npm run build

# Build + create deployable zip
npm run package:build
```

---

## Adding a New Block (Checklist)

1. Create `packages/guerrilla/blocks/[handle]/` with `controller.php` and `view.php`
2. In `view.php`, use MD3 web component tags for the block's HTML
3. In `src/material-web.js`, add `import` statements for any new components used
4. Run `npm run build` to update `dist/material-web.js`
5. In the block's `controller.php`, add `registerViewAssets()` requiring `guerrilla/material-web`

---

## Error Handling & Progressive Enhancement

- If JavaScript fails to load, the raw custom element tags (`<md-card>`, `<md-filled-button>`) are still in the DOM. Browsers render unknown elements as inline elements — add a minimal CSS fallback in `main.css` for graceful degradation.
- No white-screen risk: content is always server-rendered.
- ConcreteCMS edit mode injects drag handles around the block's outer element. Block templates should use an inner wrapper `<div>` as the direct parent of MD3 components to avoid interference.

---

## Testing

- Visual smoke test: install package on a local Concrete CMS 9.5 instance, verify components render correctly and upgrade in the browser (check `customElements.get('md-filled-button')` is defined)
- PHP block controller tests use the existing PHPUnit setup
- No JS unit tests required for web components used as markup (no custom JS logic to test)
