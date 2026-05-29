<!DOCTYPE html>
<html lang="pl" <?php echo $c->getPageWrapperClass(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $site = \Concrete\Core\Support\Facade\Site::getSite();
    $siteName = $site ? $site->getSiteName() : '';
    View::element('header_required', [
        'pageTitle'        => isset($pageTitle) ? $pageTitle : $c->getCollectionName(),
        'pageDescription'  => isset($pageDescription) ? $pageDescription : '',
        'pageMetaKeywords' => isset($pageMetaKeywords) ? $pageMetaKeywords : '',
    ]);
    ?>
    <title><?php echo $c->getCollectionName(); ?><?php echo $siteName ? ' | ' . $siteName : ''; ?></title>
    <link rel="stylesheet" href="<?php echo $view->getThemePath(); ?>/css/main.css">
</head>
<body class="ccm-page-id-<?php echo $c->getCollectionID(); ?>">

    <?php $view->inc('elements/header.php'); ?>

    <main id="main-content" class="container py-4">
        <?php
        $a = new Area('Main');
        $a->display($c);
        ?>
    </main>

    <?php $view->inc('elements/footer.php'); ?>

    <?php View::element('footer_required'); ?>
    <script src="<?php echo $view->getThemePath(); ?>/js/main.js"></script>
</body>
</html>
