<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

class Amasty_Orderarchive_Helper_Archive_Order extends Amasty_Orderarchive_Helper_Archive_Base
{

    protected function initTableNames()
    {
        $this->baseTable = Mage::getResourceModel('sales/order_grid_collection')->getMainTable();
        $this->archiveTable = Mage::getResourceModel('amorderarchive/orderGrid')->getMainTable();
    }

    public function __construct()
    {
        $this->initTableNames();
    }

    /**
     * @param array $params
     * @param Magento_Db_Adapter_Pdo_Mysql $connection
     * @return array
     */
    public function addToArchive($params, $connection)
    {
        $this->connection = $connection;
        return $this->move($this->baseTable, $this->archiveTable, $params);
    }

    /**
     * @param array $params
     * @param Magento_Db_Adapter_Pdo_Mysql $connection
     * @return array
     */
    public function removeFromArchive($params, $connection)
    {
        $this->connection = $connection;
        return $this->move($this->archiveTable, $this->baseTable, $params);
    }

    /**
     *
     * @param array $params
     * @param Magento_Db_Adapter_Pdo_Mysql $connection
     * @return int count of deleted records
     * @throws Zend_Db_Adapter_Exception
     */
    public function removePermanently($params)
    {
        $params = $this->prepareParams($params);
        $collectionOrders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter(key($params), array( 'in' => current($params)));
        $countRec = $collectionOrders->count();

        foreach($collectionOrders as $item)
        {
            //delete quotes
            $collectionQuotes = Mage::getModel('sales/quote')->getCollection()->addFieldToFilter('entity_id', array('in' => $item->getQuoteId()));

            //delete orders, if will be error throws and quote delete
            $item->delete();
            foreach($collectionQuotes as $queue){
                $queue->delete();
            }
        }
        return $countRec;
    }

    /**
     * @param string $tableName
     * @param array $params
     * @return Varien_Db_Select
     */
    protected function getSelect($tableName, array $params)
    {
        $select = $this->connection->select()
            ->from($tableName);

        if(!empty($params)) {
            $select->where($this->connection->quoteIdentifier(key($params)). " IN (?)", current($params));
        }
        if($tableName == $this->baseTable && (Mage::getStoreConfig('amorderarchive/general/enable_massfilter') == 0 || empty($params)))
        {
            $select
                ->where($this->getDayCondition($this->connection))
                ->where($this->getOrderStatusCondition($this->connection));
        }
        return $select;
    }

    /**
     * @return string
     */
    protected function getDayCondition()
    {
        $countDay = Mage::getStoreConfig('amorderarchive/general/day_ago');
        $dateCreate = new DateTime();
        $dateCreate->modify(sprintf('- %d day', $countDay));

        $condition = $this->connection->quoteInto('`created_at` < ? ', $dateCreate->format('Y-m-d 23:59:59'));
        return $condition;
    }

    /**
     * @return string
     */
    protected function getOrderStatusCondition()
    {
        $statuses = Mage::getStoreConfig('amorderarchive/general/status');
        $condition = $this->connection->quoteInto(' `status` IN (?)', explode(',', $statuses));
        return $condition;
    }

    /**
     * @param string $tableName
     * @param array $params
     * @return int
     */
    protected function getIdMovedNotes($tableName, array $params)
    {
        $select = $this->getSelect($tableName, $params);
        return $this->connection->fetchCol($select);
    }

    protected function prepareParams($params)
    {
        return array_key_exists('order', $params) ? $params['order'] : array();
    }

}