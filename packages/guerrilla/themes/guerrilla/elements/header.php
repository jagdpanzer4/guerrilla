<?php
/** @var \Concrete\Core\Page\Page $c */
/** @var \Concrete\Core\View\View $view */
defined('C5_EXECUTE') or die('Access Denied.');

$site     = \Concrete\Core\Support\Facade\Site::getSite();
$siteName = $site ? $site->getSiteName() : '';
?>
<header id="site-header" role="banner">
    <div class="g-header__inner">
        <a href="<?php echo BASE_URL; ?>" class="g-header__brand" aria-label="<?php echo t('Home'); ?>">
            <span class="g-header__brand-text"><?php echo htmlspecialchars($siteName); ?></span>
        </a>
        <div class="g-header__nav" role="navigation" aria-label="<?php echo t('Main navigation'); ?>">
            <?php
            $navArea = new GlobalArea('Main Nav');
            $navArea->setCustomTemplate('autonav', 'cream-topbar');
            $navArea->display($c);
            ?>
        </div>
    </div>
</header>
