<?php
/**
 * Testimonial block — default (card layout)
 * Guerrilla MD3 Military theme
 *
 * CCMS vars: $name, $position, $company, $companyURL, $paragraph,
 *            $image (pre-rendered HTML), $awardImage (pre-rendered HTML),
 *            $fID, $awardImageID
 */
defined('C5_EXECUTE') or die('Access Denied.');
?>
<div class="g-testimonial md3-block md3-block--light">
    <div class="g-testimonial__inner">

        <?php if ($image): ?>
        <div class="g-testimonial__avatar">
            <?= $image ?>
        </div>
        <?php endif; ?>

        <div class="g-testimonial__body">
            <blockquote class="g-testimonial__quote">
                <p><?= $paragraph ?></p>
            </blockquote>

            <footer class="g-testimonial__footer">
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

    </div>
</div>
