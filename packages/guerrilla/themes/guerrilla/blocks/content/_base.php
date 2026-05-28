<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Content Block — base template
 * @var string $content        HTML output from editor
 * @var string $colorVariant   md3-block--light | --dark | --tonal | --accent
 */
$colorVariant = $colorVariant ?? 'md3-block--light';
?>
<div class="md3-block <?= htmlspecialchars($colorVariant) ?> md3-block--content">
    <?php if (!empty($title)): ?>
    <div class="md3-block__header">
        <span class="md3-section-label"><?= htmlspecialchars($title) ?></span>
    </div>
    <?php endif; ?>
    <div class="md3-block__body">
        <?= $content ?>
    </div>
</div>
