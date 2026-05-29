<?php
/**
 * Testimonial — Hero Testimonial template
 * Full-width olive background with quote overlay.
 * Award image as bg accent (if present).
 */
defined('C5_EXECUTE') or die('Access Denied.');
?>
<div class="g-testimonial g-testimonial--hero md3-block">
    <?php if ($awardImageID): ?>
    <?php /* Award image rendered as semi-transparent background accent */ ?>
    <?php $af = File::getByID($awardImageID); ?>
    <?php if (is_object($af)): ?>
    <div class="g-testimonial__hero-bg" style="background-image:url('<?= h($af->getURL()) ?>')"></div>
    <?php endif; ?>
    <?php endif; ?>

    <div class="g-testimonial__hero-overlay"></div>

    <div class="g-testimonial__hero-content">
        <div class="g-testimonial__hero-inner">

            <?php if ($image): ?>
            <div class="g-testimonial__hero-avatar"><?= $image ?></div>
            <?php endif; ?>

            <div class="g-testimonial__hero-text">
                <blockquote class="g-testimonial__quote g-testimonial__quote--hero">
                    <p><?= $paragraph ?></p>
                </blockquote>
                <footer class="g-testimonial__footer g-testimonial__footer--hero">
                    <?php if ($name): ?>
                    <span class="g-testimonial__name"><?= h($name) ?></span>
                    <?php endif; ?>
                    <?php if ($position && $company && $companyURL): ?>
                    <span class="g-testimonial__meta">
                        <?= h($position) ?>, <a href="<?= h($companyURL) ?>" class="g-testimonial__company-link g-testimonial__company-link--light"><?= h($company) ?></a>
                    </span>
                    <?php elseif ($position && $company): ?>
                    <span class="g-testimonial__meta"><?= h($position) ?>, <?= h($company) ?></span>
                    <?php elseif ($position): ?>
                    <span class="g-testimonial__meta"><?= h($position) ?></span>
                    <?php elseif ($company && $companyURL): ?>
                    <span class="g-testimonial__meta">
                        <a href="<?= h($companyURL) ?>" class="g-testimonial__company-link g-testimonial__company-link--light"><?= h($company) ?></a>
                    </span>
                    <?php elseif ($company): ?>
                    <span class="g-testimonial__meta"><?= h($company) ?></span>
                    <?php endif; ?>
                </footer>
            </div>

        </div>
    </div>
</div>
