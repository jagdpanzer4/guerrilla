<?php
/**
 * ConcreteCMS entry point
 */
define('DIR_BASE', __DIR__);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/concrete/bootstrap/configure.php';
require __DIR__ . '/concrete/bootstrap/autoload.php';
$app = require __DIR__ . '/concrete/bootstrap/start.php';
require __DIR__ . '/concrete/bootstrap/run.php';
