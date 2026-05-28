# Material Web Components Setup Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Wire Google's `@material/web` MD3 library into the Guerrilla ConcreteCMS package so blocks can use MD3 web component tags rendered by PHP, enhanced by a single pre-built JS bundle.

**Architecture:** Vite builds `src/material-web.js` into a single IIFE at `themes/guerrilla/js/dist/material-web.js`. The package controller registers this as a named ConcreteCMS asset in `on_start()`. Each block's `registerViewAssets()` requires it. PHP renders MD3 custom element tags directly — content is always server-side; JS only upgrades the DOM.

**Tech Stack:** `@material/web` ^2, Vite ^6, PHP 8.1, Concrete CMS 9.5

---

## File Map

| Action | Path | Responsibility |
|---|---|---|
| CREATE | `packages/guerrilla/package.json` | npm manifest with Vite + `@material/web` |
| CREATE | `packages/guerrilla/vite.config.js` | Single-entry IIFE build to `js/dist/` |
| CREATE | `packages/guerrilla/src/material-web.js` | MD3 component imports (tree-shaking entry) |
| CREATE | `packages/guerrilla/themes/guerrilla/js/dist/.gitkeep` | Ensures `dist/` is tracked before first build |
| MODIFY | `packages/guerrilla/controller.php` | Add `on_start()` to register the JS asset |
| CREATE | `scripts/package-guerrilla.sh` | Builds zip for shared-hosting deployment |
| MODIFY | `.gitignore` | Add `packages/guerrilla/node_modules/`, `guerrilla-v*.zip` |
| MODIFY | `README.md` | Add frontend dev workflow section (Polish, matching existing style) |

---

## Task 1: Create `package.json`

**Files:**
- Create: `packages/guerrilla/package.json`

- [ ] **Step 1: Create the file**

```json
{
  "name": "guerrilla",
  "version": "1.0.0",
  "private": true,
  "scripts": {
    "dev": "vite build --watch",
    "build": "vite build",
    "package:build": "npm run build && bash ../../scripts/package-guerrilla.sh"
  },
  "dependencies": {
    "@material/web": "^2.2.0"
  },
  "devDependencies": {
    "vite": "^6.3.5"
  }
}
```

- [ ] **Step 2: Commit**

```bash
git add packages/guerrilla/package.json
git commit -m "chore: add package.json for Material Web + Vite toolchain"
```

---

## Task 2: Create `vite.config.js`

**Files:**
- Create: `packages/guerrilla/vite.config.js`

- [ ] **Step 1: Create the file**

```js
import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    build: {
        lib: {
            entry: resolve(__dirname, 'src/material-web.js'),
            name: 'GuerrillaWeb',
            fileName: () => 'material-web',
            formats: ['iife'],
        },
        outDir: resolve(__dirname, 'themes/guerrilla/js/dist'),
        emptyOutDir: false,
        minify: true,
        rollupOptions: {
            output: {
                entryFileNames: 'material-web.js',
            },
        },
    },
});
```

**Why `iife`:** ConcreteCMS's asset manager injects `<script src>` tags (not `<script type="module">`). IIFE format is self-executing and works without a module loader.

**Why `emptyOutDir: false`:** Prevents Vite from deleting other files in `js/dist/` (e.g. `main.js`) on each build.

- [ ] **Step 2: Commit**

```bash
git add packages/guerrilla/vite.config.js
git commit -m "chore: add Vite config for Material Web IIFE bundle"
```

---

## Task 3: Create the Material Web entry point

**Files:**
- Create: `packages/guerrilla/src/material-web.js`
- Create: `packages/guerrilla/themes/guerrilla/js/dist/.gitkeep`

- [ ] **Step 1: Create `src/material-web.js`**

```js
/**
 * Material Web Components (MD3) entry point.
 *
 * Add one import per component as new MD3 elements are used in blocks.
 * Vite tree-shakes this file — only imported components end up in the bundle.
 *
 * Full component list: https://material-web.dev/components/
 */

// --- Buttons ---
import '@material/web/button/filled-button.js';
import '@material/web/button/outlined-button.js';
import '@material/web/button/text-button.js';

// --- Cards (layout only — no dedicated <md-card> in MD3; use divs + elevation) ---

// --- Divider ---
import '@material/web/divider/divider.js';

// --- Icon ---
import '@material/web/icon/icon.js';

// --- Ripple (touch feedback) ---
import '@material/web/ripple/ripple.js';
```

- [ ] **Step 2: Create `.gitkeep` so `dist/` is tracked before first build**

```bash
mkdir -p packages/guerrilla/themes/guerrilla/js/dist
touch packages/guerrilla/themes/guerrilla/js/dist/.gitkeep
```

- [ ] **Step 3: Commit**

```bash
git add packages/guerrilla/src/material-web.js packages/guerrilla/themes/guerrilla/js/dist/.gitkeep
git commit -m "feat: add Material Web Components entry point"
```

---

## Task 4: Install dependencies and run first build

**Files:**
- No new files — verifies Tasks 1–3 produce real output.

- [ ] **Step 1: Install npm dependencies**

```bash
cd packages/guerrilla
npm install
```

Expected: `node_modules/` created, no errors.

- [ ] **Step 2: Build**

```bash
npm run build
```

Expected output (last few lines):
```
dist/material-web.js  xxx.xx kB │ gzip: xx.xx kB
✓ built in Xs
```

- [ ] **Step 3: Verify output file exists**

```bash
ls -lh packages/guerrilla/themes/guerrilla/js/dist/material-web.js
```

Expected: file exists, size > 0.

- [ ] **Step 4: Spot-check the bundle registers a custom element**

```bash
grep -c "customElements.define" packages/guerrilla/themes/guerrilla/js/dist/material-web.js
```

Expected: a number > 0 (each imported MD3 component registers itself).

- [ ] **Step 5: Commit built asset**

```bash
git add packages/guerrilla/themes/guerrilla/js/dist/material-web.js
git commit -m "build: initial Material Web bundle (MD3 web components)"
```

---

## Task 5: Register the asset in the package controller

**Files:**
- Modify: `packages/guerrilla/controller.php`

- [ ] **Step 1: Add `on_start()` and required `use` statements**

Replace the entire file content with:

```php
<?php

namespace Concrete\Package\Guerrilla;

use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends Package
{
    protected string $pkgHandle = 'guerrilla';
    protected string $appVersionRequired = '9.5.0';
    protected string $pkgVersion = '1.0.0';
    protected $pkgAutoloaderRegistries = [];

    public function getPackageDescription(): string
    {
        return t('Guerrilla theme package for Concrete CMS 9.5.');
    }

    public function getPackageName(): string
    {
        return t('Guerrilla');
    }

    public function on_start(): void
    {
        $al = AssetList::getInstance();
        $al->register(
            'javascript',
            'guerrilla/material-web',
            'js/dist/material-web.js',
            [
                'position' => Asset::ASSET_POSITION_FOOTER,
                'local'    => true,
                'version'  => $this->pkgVersion,
                'combine'  => false,
                'minify'   => false,
            ],
            $this->getPackageHandle()
        );
    }

    public function install(): void
    {
        $pkg = parent::install();

        // Install the theme
        $theme = \Concrete\Core\Page\Theme\Theme::add('guerrilla', $pkg);
        $theme->applyToSite();
    }

    public function upgrade(): void
    {
        parent::upgrade();
    }

    public function uninstall(): void
    {
        parent::uninstall();
    }
}
```

**Why `on_start()` and not `registerAssets()`:** `on_start()` fires on every request when the package is active — this registers the asset handle into `AssetList` so it's available for any block or theme to `requireAsset()`. `PageTheme::registerAssets()` only *requires* already-registered handles.

**Why `combine: false` and `minify: false`:** The bundle is already minified by Vite. Letting ConcreteCMS re-minify or combine it can corrupt the IIFE wrapper.

- [ ] **Step 2: Commit**

```bash
git add packages/guerrilla/controller.php
git commit -m "feat: register guerrilla/material-web asset in package on_start()"
```

---

## Task 6: Create the packaging script

**Files:**
- Create: `scripts/package-guerrilla.sh`

- [ ] **Step 1: Create the script**

```bash
#!/usr/bin/env bash
# Builds a deployable zip of the guerrilla package for shared hosting.
# Usage: called by `npm run package:build` from packages/guerrilla/
# Output: guerrilla-v{version}.zip at project root

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
PKG_DIR="$PROJECT_ROOT/packages/guerrilla"

# Read version string from controller.php  e.g. '1.0.0'
VERSION=$(grep "pkgVersion" "$PKG_DIR/controller.php" | grep -oE "[0-9]+\.[0-9]+\.[0-9]+")

if [ -z "$VERSION" ]; then
    echo "ERROR: Could not read version from controller.php" >&2
    exit 1
fi

ZIP_NAME="guerrilla-v${VERSION}.zip"
ZIP_PATH="$PROJECT_ROOT/$ZIP_NAME"

echo "Packaging guerrilla v${VERSION} → ${ZIP_NAME}"

rm -f "$ZIP_PATH"

cd "$PROJECT_ROOT/packages"
zip -r "$ZIP_PATH" guerrilla \
    --exclude "guerrilla/node_modules/*" \
    --exclude "guerrilla/src/*" \
    --exclude "guerrilla/vite.config.js" \
    --exclude "guerrilla/package.json" \
    --exclude "guerrilla/package-lock.json"

echo "Done: $ZIP_PATH"
```

- [ ] **Step 2: Make it executable**

```bash
chmod +x scripts/package-guerrilla.sh
```

- [ ] **Step 3: Run a dry-run to verify it works**

```bash
cd packages/guerrilla && npm run package:build
```

Expected: `guerrilla-v1.0.0.zip` created at project root.

- [ ] **Step 4: Verify zip contents are correct**

```bash
unzip -l guerrilla-v1.0.0.zip | head -30
```

Expected: `guerrilla/controller.php`, `guerrilla/themes/guerrilla/js/dist/material-web.js` present. No `node_modules/`, no `src/`, no `vite.config.js`.

- [ ] **Step 5: Clean up test zip**

```bash
rm guerrilla-v1.0.0.zip
```

- [ ] **Step 6: Commit**

```bash
git add scripts/package-guerrilla.sh
git commit -m "feat: add package-guerrilla.sh for on-demand zip packaging"
```

---

## Task 7: Update `.gitignore`

**Files:**
- Modify: `.gitignore`

- [ ] **Step 1: Add package-level ignores**

Add the following block at the end of `.gitignore`:

```gitignore
# Material Web build toolchain
/packages/guerrilla/node_modules/

# Deployment zip artefacts
guerrilla-v*.zip
```

**Note:** `packages/guerrilla/themes/guerrilla/js/dist/` is intentionally NOT ignored — built assets are committed so shared hosting deployments don't need Node.

- [ ] **Step 2: Verify `node_modules` is now ignored**

```bash
git status packages/guerrilla/node_modules
```

Expected: no output (ignored).

- [ ] **Step 3: Commit**

```bash
git add .gitignore
git commit -m "chore: gitignore package node_modules and deployment zips"
```

---

## Task 8: Update README with frontend workflow

**Files:**
- Modify: `README.md`

- [ ] **Step 1: Add a frontend development section after the existing "Rozwój motywu" section**

Insert the following before `## Licencja`:

```markdown
## Frontend – Material Web Components

Bloki pakietu Guerrilla korzystają z biblioteki [Material Web](https://material-web.dev/) (Google MD3) kompilowanej przy użyciu [Vite](https://vitejs.dev/).

### Wymagania

| Narzędzie | Wersja |
|---|---|
| Node.js | ≥ 18 |
| npm | ≥ 9 |

### Pierwsze uruchomienie

```bash
cd packages/guerrilla
npm install
```

### Polecenia

| Polecenie | Opis |
|---|---|
| `npm run dev` | Tryb watch – przebudowuje przy każdej zmianie w `src/` |
| `npm run build` | Produkcyjna kompilacja do `themes/guerrilla/js/dist/` |
| `npm run package:build` | Kompiluje assets i tworzy `guerrilla-v{wersja}.zip` |

### Dodawanie nowego komponentu MD3

1. Znajdź komponent na [material-web.dev/components](https://material-web.dev/components/)
2. Dodaj import do `packages/guerrilla/src/material-web.js`
3. Uruchom `npm run build`
4. Użyj tagu HTML w `view.php` bloku, np. `<md-filled-button>`

### Wdrożenie na hosting współdzielony

```bash
cd packages/guerrilla
npm run package:build
# Powstaje: guerrilla-v{wersja}.zip w katalogu głównym projektu
```

Prześlij zip na serwer, rozpakuj do `packages/` i zainstaluj pakiet przez panel CMS.  
**Node.js nie jest wymagany na serwerze.**
```

- [ ] **Step 2: Commit**

```bash
git add README.md
git commit -m "docs: add Material Web frontend workflow to README"
```

---

## Self-Review

**Spec coverage check:**
- ✅ Single Vite bundle (Task 1–4)
- ✅ Tree-shaking via explicit imports in `src/material-web.js` (Task 3)
- ✅ Asset registered in `on_start()` with correct API (Task 5)
- ✅ `combine: false` / `minify: false` to protect IIFE wrapper (Task 5)
- ✅ On-demand zip packaging via `npm run package:build` (Task 6)
- ✅ Shared hosting deployment: no Node required, dist committed (Task 7)
- ✅ README documentation in Polish matching existing style (Task 8)
- ✅ `dist/` committed to git, `node_modules/` ignored (Tasks 4, 7)

**Placeholder scan:** No TBD, TODO, or vague steps found. All code blocks are complete.

**Type/name consistency:**
- Asset handle `guerrilla/material-web` used consistently in Task 5 (registration) — blocks will `requireAsset('javascript', 'guerrilla/material-web')`.
- Output filename `material-web.js` matches between `vite.config.js` (Task 2) and `controller.php` registration path `js/dist/material-web.js` (Task 5).
- Script name `package:build` consistent between `package.json` (Task 1) and `scripts/package-guerrilla.sh` call (Task 6).
