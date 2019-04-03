<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

class Amasty_Orderarchive_Block_Adminhtml_OrderArchive extends Mage_Adminhtml_Block_Sales_Order
{

    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'amorderarchive';
        $this->_controller = 'adminhtml_orderArchive';
        $this->_headerText = $this->__('Archive Orders');

        $this->_removeButton('add');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

}

