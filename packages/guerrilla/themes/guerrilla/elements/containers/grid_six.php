<?php
/**
 * Container: Responsive Grid of Six (grid_six)
 * 6-up feature grid (2 on mobile, 3 on desktop).
 * Guerrilla MD3 theme.
 */
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Area\ContainerArea;
?>
<section class="g-stripe g-stripe--grid">
    <div class="g-inner">
        <div class="row g-3">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <div class="col-6 col-md-4 g-col">
                <?php
                $area = new ContainerArea($container, 'Item ' . $i);
                $area->display($c);
                ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
