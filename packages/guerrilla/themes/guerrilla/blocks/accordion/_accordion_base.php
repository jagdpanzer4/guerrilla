<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * MD3 Accordion Block — base template
 * @var array  $rows         Each row: ->title (string), ->body (HTML)
 * @var string $style        standard | filled | tactical
 * @var string $colorVariant md3-block--light | --dark | --tonal | --accent
 */
$style        = $style ?? 'standard';
$colorVariant = $colorVariant ?? 'md3-block--light';
$instanceId   = 'md3acc-' . uniqid();
?>
<div class="md3-block <?= htmlspecialchars($colorVariant) ?> md3-block--accordion md3-accordion--<?= htmlspecialchars($style) ?>"
     id="<?= $instanceId ?>">
    <?php if (!empty($rows)): ?>
        <?php foreach ($rows as $i => $row): ?>
        <div class="md3-accordion__item" data-accordion-item>
            <button class="md3-accordion__trigger md3-glow-primary"
                    type="button"
                    aria-expanded="false"
                    aria-controls="<?= $instanceId ?>-panel-<?= (int)$i ?>"
                    id="<?= $instanceId ?>-btn-<?= (int)$i ?>">
                <span class="md3-accordion__trigger-text"><?= htmlspecialchars($row->title ?? '') ?></span>
                <span class="md3-accordion__chevron" aria-hidden="true">&#9660;</span>
            </button>
            <div class="md3-accordion__panel"
                 id="<?= $instanceId ?>-panel-<?= (int)$i ?>"
                 role="region"
                 aria-labelledby="<?= $instanceId ?>-btn-<?= (int)$i ?>"
                 hidden>
                <div class="md3-accordion__body">
                    <?= $row->body ?? '' ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
(function() {
    if (window.md3AccordionLoaded) return;
    window.md3AccordionLoaded = true;

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-accordion-item] .md3-accordion__trigger');
        if (!btn) return;
        var item   = btn.closest('[data-accordion-item]');
        var panel  = document.getElementById(btn.getAttribute('aria-controls'));
        var isOpen = btn.getAttribute('aria-expanded') === 'true';

        // Close all siblings in same accordion
        var accordion = btn.closest('.md3-block--accordion');
        accordion.querySelectorAll('.md3-accordion__trigger').forEach(function(other) {
            if (other !== btn) {
                other.setAttribute('aria-expanded', 'false');
                var otherPanel = document.getElementById(other.getAttribute('aria-controls'));
                if (otherPanel) otherPanel.hidden = true;
                other.closest('[data-accordion-item]').classList.remove('md3-accordion__item--open');
            }
        });

        btn.setAttribute('aria-expanded', String(!isOpen));
        if (panel) panel.hidden = isOpen;
        item.classList.toggle('md3-accordion__item--open', !isOpen);
    });
})();
</script>
