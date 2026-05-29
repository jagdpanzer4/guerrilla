<?php defined('C5_EXECUTE') or die('Access Denied.');
$style = 'filled'; $colorVariant = 'md3-block--dark';
$rows = array_map(function($entry) {
    $row = new stdClass();
    $row->title = $entry->getTitle();
    $row->body  = $entry->getDescription();
    return $row;
}, $entries ?? []);
require dirname(__DIR__) . '/_accordion_base.php';
