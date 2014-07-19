<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
 
$installer = $this;

$installer->startSetup();

// create table for saving cashticket configurations
$installer->run(
    "DROP TABLE IF EXISTS {$this->getTable('symmetrics_cashticket')};
    CREATE TABLE {$this->getTable('symmetrics_cashticket')} (
        `item_id` int(11) NOT NULL auto_increment,
        `enable` tinyint NOT NULL,
        `currency_code` varchar(3) NOT NULL,
        `merchant_id` varchar(20) NOT NULL,
        `business_type` varchar(5) NOT NULL,
        `reporting_criteria` varchar(8) NOT NULL,
        `locale` varchar(10) NOT NULL,
        `path_pem_test` varchar(255) NOT NULL,
        `path_pem_live` varchar(255) NOT NULL,
        `path_cert` varchar(255) NOT NULL,
        `sslcert_pass` varchar(255) NOT NULL,
        `sandbox` enum('1', '0') NOT NULL,
        `created_time` datetime default NULL,
        `update_time` datetime default NULL,
        PRIMARY KEY  (`item_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$installer->endSetup(); 