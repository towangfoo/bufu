<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Model_Resource_Watchlog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct() 
    {
        $this->_init('watchlog/watchlog');
    }
    
    public function getSelectCountSql() 
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        if (count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
            $countSelect->columns('COUNT(DISTINCT ' . implode(', ', $group) . ')');
        } else {
            $countSelect->columns('COUNT(*)');
        }
        
        return $countSelect;
    }
}