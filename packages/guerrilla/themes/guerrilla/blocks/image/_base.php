<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Image Block — base template
 * @var \Concrete\Core\Entity\File\File $f
 * @var string $altText
 * @var string $title
 * @var string $caption
 * @var string $href
 * @var bool   $openLinkInNewWindow
 * @var string $colorVariant
 */
$colorVariant = $colorVariant ?? 'md3-block--light';
$src = $f ? $f->getRelativePath() : '';
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
