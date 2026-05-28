# MD3 Block Templates — Design Spec

**Date:** 2026-05-29  
**Status:** Approved  
**Scope:** ConcreteCMS custom block templates with Material Design 3 styling, military aesthetic, olive/cream/orange palette.

---

## 1. Overview

Custom block templates for 7 ConcreteCMS blocks (Basic + Navigation categories), rendering with Material Web Components (MD3) and a military-inspired design system. Templates override the HTML output of existing blocks — no new block types, no change to editor workflow.

All templates are placed in:
```
packages/guerrilla/themes/guerrilla/blocks/<block_handle>/
```

ConcreteCMS resolves theme-level block templates automatically when the theme is active.

---

## 2. Color Palette

### CSS Custom Properties (defined in `themes/guerrilla/css/main.css`)

```css
:root {
  /* Olive Green — Primary / Secondary */
  --color-ol-900: #2d3a1a;
  --color-ol-700: #4a5e28;
  --color-ol-600: #526830;
  --color-ol-500: #6b883c;
  --color-ol-300: #adc47a;
  --color-ol-200: #cdd9a8;

  /* Cream — Surface / Background */
  --color-cr-100: #f5f0e8;
  --color-cr-200: #ede6d6;
  --color-cr-300: #e0d5bf;

  /* Orange — CTA / Accent */
  --color-or-600: #c45a00;
  --color-or-500: #e06a00;
  --color-or-400: #f27c1a;
  --color-or-300: #f59a4a;

  /* Glow — controllable per theme */
  --glow-primary:   rgba(107, 136, 60, 0.40);   /* olive hover glow */
  --glow-cta:       rgba(224, 106, 0,  0.55);   /* orange hover glow */
  --glow-intensity: 1;                           /* 0–1 global multiplier */
}
```

`--glow-intensity` is applied as a CSS filter/opacity multiplier in all hover rules, enabling theme-level glow control without modifying individual templates.

---

## 3. Color Variants

Each block supports 4 color variants, selectable by the editor as alternative templates in ConcreteCMS:

| Variant | Template file | Background | Text | CTA |
|---------|--------------|------------|------|-----|
| **A — Light** *(default)* | `view.php` | `cr-100` cream | `ol-900` | `or-500` orange |
| **B — Dark** | `templates/dark.php` | `ol-900` dark olive | `cr-100` cream | `or-500` orange |
| **C — Tonal** | `templates/tonal.php` | `ol-200` light olive | `ol-900` | `ol-700` |
| **D — Accent** | `templates/accent.php` | `or-600` orange | `#fff` | `#fff` |

All variants share the same PHP logic; only the wrapping CSS class differs (`md3-block--light`, `--dark`, `--tonal`, `--accent`). Block-level CSS handles the theming.

---

## 4. Hover Glow Effect

All interactive elements (buttons, nav items, cards, accordion headers) apply glow on `:hover` via `box-shadow` or `text-shadow`:

```css
/* CTA glow (filled buttons, active nav, accordion open) */
.md3-glow-cta:hover {
  box-shadow: 0 0 14px 3px var(--glow-cta);
}

/* Primary glow (outlined buttons, cards, nav links) */
.md3-glow-primary:hover {
  box-shadow: 0 0 12px 2px var(--glow-primary);
}

/* Text glow (nav items) */
.md3-glow-text:hover {
  text-shadow: 0 0 10px var(--glow-cta);
}
```

Intensity is controlled globally: `--glow-intensity` is set in the CMS theme customizer or theme CSS.

---

## 5. Blocks — Templates & Variants

### 5.1 Content Block (`content`)
**Handle:** `content`  
**Variants:** A Light (default), B Dark, C Tonal, D Accent  
**Layout:** Single variant — styled WYSIWYG output with MD3 typography scale, cream/olive headings, orange links.  
**Files:**
```
blocks/content/view.php
blocks/content/templates/dark.php
blocks/content/templates/tonal.php
blocks/content/templates/accent.php
blocks/content/view.css
```

---

### 5.2 Image Block (`image`)
**Handle:** `image`  
**Variants:** A–D color variants  
**Layout:** Single layout — image with optional caption. Caption uses MD3 `<md-divider>` above, olive/cream text.  
**Files:**
```
blocks/image/view.php
blocks/image/templates/dark.php
blocks/image/templates/tonal.php
blocks/image/templates/accent.php
blocks/image/view.css
```

---

### 5.3 Feature Block (`feature`)
**Handle:** `feature`  
**Color variants:** A–D  
**Layout variants (compositional):**

| Template | Description |
|----------|-------------|
| `view.php` | **A Icon Top** — icon centered above title + description + optional button |
| `templates/icon-left.php` | **B Icon Left** — icon left, content right (horizontal) |
| `templates/card.php` | **C Card** — MD3 outlined card, icon + title in header, description, button + chip |

**Files:**
```
blocks/feature/view.php
blocks/feature/templates/icon-left.php
blocks/feature/templates/card.php
blocks/feature/templates/dark.php          (dark color of default layout)
blocks/feature/templates/icon-left-dark.php
blocks/feature/templates/card-dark.php
... (tonal, accent variants per layout)
blocks/feature/view.css
```

> **Note:** ConcreteCMS template selection applies one template at a time. Color + layout are combined in the template filename: `icon-left-dark.php`, `card-tonal.php` etc. Total: 3 layouts × 4 colors = 12 template files + shared CSS.

---

### 5.4 Accordion Block (`accordion`)
**Handle:** `accordion`  
**Color variants:** A–D  
**Style variants (compositional):**

| Template | Description |
|----------|-------------|
| `view.php` | **A Standard** — `ol-700` header bg, `cr-100` text; open = `ol-900` bg + orange chevron |
| `templates/filled.php` | **B Filled Panels** — closed = `ol-200` bg, open = `ol-700` bg + cream text |
| `templates/tactical.php` | **C Tactical** — uppercase titles, left border accent (`or-500` when open), militaristic |

**Files:**
```
blocks/accordion/view.php
blocks/accordion/templates/filled.php
blocks/accordion/templates/tactical.php
blocks/accordion/view.css
```

Color variants embedded per-style via CSS classes — accordion uses `md3-accordion--standard/filled/tactical` + `md3-block--light/dark/tonal/accent`.

---

### 5.5 Page List Block (`page_list`)
**Handle:** `page_list`  
**Color variants:** A–D  
**Layout variants (compositional):**

| Template | Description |
|----------|-------------|
| `view.php` | **Cards Grid** — 3-col grid, thumbnail + title + description + button |
| `templates/horizontal-list.php` | **Horizontal List** — thumbnail left, title + description right |
| `templates/featured.php` | **Featured + List** — first item large (dark bg), remaining as side list |
| `templates/minimal.php` | **Minimal List** — dot + title + date, no thumbnail, compact |

**Files:**
```
blocks/page_list/view.php
blocks/page_list/templates/horizontal-list.php
blocks/page_list/templates/featured.php
blocks/page_list/templates/minimal.php
blocks/page_list/view.css
```

---

### 5.6 Auto Nav Block (`autonav`)
**Handle:** `autonav`  
**Color variants:** A–D  
**Layout variants (compositional):**

| Template | Description |
|----------|-------------|
| `view.php` | **A Dark Topbar** — `ol-800` bg, cream links, orange active + glow, dropdown ciemny |
| `templates/cream-topbar.php` | **B Cream Topbar** — `cr-100` bg, olive links, orange active underline |
| `templates/sidebar.php` | **D Sidebar Nav** — vertical, `ol-900` bg, active item with `or-500` left border |

All variants support:
- Dropdown subpages (ConcreteCMS auto nav depth)
- Active page highlighting
- Hover glow (`--glow-cta` for active/CTA items, `--glow-primary` for standard links)
- Mobile: hamburger toggle via vanilla JS (no jQuery dependency)

**Files:**
```
blocks/autonav/view.php
blocks/autonav/templates/cream-topbar.php
blocks/autonav/templates/sidebar.php
blocks/autonav/view.css
blocks/autonav/view.js
```

---

### 5.7 Breadcrumb Block (`breadcrumb`)
**Handle:** `breadcrumb`  
**Variants:** A–D color variants, single layout  
**Layout:** Horizontal breadcrumb with `›` separator in orange, current page in olive/cream depending on variant. MD3 `<md-divider>` optionally below.

**Files:**
```
blocks/breadcrumb/view.php
blocks/breadcrumb/templates/dark.php
blocks/breadcrumb/templates/tonal.php
blocks/breadcrumb/templates/accent.php
blocks/breadcrumb/view.css
```

---

## 6. Shared CSS Architecture

All block CSS lives in per-block `view.css`. Shared palette and glow variables are inherited from `themes/guerrilla/css/main.css` (already loaded globally).

No additional bundle build step required — block CSS files are loaded by ConcreteCMS alongside the block template.

The MD3 JS bundle (`themes/guerrilla/js/dist/material-web.js`) is already loaded globally via `on_start()` / `registerAssets()`.

---

## 7. Military Aesthetic Rules

Applied consistently across all blocks:

- **Typography:** Uppercase section labels with `letter-spacing: 2–3px`
- **Icons:** MD3 `<md-icon>` within olive square containers (`border-radius: 8–10px`)
- **Borders:** Accent-colored left borders for active states (not full outlines)
- **Buttons:** `border-radius: 20px` pill shape (MD3 standard)
- **Dividers:** `<md-divider>` component between sections
- **Nomenclature:** Internal class names follow `md3-block--<variant>` pattern

---

## 8. Out of Scope

- Form block (complex, separate design cycle)
- Image Slider (requires additional JS library decision)
- Search block
- Date Navigation
- YouTube / External Media
- New custom block types (no new block handles — templates only)
- PHP unit tests for templates

---

## 9. File Structure Summary

```
packages/guerrilla/themes/guerrilla/blocks/
├── content/
│   ├── view.php               # default (Light A)
│   ├── view.css
│   └── templates/dark.php, tonal.php, accent.php
├── image/
│   └── (same pattern)
├── feature/
│   ├── view.php               # Icon Top, Light
│   ├── view.css
│   └── templates/
│       ├── icon-left.php, card.php
│       └── *-dark.php, *-tonal.php, *-accent.php
├── accordion/
│   ├── view.php               # Standard A
│   ├── view.css
│   └── templates/filled.php, tactical.php
├── page_list/
│   ├── view.php               # Cards Grid
│   ├── view.css
│   └── templates/horizontal-list.php, featured.php, minimal.php
├── autonav/
│   ├── view.php               # Dark Topbar
│   ├── view.css
│   ├── view.js
│   └── templates/cream-topbar.php, sidebar.php
└── breadcrumb/
    ├── view.php
    ├── view.css
    └── templates/dark.php, tonal.php, accent.php
```

Total template files: ~45 PHP files + 8 CSS + 1 JS.
