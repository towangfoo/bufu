<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
$installer = $this;
$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$this->getTable('watchlog')};");

$table = $installer->getConnection()
        ->newTable($installer->getTable('watchlog'))
        ->addColumn(
            'watchlog_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'primary'   => true,
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false
            )
        )
        ->addColumn('ip', Varien_Db_Ddl_Table::TYPE_TEXT, 25)
        ->addColumn('date', Varien_Db_Ddl_Table::TYPE_DATETIME, 255, array('nullable'  => false))
        ->addColumn('login', Varien_Db_Ddl_Table::TYPE_TEXT, 200)
        ->addColumn('useragent', Varien_Db_Ddl_Table::TYPE_TEXT, 1000)
        ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, 200)
        // 0 = Failed, 1 = Success, 2 = Blocked
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 1)
        ->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, 500);

$installer->getConnection()->createTable($table);

$installer->endSetup();