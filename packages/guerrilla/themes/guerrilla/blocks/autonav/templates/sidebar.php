<?php defined('C5_EXECUTE') or die('Access Denied.');
$navObjects = $controller->getNavItems();
$layout = 'sidebar';
require dirname(__DIR__) . '/_render.php';
