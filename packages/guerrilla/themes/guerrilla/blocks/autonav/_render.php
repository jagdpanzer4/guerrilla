<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Auto Nav Block — render engine
 * @var array  $navObjects  Array of NavObject from ConcreteCMS
 * @var string $layout      dark-topbar | cream-topbar | sidebar
 */
$layout = $layout ?? 'dark-topbar';
?>
<nav class="md3-nav md3-nav--<?= htmlspecialchars($layout) ?>" aria-label="<?= t('Main Navigation') ?>">

    <?php if ($layout === 'sidebar'): ?>

        <ul class="md3-nav__list md3-nav__list--sidebar">
            <?php foreach ($navObjects as $item): ?>
                <?php if ($item->level > 1) continue; ?>
                <li class="md3-nav__item<?= $item->isCurrent ? ' md3-nav__item--current' : '' ?><?= $item->isSelected ? ' md3-nav__item--selected' : '' ?>">
                    <a href="<?= htmlspecialchars($item->url) ?>"
                       class="md3-nav__link md3-glow-text"
                       <?= $item->target ? 'target="' . htmlspecialchars($item->target) . '"' . ($item->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                        <?= htmlspecialchars($item->name) ?>
                    </a>
                    <?php if ($item->hasSubmenu): ?>
                    <ul class="md3-nav__sub">
                        <?php foreach ($navObjects as $sub): ?>
                            <?php if ($sub->level !== 2) continue; ?>
                            <li class="md3-nav__sub-item<?= $sub->isCurrent ? ' md3-nav__item--current' : '' ?>">
                                <a href="<?= htmlspecialchars($sub->url) ?>"
                                   class="md3-nav__sub-link md3-glow-text"
                                   <?= $sub->target ? 'target="' . htmlspecialchars($sub->target) . '"' . ($sub->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                                    <?= htmlspecialchars($sub->name) ?>
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
                <?php foreach ($navObjects as $item): ?>
                    <?php if ($item->level > 1) continue; ?>
                    <li class="md3-nav__item<?= $item->isCurrent ? ' md3-nav__item--current' : '' ?><?= $item->hasSubmenu ? ' md3-nav__item--has-sub' : '' ?>">
                        <a href="<?= htmlspecialchars($item->url) ?>"
                           class="md3-nav__link md3-glow-text"
                           <?= $item->target ? 'target="' . htmlspecialchars($item->target) . '"' . ($item->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                            <?= htmlspecialchars($item->name) ?>
                            <?php if ($item->hasSubmenu): ?><span class="md3-nav__sub-arrow" aria-hidden="true">&#9660;</span><?php endif; ?>
                        </a>
                        <?php if ($item->hasSubmenu): ?>
                        <ul class="md3-nav__dropdown">
                            <?php foreach ($navObjects as $sub): ?>
                                <?php if ($sub->level !== 2) continue; ?>
                                <li class="md3-nav__dropdown-item<?= $sub->isCurrent ? ' md3-nav__item--current' : '' ?>">
                                    <a href="<?= htmlspecialchars($sub->url) ?>"
                                       class="md3-nav__dropdown-link md3-glow-text"
                                       <?= $sub->target ? 'target="' . htmlspecialchars($sub->target) . '"' . ($sub->target === '_blank' ? ' rel="noopener"' : '') : '' ?>>
                                        <?= htmlspecialchars($sub->name) ?>
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
(function() {
    if (window.md3NavLoaded) return;
    window.md3NavLoaded = true;

    // Hamburger toggle (mobile)
    document.addEventListener('click', function(e) {
        var toggle = e.target.closest('[data-md3-nav-toggle]');
        if (!toggle) return;
        if (window.innerWidth >= 768) return;
        e.preventDefault();
        var nav = toggle.closest('.md3-nav');
        var expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!expanded));
        nav.classList.toggle('md3-autonav--open', !expanded);
    });

    // Desktop dropdown on hover — handled via CSS :hover
    // Mobile dropdown on click
    document.addEventListener('click', function(e) {
        var link = e.target.closest('.md3-nav__item--has-sub > .md3-nav__link');
        if (!link) return;
        if (window.innerWidth >= 768) return;
        e.preventDefault();
        var item = link.closest('.md3-nav__item--has-sub');
        item.classList.toggle('md3-nav__item--sub-open');
    });
})();
</script>
