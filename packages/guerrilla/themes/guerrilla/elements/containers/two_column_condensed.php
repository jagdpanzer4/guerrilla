<?php
/**
 * Container: Two Column Condensed (two_column_condensed)
 * Two equal columns with inner horizontal padding — for text-heavy layouts.
 * Guerrilla MD3 theme.
 */
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Area\ContainerArea;
?>
<section class="g-stripe g-stripe--two-col g-stripe--condensed">
    <div class="g-inner">
        <div class="row g-0 g-md-4">
            <div class="col-md-6 g-col">
                <div class="g-col__inner">
                    <?php
                    $area = new ContainerArea($container, 'Column 1');
                    $area->display($c);
                    ?>
                </div>
            </div>
            <div class="col-md-6 g-col">
                <div class="g-col__inner">
                    <?php
                    $area = new ContainerArea($container, 'Column 2');
                    $area->display($c);
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
