<?php
/**
 * Database Schema migration setup
 *
 * @category    Que
 * @package     Que_Mytunes
 * @author      Steffen Mücke <mail@quellkunst.de>
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE `{$installer->getTable('bufu_tickets_events')}`
    ADD `is_special_price_available` TINYINT( 1 ) NOT NULL AFTER `price_special`
;");

$installer->endSetup();
