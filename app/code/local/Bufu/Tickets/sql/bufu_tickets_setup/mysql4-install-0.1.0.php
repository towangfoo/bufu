<?php
/**
 * Database Schema migration setup
 *
 * @category    Que
 * @package     Que_Mytunes
 * @author      Steffen Mücke <mail@quellkunst.de>
 */



$installer = $this;
/* @var $installer Bufu_Tickets_Model_Mysql4_Resource_Setup */

$installer->startSetup();

// add table for ticket events
$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('bufu_tickets_events')}`;
CREATE TABLE `{$installer->getTable('bufu_tickets_events')}` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `price_normal` decimal(12,4) unsigned NOT NULL DEFAULT '0.0000',
  `price_special` decimal(12,4) unsigned NOT NULL DEFAULT '0.0000',
  `event_location` varchar(255) NOT NULL,
  `event_title` varchar(255) DEFAULT NULL,
  `event_desc` text DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`event_id`),
  CONSTRAINT `FK_BUFU_TICKETS_PRODUCT` FOREIGN KEY (`product_id`)
    REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$installer->endSetup();
