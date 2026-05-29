<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Image Block — base template
 * CCMS provides: $f, $altText, $title, $linkURL, $openLinkInNewWindow
 */
$colorVariant = $colorVariant ?? 'md3-block--light';
$src = ($f && is_object($f)) ? $f->getRelativePath() : '';
$href = $linkURL ?? '';
$target = !empty($openLinkInNewWindow) ? ' target="_blank" rel="noopener"' : '';
?>
<div class="md3-block <?= htmlspecialchars($colorVariant) ?> md3-block--image">
    <?php if ($src): ?>
        <figure class="md3-block__figure">
            <?php if ($href): ?>
            <a href="<?= htmlspecialchars($href) ?>"<?= $target ?> class="md3-block__img-link md3-glow-primary">
            <?php endif; ?>
                <img src="<?= htmlspecialchars($src) ?>"
                     alt="<?= htmlspecialchars($altText ?? '') ?>"
                     class="md3-block__img"
                     <?php if (!empty($title)): ?>title="<?= htmlspecialchars($title) ?>"<?php endif; ?>>
            <?php if ($href): ?>
            </a>
            <?php endif; ?>
            <?php if (!empty($caption)): ?>
            <figcaption class="md3-block__caption"><?= htmlspecialchars($caption) ?></figcaption>
            <?php endif; ?>
        </figure>
    <?php endif; ?>
</div>
