<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Feature Block — render engine
 * @var string $layout        icon-top | icon-left | card
 * @var string $colorVariant  md3-block--light | --dark | --tonal | --accent
 * @var string $title
 * @var string $body          HTML string
 * @var string $icon          CSS class(es) e.g. "fa fa-shield"
 * @var string $link
 * @var string $linkText
 */
$layout       = $layout ?? 'icon-top';
$colorVariant = $colorVariant ?? 'md3-block--light';
?>
<div class="md3-block <?= htmlspecialchars($colorVariant) ?> md3-block--feature md3-feature--<?= htmlspecialchars($layout) ?>">

    <?php if ($layout === 'icon-left'): ?>
    <div class="md3-feature__inner md3-feature__inner--row">
        <?php if (!empty($iconHtml)): ?>
        <div class="md3-feature__icon-wrap">
            <?= $iconHtml ?>
        </div>
        <?php elseif (!empty($icon)): ?>
        <div class="md3-feature__icon-wrap">
            <span class="md3-feature__icon <?= htmlspecialchars($icon) ?>"></span>
        </div>
        <?php endif; ?>
        <div class="md3-feature__content">
    <?php else: ?>
    <div class="md3-feature__inner">
        <?php if (!empty($iconHtml)): ?>
        <div class="md3-feature__icon-wrap">
            <?= $iconHtml ?>
        </div>
        <?php elseif (!empty($icon)): ?>
        <div class="md3-feature__icon-wrap">
            <span class="md3-feature__icon <?= htmlspecialchars($icon) ?>"></span>
        </div>
        <?php endif; ?>
        <div class="md3-feature__content">
    <?php endif; ?>

            <?php if (!empty($title)): ?>
            <h3 class="md3-feature__title"><?= htmlspecialchars($title) ?></h3>
            <?php endif; ?>

            <?php if (!empty($body)): ?>
            <div class="md3-feature__body"><?= $body ?></div>
            <?php endif; ?>

            <?php if (!empty($link) && !empty($linkText)): ?>
            <a href="<?= htmlspecialchars($link) ?>" class="md3-btn md3-btn--filled md3-glow-cta">
                <?= htmlspecialchars($linkText) ?>
            </a>
            <?php elseif (!empty($link)): ?>
            <a href="<?= htmlspecialchars($link) ?>" class="md3-btn md3-btn--outlined md3-glow-primary">
                <?= t('Learn More') ?>
            </a>
            <?php endif; ?>

        </div><!-- /.md3-feature__content -->
    </div><!-- /.md3-feature__inner -->
</div><!-- /.md3-block--feature -->
