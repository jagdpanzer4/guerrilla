<?php
/**
 * Image Slider — Hero Slider template
 * Full-viewport slide with large centred title/description.
 * Guerrilla MD3 Military theme.
 */
defined('C5_EXECUTE') or die('Access Denied.');

$c = Page::getCurrentPage();
$navTypeText = (0 == $navigationType) ? 'arrows' : 'pages';
?>
<?php if ($c && $c->isEditMode()): ?>
<div class="ccm-edit-mode-disabled-item g-slider__edit-placeholder g-slider__edit-placeholder--hero">
    <i class="fas fa-images" aria-hidden="true"></i>
    <span><?= t('Hero Slider — disabled in edit mode') ?></span>
    <?php if (count($rows)): ?>
    <small><?= t('%d slide(s)', count($rows)) ?></small>
    <?php endif; ?>
</div>
<?php else: ?>

<div class="g-slider g-slider--hero ccm-image-slider-container ccm-image-slider-hero-image ccm-block-image-slider-<?= $navTypeText ?>" data-block-id="<?= (int)$bID ?>">
    <div class="ccm-image-slider">
        <div class="ccm-image-slider-inner">
            <?php if (count($rows) > 0): ?>
            <ul class="rslides" id="ccm-image-slider-<?= (int)$bID ?>">
                <?php foreach ($rows as $row): ?>
                <li class="g-slider__slide">
                    <?php if ($row['linkURL']): ?>
                    <a href="<?= h($row['linkURL']) ?>" class="mega-link-overlay" aria-label="<?= h($row['title'] ?? '') ?>"></a>
                    <?php endif; ?>

                    <?php $f = File::getByID($row['fID']); ?>
                    <?php if (is_object($f)):
                        $tag = Core::make('html/image', ['f' => $f])->getTag();
                        $tag->alt(h($row['title'] ?: 'slide'));
                        echo $tag;
                    endif; ?>

                    <?php if ($row['title'] || $row['description']): ?>
                    <div class="g-slider__hero-caption">
                        <div class="g-slider__hero-caption-inner">
                            <?php if ($row['title']): ?>
                            <h1 class="g-slider__hero-title"><?= h($row['title']) ?></h1>
                            <?php endif; ?>
                            <?php if ($row['description']): ?>
                            <div class="g-slider__hero-desc"><?= $row['description'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="g-slider__empty"><p><?= t('No Slides Entered.') ?></p></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function() {
    $(document).ready(function() {
        var opts = {
            prevText: '',
            nextText: '',
            <?php if ($navigationType == 0): ?>nav: true,
            <?php elseif ($navigationType == 1): ?>pager: true,
            <?php elseif ($navigationType == 2): ?>nav: true, pager: true,
            <?php else: ?>nav: false, pager: false,
            <?php endif; ?>
            <?= $timeout  ? "timeout: {$timeout},"  : '' ?>
            <?= $speed    ? "speed: {$speed},"      : '' ?>
            <?= $pause    ? "pause: true,"           : '' ?>
            <?= $noAnimate? "auto: false,"           : '' ?>
            <?= $maxWidth ? "maxwidth: {$maxWidth}," : '' ?>
        };
        $('#ccm-image-slider-<?= (int)$bID ?>').responsiveSlides(opts);
    });
}());
</script>
<?php endif; ?>
