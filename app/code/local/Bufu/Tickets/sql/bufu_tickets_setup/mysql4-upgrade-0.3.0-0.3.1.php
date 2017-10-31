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
    DROP COLUMN `qty_special`
;");

$installer->endSetup();
