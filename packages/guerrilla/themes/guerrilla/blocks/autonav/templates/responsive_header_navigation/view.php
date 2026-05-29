<?php defined('C5_EXECUTE') or die('Access Denied.');
$navObjects = $controller->getNavItems();
$layout = 'dark-topbar';
require dirname(dirname(__DIR__)) . '/_render.php';
