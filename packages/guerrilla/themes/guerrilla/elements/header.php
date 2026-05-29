<?php
/** @var \Concrete\Core\Page\Page $c */
/** @var \Concrete\Core\View\View $view */

$site = \Concrete\Core\Support\Facade\Site::getSite();
$siteName = $site ? $site->getSiteName() : '';
?>
<header id="site-header" class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
            <?php echo $siteName; ?>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMain"
                aria-controls="navbarMain"
                aria-expanded="false"
                aria-label="<?php echo t('Toggle navigation'); ?>">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <nav class="navbar-nav ms-auto" aria-label="<?php echo t('Main navigation'); ?>">
                <?php
                $navBlock = new GlobalArea('Main Nav');
                $navBlock->display($c);
                ?>
            </nav>
        </div>
    </div>
</header>
