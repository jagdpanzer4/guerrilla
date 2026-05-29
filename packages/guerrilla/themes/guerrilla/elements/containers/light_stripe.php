<?php
/**
 * Container: Highlight Stripe (light_stripe)
 * Full-width section with optional title area + content body.
 * Guerrilla MD3 theme — cream-on-olive stripe.
 */
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Area\ContainerArea;

$titleArea = new ContainerArea($container, 'Title');
$bodyArea  = new ContainerArea($container, 'Body');
$bodyArea->setAreaGridMaximumColumns(12);
$bodyArea->enableGridContainer();
?>
<section class="g-stripe g-stripe--highlight">
    <?php if ($c->isEditMode() || $titleArea->getTotalBlocksInArea($c) > 0): ?>
    <div class="g-stripe__title-wrap">
        <div class="g-inner">
            <?php $titleArea->display($c); ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="g-stripe__body">
        <?php $bodyArea->display($c); ?>
    </div>
</section>
