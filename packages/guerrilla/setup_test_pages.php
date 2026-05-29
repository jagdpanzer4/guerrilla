<?php
/**
 * Guerrilla CMS — Test Pages Setup Script
 * Run via: php concrete/bin/concrete5 c5:exec packages/guerrilla/setup_test_pages.php
 *
 * Creates three test pages under site root:
 *   /typography-test   — all typography / content block variants
 *   /multimedia-test   — hero_image, image_slider, testimonial, image, accordion
 *   /navigation-test   — autonav, breadcrumbs, page_list
 *
 * Each block is added in every available Guerrilla template variant.
 */
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\File\Importer;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Support\Facade\Application;

$app = Application::getFacadeApplication();
$output = $app->make('helper/concrete/ui');

// ─────────────────────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────────────────────

function g_log(string $msg): void { echo "  → {$msg}" . PHP_EOL; }

/**
 * Add a block to a page, optionally set custom template, return Block.
 */
function g_add_block(Page $page, string $btHandle, array $data, string $template = ''): ?\Concrete\Core\Block\Block
{
    $bt = BlockType::getByHandle($btHandle);
    if (!$bt) {
        g_log("SKIP: block type '{$btHandle}' not found");
        return null;
    }
    $area = new Area('Main');
    $b = $page->addBlock($bt, $area, $data);
    if ($b && $template) {
        $b->setCustomTemplate($template);
        g_log("  template={$template}");
    }
    return $b ?: null;
}

/**
 * Generate a placeholder JPEG using PHP GD and import it into CCMS file manager.
 * Returns the file ID, or 0 on failure.
 */
function g_make_image(string $label, int $w, int $h, array $bg, array $fg): int
{
    if (!function_exists('imagecreatetruecolor')) {
        g_log("GD not available — skipping image generation");
        return 0;
    }
    $img  = imagecreatetruecolor($w, $h);
    $bgC  = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
    $fgC  = imagecolorallocate($img, $fg[0], $fg[1], $fg[2]);
    imagefill($img, 0, 0, $bgC);

    // gradient stripe
    for ($i = 0; $i < $h; $i++) {
        $alpha = (int)(($i / $h) * 80);
        $lineC = imagecolorallocatealpha($img, 0, 0, 0, $alpha);
        imageline($img, 0, $i, $w, $i, $lineC);
    }

    // centre label
    $fontSize = max(3, (int)($w / 120));
    $tW = imagefontwidth($fontSize) * strlen($label);
    $tH = imagefontheight($fontSize);
    imagestring($img, $fontSize, ($w - $tW) / 2, ($h - $tH) / 2, $label, $fgC);

    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($label));
    $path = sys_get_temp_dir() . "/g-{$slug}-{$w}x{$h}.jpg";
    imagejpeg($img, $path, 88);
    imagedestroy($img);

    try {
        $importer = new Importer();
        $fv = $importer->import($path, basename($path));
        @unlink($path);
        if ($fv instanceof \Concrete\Core\Entity\File\Version) {
            $fID = $fv->getFileID();
            g_log("Image '{$label}' → fID={$fID}");
            return (int)$fID;
        }
        if (is_object($fv) && method_exists($fv, 'getFileID')) {
            $fID = (int)$fv->getFileID();
            g_log("Image '{$label}' → fID={$fID}");
            return $fID;
        }
    } catch (\Throwable $e) {
        g_log("Image import failed: " . $e->getMessage());
    }
    return 0;
}

/**
 * Delete existing page at given path so we can re-create it.
 */
function g_delete_if_exists(string $path): void
{
    $p = Page::getByPath($path);
    if ($p && !$p->isError()) {
        $p->delete();
        g_log("Deleted existing page: {$path}");
    }
}

/**
 * Create page under home with full_width template.
 */
function g_create_page(string $name, string $handle, string $description = ''): ?Page
{
    $home = Page::getByID(Page::getHomePageID());
    $pt   = PageType::getByHandle('page');   // generic 'page' type
    if (!$pt) {
        g_log("ERROR: page type 'page' not found");
        return null;
    }
    $tmpl = PageTemplate::getByHandle('full_width');
    if (!$tmpl) {
        g_log("ERROR: page template 'full_width' not found — run package update first");
        return null;
    }
    $page = $home->add($pt, [
        'cName'        => $name,
        'cHandle'      => $handle,
        'cDescription' => $description,
    ]);
    if (!$page || $page->isError()) {
        g_log("ERROR creating page '{$handle}'");
        return null;
    }
    // Page::add() ignores pTemplateID — set template via direct DB update
    $db = \Concrete\Core\Support\Facade\Application::getFacadeApplication()->make('database/connection');
    $db->executeQuery(
        'UPDATE CollectionVersions cv JOIN Collections c ON c.cID=cv.cID SET cv.pTemplateID=? WHERE c.cHandle=?',
        [$tmpl->getPageTemplateID(), $handle]
    );
    g_log("Created page: /{$handle} (ID={$page->getCollectionID()}, template={$tmpl->getPageTemplateHandle()})");
    return $page;
}

// ─────────────────────────────────────────────────────────────
// STEP 0: Register full_width page template if missing
// ─────────────────────────────────────────────────────────────
echo PHP_EOL . "=== Guerrilla Test Pages Setup ===" . PHP_EOL . PHP_EOL;
echo "[ STEP 0 ] Page template 'full_width'" . PHP_EOL;
$fwTmpl = PageTemplate::getByHandle('full_width');
if (!$fwTmpl) {
    $fwTmpl = PageTemplate::add('full_width', 'Full Width');
    g_log("Registered 'full_width' page template");
} else {
    g_log("Template 'full_width' already exists (ID={$fwTmpl->getPageTemplateID()})");
}

// ─────────────────────────────────────────────────────────────
// STEP 1: Generate placeholder images
// ─────────────────────────────────────────────────────────────
echo PHP_EOL . "[ STEP 1 ] Generating placeholder images" . PHP_EOL;

// Colour palette (Guerrilla MD3)
$olive   = [45, 58, 26];
$cream   = [240, 234, 214];
$orange  = [224, 106, 0];
$dark    = [20, 28, 10];
$tonal   = [90, 112, 50];

$imgHero1    = g_make_image('HERO — Default',        1400, 700, $olive,  $cream);
$imgHero2    = g_make_image('HERO — Offset Title',   1400, 700, $dark,   $orange);
$imgSlide1   = g_make_image('SLIDE 1 — Assault',     1200, 600, $olive,  $cream);
$imgSlide2   = g_make_image('SLIDE 2 — Recon',       1200, 600, $tonal,  $cream);
$imgSlide3   = g_make_image('SLIDE 3 — Extraction',  1200, 600, $dark,   $orange);
$imgTestim   = g_make_image('PORTRAIT',               400, 400, $tonal,  $cream);
$imgTestimHero = g_make_image('TESTIMONIAL HERO BG', 1400, 700, $olive,  $cream);
$imgImg1     = g_make_image('IMAGE — Default',        800, 500, $olive,  $cream);
$imgImg2     = g_make_image('IMAGE — Dark',           800, 500, $dark,   $orange);
$imgImg3     = g_make_image('IMAGE — Accent',         800, 500, $orange, $dark);
$imgImg4     = g_make_image('IMAGE — Tonal',          800, 500, $tonal,  $cream);

// ─────────────────────────────────────────────────────────────
// STEP 2: TYPOGRAPHY TEST PAGE
// ─────────────────────────────────────────────────────────────
echo PHP_EOL . "[ STEP 2 ] Creating Typography Test page" . PHP_EOL;
g_delete_if_exists('/typography-test');
$typePage = g_create_page('Typography Test', 'typography-test', 'All typographic elements — Guerrilla MD3');

if ($typePage) {
    $typographyHTML = <<<'HTML'
<h1>Heading Level 1 — H1</h1>
<p>This is a body paragraph. Guerrilla MD3 typography uses a military-industrial design language with strong olive and cream contrasts. The <strong>bold text</strong> carries emphasis, while <em>italic text</em> conveys nuance. <a href="#">Hyperlinks</a> appear in amber-orange to signal action.</p>

<h2>Heading Level 2 — H2</h2>
<p>Second-level headings introduce major sections. Use sparingly — they divide the page into operational zones. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

<h3>Heading Level 3 — H3</h3>
<p>Tertiary headings are workhorses — clear without dominating. They guide readers through sub-sections. Pair with a short paragraph lead-in for best scan results.</p>

<h4>Heading Level 4 — H4</h4>
<p>H4 sits at the block level — useful for card titles, feature headers, and FAQ answers. Size is designed to differentiate from body without drawing excessive attention.</p>

<h5>Heading Level 5 — H5</h5>
<p>H5 is label-sized. Use for captions, sidebar titles, or supplemental data labels. Keep copy brief.</p>

<h6>Heading Level 6 — H6</h6>
<p>Smallest heading level. Treated almost as bold body text — best used sparingly for micro-labels.</p>

<hr>

<h2>Block Quotation</h2>
<blockquote>
    <p>"No battle plan survives contact with the enemy. But planning is everything."</p>
    <footer>— Field Marshal Helmuth von Moltke</footer>
</blockquote>

<hr>

<h2>Lists</h2>
<h3>Unordered List</h3>
<ul>
    <li>Tactical superiority through design clarity</li>
    <li>Material Design 3 — surface, container, content</li>
    <li>Guerrilla palette: olive, cream, orange, dark</li>
    <li>Bootstrap 5 grid — 12 columns, fluid containers</li>
    <li>ConcreteCMS 9.5 block architecture</li>
</ul>

<h3>Ordered List</h3>
<ol>
    <li>Establish base camp (install theme)</li>
    <li>Deploy reconnaissance (configure blocks)</li>
    <li>Advance position (customise templates)</li>
    <li>Secure perimeter (review on all breakpoints)</li>
    <li>Mission complete (go live)</li>
</ol>

<h3>Nested List</h3>
<ul>
    <li>Primary objective
        <ul>
            <li>Sub-task alpha</li>
            <li>Sub-task bravo</li>
        </ul>
    </li>
    <li>Secondary objective
        <ol>
            <li>Execute phase one</li>
            <li>Execute phase two</li>
        </ol>
    </li>
</ul>

<hr>

<h2>Inline Formatting</h2>
<p>
    <strong>Bold / strong</strong> — <em>Italic / emphasis</em> — <u>Underline</u> —
    <s>Strikethrough</s> — <mark>Highlighted text</mark> —
    <small>Small text</small> — <sup>Superscript</sup> — <sub>Subscript</sub>
</p>
<p>Abbreviation: <abbr title="Material Design 3">MD3</abbr></p>

<hr>

<h2>Code</h2>
<p>Inline code: use <code>var(--g-or-500)</code> for the primary orange token.</p>
<pre><code>/* Guerrilla MD3 — primary token definitions */
:root {
    --g-ol-500:  #556B2F;   /* olive mid */
    --g-or-500:  #E06A00;   /* orange CTA */
    --g-cr-100:  #F0EAD6;   /* cream bg */
}</code></pre>

<hr>

<h2>Table</h2>
<table>
    <thead>
        <tr>
            <th>Token</th>
            <th>Value</th>
            <th>Usage</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>--g-ol-500</code></td>
            <td><code>#556B2F</code></td>
            <td>Primary brand colour — headers, accents</td>
        </tr>
        <tr>
            <td><code>--g-or-500</code></td>
            <td><code>#E06A00</code></td>
            <td>Call-to-action — buttons, highlights</td>
        </tr>
        <tr>
            <td><code>--g-cr-100</code></td>
            <td><code>#F0EAD6</code></td>
            <td>Background — cream surface</td>
        </tr>
        <tr>
            <td><code>--g-ol-900</code></td>
            <td><code>#1A2208</code></td>
            <td>Dark surface — footers, dark blocks</td>
        </tr>
    </tbody>
</table>

<hr>

<h2>Long-form Paragraph Copy</h2>
<p>Guerrilla is a ConcreteCMS 9.5 theme built around the Material Design 3 specification. Its aesthetic draws from military-industrial iconography — olive drab greens, cream khakis, and high-contrast orange accents that mirror field markings and equipment labels. Every design decision reflects the battlefield principle of <strong>clarity under pressure</strong>: typography must be legible at distance, UI components must respond instantly, and colour contrast must pass WCAG AA at minimum.</p>
<p>The block system leverages ConcreteCMS's native edit mode while layering MD3 surface, container, and content roles. Full-width hero zones establish dominance at page entry; stripe containers break the page into tactical sectors; card grids deploy information with grid discipline. Navigation follows a two-variant system — cream for primary header use, dark for deep-content zones — ensuring identity consistency across all breakpoints.</p>
HTML;

    // Default content block
    g_log("Adding content block — default");
    g_add_block($typePage, 'content', ['content' => $typographyHTML]);

    // Dark template
    g_log("Adding content block — dark");
    g_add_block($typePage, 'content', [
        'content' => '<h2>Content Block — Dark Template</h2><p>This content block uses the <strong>dark</strong> template. It renders on a deep olive background with cream/white text. Ideal for high-contrast sections, mission statements, or featured callouts.</p><ul><li>Strong contrast ratio</li><li>Cream body text on olive-900</li><li>Orange links and highlights</li></ul><blockquote><p>"Darkness is your friend. Use it."</p></blockquote>',
    ], 'dark.php');

    // Accent template
    g_log("Adding content block — accent");
    g_add_block($typePage, 'content', [
        'content' => '<h2>Content Block — Accent Template</h2><p>The <strong>accent</strong> template applies the orange CTA surface. Use this for <em>critical callout boxes</em>, warnings, or highlighted announcements that demand immediate attention. Text is dark for maximum legibility.</p><ol><li>Immediate visual priority</li><li>Orange-500 background</li><li>Dark olive text</li></ol>',
    ], 'accent.php');

    // Tonal template
    g_log("Adding content block — tonal");
    g_add_block($typePage, 'content', [
        'content' => '<h2>Content Block — Tonal Template</h2><p>The <strong>tonal</strong> template uses a muted olive-tonal surface — lighter than dark but richer than default. Excellent for secondary sections, sidebars rendered full-width, or neutral informational zones.</p><table><thead><tr><th>Variant</th><th>Surface</th><th>Use case</th></tr></thead><tbody><tr><td>Default</td><td>Cream</td><td>Main body content</td></tr><tr><td>Dark</td><td>Olive-900</td><td>Hero alternates, statements</td></tr><tr><td>Accent</td><td>Orange-500</td><td>Alerts, CTAs</td></tr><tr><td>Tonal</td><td>Olive-200</td><td>Neutral zones</td></tr></tbody></table>',
    ], 'tonal.php');

    g_log("Typography page complete ✓");
}

// ─────────────────────────────────────────────────────────────
// STEP 3: MULTIMEDIA TEST PAGE
// ─────────────────────────────────────────────────────────────
echo PHP_EOL . "[ STEP 3 ] Creating Multimedia Test page" . PHP_EOL;
g_delete_if_exists('/multimedia-test');
$mediaPage = g_create_page('Multimedia Test', 'multimedia-test', 'All multimedia blocks — Guerrilla MD3');

if ($mediaPage) {

    // ── Hero Image: Default ──────────────────────────────────
    g_log("Adding hero_image — default");
    if ($imgHero1) {
        g_add_block($mediaPage, 'hero_image', [
            'image'       => $imgHero1,
            'title'       => 'Establish Dominance',
            'body'        => '<p>The default hero uses a full-width background image with a dark olive overlay. Title, body and CTA are positioned on the left using MD3 spacing tokens. Ideal for landing page entry points.</p>',
            'buttonText'  => 'Begin Mission',
            'buttonStyle' => 'filled',
            'buttonSize'  => '',
            'height'      => 70,
            'titleFormat' => 'h1',
        ]);
    }

    // ── Hero Image: Offset Title ─────────────────────────────
    g_log("Adding hero_image — offset_title");
    if ($imgHero2) {
        g_add_block($mediaPage, 'hero_image', [
            'image'       => $imgHero2,
            'title'       => 'Split Formation',
            'body'        => '<p>The <em>Offset Title</em> template splits the block horizontally — image occupies the left half, the text panel the right. Uses dark olive background with an orange border-left accent for the text panel. Best for team/product introduction sections.</p>',
            'buttonText'  => 'Learn Tactics',
            'buttonStyle' => 'outline',
            'buttonSize'  => '',
            'height'      => 60,
            'titleFormat' => 'h2',
        ], 'offset_title.php');
    }

    // ── Image Slider: Default ────────────────────────────────
    g_log("Adding image_slider — default");
    if ($imgSlide1 || $imgSlide2 || $imgSlide3) {
        $sliderBt = BlockType::getByHandle('image_slider');
        if ($sliderBt) {
            $area = new Area('Main');
            $sb = $mediaPage->addBlock($sliderBt, $area, [
                'navigationType' => 2,  // arrows + pager
                'timeout'        => 5000,
                'speed'          => 600,
                'pause'          => 1,
                'noAnimate'      => 0,
                'maxWidth'       => 0,
                'rows'           => [
                    ['fID' => $imgSlide1, 'title' => 'Assault Phase',     'description' => 'First contact — establish forward position and neutralise resistance.', 'linkURL' => ''],
                    ['fID' => $imgSlide2, 'title' => 'Reconnaissance',    'description' => 'Gather intelligence. Know the terrain before you advance.', 'linkURL' => ''],
                    ['fID' => $imgSlide3, 'title' => 'Extraction',        'description' => 'Mission complete — secure assets and withdraw.', 'linkURL' => ''],
                ],
            ]);
            g_log("image_slider default added (bID=" . ($sb ? $sb->getBlockID() : '?') . ")");
        }
    }

    // ── Image Slider: Hero Slider ────────────────────────────
    g_log("Adding image_slider — hero_slider");
    if ($imgSlide1 || $imgSlide2 || $imgSlide3) {
        $sliderBt = BlockType::getByHandle('image_slider');
        if ($sliderBt) {
            $area = new Area('Main');
            $sb2 = $mediaPage->addBlock($sliderBt, $area, [
                'navigationType' => 0,  // arrows only
                'timeout'        => 6000,
                'speed'          => 800,
                'pause'          => 1,
                'noAnimate'      => 0,
                'maxWidth'       => 0,
                'rows'           => [
                    ['fID' => $imgSlide1, 'title' => 'GUERRILLA',         'description' => 'Enter the field. Own the terrain.', 'linkURL' => ''],
                    ['fID' => $imgSlide2, 'title' => 'ADAPT',             'description' => 'Intelligence before action.', 'linkURL' => ''],
                    ['fID' => $imgSlide3, 'title' => 'EXECUTE',           'description' => 'Strike with precision. Withdraw with honour.', 'linkURL' => ''],
                ],
            ]);
            if ($sb2) { $sb2->setCustomTemplate('hero_slider.php'); }
            g_log("image_slider hero_slider added");
        }
    }

    // ── Testimonial: Default ─────────────────────────────────
    g_log("Adding testimonial — default");
    g_add_block($mediaPage, 'testimonial', [
        'fID'        => $imgTestim,
        'name'       => 'Maj. Aleksander Nowak',
        'position'   => 'Chief Strategy Officer',
        'company'    => 'Guerrilla Command',
        'companyURL' => 'https://example.com',
        'paragraph'  => 'The Guerrilla MD3 theme transformed our presence from a static page into a living command centre. Clarity, speed and tactical precision — everything our organisation required.',
    ]);

    // ── Testimonial: Hero Testimonial ────────────────────────
    g_log("Adding testimonial — hero_testimonial");
    g_add_block($mediaPage, 'testimonial', [
        'fID'          => $imgTestim,
        'awardImageID' => $imgTestimHero,
        'name'         => 'Płk. Maria Kowalska',
        'position'     => 'Director of Digital Operations',
        'company'      => 'GRTF Alliance',
        'companyURL'   => '',
        'paragraph'    => 'We deployed Guerrilla theme across twelve regional microsites in under 48 hours. The MD3 token system made every customisation predictable, consistent and — crucially — fast.',
    ], 'hero_testimonial.php');

    // ── Testimonial: Circle ──────────────────────────────────
    g_log("Adding testimonial — testimonial_circle");
    g_add_block($mediaPage, 'testimonial', [
        'fID'        => $imgTestim,
        'name'       => 'Sgt. Piotr Wiśniewski',
        'position'   => 'Field Technician',
        'company'    => '',
        'companyURL' => '',
        'paragraph'  => 'Clean. Fast. Battle-tested. I\'ve used dozens of CMS themes over the years and this is the first one that doesn\'t get in the way of the mission.',
    ], 'testimonial_circle.php');

    // ── Image Block: 4 templates ─────────────────────────────
    foreach ([
        ['default', '', $imgImg1, 'Image — Default Template', 'Standard image rendering with cream background.'],
        ['dark',   'dark.php',   $imgImg2, 'Image — Dark Template',    'Image on dark olive surface.'],
        ['accent', 'accent.php', $imgImg3, 'Image — Accent Template',  'Image with orange accent framing.'],
        ['tonal',  'tonal.php',  $imgImg4, 'Image — Tonal Template',   'Image on tonal olive-200 surface.'],
    ] as [$label, $tmpl, $fid, $title, $altText]) {
        if (!$fid) continue;
        g_log("Adding image block — {$label}");
        $b = g_add_block($mediaPage, 'image', ['fID' => $fid, 'altText' => $altText]);
        if ($b && $tmpl) $b->setCustomTemplate($tmpl);
    }

    // ── Accordion: selected template variants ────────────────
    $accordionEntries = [
        ['title' => 'Mission Briefing',     'description' => '<p>Every operation begins with a mission briefing. Know your objective, your assets, your constraints. No improvisation without preparation.</p>'],
        ['title' => 'Rules of Engagement',  'description' => '<p>Precise rules keep operations clean. Define your scope, your escalation path, and your exit criteria before you enter.</p>'],
        ['title' => 'After-Action Review',  'description' => '<p>What worked? What failed? The after-action review turns every mission — successful or not — into future capability.</p>'],
    ];

    foreach ([
        ['standard-light',    'standard-light.php'],
        ['standard-dark',     'standard-dark.php'],
        ['standard-accent',   'standard-accent.php'],
        ['filled-dark',       'filled-dark.php'],
        ['tactical-dark',     'tactical-dark.php'],
        ['tactical-tonal',    'tactical-tonal.php'],
    ] as [$label, $tmpl]) {
        g_log("Adding accordion — {$label}");
        $ab = g_add_block($mediaPage, 'accordion', [
            'initialState'       => 'openfirst',
            'itemHeadingFormat'  => 'h3',
            'alwaysOpen'         => 0,
            'flush'              => 0,
            'entries'            => $accordionEntries,
        ]);
        if ($ab && $tmpl) $ab->setCustomTemplate($tmpl);
    }

    g_log("Multimedia page complete ✓");
}

// ─────────────────────────────────────────────────────────────
// STEP 4: NAVIGATION TEST PAGE
// ─────────────────────────────────────────────────────────────
echo PHP_EOL . "[ STEP 4 ] Creating Navigation Test page" . PHP_EOL;
g_delete_if_exists('/navigation-test');
$navPage = g_create_page('Navigation Test', 'navigation-test', 'Navigation and listing blocks — Guerrilla MD3');

if ($navPage) {
    $navData = [
        'orderBy'                 => 'alpha_asc',
        'displayPages'            => 'top',
        'displayPagesCID'         => 1,
        'displayPagesIncludeSelf' => 0,
        'displaySubPages'         => 'all',
        'displaySubPageLevels'    => 'all',
        'displaySubPageLevelsNum' => 0,
        'displayUnavailablePages' => 0,
    ];

    // ── AutoNav: Default ─────────────────────────────────────
    g_log("Adding autonav — default");
    g_add_block($navPage, 'autonav', $navData);

    // ── AutoNav: Cream-Topbar ────────────────────────────────
    g_log("Adding autonav — cream-topbar");
    g_add_block($navPage, 'autonav', $navData, 'cream-topbar.php');

    // ── AutoNav: Sidebar ─────────────────────────────────────
    g_log("Adding autonav — sidebar");
    g_add_block($navPage, 'autonav', $navData, 'sidebar.php');

    // ── Breadcrumbs: 4 variants ──────────────────────────────
    foreach ([
        ['default', ''],
        ['dark',    'dark.php'],
        ['accent',  'accent.php'],
        ['tonal',   'tonal.php'],
    ] as [$label, $tmpl]) {
        g_log("Adding breadcrumbs — {$label}");
        $bb = g_add_block($navPage, 'breadcrumbs', []);
        if ($bb && $tmpl) $bb->setCustomTemplate($tmpl);
    }

    // ── Page List: 5 variant groups ──────────────────────────
    $plBase = [
        'num'                => 6,
        'orderBy'            => 'display_asc',
        'cParentID'          => 1,
        'cThis'              => 0,
        'includeName'        => 1,
        'includeDescription' => 1,
        'includeDate'        => 0,
        'displayAliases'     => 1,
        'displayThumbnail'   => 1,
        'truncateSummaries'  => 1,
        'truncateChars'      => 128,
        'paginate'           => 0,
        'titleFormat'        => 'h5',
    ];

    foreach ([
        ['cards-grid-light',       'cards-grid-light.php'],
        ['cards-grid-dark',        'cards-grid-dark.php'],
        ['cards-grid-accent',      'cards-grid-accent.php'],
        ['cards-grid-tonal',       'cards-grid-tonal.php'],
        ['featured-list-light',    'featured-list-light.php'],
        ['featured-list-dark',     'featured-list-dark.php'],
        ['horizontal-list-light',  'horizontal-list-light.php'],
        ['horizontal-list-dark',   'horizontal-list-dark.php'],
        ['minimal-light',          'minimal-light.php'],
        ['minimal-dark',           'minimal-dark.php'],
        ['minimal-accent',         'minimal-accent.php'],
    ] as [$label, $tmpl]) {
        g_log("Adding page_list — {$label}");
        $lb = g_add_block($navPage, 'page_list', $plBase);
        if ($lb && $tmpl) $lb->setCustomTemplate($tmpl);
    }

    g_log("Navigation page complete ✓");
}

// ─────────────────────────────────────────────────────────────
// STEP 5: Clear cache
// ─────────────────────────────────────────────────────────────
echo PHP_EOL . "[ STEP 5 ] Clearing cache" . PHP_EOL;
try {
    $cache = $app->make(\Concrete\Core\Cache\Page\PageCache::class);
    $cache->flush();
    $app->make('cache')->flush();
    $app->make('cache/expensive')->flush();
    g_log("Cache cleared ✓");
} catch (\Throwable $e) {
    g_log("Cache clear warning: " . $e->getMessage());
}

echo PHP_EOL . "=== DONE ===" . PHP_EOL;
echo "  /typography-test  →  http://localhost:8080/typography-test" . PHP_EOL;
echo "  /multimedia-test  →  http://localhost:8080/multimedia-test" . PHP_EOL;
echo "  /navigation-test  →  http://localhost:8080/navigation-test" . PHP_EOL . PHP_EOL;
