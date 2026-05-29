<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * CCMS provides: $title, $iconTag (HTML), $paragraph (HTML), $linkURL, $f
 * Map to MD3 render variables.
 */
$layout       = 'icon-top';
$colorVariant = 'md3-block--light';

// $title is a block model property — available directly
$body     = $paragraph ?? '';
$icon     = '';   // $iconTag is full HTML — pass as-is via $iconHtml
$iconHtml = isset($iconTag) ? (string)$iconTag : '';
$link     = $linkURL ?? '';
$linkText = '';   // no linkText in CCMS feature block

require __DIR__ . '/_render.php';
