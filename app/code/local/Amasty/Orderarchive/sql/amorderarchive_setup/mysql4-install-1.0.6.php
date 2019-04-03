<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


/** @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

// Resolve support. Original Grid can have another column set.
$connection = $installer->getConnection();
$table = $connection->createTableByDdl(
    $installer->getTable('sales/order_grid'),
    $installer->getTable('amorderarchive/order_archive_grid')
)
    ->addColumn(
        'uid',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('nullable' => true),
        'uid field for Amasty Perm Module'
    );
$connection->createTable($table);

$table = $connection->createTableByDdl(
    $installer->getTable('sales/shipment_grid'),
    $installer->getTable('amorderarchive/shipment_archive_grid')
);
$connection->createTable($table);

$table = $connection->createTableByDdl(
    $installer->getTable('sales/creditmemo_grid'),
    $installer->getTable('amorderarchive/creditmemo_archive_grid')
);
$connection->createTable($table);

$table = $connection->createTableByDdl(
    $installer->getTable('sales/invoice_grid'),
    $installer->getTable('amorderarchive/invoice_archive_grid')
);
$connection->createTable($table);

$installer->endSetup();