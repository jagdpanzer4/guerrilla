<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Auto Nav Block — render engine
 * @var array  $navObjects  NavItem objects from ConcreteCMS (DFS order: parent then its children)
 * @var string $layout      dark-topbar | cream-topbar | sidebar
 */
$layout = $layout ?? 'dark-topbar';

// Build parent→children map using DFS order.
// CCMS returns items depth-first: a level-1 item is immediately followed by its level-2 children.
$topItems    = [];
$childrenMap = [];
foreach ($navObjects as $item) {
    if ((int) $item->level === 1) {
        $topItems[]                        = $item;
        $childrenMap[count($topItems) - 1] = [];
    } elseif ((int) $item->level === 2 && count($topItems) > 0) {
        $childrenMap[count($topItems) - 1][] = $item;
    }
}
?>
<nav class="md3-nav md3-nav--<?= $layout ?>" aria-label="<?= t('Main Navigation') ?>">

    <?php if ($layout === 'sidebar'): ?>

        <ul class="md3-nav__list md3-nav__list--sidebar">
            <?php foreach ($topItems as $idx => $item):
                $children = $childrenMap[$idx];
            ?>
                <li class="md3-nav__item<?= $item->isCurrent ? ' md3-nav__item--current' : '' ?><?= $item->inPath ? ' md3-nav__item--in-path' : '' ?>">
                    <a href="<?= $item->url ?>"
                       class="md3-nav__link md3-glow-text"
                       <?= $item->target ? 'target="' . $item->target . '"' . ($item->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                        <?= $item->name ?>
                    </a>
                    <?php if (!empty($children)): ?>
                    <ul class="md3-nav__sub">
                        <?php foreach ($children as $sub): ?>
                            <li class="md3-nav__sub-item<?= $sub->isCurrent ? ' md3-nav__item--current' : '' ?>">
                                <a href="<?= $sub->url ?>"
                                   class="md3-nav__sub-link md3-glow-text"
                                   <?= $sub->target ? 'target="' . $sub->target . '"' . ($sub->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                                    <?= $sub->name ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php else: /* dark-topbar or cream-topbar */ ?>

        <div class="md3-nav__bar">
            <button class="md3-nav__toggle" type="button"
                    aria-label="<?= t('Toggle navigation') ?>"
                    aria-expanded="false"
                    data-md3-nav-toggle>
                <span class="md3-nav__hamburger"></span>
                <span class="md3-nav__hamburger"></span>
                <span class="md3-nav__hamburger"></span>
            </button>

            <ul class="md3-nav__list" role="list">
                <?php foreach ($topItems as $idx => $item):
                    $children    = $childrenMap[$idx];
                    $hasChildren = !empty($children);
                ?>
                    <li class="md3-nav__item<?= $item->isCurrent ? ' md3-nav__item--current' : '' ?><?= $item->inPath ? ' md3-nav__item--in-path' : '' ?><?= $hasChildren ? ' md3-nav__item--has-sub' : '' ?>">
                        <a href="<?= $item->url ?>"
                           class="md3-nav__link md3-glow-text"
                           <?= $item->target ? 'target="' . $item->target . '"' . ($item->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                            <?= $item->name ?>
                            <?php if ($hasChildren): ?>
                                <svg class="md3-nav__sub-arrow" aria-hidden="true" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
                            <?php endif; ?>
                        </a>
                        <?php if ($hasChildren): ?>
                        <ul class="md3-nav__dropdown" role="list">
                            <?php foreach ($children as $sub): ?>
                                <li class="md3-nav__dropdown-item<?= $sub->isCurrent ? ' md3-nav__item--current' : '' ?>">
                                    <a href="<?= $sub->url ?>"
                                       class="md3-nav__dropdown-link md3-glow-text"
                                       <?= $sub->target ? 'target="' . $sub->target . '"' . ($sub->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                                        <?= $sub->name ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    <?php endif; ?>
</nav>

<script>
(function () {
    'use strict';
    if (window._md3NavReady) return;
    window._md3NavReady = true;

    // Hamburger: toggle mobile menu
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-md3-nav-toggle]');
        if (!btn) return;
        var nav = btn.closest('.md3-nav');
        var open = nav.classList.toggle('md3-autonav--open');
        btn.setAttribute('aria-expanded', String(open));
    });

    // Sub-menu: click on parent link on touch/mobile
    document.addEventListener('click', function (e) {
        var link = e.target.closest('.md3-nav__item--has-sub > .md3-nav__link');
        if (!link) return;
        if (window.matchMedia('(hover: hover)').matches) return; // desktop: CSS :hover handles it
        e.preventDefault();
        link.closest('.md3-nav__item--has-sub').classList.toggle('md3-nav__item--sub-open');
    });

    // Close open menus when clicking outside
    document.addEventListener('click', function (e) {
        if (e.target.closest('.md3-nav')) return;
        document.querySelectorAll('.md3-nav.md3-autonav--open').forEach(function (nav) {
            nav.classList.remove('md3-autonav--open');
            var btn = nav.querySelector('[data-md3-nav-toggle]');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    });
}());
</script>
