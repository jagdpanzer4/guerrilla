<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Page List Block — render engine
 * @var array  $pages        Array of Page objects (fallback from $cArray)
 * @var string $layout       cards-grid | horizontal-list | featured-list | minimal
 * @var string $colorVariant md3-block--light | --dark | --tonal | --accent
 */
$layout       = $layout ?? 'cards-grid';
$colorVariant = $colorVariant ?? 'md3-block--light';
$pages        = $pages ?? $cArray ?? [];
?>
<div class="md3-block <?= htmlspecialchars($colorVariant) ?> md3-block--page-list md3-page-list--<?= htmlspecialchars($layout) ?>">

    <?php if (empty($pages)): ?>
        <p class="md3-page-list__empty"><?= t('No pages found.') ?></p>
    <?php elseif ($layout === 'cards-grid'): ?>

        <div class="md3-page-list__grid">
            <?php foreach ($pages as $page): ?>
            <article class="md3-page-list__card md3-glow-primary">
                <?php
                $thumb = $page->getAttribute('thumbnail');
                if ($thumb): ?>
                <div class="md3-page-list__card-img">
                    <img src="<?= htmlspecialchars($thumb->getRelativePath()) ?>"
                         alt="<?= htmlspecialchars($page->getCollectionName()) ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>
                <div class="md3-page-list__card-body">
                    <h3 class="md3-page-list__title">
                        <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>">
                            <?= htmlspecialchars($page->getCollectionName()) ?>
                        </a>
                    </h3>
                    <?php $desc = $page->getCollectionDescription(); if ($desc): ?>
                    <p class="md3-page-list__desc"><?= htmlspecialchars($desc) ?></p>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>"
                       class="md3-btn md3-btn--outlined md3-glow-primary">
                        <?= t('Read More') ?>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

    <?php elseif ($layout === 'horizontal-list'): ?>

        <ul class="md3-page-list__hlist">
            <?php foreach ($pages as $page): ?>
            <li class="md3-page-list__hitem md3-glow-primary">
                <?php
                $thumb = $page->getAttribute('thumbnail');
                if ($thumb): ?>
                <div class="md3-page-list__hitem-img">
                    <img src="<?= htmlspecialchars($thumb->getRelativePath()) ?>"
                         alt="<?= htmlspecialchars($page->getCollectionName()) ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>
                <div class="md3-page-list__hitem-body">
                    <h3 class="md3-page-list__title">
                        <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>">
                            <?= htmlspecialchars($page->getCollectionName()) ?>
                        </a>
                    </h3>
                    <?php $desc = $page->getCollectionDescription(); if ($desc): ?>
                    <p class="md3-page-list__desc"><?= htmlspecialchars($desc) ?></p>
                    <?php endif; ?>
                    <span class="md3-page-list__date"><?= htmlspecialchars($page->getCollectionDatePublic()) ?></span>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>

    <?php elseif ($layout === 'featured-list'): ?>

        <?php $featured = array_shift($pages); ?>
        <?php if ($featured): ?>
        <div class="md3-page-list__featured">
            <?php
            $thumb = $featured ? $featured->getAttribute('thumbnail') : null;
            if ($thumb): ?>
            <div class="md3-page-list__featured-img">
                <img src="<?= htmlspecialchars($thumb->getRelativePath()) ?>"
                     alt="<?= htmlspecialchars($featured->getCollectionName()) ?>"
                     loading="lazy">
            </div>
            <?php endif; ?>
            <div class="md3-page-list__featured-body">
                <span class="md3-section-label"><?= t('Featured') ?></span>
                <h2 class="md3-page-list__featured-title">
                    <a href="<?= htmlspecialchars($featured->getCollectionLink()) ?>">
                        <?= htmlspecialchars($featured->getCollectionName()) ?>
                    </a>
                </h2>
                <?php $desc = $featured->getCollectionDescription(); if ($desc): ?>
                <p class="md3-page-list__desc"><?= htmlspecialchars($desc) ?></p>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($featured->getCollectionLink()) ?>"
                   class="md3-btn md3-btn--filled md3-glow-cta">
                    <?= t('Read Article') ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($pages)): ?>
        <ul class="md3-page-list__secondary">
            <?php foreach ($pages as $page): ?>
            <li class="md3-page-list__secondary-item">
                <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>"
                   class="md3-page-list__secondary-link md3-glow-text">
                    <span class="md3-page-list__sep" aria-hidden="true">&#9658;</span>
                    <?= htmlspecialchars($page->getCollectionName()) ?>
                </a>
                <span class="md3-page-list__date"><?= htmlspecialchars($page->getCollectionDatePublic()) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

    <?php elseif ($layout === 'minimal'): ?>

        <ul class="md3-page-list__minimal">
            <?php foreach ($pages as $page): ?>
            <li class="md3-page-list__minimal-item">
                <a href="<?= htmlspecialchars($page->getCollectionLink()) ?>"
                   class="md3-page-list__minimal-link md3-glow-text">
                    <?= htmlspecialchars($page->getCollectionName()) ?>
                </a>
                <span class="md3-page-list__date"><?= htmlspecialchars($page->getCollectionDatePublic()) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>
</div>
