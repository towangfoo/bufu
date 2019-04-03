<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

abstract class Amasty_Orderarchive_Helper_Archive_Base extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     */
    protected $baseTable;

    /**
     * @var string
     */
    protected $archiveTable;

    /**
     * @var Magento_Db_Adapter_Pdo_Mysql
     */
    protected $connection;

    abstract protected function initTableNames();

    /**
     * @param array $params
     * @param Magento_Db_Adapter_Pdo_Mysql $connection
     * @return mixed
     */
    abstract public function addToArchive($params, $connection);

    /**
     * @param array $params
     * @param Magento_Db_Adapter_Pdo_Mysql $connection
     * @return mixed
     */
    abstract public function removeFromArchive($params, $connection);

    public function removePermanently($params){}

    /**
     * @param string $tableName
     * @param array $params
     * @return Varien_Db_Select
     */
    abstract protected function getSelect($tableName, array $params);

    /**
     * remove Table from grid
     * @param string $tableName table name. Must be table name of grid.
     * @param array $params
     * @return int
     * @throws Zend_Db_Adapter_Exception
     */
    protected function removeFromGrid($tableName, array $params)
    {
        $select = $this->getSelect($tableName, $params);
        return $this->connection->exec($select->deleteFromSelect($tableName));
    }

    /**
     *
     * Move data by params from one table in other one
     * @param string $tableFrom
     * @param string $tableTo
     * @param array &$params
     * @return array Array displaced Orders
     */
    protected function move($tableFrom, $tableTo, array &$params)
    {
        $params = $this->prepareParams($params);

        $insertFields = array_intersect(
            array_keys($this->connection->describeTable($tableFrom)),
            array_keys($this->connection->describeTable($tableTo))
        );

        /** @var Varien_Db_Select $select */
        $select = $this->getSelect($tableFrom, $params);

        // for unarchiving check add only fields which are containing in both tables
        // fix for Amasty Perm Module
        if ($tableFrom == Mage::getSingleton('core/resource')->getTableName('amorderarchive/order_archive_grid')) {
            $select
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns($insertFields);
        }

        $movedIds = $this->connection->fetchCol($select);

        $this->connection->exec($select->insertFromSelect($tableTo, $insertFields, true));
        $this->removeFromGrid($tableFrom, $params);
        return $movedIds;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function prepareParams($params)
    {
        return $params;
    }
}