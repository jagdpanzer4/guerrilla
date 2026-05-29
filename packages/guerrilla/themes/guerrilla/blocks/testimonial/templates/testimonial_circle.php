<?php
/**
 * Testimonial — Circle template
 * Circular avatar centred above quote, stacked layout.
 */
defined('C5_EXECUTE') or die('Access Denied.');
?>
<div class="g-testimonial g-testimonial--circle md3-block md3-block--light">
    <?php if ($image): ?>
    <div class="g-testimonial__circle-avatar"><?= $image ?></div>
    <?php endif; ?>

    <blockquote class="g-testimonial__quote g-testimonial__quote--center">
        <p><?= $paragraph ?></p>
    </blockquote>

    <footer class="g-testimonial__footer g-testimonial__footer--center">
        <?php if ($name): ?>
        <span class="g-testimonial__name"><?= h($name) ?></span>
        <?php endif; ?>
        <?php if ($position && $company && $companyURL): ?>
        <span class="g-testimonial__meta">
            <?= h($position) ?>, <a href="<?= h($companyURL) ?>" class="g-testimonial__company-link"><?= h($company) ?></a>
        </span>
        <?php elseif ($position && $company): ?>
        <span class="g-testimonial__meta"><?= h($position) ?>, <?= h($company) ?></span>
        <?php elseif ($position): ?>
        <span class="g-testimonial__meta"><?= h($position) ?></span>
        <?php elseif ($company && $companyURL): ?>
        <span class="g-testimonial__meta">
            <a href="<?= h($companyURL) ?>" class="g-testimonial__company-link"><?= h($company) ?></a>
        </span>
        <?php elseif ($company): ?>
        <span class="g-testimonial__meta"><?= h($company) ?></span>
        <?php endif; ?>

        <?php if ($awardImage): ?>
        <div class="g-testimonial__award"><?= $awardImage ?></div>
        <?php endif; ?>
    </footer>
</div>
