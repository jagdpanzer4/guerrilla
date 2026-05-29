<?php
/**
 * Hero Image block — default (full-width overlay)
 * Guerrilla MD3 Military theme
 *
 * CCMS vars: $image (File), $title, $body, $button (Link|null),
 *            $height (vh%), $titleFormat, $buttonStyle, $buttonColor,
 *            $buttonSize, $iconTag (HTML string), $bID
 */
defined('C5_EXECUTE') or die('Access Denied.');

if (!$image) return;

$height  = (int)($height ?? 0);
$hStyle  = $height ? "min-height:{$height}vh" : '';
$titleTag = htmlspecialchars($titleFormat ?? 'h1');

/* Map CCMS button style → MD3 class */
$btnClass = 'md3-btn md3-btn--filled';
if (($buttonStyle ?? '') === 'outline') {
    $btnClass = 'md3-btn md3-btn--outlined md3-btn--outlined-light';
} elseif (($buttonStyle ?? '') === 'link') {
    $btnClass = 'md3-btn md3-btn--text';
}
if (($buttonSize ?? '') === 'lg') $btnClass .= ' md3-btn--lg';
if (($buttonSize ?? '') === 'sm') $btnClass .= ' md3-btn--sm';
?>
<div class="g-hero" <?= $hStyle ? "style=\"{$hStyle}\"" : '' ?> data-block-id="<?= (int)$bID ?>">
    <div class="g-hero__bg" style="background-image:url('<?= h($image->getURL()) ?>')" <?= $hStyle ? "style=\"{$hStyle}\"" : '' ?>></div>
    <div class="g-hero__overlay" <?= $hStyle ? "style=\"{$hStyle}\"" : '' ?>></div>
    <div class="g-hero__content" <?= $hStyle ? "style=\"{$hStyle}\"" : '' ?>>
        <div class="g-hero__inner">
            <?php if ($title): ?>
            <<?= $titleTag ?> class="g-hero__title"><?= $title ?></<?= $titleTag ?>>
            <?php endif; ?>
            <?php if ((string)($body ?? '') !== ''): ?>
            <div class="g-hero__body"><?= $body ?></div>
            <?php endif; ?>
            <?php if (isset($button) && $button->getValue()): ?>
            <div class="g-hero__cta">
                <a href="<?= h($button->getHref()) ?>"
                   class="<?= $btnClass ?> md3-glow-cta">
                   <?php if ($iconTag): ?><span class="g-hero__btn-icon"><?= $iconTag ?></span><?php endif; ?>
                   <?= h($button->getValue()) ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
