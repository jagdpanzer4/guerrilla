<?php
/**
 * Hero Image — Offset Title template
 * Image fills left; text panel overlaps from right (military card style).
 *
 * CCMS vars: $image, $title, $body, $button, $height, $bID,
 *            $iconTag, $buttonStyle, $buttonSize
 */
defined('C5_EXECUTE') or die('Access Denied.');

if (!$image) return;

$height   = (int)($height ?? 0);
$hRatio   = $height ? $height / 100 : 1.0;
$btnClass = 'md3-btn md3-btn--filled md3-glow-cta';
if (($buttonStyle ?? '') === 'outline') {
    $btnClass = 'md3-btn md3-btn--outlined md3-btn--outlined-dark md3-glow-primary';
} elseif (($buttonStyle ?? '') === 'link') {
    $btnClass = 'md3-btn md3-btn--text';
}
?>
<div class="g-hero g-hero--offset" data-block-id="<?= (int)$bID ?>">
    <div class="g-hero__offset-img">
        <img src="<?= h($image->getURL()) ?>"
             alt="<?= isset($title) ? h($title) : '' ?>"
             data-height-ratio="<?= $hRatio ?>"
             style="<?= $height ? "min-height:{$height}vh;object-fit:cover;width:100%" : '' ?>">
    </div>
    <div class="g-hero__offset-panel">
        <?php if ($title): ?>
        <h1 class="g-hero__title"><?= $title ?></h1>
        <?php endif; ?>
        <?php if ((string)($body ?? '') !== ''): ?>
        <div class="g-hero__body"><?= $body ?></div>
        <?php endif; ?>
        <?php if (isset($button) && $button->getValue()): ?>
        <div class="g-hero__cta">
            <a href="<?= h($button->getHref()) ?>" class="<?= $btnClass ?>">
               <?php if ($iconTag): ?><span class="g-hero__btn-icon"><?= $iconTag ?></span><?php endif; ?>
               <?= h($button->getValue()) ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
