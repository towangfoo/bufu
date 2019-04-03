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
$connection       = $installer->getConnection();
$archiveTableName = $installer->getTable('amorderarchive/order_archive_grid');
$tmpTableName     = $archiveTableName . '_tmp';
$tmpTableCreated  = false;

$select = $connection->select()
    ->from($archiveTableName, array('entity_id'))
    ->limit('1');

if ($connection->fetchOne($select)) {
    $tmpTable = $connection
        ->createTableByDdl(
            $archiveTableName,
            $tmpTableName
        );
    $connection->createTemporaryTable($tmpTable);
    $select = $connection->select()->from($archiveTableName);
    $connection->query($connection->insertFromSelect($select, $tmpTableName));
    $tmpTableCreated = true;
}
$connection->dropTable($archiveTableName);
$table = $connection
    ->createTableByDdl(
        $installer->getTable('sales/order_grid'),
        $archiveTableName
    )
    ->addColumn(
        'uid',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array('nullable' => true),
        'uid field for Amasty Perm Module'
    );
$connection->createTable($table);

if ($tmpTableCreated) {
    $select = $connection->select()->from($tmpTableName);
    $connection->query($connection->insertFromSelect($select, $archiveTableName));
    $connection->dropTemporaryTable($tmpTableName);
}

$installer->endSetup();
