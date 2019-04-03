<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */ 
class Amasty_Orderarchive_Model_Resource_ShipmentGrid extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('amorderarchive/shipment_archive_grid', 'entity_id');
    }
}