<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Breadcrumb Block — base template
 * @var string $colorVariant
 */
$colorVariant = $colorVariant ?? 'md3-block--light';

// Build breadcrumb trail
use Concrete\Core\Page\Page;

$crumbs = [];

// Try to get the current page trail
$currentPage = Page::getCurrentPage();
if ($currentPage && !$currentPage->isError()) {
    // Build from page hierarchy
    $page = $currentPage;
    $trail = [];
    while ($page && !$page->isError() && (int)$page->getCollectionID() !== 0) {
        $trail[] = $page;
        $parentID = (int)$page->getCollectionParentID();
        if ($parentID <= 0) break;
        $page = Page::getByID($parentID);
    }
    $trail = array_reverse($trail);
    
    foreach ($trail as $p) {
        $crumbs[] = [
            'name' => $p->getCollectionName(),
            'url'  => (string)$p->getCollectionLink(),
            'current' => ((int)$p->getCollectionID() === (int)$currentPage->getCollectionID()),
        ];
    }
}
?>
<nav class="md3-block <?= htmlspecialchars($colorVariant) ?> md3-block--breadcrumb" aria-label="<?= t('Breadcrumb') ?>">
    <ol class="md3-breadcrumb__list">
        <?php foreach ($crumbs as $i => $crumb): ?>
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
