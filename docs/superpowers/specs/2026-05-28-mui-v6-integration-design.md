# MUI v6 Integration Design

**Date:** 2026-05-28  
**Project:** Guerrilla — Concrete CMS 9.5 package  
**Status:** Approved

---

## Overview

Integrate Material UI v6 (MUI) into the `guerrilla` Concrete CMS package as the component library for React-based custom blocks. The build toolchain runs on the developer's machine only; shared hosting receives pre-built assets and a deployable zip package.

---

## Architecture

```
packages/guerrilla/
├── package.json               ← Vite + MUI + Emotion dependencies
├── vite.config.js             ← Multi-entry build, React externalized
├── src/
│   └── blocks/                ← One React entry point per block
│       └── [block-handle]/
│           └── index.jsx
└── themes/guerrilla/
    └── js/dist/               ← Vite build output (committed to git)
        ├── vendor.js           ← MUI + Emotion shared chunk
        └── blocks/
            └── [block-handle].js
```

**Key decisions:**

- React and ReactDOM are **externalized** — not bundled. Concrete CMS provides them at runtime via its own asset pipeline (`window.React`, `window.ReactDOM`).
- MUI (`@mui/material`) and Emotion (`@emotion/react`, `@emotion/styled`) are bundled into a single `vendor.js`, loaded once and shared across all blocks.
- `dist/` is **committed to git** so shared hosting deployments require no Node.js or npm.
- `node_modules/` and `src/` are excluded from deployment packages.

---

## Dependencies

**Runtime (bundled into `vendor.js`):**
- `@mui/material` ^6
- `@emotion/react`
- `@emotion/styled`

**Dev only:**
- `vite`
- `@vitejs/plugin-react`

React and ReactDOM are provided by Concrete CMS — no npm dependency needed.

---

## Vite Configuration

`packages/guerrilla/vite.config.js`:

- **Entry points:** auto-discovered from `src/blocks/*/index.jsx`
- **Externals:** `react`, `react-dom`
- **Globals:** `{ react: 'React', 'react-dom': 'ReactDOM' }`
- **manualChunks:** MUI + Emotion forced into `vendor.js`
- **Output directory:** `themes/guerrilla/js/dist/`
- **Format:** `iife` per block (self-executing, no module loader required)

---

## Asset Registration

### In `PageTheme::registerAssets()`

The shared vendor chunk is registered once for the whole theme:

```php
$al->register(
    'javascript', 'guerrilla/vendor',
    'js/dist/vendor.js',
    ['version' => '1.0', 'position' => Asset::ASSET_POSITION_FOOTER],
    $pkg
);
```

### In each block's `controller.php`

```php
public function registerViewAssets($outputContent = ''): void
{
    $this->requireAsset('javascript', 'guerrilla/vendor');
    $this->requireAsset('javascript', 'guerrilla/block/[handle]');
}
```

---

## Block Mounting Pattern

### `view.php` — PHP side

Outputs a mount target and passes CMS data as JSON:

```php
<div
    id="guerrilla-[handle]-<?= $bID ?>"
    data-props='<?= htmlspecialchars(json_encode($props), ENT_QUOTES) ?>'
></div>
```

### `src/blocks/[handle]/index.jsx` — React side

Finds all mount targets on the page and renders:

```jsx
import { createRoot } from 'react-dom/client';
import MyBlock from './MyBlock';

document.querySelectorAll('[id^="guerrilla-[handle]-"]').forEach(el => {
    const props = JSON.parse(el.dataset.props);
    createRoot(el).render(<MyBlock {...props} />);
});
```

This pattern supports multiple instances of the same block on one page.

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
            ├── js/dist/vendor.js
            ├── js/dist/blocks/[block-handle].js
            └── ...
```

### Deployment to shared hosting

1. Run `npm run package:build` on your dev machine
2. Upload `guerrilla-v{version}.zip` to the server's `packages/` directory
3. Extract: `unzip guerrilla-v{version}.zip -d packages/`
4. Install via Concrete CMS dashboard → Extend Concrete CMS → Install

No Node.js, npm, or build tools required on the server.

---

## Git Strategy

- `dist/` — **committed** (pre-built assets for hostings without Node)
- `node_modules/` — gitignored
- `src/` — committed (source of truth for block JS)
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

## Error Handling

- If React is not available on `window.React` at block mount time, the block logs a console error and renders nothing (no white-screen crash).
- MUI `ThemeProvider` wraps each block root to allow per-project theming without global CSS conflicts.
- The `data-props` attribute is escaped with `htmlspecialchars` to prevent XSS from CMS content.

---

## Testing

- Unit tests for React components use Vitest + `@testing-library/react`
- PHP block controller tests use the existing PHPUnit setup
- Visual smoke test: install package on a local Concrete CMS 9.5 instance and verify each block renders without JS console errors
