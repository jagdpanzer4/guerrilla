<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Block\Accordion\AccordionEntry[] $entries
 */
$style        = 'standard';
$colorVariant = 'md3-block--light';

// Map CCMS AccordionEntry objects to the format expected by _accordion_base.php
$rows = array_map(function($entry) {
    $row = new stdClass();
    $row->title = $entry->getTitle();
    $row->body  = $entry->getDescription();
    return $row;
}, $entries ?? []);

require __DIR__ . '/_accordion_base.php';
