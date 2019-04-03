<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

class Amasty_Orderarchive_Model_Observer
{
    const SALES_ORDER_GRID_CONTROLLER = 'sales_order';

    const ORDER_ARCHIVE_GRID_CONTROLLER = 'orderArchive';

    //fixes conflict with MageWorx OrdersPro module
    const ORDERSPRO_ORDER = 'orderspro_order';

    /**
     * @param Varien_Event_Observer $observer
     * @throws Exception
     * @return null
     */
    public function coreBlockAbstractPrepareLayoutBefore(Varien_Event_Observer $observer)
    {
        $block =$observer->getEvent()->getBlock();

        if ($this->isAllowedAddingMassactions($block) === true) {
            return $this->addMassActions($block);
        } elseif($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            return $this->addButtonArchive($block);
        }
    }

    /**
     * @param Mage_Adminhtml_Block_Page $block
     * @return bool
     */
    protected function isAllowedAddingMassactions($block)
    {
        return ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction
                || $block instanceof Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction)
            && Mage::getStoreConfig('amorderarchive/general/turnedon') == 1;

    }

    /**
     * @param Mage_Adminhtml_Block_Page $block
     * @throws Exception
     * @return void
     */
    protected function addMassActions($block)
    {
        /**
         * @var Mage_Adminhtml_Block_Page $block
         */
        switch($block->getRequest()->getControllerName()) {
            case self::SALES_ORDER_GRID_CONTROLLER:
            case self::ORDERSPRO_ORDER:
                if (Mage::getSingleton('admin/session')->isAllowed('sales/amorderarchive/actions/add_to_archive')) {
                    $block->addItem('add_to_archive', array(
                        'label'=> Mage::helper('amorderarchive')->__('Add to Archive'),
                        'url'  => Mage::app()->getStore()->getUrl('*/orderArchive/massAddToArchive')
                    ));
                }
                if (Mage::getSingleton('admin/session')->isAllowed('sales/amorderarchive/actions/remove_permanently')) {
                    $block->addItem('delete_permanently', array(
                        'label'=> Mage::helper('amorderarchive')->__('Delete Permanently'),
                        'url'  => Mage::app()->getStore()->getUrl('*/orderArchive/massRemovePermanently'),
                        'confirm' => Mage::helper('amorderarchive')->__('Are you sure you want to delete permanently this orders? The action is irreversible.')
                    ));
                }
                break;
            case self::ORDER_ARCHIVE_GRID_CONTROLLER:
                if (Mage::getSingleton('admin/session')->isAllowed('sales/amorderarchive/actions/remove_from_archive')) {
                    $block->addItem('remove_from_archive', array(
                        'label'=> Mage::helper('amorderarchive')->__('Remove from Archive'),
                        'url'  => Mage::app()->getStore()->getUrl('*/orderArchive/massRemoveFromArchive')
                    ));
                }
                if (Mage::getSingleton('admin/session')->isAllowed('sales/amorderarchive/actions/remove_permanently')) {
                    $block->addItem('delete_permanently', array(
                        'label'=> Mage::helper('amorderarchive')->__('Delete Permanently'),
                        'url'  => Mage::app()->getStore()->getUrl('*/orderArchive/massRemovePermanently'),
                        'confirm' => Mage::helper('amorderarchive')->__('Are you sure you want to delete permanently this orders? The action is irreversible.')
                    ));

                }
                break;
        }
    }

    /**
     * @param Mage_Adminhtml_Block_Sales_Order_View $block
     */
    protected function addButtonArchive($block)
    {
        $orderArchive = Mage::getModel('amorderarchive/orderGrid')->load($block->getOrderId());
        if(!$orderArchive->getId()) {
            $message = Mage::helper('amorderarchive')->__('Are you sure you want to archive this order?');
            $block->addButton('archive_order', array(
                'label'     => Mage::helper('amorderarchive')->__('Archive'),
                'onclick'   => "confirmSetLocation('{$message}', '{$block->getUrl( '*/orderArchive/addToArchive')}')",
            ));
        } elseif($orderArchive->getId()) {
            $message = Mage::helper('amorderarchive')->__('Are you sure you want to remove from archive this order?');

            $block->addButton('remove_from_archive_order', array(
                'label'     => Mage::helper('amorderarchive')->__('Remove From Archive'),
                'onclick'   => "confirmSetLocation('{$message}', '{$block->getUrl('*/orderArchive/removeFromArchive')}')",
            ));
        }
    }

    public function amorderarchiveOrderArchivedAfter($observer = null, $params)
    {
        /**
         * @var Mage_Cron_Model_Schedule $observer
         */
        $executeTime = !$observer ? date('Y-m-d H:i:s') : $observer->getExecutedAt();
        if(Mage::getStoreConfig('amorderarchive/email/turnedon'))
        {
            Mage::helper('amorderarchive/email_data')->sendEmailArchiving(count($params['order']), $executeTime);
        }
    }

    /**
     * Run observer method by cron
     * @param $observer
     * @return bool
     */
    public function amorderarchiveArchiving($observer = null)
    {
        if (!Mage::getStoreConfig('amorderarchive/general/turnedon') == 1) {
            return false;
        }

        $result =  Mage::helper('amorderarchive/archive')
            ->addToArchive(array());

        if (is_array($result) && count($result['order']) > 0
            && array_key_exists('order', $result)
        ) {
            $this->amorderarchiveOrderArchivedAfter($observer, $result);
        }
        return true;
    }

    /**
     * Hide archived orders in frontend
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function amorderarchiveFilterArchivedOrders(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('amorderarchive/general/hide_archive_in_frontend')) {
            return $this;
        }
        /** @var Mage_Sales_Model_Resource_Order_Collection $orderCollection */
        $orderCollection = $observer->getData('order_collection');
        $orderCollection->join(
            array('order_grid' => 'sales/order_grid'),
            'main_table.entity_id = order_grid.entity_id',
            array('grid_created_at' =>'order_grid.created_at')
        );

        return $this;
    }

    /**
     * Hide archived orders in frontend by reference link
     * If Order contains in archive, reset data him
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function amorderarchiveFilterArchivedOrderModel(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order  $order */
        $order = $observer->getData('order');
        $archivedGrid = Mage::getModel('amorderarchive/orderGrid')->load($order->getId());

        if ($archivedGrid->getId()
            && Mage::getStoreConfig(
                'amorderarchive/general/hide_archive_in_frontend'
            )
        ) {
            $order->reset();
        }
        return $this;
    }
}