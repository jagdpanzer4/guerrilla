<?php defined('C5_EXECUTE') or die('Access Denied.');
$layout = 'icon-top'; $colorVariant = 'md3-block--accent';
$body     = $paragraph ?? '';
$iconHtml = isset($iconTag) ? (string)$iconTag : '';
$icon     = '';
$link     = $linkURL ?? '';
$linkText = '';
require dirname(__DIR__) . '/_render.php';
