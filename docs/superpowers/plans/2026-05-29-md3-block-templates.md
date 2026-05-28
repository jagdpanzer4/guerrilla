# MD3 Block Templates Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create custom block templates for 7 ConcreteCMS blocks (content, image, feature, accordion, page_list, autonav, breadcrumb) with MD3 styling, military olive/cream/orange palette, 4 color variants each, and multiple layout variants for complex blocks.

**Architecture:** Theme-level PHP templates at `packages/guerrilla/themes/guerrilla/blocks/<handle>/`. Simple blocks (content, image, breadcrumb) use a shared `_base.php` included by thin 3-line variant wrappers. Multi-layout blocks (feature, page_list, autonav) use `_render.php` with `$layout` + `$colorVariant` PHP variables. Accordion uses separate files per style. All color theming via CSS custom properties. Accordion and Auto Nav use inline JS with `data-md3-init` guard (safe for multiple instances per page).

**Tech Stack:** PHP 8.x, ConcreteCMS 9.5 custom block templates, Material Web Components (already loaded globally), CSS custom properties, Vanilla JS (no jQuery dependency).

> **ConcreteCMS note:** Block template `view.css` files in `themes/guerrilla/blocks/<handle>/` are automatically enqueued by ConcreteCMS when a block using that template is on the page. The MD3 JS bundle is already registered globally via `on_start()`.

---

## File Map

```
packages/guerrilla/themes/guerrilla/
├── css/
│   └── main.css                                  MODIFY — add MD3 palette vars + glow + shared block classes
└── blocks/
    ├── content/
    │   ├── _base.php                             CREATE — full HTML template
    │   ├── view.php                              CREATE — light variant (default)
    │   ├── view.css                              CREATE — content block styles
    │   └── templates/
    │       ├── dark.php                          CREATE — dark variant
    │       ├── tonal.php                         CREATE — tonal variant
    │       └── accent.php                        CREATE — accent variant
    ├── image/
    │   ├── _base.php                             CREATE
    │   ├── view.php                              CREATE
    │   ├── view.css                              CREATE
    │   └── templates/ dark.php tonal.php accent.php
    ├── breadcrumb/
    │   ├── _base.php                             CREATE
    │   ├── view.php                              CREATE
    │   ├── view.css                              CREATE
    │   └── templates/ dark.php tonal.php accent.php
    ├── feature/
    │   ├── _render.php                           CREATE — handles all 3 layouts via $layout var
    │   ├── view.php                              CREATE — icon-top + light
    │   ├── view.css                              CREATE
    │   └── templates/                            CREATE — 11 files (all layout×color combos)
    │       icon-left.php, card.php
    │       dark.php, icon-left-dark.php, card-dark.php
    │       tonal.php, icon-left-tonal.php, card-tonal.php
    │       accent.php, icon-left-accent.php, card-accent.php
    ├── accordion/
    │   ├── view.php                              CREATE — Standard style (light)
    │   ├── view.css                              CREATE
    │   └── templates/
    │       filled.php, tactical.php              CREATE — layout variants (light only)
    │       standard-dark.php, standard-tonal.php, standard-accent.php
    │       filled-dark.php, filled-tonal.php, filled-accent.php
    │       tactical-dark.php, tactical-tonal.php, tactical-accent.php
    ├── page_list/
    │   ├── _render.php                           CREATE — handles all 4 layouts
    │   ├── view.php                              CREATE — cards-grid + light
    │   ├── view.css                              CREATE
    │   └── templates/
    │       horizontal-list.php, featured.php, minimal.php
    │       cards-dark.php, cards-tonal.php, cards-accent.php
    │       horizontal-list-dark.php ... (etc for each layout×color)
    └── autonav/
        ├── _render.php                           CREATE — handles all 3 nav layouts
        ├── view.php                              CREATE — dark-topbar + light (default)
        ├── view.css                              CREATE
        └── templates/
            cream-topbar.php, sidebar.php
```

---

## Task 1: Shared CSS — Palette, Glow & Block Base Classes

**Files:**
- Modify: `packages/guerrilla/themes/guerrilla/css/main.css`

- [ ] **Step 1: Add MD3 palette variables and glow properties to `main.css`**

Append to the end of `packages/guerrilla/themes/guerrilla/css/main.css`:

```css
/* ============================================================
   MD3 Block Templates — Design System
   ============================================================ */

/* --- Palette Variables --- */
:root {
    /* Olive Green — Primary / Secondary */
    --g-ol-900: #2d3a1a;
    --g-ol-800: #364820;
    --g-ol-700: #4a5e28;
    --g-ol-600: #526830;
    --g-ol-500: #6b883c;
    --g-ol-300: #adc47a;
    --g-ol-200: #cdd9a8;

    /* Cream — Surface / Background */
    --g-cr-100: #f5f0e8;
    --g-cr-200: #ede6d6;
    --g-cr-300: #e0d5bf;

    /* Orange — CTA / Accent */
    --g-or-600: #c45a00;
    --g-or-500: #e06a00;
    --g-or-400: #f27c1a;
    --g-or-300: #f59a4a;

    /* Glow — controllable per deployment */
    --g-glow-primary:    rgba(107, 136, 60, 0.40);
    --g-glow-cta:        rgba(224, 106, 0,  0.55);
    --g-glow-intensity:  1;
}

/* --- Shared Block Wrapper --- */
.md3-block {
    box-sizing: border-box;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

/* Color Variant — Light (default) */
.md3-block--light {
    background-color: var(--g-cr-100);
    color: var(--g-ol-900);
}

/* Color Variant — Dark */
.md3-block--dark {
    background-color: var(--g-ol-900);
    color: var(--g-cr-100);
}
.md3-block--dark .md3-block__subtitle,
.md3-block--dark .md3-block__meta {
    color: var(--g-ol-300);
}

/* Color Variant — Tonal */
.md3-block--tonal {
    background-color: var(--g-ol-200);
    color: var(--g-ol-900);
}
.md3-block--tonal .md3-block__subtitle {
    color: var(--g-ol-700);
}

/* Color Variant — Accent */
.md3-block--accent {
    background-color: var(--g-or-600);
    color: #ffffff;
}

/* --- Shared Glow Utilities --- */
.md3-glow-cta {
    transition: box-shadow 0.2s ease, background-color 0.2s ease;
}
.md3-glow-cta:hover {
    box-shadow: 0 0 14px 3px var(--g-glow-cta);
}

.md3-glow-primary {
    transition: box-shadow 0.2s ease;
}
.md3-glow-primary:hover {
    box-shadow: 0 0 12px 2px var(--g-glow-primary);
}

.md3-glow-text {
    transition: text-shadow 0.2s ease, color 0.2s ease;
}
.md3-glow-text:hover {
    text-shadow: 0 0 10px var(--g-glow-cta);
}

/* --- Shared MD3 Button Styles --- */
.md3-btn {
    display: inline-block;
    padding: 10px 24px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: box-shadow 0.2s ease, background-color 0.2s ease;
}

.md3-btn--filled {
    background-color: var(--g-or-500);
    color: #ffffff;
}
.md3-btn--filled:hover {
    background-color: var(--g-or-600);
    box-shadow: 0 0 14px 3px var(--g-glow-cta);
    color: #ffffff;
    text-decoration: none;
}

.md3-btn--outlined {
    background-color: transparent;
    color: var(--g-ol-700);
    border: 2px solid var(--g-ol-500);
}
.md3-btn--outlined:hover {
    box-shadow: 0 0 12px 2px var(--g-glow-primary);
}

.md3-btn--tonal {
    background-color: var(--g-ol-200);
    color: var(--g-ol-900);
}
.md3-btn--tonal:hover {
    box-shadow: 0 0 12px 2px var(--g-glow-primary);
}

/* Dark variant button overrides */
.md3-block--dark .md3-btn--outlined {
    color: var(--g-ol-300);
    border-color: var(--g-ol-500);
}

/* --- Section Label (military uppercase) --- */
.md3-section-label {
    display: block;
    font-size: 11px;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--g-ol-500);
    margin-bottom: 8px;
}
.md3-block--dark .md3-section-label {
    color: var(--g-ol-300);
}
.md3-block--accent .md3-section-label {
    color: rgba(255, 255, 255, 0.75);
}
```

- [ ] **Step 2: Verify CSS loads without errors**

Open your ConcreteCMS site in a browser. Open DevTools (F12) → Console. Confirm no CSS parse errors. The new variables should appear in the `:root` section of DevTools → Elements → Computed.

- [ ] **Step 3: Commit**

```bash
cd /Volumes/Drewutnia/web/Guerrilla
git add packages/guerrilla/themes/guerrilla/css/main.css
git commit -m "feat(css): add MD3 palette variables, glow utilities and block base classes"
```

---

## Task 2: Content Block Templates

**Files:**
- Create: `packages/guerrilla/themes/guerrilla/blocks/content/_base.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/content/view.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/content/view.css`
- Create: `packages/guerrilla/themes/guerrilla/blocks/content/templates/dark.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/content/templates/tonal.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/content/templates/accent.php`

> The ConcreteCMS `content` block provides `$content` — an HTML string from the WYSIWYG editor.

- [ ] **Step 1: Create `_base.php` (shared HTML)**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/content/_base.php
defined('C5_EXECUTE') or die('Access Denied.');
$v = $colorVariant ?? 'light';
?>
<div class="md3-block md3-content md3-block--<?= htmlspecialchars($v) ?>">
    <?php if (!empty($title)): ?>
        <span class="md3-section-label"><?= htmlspecialchars($title) ?></span>
    <?php endif; ?>
    <div class="md3-content__body">
        <?= $content ?>
    </div>
</div>
```

- [ ] **Step 2: Create `view.php` (Light — default)**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/content/view.php
defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'light';
require __DIR__ . '/_base.php';
```

- [ ] **Step 3: Create `view.css`**

```css
/* packages/guerrilla/themes/guerrilla/blocks/content/view.css */
.md3-content {
    padding: 1.5rem 0;
}

.md3-content__body {
    line-height: 1.75;
}

/* Typography in Light variant */
.md3-content.md3-block--light h1,
.md3-content.md3-block--light h2,
.md3-content.md3-block--light h3,
.md3-content.md3-block--light h4 {
    color: var(--g-ol-900);
    letter-spacing: 0.5px;
}

.md3-content.md3-block--light a {
    color: var(--g-or-500);
    text-decoration: underline;
    transition: color 0.2s, text-shadow 0.2s;
}
.md3-content.md3-block--light a:hover {
    color: var(--g-or-600);
    text-shadow: 0 0 8px var(--g-glow-cta);
}

/* Typography in Dark variant */
.md3-content.md3-block--dark h1,
.md3-content.md3-block--dark h2,
.md3-content.md3-block--dark h3,
.md3-content.md3-block--dark h4 {
    color: var(--g-cr-100);
}

.md3-content.md3-block--dark p,
.md3-content.md3-block--dark li {
    color: var(--g-ol-300);
}

.md3-content.md3-block--dark a {
    color: var(--g-or-400);
}
.md3-content.md3-block--dark a:hover {
    color: var(--g-or-300);
    text-shadow: 0 0 8px var(--g-glow-cta);
}

/* Tonal variant */
.md3-content.md3-block--tonal h1,
.md3-content.md3-block--tonal h2,
.md3-content.md3-block--tonal h3 {
    color: var(--g-ol-900);
}

.md3-content.md3-block--tonal a {
    color: var(--g-or-500);
}

/* Accent variant */
.md3-content.md3-block--accent,
.md3-content.md3-block--accent h1,
.md3-content.md3-block--accent h2,
.md3-content.md3-block--accent h3,
.md3-content.md3-block--accent p,
.md3-content.md3-block--accent li {
    color: #ffffff;
}
.md3-content.md3-block--accent a {
    color: #ffffff;
    text-decoration: underline;
}

/* md3-divider separator */
.md3-content md-divider {
    margin: 1.5rem 0;
}

/* md3-section-label in content block */
.md3-content .md3-section-label {
    margin-bottom: 0.75rem;
}
```

- [ ] **Step 4: Create `templates/dark.php`, `tonal.php`, `accent.php`**

```php
<?php
// templates/dark.php
defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'dark';
require dirname(__DIR__) . '/_base.php';
```

```php
<?php
// templates/tonal.php
defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'tonal';
require dirname(__DIR__) . '/_base.php';
```

```php
<?php
// templates/accent.php
defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'accent';
require dirname(__DIR__) . '/_base.php';
```

- [ ] **Step 5: Create the `templates/` directory and verify structure**

```bash
ls packages/guerrilla/themes/guerrilla/blocks/content/
# Expected: _base.php  view.php  view.css  templates/
ls packages/guerrilla/themes/guerrilla/blocks/content/templates/
# Expected: dark.php  tonal.php  accent.php
```

- [ ] **Step 6: Manual test**

1. Log into ConcreteCMS admin → edit a page
2. Add a **Content** block with some text and a heading
3. Save and view the page — the block should render with cream background and olive heading
4. Edit the block → Design → select template "dark" → save and view
5. Verify dark olive background with cream text
6. Repeat for "tonal" (light olive surface) and "accent" (orange background)

- [ ] **Step 7: Commit**

```bash
git add packages/guerrilla/themes/guerrilla/blocks/content/
git commit -m "feat(blocks): add Content block MD3 template (4 color variants)"
```

---

## Task 3: Image Block Templates

**Files:**
- Create: `packages/guerrilla/themes/guerrilla/blocks/image/_base.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/image/view.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/image/view.css`
- Create: `packages/guerrilla/themes/guerrilla/blocks/image/templates/dark.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/image/templates/tonal.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/image/templates/accent.php`

> ConcreteCMS `image` block variables: `$f` (File entity, may be null), `$altText` (string), `$title` (string), `$caption` (string), `$href` (URL string), `$openLinkInNewWindow` (bool).

- [ ] **Step 1: Create `_base.php`**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/image/_base.php
defined('C5_EXECUTE') or die('Access Denied.');
$v = $colorVariant ?? 'light';
$imgSrc = ($f instanceof \Concrete\Core\Entity\File\File)
    ? $f->getRelativePath()
    : '';
$imgAlt = htmlspecialchars($altText ?? '');
$target = !empty($openLinkInNewWindow) ? '_blank' : '_self';
?>
<div class="md3-block md3-image md3-block--<?= htmlspecialchars($v) ?>">

    <?php if ($imgSrc): ?>
        <figure class="md3-image__figure">
            <?php if (!empty($href)): ?>
                <a href="<?= htmlspecialchars($href) ?>" target="<?= $target ?>" class="md3-image__link md3-glow-primary">
                    <img src="<?= htmlspecialchars($imgSrc) ?>"
                         alt="<?= $imgAlt ?>"
                         class="md3-image__img">
                </a>
            <?php else: ?>
                <img src="<?= htmlspecialchars($imgSrc) ?>"
                     alt="<?= $imgAlt ?>"
                     class="md3-image__img">
            <?php endif; ?>

            <?php if (!empty($caption)): ?>
                <figcaption class="md3-image__caption">
                    <md-divider></md-divider>
                    <?= htmlspecialchars($caption) ?>
                </figcaption>
            <?php endif; ?>
        </figure>
    <?php endif; ?>

</div>
```

- [ ] **Step 2: Create `view.php` (Light)**

```php
<?php
defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'light';
require __DIR__ . '/_base.php';
```

- [ ] **Step 3: Create `view.css`**

```css
/* packages/guerrilla/themes/guerrilla/blocks/image/view.css */
.md3-image {
    padding: 1rem 0;
}

.md3-image__figure {
    margin: 0;
}

.md3-image__img {
    max-width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
}

.md3-image__link {
    display: block;
    border-radius: 4px;
    overflow: hidden;
}

.md3-image__caption {
    padding: 0.6rem 0 0;
    font-size: 13px;
    font-style: italic;
}

.md3-image.md3-block--light .md3-image__caption {
    color: var(--g-ol-700);
}

.md3-image.md3-block--dark .md3-image__caption {
    color: var(--g-ol-300);
}

.md3-image.md3-block--tonal .md3-image__caption {
    color: var(--g-ol-700);
}

.md3-image.md3-block--accent .md3-image__caption {
    color: rgba(255, 255, 255, 0.85);
}

.md3-image.md3-block--dark {
    padding: 1rem;
    border-radius: 8px;
}

.md3-image.md3-block--tonal {
    padding: 1rem;
    border-radius: 8px;
}

.md3-image.md3-block--accent {
    padding: 1rem;
    border-radius: 8px;
}
```

- [ ] **Step 4: Create `templates/dark.php`, `tonal.php`, `accent.php`**

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'dark'; require dirname(__DIR__) . '/_base.php';
```

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'tonal'; require dirname(__DIR__) . '/_base.php';
```

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'accent'; require dirname(__DIR__) . '/_base.php';
```

- [ ] **Step 5: Manual test**

1. Add an Image block to a test page with an image file, alt text, and caption
2. Verify default (light) renders: image full-width, italic caption below md-divider
3. Switch to Dark template: verify image gains dark olive padding wrapper
4. Verify accent template: orange background with white caption

- [ ] **Step 6: Commit**

```bash
git add packages/guerrilla/themes/guerrilla/blocks/image/
git commit -m "feat(blocks): add Image block MD3 template (4 color variants)"
```

---

## Task 4: Breadcrumb Block Templates

**Files:**
- Create: `packages/guerrilla/themes/guerrilla/blocks/breadcrumb/_base.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/breadcrumb/view.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/breadcrumb/view.css`
- Create: `packages/guerrilla/themes/guerrilla/blocks/breadcrumb/templates/dark.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/breadcrumb/templates/tonal.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/breadcrumb/templates/accent.php`

> ConcreteCMS `breadcrumb` block: verify variable name against `concrete/blocks/breadcrumb/view.php` on [GitHub](https://github.com/concretecms/concretecms). The block provides an array of Page objects representing the trail from root to current page. Common variable: `$breadcrumb` array or loop via `$this->getSiteTreeObject()`. Fallback: build trail from current page in template if block variable is unavailable.

- [ ] **Step 1: Create `_base.php`**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/breadcrumb/_base.php
defined('C5_EXECUTE') or die('Access Denied.');
$v = $colorVariant ?? 'light';

// ConcreteCMS breadcrumb block provides $breadcrumb array of page objects.
// If $breadcrumb is not set (verify against concrete/blocks/breadcrumb/view.php),
// build trail from current page:
if (empty($breadcrumb)) {
    $currentPage = \Concrete\Core\Page\Page::getCurrentPage();
    $breadcrumb = [];
    $page = $currentPage;
    while ($page && !$page->isError() && $page->getCollectionID() > 1) {
        array_unshift($breadcrumb, $page);
        $page = \Concrete\Core\Page\Page::getByID($page->getCollectionParentID());
    }
    // Add home
    $home = \Concrete\Core\Page\Page::getByID(1);
    if ($home) array_unshift($breadcrumb, $home);
    $breadcrumb[] = $currentPage; // ensure current is last (may duplicate, dedup below)
    $breadcrumb = array_unique($breadcrumb, SORT_REGULAR);
}
?>
<nav class="md3-block md3-breadcrumb md3-block--<?= htmlspecialchars($v) ?>" aria-label="<?= t('Breadcrumb') ?>">
    <ol class="md3-breadcrumb__list">
        <?php foreach ($breadcrumb as $i => $crumbPage):
            $isLast = ($i === count($breadcrumb) - 1);
            $crumbName = htmlspecialchars($crumbPage->getCollectionName());
            $crumbUrl  = $crumbPage->getCollectionLink();
        ?>
            <li class="md3-breadcrumb__item<?= $isLast ? ' md3-breadcrumb__item--current' : '' ?>">
                <?php if (!$isLast): ?>
                    <a href="<?= htmlspecialchars($crumbUrl) ?>"
                       class="md3-breadcrumb__link md3-glow-text">
                        <?= $crumbName ?>
                    </a>
                    <span class="md3-breadcrumb__separator" aria-hidden="true">›</span>
                <?php else: ?>
                    <span class="md3-breadcrumb__current" aria-current="page">
                        <?= $crumbName ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
```

- [ ] **Step 2: Create `view.php`, `templates/dark.php`, `tonal.php`, `accent.php`**

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'light'; require __DIR__ . '/_base.php';
```

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'dark'; require dirname(__DIR__) . '/_base.php';
```

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'tonal'; require dirname(__DIR__) . '/_base.php';
```

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'accent'; require dirname(__DIR__) . '/_base.php';
```

- [ ] **Step 3: Create `view.css`**

```css
/* packages/guerrilla/themes/guerrilla/blocks/breadcrumb/view.css */
.md3-breadcrumb {
    padding: 0.5rem 0;
}

.md3-breadcrumb__list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 2px;
    font-size: 13px;
}

.md3-breadcrumb__item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.md3-breadcrumb__separator {
    color: var(--g-or-400);
    font-weight: 700;
    font-size: 15px;
    line-height: 1;
}

/* Light variant */
.md3-breadcrumb.md3-block--light .md3-breadcrumb__link {
    color: var(--g-ol-700);
    text-decoration: none;
    font-weight: 500;
}
.md3-breadcrumb.md3-block--light .md3-breadcrumb__link:hover {
    color: var(--g-or-500);
    text-shadow: 0 0 8px var(--g-glow-cta);
}
.md3-breadcrumb.md3-block--light .md3-breadcrumb__current {
    color: var(--g-ol-900);
    font-weight: 600;
}

/* Dark variant */
.md3-breadcrumb.md3-block--dark .md3-breadcrumb__link {
    color: var(--g-ol-300);
    text-decoration: none;
}
.md3-breadcrumb.md3-block--dark .md3-breadcrumb__link:hover {
    color: var(--g-or-300);
    text-shadow: 0 0 8px var(--g-glow-cta);
}
.md3-breadcrumb.md3-block--dark .md3-breadcrumb__current {
    color: var(--g-cr-100);
    font-weight: 600;
}

/* Tonal variant */
.md3-breadcrumb.md3-block--tonal .md3-breadcrumb__link {
    color: var(--g-ol-700);
    text-decoration: none;
}
.md3-breadcrumb.md3-block--tonal .md3-breadcrumb__link:hover {
    color: var(--g-or-500);
}
.md3-breadcrumb.md3-block--tonal .md3-breadcrumb__current {
    color: var(--g-ol-900);
    font-weight: 600;
}

/* Accent variant */
.md3-breadcrumb.md3-block--accent .md3-breadcrumb__link {
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
}
.md3-breadcrumb.md3-block--accent .md3-breadcrumb__link:hover {
    color: #ffffff;
    text-shadow: 0 0 8px rgba(255, 255, 255, 0.6);
}
.md3-breadcrumb.md3-block--accent .md3-breadcrumb__current {
    color: #ffffff;
    font-weight: 700;
}
.md3-breadcrumb.md3-block--accent .md3-breadcrumb__separator {
    color: rgba(255, 255, 255, 0.6);
}
```

- [ ] **Step 4: Manual test**

1. Add a Breadcrumb block to a sub-page (at least 2 levels deep)
2. Verify trail renders correctly with orange `›` separators
3. Verify current page is bold, not a link
4. Verify hover glow on parent links (orange text-shadow)
5. Test dark template: cream links with olive-300 default

- [ ] **Step 5: Commit**

```bash
git add packages/guerrilla/themes/guerrilla/blocks/breadcrumb/
git commit -m "feat(blocks): add Breadcrumb block MD3 template (4 color variants)"
```

---

## Task 5: Feature Block Templates

**Files:**
- Create: `packages/guerrilla/themes/guerrilla/blocks/feature/_render.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/feature/view.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/feature/view.css`
- Create: 11 variant files in `packages/guerrilla/themes/guerrilla/blocks/feature/templates/`

> ConcreteCMS `feature` block variables: `$title` (string), `$body` (HTML), `$icon` (CSS class string, e.g. `fa fa-star`), `$link` (URL string), `$linkText` (string). Verify against `concrete/blocks/feature/view.php` on GitHub.

- [ ] **Step 1: Create `_render.php` (all 3 layouts)**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/feature/_render.php
defined('C5_EXECUTE') or die('Access Denied.');
$v  = $colorVariant ?? 'light';
$l  = $layout      ?? 'icon-top';
$linkUrl  = !empty($link)     ? htmlspecialchars($link)     : '';
$linkLabel = !empty($linkText) ? htmlspecialchars($linkText) : t('Learn More');
?>
<div class="md3-block md3-feature md3-block--<?= $v ?> md3-feature--<?= $l ?>">

    <?php if ($l === 'icon-top'): ?>
        <!-- LAYOUT A: Icon Top -->
        <div class="md3-feature__icon-wrap">
            <?php if (!empty($icon)): ?>
                <span class="md3-feature__icon">
                    <i class="<?= htmlspecialchars($icon) ?>"></i>
                </span>
            <?php endif; ?>
        </div>
        <div class="md3-feature__content md3-feature__content--centered">
            <?php if (!empty($title)): ?>
                <h3 class="md3-feature__title"><?= htmlspecialchars($title) ?></h3>
            <?php endif; ?>
            <?php if (!empty($body)): ?>
                <div class="md3-feature__body"><?= $body ?></div>
            <?php endif; ?>
            <?php if ($linkUrl): ?>
                <a href="<?= $linkUrl ?>" class="md3-btn md3-btn--filled md3-glow-cta">
                    <?= $linkLabel ?>
                </a>
            <?php endif; ?>
        </div>

    <?php elseif ($l === 'icon-left'): ?>
        <!-- LAYOUT B: Icon Left -->
        <div class="md3-feature__icon-wrap md3-feature__icon-wrap--left">
            <?php if (!empty($icon)): ?>
                <span class="md3-feature__icon">
                    <i class="<?= htmlspecialchars($icon) ?>"></i>
                </span>
            <?php endif; ?>
        </div>
        <div class="md3-feature__content">
            <?php if (!empty($title)): ?>
                <h3 class="md3-feature__title"><?= htmlspecialchars($title) ?></h3>
            <?php endif; ?>
            <?php if (!empty($body)): ?>
                <div class="md3-feature__body"><?= $body ?></div>
            <?php endif; ?>
            <?php if ($linkUrl): ?>
                <a href="<?= $linkUrl ?>" class="md3-btn md3-btn--filled md3-glow-cta">
                    <?= $linkLabel ?>
                </a>
            <?php endif; ?>
        </div>

    <?php else: /* card */ ?>
        <!-- LAYOUT C: Card -->
        <div class="md3-feature__card md3-glow-primary">
            <div class="md3-feature__card-header">
                <?php if (!empty($icon)): ?>
                    <span class="md3-feature__icon md3-feature__icon--small">
                        <i class="<?= htmlspecialchars($icon) ?>"></i>
                    </span>
                <?php endif; ?>
                <?php if (!empty($title)): ?>
                    <h3 class="md3-feature__title"><?= htmlspecialchars($title) ?></h3>
                <?php endif; ?>
            </div>
            <?php if (!empty($body)): ?>
                <div class="md3-feature__body"><?= $body ?></div>
            <?php endif; ?>
            <?php if ($linkUrl): ?>
                <div class="md3-feature__card-footer">
                    <a href="<?= $linkUrl ?>" class="md3-btn md3-btn--filled md3-glow-cta">
                        <?= $linkLabel ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</div>
```

- [ ] **Step 2: Create `view.php` (icon-top + light)**

```php
<?php
defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant = 'light';
$layout = 'icon-top';
require __DIR__ . '/_render.php';
```

- [ ] **Step 3: Create all 11 template variant files**

```php
// templates/icon-left.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='light'; $layout='icon-left'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/card.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='light'; $layout='card'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/dark.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='dark'; $layout='icon-top'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/icon-left-dark.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='dark'; $layout='icon-left'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/card-dark.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='dark'; $layout='card'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/tonal.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='tonal'; $layout='icon-top'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/icon-left-tonal.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='tonal'; $layout='icon-left'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/card-tonal.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='tonal'; $layout='card'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/accent.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='accent'; $layout='icon-top'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/icon-left-accent.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='accent'; $layout='icon-left'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/card-accent.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$colorVariant='accent'; $layout='card'; require dirname(__DIR__).'/_render.php';
```

- [ ] **Step 4: Create `view.css`**

```css
/* packages/guerrilla/themes/guerrilla/blocks/feature/view.css */

/* --- Base --- */
.md3-feature {
    padding: 1.5rem;
}

/* Icon container */
.md3-feature__icon-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}
.md3-feature__icon-wrap--left {
    justify-content: flex-start;
    margin-bottom: 0;
    flex-shrink: 0;
    margin-right: 1rem;
}

.md3-feature__icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background-color: var(--g-ol-500);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #ffffff;
}
.md3-feature__icon--small {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-size: 15px;
}

/* Layout A — Icon Top */
.md3-feature.md3-feature--icon-top {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Layout B — Icon Left */
.md3-feature.md3-feature--icon-left {
    display: flex;
    align-items: flex-start;
    text-align: left;
}

/* Layout C — Card */
.md3-feature.md3-feature--card {
    padding: 0;
}
.md3-feature__card {
    border: 1px solid var(--g-cr-300);
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.md3-feature__card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    border-bottom: 1px solid var(--g-cr-300);
}
.md3-feature__body {
    padding: 12px 16px;
    font-size: 14px;
    line-height: 1.6;
}
.md3-feature__card-footer {
    padding: 0 16px 14px;
}

/* Light variant card border */
.md3-block--light .md3-feature__card {
    border-color: var(--g-cr-300);
    background: #ffffff;
}

/* Dark variant card */
.md3-block--dark .md3-feature__card {
    border-color: var(--g-ol-700);
    background: var(--g-ol-800);
}
.md3-block--dark .md3-feature__card-header {
    border-bottom-color: var(--g-ol-700);
}
.md3-block--dark .md3-feature__icon {
    background-color: var(--g-ol-600);
}

/* Tonal variant */
.md3-block--tonal .md3-feature__card {
    border-color: var(--g-ol-300);
    background: var(--g-cr-100);
}
.md3-block--tonal .md3-feature__card-header {
    border-bottom-color: var(--g-ol-300);
}

/* Accent variant */
.md3-block--accent .md3-feature__card {
    border-color: rgba(255,255,255,0.3);
    background: rgba(0,0,0,0.1);
}
.md3-block--accent .md3-feature__icon {
    background-color: rgba(255,255,255,0.2);
}
.md3-block--accent .md3-btn--filled {
    background-color: #ffffff;
    color: var(--g-or-600);
}

/* Typography */
.md3-feature__title {
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 0.5rem;
    letter-spacing: 0.3px;
}
.md3-block--light .md3-feature__title { color: var(--g-ol-900); }
.md3-block--dark  .md3-feature__title { color: var(--g-cr-100); }
.md3-block--tonal .md3-feature__title { color: var(--g-ol-900); }
.md3-block--accent .md3-feature__title { color: #ffffff; }

.md3-block--light .md3-feature__body { color: #555555; }
.md3-block--dark  .md3-feature__body { color: var(--g-ol-300); }
.md3-block--tonal .md3-feature__body { color: var(--g-ol-700); }
.md3-block--accent .md3-feature__body { color: rgba(255,255,255,0.9); }
```

- [ ] **Step 5: Verify file count**

```bash
find packages/guerrilla/themes/guerrilla/blocks/feature/ -name "*.php" | wc -l
# Expected: 13 (view.php + _render.php + 11 in templates/)
```

- [ ] **Step 6: Manual test**

1. Add Feature block with title, body text, icon class (e.g. `fa fa-shield`), and link
2. Default (icon-top, light): centered icon above title, orange filled button
3. Switch to "card" template: block renders as outlined card with icon + title in header
4. Switch to "icon-left-dark": horizontal layout on dark olive background
5. Verify icon square is olive-500 background with white icon
6. Verify card hover glow (olive) on `.md3-feature__card`

- [ ] **Step 7: Commit**

```bash
git add packages/guerrilla/themes/guerrilla/blocks/feature/
git commit -m "feat(blocks): add Feature block MD3 templates (3 layouts × 4 colors)"
```

---

## Task 6: Accordion Block Templates

**Files:**
- Create: `packages/guerrilla/themes/guerrilla/blocks/accordion/view.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/accordion/view.css`
- Create: 11 files in `packages/guerrilla/themes/guerrilla/blocks/accordion/templates/`

> ConcreteCMS `accordion` block: verify the items variable against `concrete/blocks/accordion/view.php` on GitHub. Expected: `$rows` array where each row has `title` (string) and `body` (HTML string). Update variable name in `_accordion_items()` call if different.

- [ ] **Step 1: Create `view.php` (Standard style + Light variant)**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/accordion/view.php
defined('C5_EXECUTE') or die('Access Denied.');
// Verify $rows variable against concrete/blocks/accordion/view.php
// Each $row expected to have: $row->title (string), $row->body (HTML string)
$style        = 'standard';
$colorVariant = 'light';
?>
<div class="md3-block md3-accordion md3-block--<?= $colorVariant ?>
            md3-accordion--<?= $style ?>"
     data-md3-accordion>

    <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $i => $row): ?>
            <div class="md3-accordion__item" id="md3-acc-item-<?= (int)$i ?>">
                <button class="md3-accordion__header"
                        aria-expanded="false"
                        aria-controls="md3-acc-body-<?= (int)$i ?>">
                    <span class="md3-accordion__title">
                        <?= htmlspecialchars($row->title ?? '') ?>
                    </span>
                    <span class="md3-accordion__chevron" aria-hidden="true">▼</span>
                </button>
                <div class="md3-accordion__body"
                     id="md3-acc-body-<?= (int)$i ?>"
                     role="region"
                     aria-labelledby="md3-acc-item-<?= (int)$i ?>"
                     style="max-height:0;overflow:hidden;">
                    <div class="md3-accordion__content">
                        <?= $row->body ?? '' ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script>
(function() {
    // Guard: only attach listener once per page even if multiple accordion blocks exist
    if (window.md3AccordionLoaded) return;
    window.md3AccordionLoaded = true;
    document.addEventListener('click', function(e) {
        var header = e.target.closest('[data-md3-accordion] .md3-accordion__header');
        if (!header) return;
        var item      = header.closest('.md3-accordion__item');
        var accordion = header.closest('[data-md3-accordion]');
        var isOpen    = item.classList.contains('is-open');

        // Close all items in this accordion
        accordion.querySelectorAll('.md3-accordion__item.is-open').forEach(function(open) {
            open.classList.remove('is-open');
            open.querySelector('.md3-accordion__header').setAttribute('aria-expanded', 'false');
            open.querySelector('.md3-accordion__body').style.maxHeight = '0';
        });

        // Open clicked item if it was closed
        if (!isOpen) {
            item.classList.add('is-open');
            header.setAttribute('aria-expanded', 'true');
            var body = item.querySelector('.md3-accordion__body');
            body.style.maxHeight = body.scrollHeight + 'px';
        }
    });
})();
</script>
```

- [ ] **Step 2: Create all `templates/` variant files**

Create each file with the content pattern below — only `$style` and `$colorVariant` change:

```php
// templates/filled.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='filled'; $colorVariant='light'; ?>
<?php include dirname(__DIR__) . '/view.php'; // reuse view.php HTML — but we need _render
```

Wait — because `view.php` is the full template with JS, we cannot `include` it easily without re-running the script guard. Instead, extract the HTML to a `_base.php` shared file. Refactor:

**Refactor Step 2a: Move HTML to `_accordion_base.php`**

Create `packages/guerrilla/themes/guerrilla/blocks/accordion/_accordion_base.php`:

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/accordion/_accordion_base.php
defined('C5_EXECUTE') or die('Access Denied.');
$style        = $style        ?? 'standard';
$colorVariant = $colorVariant ?? 'light';
?>
<div class="md3-block md3-accordion md3-block--<?= $colorVariant ?>
            md3-accordion--<?= $style ?>"
     data-md3-accordion>

    <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $i => $row): ?>
            <div class="md3-accordion__item" id="md3-acc-item-<?= (int)$i ?>">
                <button class="md3-accordion__header"
                        aria-expanded="false"
                        aria-controls="md3-acc-body-<?= (int)$i ?>">
                    <?php if ($style === 'tactical'): ?>
                        <span class="md3-accordion__tac-icon" aria-hidden="true">▶</span>
                    <?php endif; ?>
                    <span class="md3-accordion__title">
                        <?= htmlspecialchars($row->title ?? '') ?>
                    </span>
                    <?php if ($style !== 'tactical'): ?>
                        <span class="md3-accordion__chevron" aria-hidden="true">▼</span>
                    <?php endif; ?>
                </button>
                <div class="md3-accordion__body"
                     id="md3-acc-body-<?= (int)$i ?>"
                     role="region"
                     style="max-height:0;overflow:hidden;transition:max-height 0.3s ease;">
                    <div class="md3-accordion__content">
                        <?= $row->body ?? '' ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<script>
(function() {
    if (window.md3AccordionLoaded) return;
    window.md3AccordionLoaded = true;
    document.addEventListener('click', function(e) {
        var header = e.target.closest('[data-md3-accordion] .md3-accordion__header');
        if (!header) return;
        var item      = header.closest('.md3-accordion__item');
        var accordion = header.closest('[data-md3-accordion]');
        var isOpen    = item.classList.contains('is-open');
        accordion.querySelectorAll('.md3-accordion__item.is-open').forEach(function(open) {
            open.classList.remove('is-open');
            open.querySelector('.md3-accordion__header').setAttribute('aria-expanded', 'false');
            open.querySelector('.md3-accordion__body').style.maxHeight = '0';
        });
        if (!isOpen) {
            item.classList.add('is-open');
            header.setAttribute('aria-expanded', 'true');
            var body = item.querySelector('.md3-accordion__body');
            body.style.maxHeight = body.scrollHeight + 'px';
        }
    });
})();
</script>
```

**Refactor Step 2b: Update `view.php` to use `_accordion_base.php`**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/accordion/view.php
defined('C5_EXECUTE') or die('Access Denied.');
$style        = 'standard';
$colorVariant = 'light';
require __DIR__ . '/_accordion_base.php';
```

**Step 2c: Create all 11 template files**

```php
// templates/filled.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='filled'; $colorVariant='light'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/tactical.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='tactical'; $colorVariant='light'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/standard-dark.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='standard'; $colorVariant='dark'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/standard-tonal.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='standard'; $colorVariant='tonal'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/standard-accent.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='standard'; $colorVariant='accent'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/filled-dark.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='filled'; $colorVariant='dark'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/filled-tonal.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='filled'; $colorVariant='tonal'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/filled-accent.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='filled'; $colorVariant='accent'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/tactical-dark.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='tactical'; $colorVariant='dark'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/tactical-tonal.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='tactical'; $colorVariant='tonal'; require dirname(__DIR__).'/_accordion_base.php';
```

```php
// templates/tactical-accent.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$style='tactical'; $colorVariant='accent'; require dirname(__DIR__).'/_accordion_base.php';
```

- [ ] **Step 3: Create `view.css`**

```css
/* packages/guerrilla/themes/guerrilla/blocks/accordion/view.css */

/* === Standard Style === */
.md3-accordion--standard .md3-accordion__item {
    margin-bottom: 3px;
}

.md3-accordion--standard .md3-accordion__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 10px 14px;
    background-color: var(--g-ol-700);
    color: var(--g-cr-100);
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-align: left;
    transition: background-color 0.15s, box-shadow 0.2s;
}
.md3-accordion--standard .md3-accordion__header:hover {
    background-color: var(--g-ol-600);
    box-shadow: 0 0 10px 2px var(--g-glow-primary);
}
.md3-accordion--standard .md3-accordion__item.is-open .md3-accordion__header {
    background-color: var(--g-ol-900);
    color: var(--g-cr-100);
    border-radius: 4px 4px 0 0;
}
.md3-accordion--standard .md3-accordion__item.is-open .md3-accordion__chevron {
    color: var(--g-or-400);
    transform: rotate(180deg);
}
.md3-accordion--standard .md3-accordion__chevron {
    font-size: 11px;
    color: var(--g-ol-300);
    transition: transform 0.25s;
    flex-shrink: 0;
}
.md3-accordion--standard .md3-accordion__content {
    padding: 12px 14px;
    background-color: #ffffff;
    border: 1px solid var(--g-cr-300);
    border-top: none;
    border-radius: 0 0 4px 4px;
    font-size: 14px;
    line-height: 1.65;
    color: #444;
}

/* Standard — Dark variant */
.md3-accordion--standard.md3-block--dark .md3-accordion__content {
    background-color: var(--g-ol-800);
    border-color: var(--g-ol-700);
    color: var(--g-ol-200);
}

/* === Filled Style === */
.md3-accordion--filled .md3-accordion__item {
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 5px;
}
.md3-accordion--filled .md3-accordion__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 10px 14px;
    background-color: var(--g-ol-200);
    color: var(--g-ol-900);
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-align: left;
    transition: background-color 0.15s;
}
.md3-accordion--filled .md3-accordion__item.is-open .md3-accordion__header {
    background-color: var(--g-ol-700);
    color: var(--g-cr-100);
}
.md3-accordion--filled .md3-accordion__item.is-open .md3-accordion__chevron {
    transform: rotate(180deg);
    color: var(--g-ol-300);
}
.md3-accordion--filled .md3-accordion__chevron {
    font-size: 11px;
    transition: transform 0.25s;
    flex-shrink: 0;
}
.md3-accordion--filled .md3-accordion__content {
    padding: 12px 14px;
    background-color: var(--g-cr-200);
    font-size: 14px;
    line-height: 1.65;
    color: var(--g-ol-700);
}

/* Filled — Dark variant */
.md3-accordion--filled.md3-block--dark .md3-accordion__header {
    background-color: var(--g-ol-700);
    color: var(--g-cr-100);
}
.md3-accordion--filled.md3-block--dark .md3-accordion__item.is-open .md3-accordion__header {
    background-color: var(--g-ol-500);
}
.md3-accordion--filled.md3-block--dark .md3-accordion__content {
    background-color: var(--g-ol-800);
    color: var(--g-ol-200);
}

/* === Tactical Style === */
.md3-accordion--tactical .md3-accordion__item {
    border-left: 3px solid var(--g-ol-500);
    margin-bottom: 4px;
    transition: border-color 0.2s;
}
.md3-accordion--tactical .md3-accordion__item.is-open {
    border-left-color: var(--g-or-500);
}
.md3-accordion--tactical .md3-accordion__header {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 9px 12px;
    background-color: var(--g-cr-200);
    color: var(--g-ol-900);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    cursor: pointer;
    text-align: left;
    transition: background-color 0.15s;
}
.md3-accordion--tactical .md3-accordion__item.is-open .md3-accordion__header {
    background-color: var(--g-cr-100);
    color: var(--g-or-500);
}
.md3-accordion--tactical .md3-accordion__tac-icon {
    font-size: 10px;
    transition: transform 0.25s;
    color: var(--g-ol-500);
    flex-shrink: 0;
}
.md3-accordion--tactical .md3-accordion__item.is-open .md3-accordion__tac-icon {
    transform: rotate(90deg);
    color: var(--g-or-500);
}
.md3-accordion--tactical .md3-accordion__content {
    padding: 8px 12px 12px 28px;
    background-color: var(--g-cr-100);
    border-top: 1px solid var(--g-cr-300);
    font-size: 14px;
    line-height: 1.65;
    color: #555;
}

/* Tactical — Dark variant */
.md3-accordion--tactical.md3-block--dark .md3-accordion__header {
    background-color: var(--g-ol-800);
    color: var(--g-ol-200);
}
.md3-accordion--tactical.md3-block--dark .md3-accordion__item.is-open .md3-accordion__header {
    background-color: var(--g-ol-900);
    color: var(--g-or-400);
}
.md3-accordion--tactical.md3-block--dark .md3-accordion__content {
    background-color: var(--g-ol-900);
    border-top-color: var(--g-ol-700);
    color: var(--g-ol-200);
}
```

- [ ] **Step 4: Manual test**

1. Add Accordion block with 3 entries (each with title and body HTML)
2. Default template (standard light): olive-700 headers with cream text, click to expand — white body panel appears
3. Test JS: clicking item opens it; clicking again closes; clicking another item closes the first
4. Switch to "tactical" template: uppercase titles, left orange border on active item
5. Verify `aria-expanded` attribute changes correctly on click (DevTools → Elements)

- [ ] **Step 5: Commit**

```bash
git add packages/guerrilla/themes/guerrilla/blocks/accordion/
git commit -m "feat(blocks): add Accordion block MD3 templates (3 styles × 4 colors + JS)"
```

---

## Task 7: Page List Block Templates

**Files:**
- Create: `packages/guerrilla/themes/guerrilla/blocks/page_list/_render.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/page_list/view.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/page_list/view.css`
- Create: `packages/guerrilla/themes/guerrilla/blocks/page_list/templates/` (14 files)

> ConcreteCMS `page_list` block: verify variable against `concrete/blocks/page_list/view.php`. Expected: `$pages` array of `\Concrete\Core\Page\Page` objects. Each page exposes: `getCollectionName()`, `getCollectionDescription()`, `getCollectionDatePublic('Y')`, `getCollectionLink()`, `getThumbnail()` (returns `\Concrete\Core\File\Image\Thumbnail\Type\Version` or null). For thumbnail URL: `$thumb ? $thumb->getURL() : ''`.

- [ ] **Step 1: Create `_render.php`**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/page_list/_render.php
defined('C5_EXECUTE') or die('Access Denied.');
$layout       = $layout       ?? 'cards';
$colorVariant = $colorVariant ?? 'light';
?>
<div class="md3-block md3-page-list md3-block--<?= $colorVariant ?>
            md3-page-list--<?= $layout ?>">

    <?php if (empty($pages)): ?>
        <p class="md3-page-list__empty"><?= t('No pages found.') ?></p>

    <?php elseif ($layout === 'cards'): ?>
        <!-- LAYOUT: Cards Grid -->
        <div class="md3-page-list__grid">
            <?php foreach ($pages as $page):
                $thumb = $page->getThumbnail();
                $thumbUrl = ($thumb && method_exists($thumb, 'getURL')) ? $thumb->getURL() : '';
            ?>
                <article class="md3-page-list__card md3-glow-primary">
                    <?php if ($thumbUrl): ?>
                        <div class="md3-page-list__card-img"
                             style="background-image:url('<?= htmlspecialchars($thumbUrl) ?>')">
                        </div>
                    <?php else: ?>
                        <div class="md3-page-list__card-img md3-page-list__card-img--placeholder"></div>
                    <?php endif; ?>
                    <div class="md3-page-list__card-body">
                        <h3 class="md3-page-list__title">
                            <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>">
                                <?= htmlspecialchars($page->getCollectionName()) ?>
                            </a>
                        </h3>
                        <?php if ($desc = $page->getCollectionDescription()): ?>
                            <p class="md3-page-list__desc"><?= htmlspecialchars($desc) ?></p>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>"
                           class="md3-btn md3-btn--filled md3-glow-cta">
                            <?= t('Read more') ?> →
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

    <?php elseif ($layout === 'horizontal-list'): ?>
        <!-- LAYOUT: Horizontal List -->
        <div class="md3-page-list__hlist">
            <?php foreach ($pages as $page):
                $thumb = $page->getThumbnail();
                $thumbUrl = ($thumb && method_exists($thumb, 'getURL')) ? $thumb->getURL() : '';
            ?>
                <article class="md3-page-list__hitem">
                    <?php if ($thumbUrl): ?>
                        <div class="md3-page-list__hthumb"
                             style="background-image:url('<?= htmlspecialchars($thumbUrl) ?>')">
                        </div>
                    <?php else: ?>
                        <div class="md3-page-list__hthumb md3-page-list__hthumb--placeholder"></div>
                    <?php endif; ?>
                    <div class="md3-page-list__hcontent">
                        <h3 class="md3-page-list__title">
                            <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>"
                               class="md3-glow-text">
                                <?= htmlspecialchars($page->getCollectionName()) ?>
                            </a>
                        </h3>
                        <?php if ($desc = $page->getCollectionDescription()): ?>
                            <p class="md3-page-list__desc"><?= htmlspecialchars($desc) ?></p>
                        <?php endif; ?>
                    </div>
                </article>
                <md-divider></md-divider>
            <?php endforeach; ?>
        </div>

    <?php elseif ($layout === 'featured'): ?>
        <!-- LAYOUT: Featured + Side List -->
        <div class="md3-page-list__featured">
            <?php $first = array_shift($pages);
                  $firstThumb = $first ? $first->getThumbnail() : null;
                  $firstThumbUrl = ($firstThumb && method_exists($firstThumb,'getURL'))
                                    ? $firstThumb->getURL() : ''; ?>
            <?php if ($first): ?>
                <article class="md3-page-list__featured-main"
                         <?= $firstThumbUrl ? "style=\"background-image:url('" . htmlspecialchars($firstThumbUrl) . "')\"" : '' ?>>
                    <div class="md3-page-list__featured-overlay">
                        <span class="md3-section-label"><?= t('Featured') ?></span>
                        <h2 class="md3-page-list__featured-title">
                            <a href="<?= htmlspecialchars($first->getCollectionLink()) ?>">
                                <?= htmlspecialchars($first->getCollectionName()) ?>
                            </a>
                        </h2>
                        <?php if ($desc = $first->getCollectionDescription()): ?>
                            <p class="md3-page-list__desc"><?= htmlspecialchars($desc) ?></p>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($first->getCollectionLink()) ?>"
                           class="md3-btn md3-btn--filled md3-glow-cta">
                            <?= t('Read more') ?> →
                        </a>
                    </div>
                </article>
            <?php endif; ?>
            <div class="md3-page-list__featured-side">
                <?php foreach ($pages as $page): ?>
                    <article class="md3-page-list__side-item">
                        <h4 class="md3-page-list__title">
                            <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>"
                               class="md3-glow-text">
                                <?= htmlspecialchars($page->getCollectionName()) ?>
                            </a>
                        </h4>
                        <?php if ($desc = $page->getCollectionDescription()): ?>
                            <p class="md3-page-list__side-desc"><?= htmlspecialchars($desc) ?></p>
                        <?php endif; ?>
                    </article>
                    <md-divider></md-divider>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else: /* minimal */ ?>
        <!-- LAYOUT: Minimal List -->
        <ul class="md3-page-list__minimal">
            <?php foreach ($pages as $page): ?>
                <li class="md3-page-list__minimal-item">
                    <span class="md3-page-list__minimal-dot" aria-hidden="true"></span>
                    <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>"
                       class="md3-page-list__minimal-link md3-glow-text">
                        <?= htmlspecialchars($page->getCollectionName()) ?>
                    </a>
                    <span class="md3-page-list__minimal-date">
                        <?= htmlspecialchars($page->getCollectionDatePublic('Y')) ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

</div>
```

- [ ] **Step 2: Create `view.php` and all 14 template files**

```php
// view.php (cards + light — default)
<?php defined('C5_EXECUTE') or die('Access Denied.');
$layout='cards'; $colorVariant='light'; require __DIR__.'/_render.php';
```

```php
// templates/horizontal-list.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$layout='horizontal-list'; $colorVariant='light'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/featured.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$layout='featured'; $colorVariant='light'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/minimal.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$layout='minimal'; $colorVariant='light'; require dirname(__DIR__).'/_render.php';
```

For each layout, create dark/tonal/accent variants (12 more files):

```php
// templates/cards-dark.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$layout='cards'; $colorVariant='dark'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/cards-tonal.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$layout='cards'; $colorVariant='tonal'; require dirname(__DIR__).'/_render.php';
```

```php
// templates/cards-accent.php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$layout='cards'; $colorVariant='accent'; require dirname(__DIR__).'/_render.php';
```

*(Create equivalent files for `horizontal-list-dark/tonal/accent`, `featured-dark/tonal/accent`, `minimal-dark/tonal/accent` — same 3-line pattern, changing `$layout` and `$colorVariant`.)*

- [ ] **Step 3: Create `view.css`**

```css
/* packages/guerrilla/themes/guerrilla/blocks/page_list/view.css */

/* === Cards Grid === */
.md3-page-list--cards .md3-page-list__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.25rem;
}

.md3-page-list__card {
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.md3-block--light .md3-page-list__card {
    background: #ffffff;
    border: 1px solid var(--g-cr-300);
}
.md3-block--dark .md3-page-list__card {
    background: var(--g-ol-800);
    border: 1px solid var(--g-ol-700);
}
.md3-block--tonal .md3-page-list__card {
    background: var(--g-cr-100);
    border: 1px solid var(--g-ol-300);
}
.md3-block--accent .md3-page-list__card {
    background: rgba(0,0,0,0.15);
    border: 1px solid rgba(255,255,255,0.25);
}

.md3-page-list__card-img {
    height: 140px;
    background-size: cover;
    background-position: center;
    background-color: var(--g-ol-300);
}
.md3-page-list__card-img--placeholder {
    background-color: var(--g-ol-500);
}
.md3-page-list__card-body {
    padding: 14px;
}

/* === Horizontal List === */
.md3-page-list--horizontal-list .md3-page-list__hitem {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding: 14px 0;
}
.md3-page-list__hthumb {
    width: 80px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 6px;
    background-size: cover;
    background-position: center;
    background-color: var(--g-ol-300);
}
.md3-page-list__hthumb--placeholder {
    background-color: var(--g-ol-500);
}
.md3-page-list__hcontent { flex: 1; }

/* === Featured + Side === */
.md3-page-list--featured .md3-page-list__featured {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    min-height: 280px;
}
@media (max-width: 640px) {
    .md3-page-list--featured .md3-page-list__featured {
        grid-template-columns: 1fr;
    }
}
.md3-page-list__featured-main {
    background: var(--g-ol-900);
    background-size: cover;
    background-position: center;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    min-height: 240px;
}
.md3-page-list__featured-overlay {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 16px;
    background: linear-gradient(to top, rgba(10,15,5,0.9), transparent);
}
.md3-page-list__featured-title {
    font-size: 20px;
    font-weight: 700;
    margin: 4px 0 8px;
}
.md3-page-list__featured-title a {
    color: var(--g-cr-100);
    text-decoration: none;
}
.md3-page-list__featured-side {
    display: flex;
    flex-direction: column;
}
.md3-page-list__side-item {
    padding: 10px 0;
}
.md3-page-list__side-desc {
    font-size: 12px;
    margin: 4px 0 0;
    opacity: 0.75;
}

/* === Minimal List === */
.md3-page-list--minimal .md3-page-list__minimal {
    list-style: none;
    padding: 0;
    margin: 0;
}
.md3-page-list__minimal-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 0;
    border-bottom: 1px solid var(--g-cr-300);
}
.md3-block--dark .md3-page-list__minimal-item {
    border-bottom-color: var(--g-ol-700);
}
.md3-page-list__minimal-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--g-or-500);
    flex-shrink: 0;
}
.md3-page-list__minimal-link {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
}
.md3-block--light .md3-page-list__minimal-link { color: var(--g-ol-900); }
.md3-block--dark  .md3-page-list__minimal-link { color: var(--g-cr-100); }
.md3-block--tonal .md3-page-list__minimal-link { color: var(--g-ol-900); }
.md3-block--accent .md3-page-list__minimal-link { color: #ffffff; }

.md3-page-list__minimal-date {
    font-size: 11px;
    color: var(--g-ol-500);
    flex-shrink: 0;
}
.md3-block--dark .md3-page-list__minimal-date { color: var(--g-ol-300); }

/* === Shared typography === */
.md3-page-list__title {
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 6px;
    line-height: 1.3;
}
.md3-page-list__title a { text-decoration: none; }
.md3-block--light .md3-page-list__title a { color: var(--g-ol-900); }
.md3-block--dark  .md3-page-list__title a { color: var(--g-cr-100); }
.md3-block--tonal .md3-page-list__title a { color: var(--g-ol-900); }
.md3-block--accent .md3-page-list__title a { color: #ffffff; }

.md3-page-list__desc {
    font-size: 13px;
    margin: 0 0 12px;
    line-height: 1.5;
}
.md3-block--light .md3-page-list__desc { color: #666; }
.md3-block--dark  .md3-page-list__desc { color: var(--g-ol-300); }
.md3-block--tonal .md3-page-list__desc { color: var(--g-ol-700); }
.md3-block--accent .md3-page-list__desc { color: rgba(255,255,255,0.85); }

.md3-page-list__empty {
    font-style: italic;
    opacity: 0.65;
    padding: 1rem 0;
}
```

- [ ] **Step 4: Manual test**

1. Create at least 4 sub-pages under a test page, each with a title, description, and thumbnail image
2. Add Page List block, configure it to show those sub-pages
3. Default (cards, light): 3-col grid with thumbnails, descriptions, orange "Read more" button
4. Cards hover: olive glow on each card
5. Switch to "featured" template: first page large on dark bg, rest in side list
6. Switch to "minimal" template: compact list with orange dots and year dates

- [ ] **Step 5: Commit**

```bash
git add packages/guerrilla/themes/guerrilla/blocks/page_list/
git commit -m "feat(blocks): add Page List block MD3 templates (4 layouts × 4 colors)"
```

---

## Task 8: Auto Nav Block Templates

**Files:**
- Create: `packages/guerrilla/themes/guerrilla/blocks/autonav/_render.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/autonav/view.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/autonav/view.css`
- Create: `packages/guerrilla/themes/guerrilla/blocks/autonav/templates/cream-topbar.php`
- Create: `packages/guerrilla/themes/guerrilla/blocks/autonav/templates/sidebar.php`

> ConcreteCMS `autonav` block: Reference `concrete/blocks/autonav/view.php` on [GitHub](https://github.com/concretecms/concretecms/blob/9.x/concrete/blocks/auto_nav/view.php). The block exposes a `$navObjects` array (array of `NavObject` objects). Each `NavObject` has: `->url`, `->name`, `->target`, `->isSelected` (bool), `->isCurrent` (bool), `->subNavObjects` (array of child `NavObject`s), `->hasSubmenu` (bool), `->level` (int). The block handle in the filesystem is `auto_nav` — verify the exact directory name with `ls concrete/blocks/`.

- [ ] **Step 1: Create `_render.php`**

```php
<?php
// packages/guerrilla/themes/guerrilla/blocks/autonav/_render.php
defined('C5_EXECUTE') or die('Access Denied.');
$navStyle     = $navStyle     ?? 'dark-topbar';
$colorVariant = $colorVariant ?? 'light';

/**
 * Recursive helper to render nav items as <li> elements.
 */
function md3_render_nav_items(array $items, int $depth = 0): void {
    foreach ($items as $item): ?>
        <li class="md3-nav__item
                   <?= $item->isSelected ? 'md3-nav__item--active' : '' ?>
                   <?= $item->isCurrent  ? 'md3-nav__item--current' : '' ?>
                   <?= $item->hasSubmenu ? 'md3-nav__item--has-children' : '' ?>
                   md3-nav__item--depth-<?= (int)$depth ?>">
            <a href="<?= htmlspecialchars($item->url) ?>"
               target="<?= htmlspecialchars($item->target ?? '_self') ?>"
               class="md3-nav__link md3-glow-text
                      <?= $item->isSelected ? 'md3-nav__link--active' : '' ?>">
                <?= htmlspecialchars($item->name) ?>
                <?php if ($item->hasSubmenu): ?>
                    <span class="md3-nav__dropdown-icon" aria-hidden="true">▾</span>
                <?php endif; ?>
            </a>
            <?php if (!empty($item->subNavObjects)): ?>
                <ul class="md3-nav__dropdown">
                    <?php md3_render_nav_items($item->subNavObjects, $depth + 1); ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach;
}
?>
<nav class="md3-block md3-autonav md3-autonav--<?= $navStyle ?> md3-block--<?= $colorVariant ?>"
     aria-label="<?= t('Site Navigation') ?>">

    <!-- Mobile hamburger toggle -->
    <button class="md3-autonav__hamburger" aria-label="<?= t('Toggle navigation') ?>"
            aria-expanded="false" data-md3-nav-toggle>
        <span></span><span></span><span></span>
    </button>

    <ul class="md3-autonav__list" role="list">
        <?php if (!empty($navObjects)): ?>
            <?php md3_render_nav_items($navObjects); ?>
        <?php endif; ?>
    </ul>

</nav>

<script>
(function() {
    if (window.md3NavLoaded) return;
    window.md3NavLoaded = true;

    // Hamburger toggle
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-md3-nav-toggle]');
        if (btn) {
            var nav = btn.closest('.md3-autonav');
            var isOpen = nav.classList.toggle('md3-autonav--open');
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            return;
        }

        // Mobile dropdown toggle (only on small screens)
        if (window.innerWidth < 768) {
            var parentLink = e.target.closest('.md3-nav__item--has-children > .md3-nav__link');
            if (parentLink) {
                e.preventDefault();
                var item = parentLink.closest('.md3-nav__item--has-children');
                item.classList.toggle('md3-nav__item--dropdown-open');
            }
        }
    });
})();
</script>
```

- [ ] **Step 2: Create `view.php` (dark-topbar, light)**

```php
<?php
defined('C5_EXECUTE') or die('Access Denied.');
$navStyle     = 'dark-topbar';
$colorVariant = 'light';
require __DIR__ . '/_render.php';
```

- [ ] **Step 3: Create `templates/cream-topbar.php` and `templates/sidebar.php`**

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$navStyle='cream-topbar'; $colorVariant='light';
require dirname(__DIR__).'/_render.php';
```

```php
<?php defined('C5_EXECUTE') or die('Access Denied.');
$navStyle='sidebar'; $colorVariant='light';
require dirname(__DIR__).'/_render.php';
```

- [ ] **Step 4: Create `view.css`**

```css
/* packages/guerrilla/themes/guerrilla/blocks/autonav/view.css */

/* === Common === */
.md3-autonav {
    position: relative;
}
.md3-autonav__list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.md3-nav__link {
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color 0.2s, text-shadow 0.2s, border-color 0.2s;
}
.md3-nav__dropdown {
    list-style: none;
    padding: 4px 0;
    margin: 0;
}
.md3-nav__dropdown-icon {
    font-size: 11px;
    opacity: 0.7;
}

/* Hamburger */
.md3-autonav__hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
}
.md3-autonav__hamburger span {
    display: block;
    width: 22px;
    height: 2px;
    border-radius: 2px;
    background-color: var(--g-cr-100);
    transition: background-color 0.2s;
}

@media (max-width: 767px) {
    .md3-autonav__hamburger { display: flex; }
    .md3-autonav__list { display: none; flex-direction: column; padding: 8px 0; }
    .md3-autonav--open .md3-autonav__list { display: flex; }
    .md3-nav__dropdown { display: none; }
    .md3-nav__item--dropdown-open > .md3-nav__dropdown { display: block; }
}

/* === Dark Topbar === */
.md3-autonav--dark-topbar {
    background-color: var(--g-ol-800);
    border-radius: 6px;
}
.md3-autonav--dark-topbar .md3-autonav__list {
    display: flex;
    align-items: center;
    height: 56px;
    padding: 0 16px;
    gap: 4px;
}
.md3-autonav--dark-topbar .md3-nav__item {
    position: relative;
    height: 56px;
    display: flex;
    align-items: stretch;
}
.md3-autonav--dark-topbar .md3-nav__link {
    padding: 0 14px;
    color: var(--g-ol-300);
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.3px;
    border-bottom: 2px solid transparent;
    align-items: center;
}
.md3-autonav--dark-topbar .md3-nav__link:hover,
.md3-autonav--dark-topbar .md3-nav__link--active {
    color: var(--g-cr-100);
    border-bottom-color: var(--g-or-400);
    text-shadow: 0 0 10px var(--g-glow-cta);
}
/* Dropdown */
.md3-autonav--dark-topbar .md3-nav__dropdown {
    position: absolute;
    top: 56px;
    left: 0;
    min-width: 180px;
    background: var(--g-ol-900);
    border: 1px solid var(--g-ol-700);
    border-top: none;
    border-radius: 0 0 6px 6px;
    display: none;
    z-index: 100;
}
.md3-autonav--dark-topbar .md3-nav__item--has-children:hover > .md3-nav__dropdown,
.md3-autonav--dark-topbar .md3-nav__item--dropdown-open > .md3-nav__dropdown {
    display: block;
}
.md3-autonav--dark-topbar .md3-nav__dropdown .md3-nav__link {
    padding: 9px 16px;
    border-bottom: none;
    font-size: 13px;
    height: auto;
}
.md3-autonav--dark-topbar .md3-nav__dropdown .md3-nav__link:hover {
    background: var(--g-ol-800);
    color: var(--g-or-300);
    text-shadow: 0 0 8px var(--g-glow-cta);
}

@media (max-width: 767px) {
    .md3-autonav--dark-topbar .md3-autonav__list {
        height: auto;
        flex-direction: column;
        padding: 8px 16px;
        align-items: flex-start;
        gap: 0;
    }
    .md3-autonav--dark-topbar .md3-nav__item { height: auto; }
    .md3-autonav--dark-topbar .md3-nav__link {
        padding: 10px 0;
        border-bottom: none;
        border-left: 2px solid transparent;
    }
    .md3-autonav--dark-topbar .md3-nav__dropdown {
        position: static;
        border-radius: 0;
        border: none;
        padding-left: 16px;
    }
}

/* === Cream Topbar === */
.md3-autonav--cream-topbar {
    background-color: var(--g-cr-100);
    border: 1px solid var(--g-cr-300);
    border-radius: 6px;
}
.md3-autonav--cream-topbar .md3-autonav__list {
    display: flex;
    align-items: center;
    height: 56px;
    padding: 0 16px;
    gap: 4px;
}
.md3-autonav--cream-topbar .md3-nav__item {
    position: relative;
    height: 56px;
    display: flex;
    align-items: stretch;
}
.md3-autonav--cream-topbar .md3-nav__link {
    padding: 0 14px;
    color: var(--g-ol-700);
    font-size: 13px;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    align-items: center;
}
.md3-autonav--cream-topbar .md3-nav__link:hover {
    color: var(--g-ol-500);
    border-bottom-color: var(--g-or-500);
    text-shadow: none;
}
.md3-autonav--cream-topbar .md3-nav__link--active {
    color: var(--g-ol-900);
    border-bottom-color: var(--g-ol-500);
}
.md3-autonav--cream-topbar .md3-nav__dropdown {
    position: absolute;
    top: 56px;
    left: 0;
    min-width: 180px;
    background: var(--g-cr-100);
    border: 1px solid var(--g-cr-300);
    border-top: none;
    border-radius: 0 0 6px 6px;
    display: none;
    z-index: 100;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.md3-autonav--cream-topbar .md3-nav__item--has-children:hover > .md3-nav__dropdown,
.md3-autonav--cream-topbar .md3-nav__item--dropdown-open > .md3-nav__dropdown {
    display: block;
}
.md3-autonav--cream-topbar .md3-nav__dropdown .md3-nav__link {
    padding: 9px 16px;
    border-bottom: none;
    color: var(--g-ol-700);
    height: auto;
}
.md3-autonav--cream-topbar .md3-nav__dropdown .md3-nav__link:hover {
    color: var(--g-or-500);
    background: var(--g-cr-200);
}
.md3-autonav--cream-topbar .md3-autonav__hamburger span {
    background-color: var(--g-ol-700);
}

/* === Sidebar Nav === */
.md3-autonav--sidebar {
    background-color: var(--g-ol-900);
    border-radius: 8px;
    padding: 8px 0;
}
.md3-autonav--sidebar .md3-autonav__list {
    display: flex;
    flex-direction: column;
}
.md3-autonav--sidebar .md3-nav__item {
    border-left: 3px solid transparent;
    transition: border-color 0.15s;
}
.md3-autonav--sidebar .md3-nav__item--active,
.md3-autonav--sidebar .md3-nav__item--current {
    border-left-color: var(--g-or-500);
    background-color: rgba(255,255,255,0.04);
}
.md3-autonav--sidebar .md3-nav__link {
    padding: 10px 16px;
    color: var(--g-ol-300);
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.3px;
}
.md3-autonav--sidebar .md3-nav__link:hover {
    color: var(--g-cr-100);
    text-shadow: 0 0 8px var(--g-glow-primary);
}
.md3-autonav--sidebar .md3-nav__item--active .md3-nav__link,
.md3-autonav--sidebar .md3-nav__item--current .md3-nav__link {
    color: var(--g-cr-100);
    font-weight: 700;
}
/* Sidebar sub-items */
.md3-autonav--sidebar .md3-nav__dropdown {
    background: rgba(0,0,0,0.15);
}
.md3-autonav--sidebar .md3-nav__dropdown .md3-nav__link {
    padding: 7px 16px 7px 28px;
    font-size: 12px;
    color: var(--g-ol-200);
}
.md3-autonav--sidebar .md3-nav__dropdown .md3-nav__link:hover {
    color: var(--g-or-300);
}
/* Sidebar always shows sub-items (open) */
.md3-autonav--sidebar .md3-nav__dropdown {
    display: block;
}
/* Hamburger not needed in sidebar context */
.md3-autonav--sidebar .md3-autonav__hamburger {
    display: none;
}
```

- [ ] **Step 5: Manual test**

1. Add Auto Nav block to a page — configure to display top-level pages
2. Default (dark-topbar): olive-800 bar, olive-300 links → hover turns cream + orange underline + glow
3. Active page: orange underline, white text
4. Resize to mobile (< 768px): hamburger appears, links collapse, hamburger click expands them
5. Pages with children: hover over parent link on desktop → dropdown appears with dark olive background
6. Switch to "cream-topbar": cream background, olive links, no glow text-shadow on hover (olive glow instead)
7. Switch to "sidebar": vertical dark nav, active item orange left border, sub-pages indented

- [ ] **Step 6: Commit**

```bash
git add packages/guerrilla/themes/guerrilla/blocks/autonav/
git commit -m "feat(blocks): add Auto Nav block MD3 templates (dark-topbar, cream-topbar, sidebar + mobile JS)"
```

---

## Task 9: Final Verification & Push

- [ ] **Step 1: Verify all block template directories exist**

```bash
find packages/guerrilla/themes/guerrilla/blocks/ -name "view.php" | sort
# Expected output (7 files):
# blocks/accordion/view.php
# blocks/autonav/view.php
# blocks/breadcrumb/view.php
# blocks/content/view.php
# blocks/feature/view.php
# blocks/image/view.php
# blocks/page_list/view.php
```

- [ ] **Step 2: Count total PHP template files**

```bash
find packages/guerrilla/themes/guerrilla/blocks/ -name "*.php" | wc -l
# Expected: ~58 files
# (view.php + _base/_render + templates/... per block)
```

- [ ] **Step 3: Spot-check contrast on all 4 color variants for 2 blocks**

Open the ConcreteCMS site, test both a Content block and a Feature block with all 4 color variants. Confirm:
- Light: `#2d3a1a` text on `#f5f0e8` bg — should be clearly readable
- Dark: `#f5f0e8` text on `#2d3a1a` bg — should be clearly readable
- Tonal: `#2d3a1a` text on `#cdd9a8` bg — should be clearly readable
- Accent: `#ffffff` text on `#c45a00` bg — should be clearly readable
- Buttons `#ffffff` on `#e06a00` — readable (just meets WCAG AA)

- [ ] **Step 4: Verify glow on hover for all variants**

Check that hover glow appears on:
- CTA buttons (orange glow)
- Outlined buttons (olive glow)
- Nav links (orange text-shadow)
- Cards / feature cards (olive box-shadow)
- Accordion headers (olive box-shadow)
Adjust `--g-glow-intensity` in `main.css` if glow is too strong/weak.

- [ ] **Step 5: Push to origin**

```bash
git push origin main
```

---

## Appendix: ConcreteCMS Variable Reference

If block variables differ from what's listed in this plan, look up the exact variable names:

```bash
# ConcreteCMS is gitignored — check GitHub:
# https://github.com/concretecms/concretecms/blob/9.x/concrete/blocks/
```

Key blocks to verify:
- `accordion` — `$rows` array; each row `->title`, `->body`
- `page_list` — `$pages` array of `Page` objects; may be `$cArray` in older versions
- `autonav` — `$navObjects` array of `NavObject`; may require `$this->controller->getNavItems()` call
- `feature` — `$title`, `$body`, `$icon`, `$link`, `$linkText`
- `breadcrumb` — may need to be built from current page if `$breadcrumb` not provided

If a variable is missing, add at the top of `_base.php` / `_render.php`:
```php
$variableName = $variableName ?? $this->controller->getVariableName();
```
