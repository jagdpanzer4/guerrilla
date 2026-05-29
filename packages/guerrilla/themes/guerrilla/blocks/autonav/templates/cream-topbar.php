<?php defined('C5_EXECUTE') or die('Access Denied.');
$navObjects = $controller->getNavItems();
$layout = 'cream-topbar';
require dirname(__DIR__) . '/_render.php';
