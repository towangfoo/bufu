<?php
/**
 * Database Schema migration setup
 *
 * @package     Bufu_Tickets
 * @author      Steffen Mücke <mail@quellkunst.de>
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE `{$installer->getTable('bufu_tickets_events')}`
    ADD `is_track_qty` TINYINT( 1 ) NOT NULL DEFAULT 0 AFTER `is_available`
;");

$installer->run("
ALTER TABLE `{$installer->getTable('bufu_tickets_events')}`
    ADD `qty_normal` INT( 11 ) DEFAULT NULL AFTER `is_track_qty`
;");

$installer->run("
ALTER TABLE `{$installer->getTable('bufu_tickets_events')}`
    ADD `qty_special` INT( 11 ) DEFAULT NULL AFTER `qty_normal`
;");

$installer->endSetup();
