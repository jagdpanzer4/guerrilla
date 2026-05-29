<?php
/**
 * Board block — Guerrilla MD3 wrapper
 * CCMS provides $renderer (Renderer) and $instance (Instance).
 * All rendering is delegated to the CCMS Board renderer;
 * we only wrap in our design-system container.
 */
defined('C5_EXECUTE') or die('Access Denied.');

$renderer = $renderer ?? null;
$instance  = $instance  ?? null;
if (!$renderer) return;
?>
<div class="g-board md3-block md3-block--light">
    <?php $renderer->render($instance); ?>
</div>
