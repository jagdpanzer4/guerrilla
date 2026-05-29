<?php
/** @var \Concrete\Core\Page\Page $c */
/** @var \Concrete\Core\View\View $view */
defined('C5_EXECUTE') or die('Access Denied.');

$site     = \Concrete\Core\Support\Facade\Site::getSite();
$siteName = $site ? $site->getSiteName() : '';
?>
<footer id="site-footer" role="contentinfo">
    <div class="g-footer__inner">
        <div class="g-footer__areas">
            <div class="g-footer__area">
                <?php
                $footerArea = new GlobalArea('Footer');
                $footerArea->display($c);
                ?>
            </div>
            <div class="g-footer__brand">
                <span class="g-footer__brand-name"><?php echo htmlspecialchars($siteName); ?></span>
                <p class="g-footer__copy">
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>.
                </p>
            </div>
        </div>
        <div class="g-footer__bar">
            <span class="g-footer__built">
                <?php echo t('Powered by'); ?>
                <a href="https://www.concretecms.com" target="_blank" rel="noopener">Concrete CMS</a>
            </span>
        </div>
    </div>
</footer>
