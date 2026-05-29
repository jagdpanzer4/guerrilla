<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Breadcrumbs Block — base template
 * CCMS provides $breadcrumb (PageBreadcrumb) with getItems()
 * Each Item: getName(), getUrl(), isActive()
 */
$colorVariant = $colorVariant ?? 'md3-block--light';

$crumbs = [];
if (!empty($breadcrumb)) {
    foreach ($breadcrumb->getItems() as $item) {
        $crumbs[] = [
            'name'    => $item->getName(),
            'url'     => $item->getUrl(),
            'current' => $item->isActive(),
        ];
    }
}
if (empty($crumbs)) {
    return; // nothing to render
}
?>
<nav class="md3-block <?= htmlspecialchars($colorVariant) ?> md3-block--breadcrumb" aria-label="<?= t('Breadcrumb') ?>">
    <ol class="md3-breadcrumb__list">
        <?php foreach ($crumbs as $crumb): ?>
        <li class="md3-breadcrumb__item<?= $crumb['current'] ? ' md3-breadcrumb__item--current' : '' ?>">
            <?php if ($crumb['current']): ?>
                <span class="md3-breadcrumb__label" aria-current="page">
                    <?= htmlspecialchars($crumb['name']) ?>
                </span>
            <?php else: ?>
                <a href="<?= htmlspecialchars($crumb['url']) ?>" class="md3-breadcrumb__link md3-glow-text">
                    <?= htmlspecialchars($crumb['name']) ?>
                </a>
                <span class="md3-breadcrumb__sep" aria-hidden="true">&#9658;</span>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ol>
</nav>
