<?php
/** @var \Concrete\Core\Page\Page $c */
/** @var \Concrete\Core\View\View $view */
/** @var \Concrete\Core\Site\Service $site */
?>
<footer id="site-footer" class="bg-dark text-light py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <?php
                $footerArea = new GlobalArea('Footer');
                $footerArea->display($c);
                ?>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 small">
                    &copy; <?php echo date('Y'); ?> <?php echo $site->getName(); ?>.
                    <?php echo t('Built with'); ?>
                    <a href="https://www.concretecms.com" class="text-light" target="_blank" rel="noopener">Concrete CMS</a>.
                </p>
            </div>
        </div>
    </div>
</footer>
