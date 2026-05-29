<!DOCTYPE html>
<html lang="pl" <?php echo $c->getPageWrapperClass(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $view->inc('elements/header_required.php'); ?>
    <title><?php echo $c->getCollectionName(); ?> | <?php echo $site->getName(); ?></title>
    <link rel="stylesheet" href="<?php echo $view->getThemePath(); ?>/css/main.css">
</head>
<body class="<?php echo $c->getBodyClass(); ?> g-full-width-body">

    <?php $view->inc('elements/header.php'); ?>

    <main id="main-content" class="g-fw-main">
        <?php
        $a = new Area('Main');
        $a->display($c);
        ?>
    </main>

    <?php $view->inc('elements/footer.php'); ?>

    <?php $view->inc('elements/footer_required.php'); ?>
    <script src="<?php echo $view->getThemePath(); ?>/js/main.js"></script>
</body>
</html>
