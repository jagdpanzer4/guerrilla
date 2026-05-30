<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Responsive Header Navigation — Guerrilla theme
 * Self-contained template: does NOT rely on parent view.css (CCMS loads template-dir CSS only).
 * Desktop: horizontal nav bar with hover dropdowns.
 * Mobile: hamburger + slide-down menu.
 */
$navObjects = $controller->getNavItems();

// Build parent→children map (items arrive in DFS order from CCMS)
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

$navId = 'g-topnav-' . $bID;
?>
<nav class="g-topnav" aria-label="<?= t('Main Navigation') ?>">
    <button class="g-topnav__toggle"
            type="button"
            aria-label="<?= t('Toggle navigation') ?>"
            aria-expanded="false"
            aria-controls="<?= $navId ?>">
        <span class="g-topnav__bar"></span>
        <span class="g-topnav__bar"></span>
        <span class="g-topnav__bar"></span>
    </button>

    <ul class="g-topnav__menu" id="<?= $navId ?>" role="list">
        <?php foreach ($topItems as $idx => $item):
            $children    = $childrenMap[$idx];
            $hasChildren = !empty($children);
        ?>
        <li class="g-topnav__item<?= $item->isCurrent ? ' is-current' : '' ?><?= $item->inPath ? ' is-in-path' : '' ?><?= $hasChildren ? ' has-sub' : '' ?>">
            <a href="<?= $item->url ?>"
               class="g-topnav__link"
               <?= $item->target ? 'target="' . $item->target . '"' . ($item->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                <?= $item->name ?>
                <?php if ($hasChildren): ?>
                    <svg class="g-topnav__chevron" aria-hidden="true" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                <?php endif; ?>
            </a>
            <?php if ($hasChildren): ?>
            <ul class="g-topnav__dropdown" role="list">
                <?php foreach ($children as $sub): ?>
                    <li class="g-topnav__dropdown-item<?= $sub->isCurrent ? ' is-current' : '' ?>">
                        <a href="<?= $sub->url ?>"
                           class="g-topnav__dropdown-link"
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
</nav>

<script>
(function () {
    'use strict';
    var nav = document.getElementById('<?= $navId ?>');
    if (!nav) return;
    var root = nav.closest('.g-topnav');
    var toggle = root.querySelector('.g-topnav__toggle');

    // Hamburger: toggle mobile menu
    toggle.addEventListener('click', function () {
        var open = root.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(open));
    });

    // Sub-menu: on touch devices prevent link navigation, toggle instead
    root.querySelectorAll('.g-topnav__item.has-sub').forEach(function (item) {
        item.querySelector(':scope > .g-topnav__link').addEventListener('click', function (e) {
            if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
                e.preventDefault();
                item.classList.toggle('is-sub-open');
            }
        });
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.g-topnav')) {
            root.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}());
</script>
