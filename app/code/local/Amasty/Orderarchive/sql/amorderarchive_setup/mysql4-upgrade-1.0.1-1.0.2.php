<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

$installer->getConnection()
          ->addColumn(
              $installer->getTable('amorderarchive/order_archive_grid'), 'uid',
              array(
                  'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
                  'nullable' => true,
                  'COMMENT'  => 'uid field for Amasty Perm Module',
              )
          );

$installer->endSetup();