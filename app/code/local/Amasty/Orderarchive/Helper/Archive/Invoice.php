<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

class Amasty_Orderarchive_Helper_Archive_Invoice extends Amasty_Orderarchive_Helper_Archive_Base
{

    public function __construct()
    {
        $this->initTableNames();
    }

    protected function initTableNames()
    {
        $this->baseTable = Mage::getResourceModel('sales/order_invoice_grid_collection')->getMainTable();
        $this->archiveTable = Mage::getResourceModel('amorderarchive/invoiceArchive')->getMainTable();
    }

    /**
     * @inheritdoc
     */
    public function addToArchive($params, $connection)
    {
        $this->connection = $connection;
        return $this->move($this->baseTable, $this->archiveTable, $params);
    }

    /**
     * @inheritdoc
     */
    public function removeFromArchive($params, $connection)
    {
        $this->connection = $connection;
        return $this->move($this->archiveTable, $this->baseTable, $params);
    }

    /**
     * @inheritdoc
     */
    protected function getSelect($tableName, array $params)
    {
        $select = $this->connection->select()
            ->from($tableName);

        if(isset($params['order_id'])) {
            $select->where($this->getOrderIdCondition($params));
        }
        else {
            $select->where('0');
        }
        return $select;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function getOrderIdCondition(array $params)
    {
        $condition = $this->connection->quoteInto(' `order_id` IN (?)', $params['order_id']);
        return $condition;
    }

    protected function prepareParams($params)
    {
        return $params;
    }

}