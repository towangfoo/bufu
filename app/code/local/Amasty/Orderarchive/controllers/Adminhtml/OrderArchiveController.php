<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */
require_once ('Mage/Adminhtml/controllers/Sales/OrderController.php');

class Amasty_Orderarchive_Adminhtml_OrderArchiveController extends Mage_Adminhtml_Sales_OrderController
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Archive Orders'));
        $this->_initAction()
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('amorderarchive/adminhtml_orderArchive_grid')
                ->toHtml()
            );
    }

    protected function _construct()
    {
        $this->setUsedModuleName('Amasty_Orderarchive');
    }

    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('amorderarchive/adminhtml_orderArchive_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('amorderarchive/adminhtml_orderArchive_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/order')
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'))
        ;
        return $this;
    }

    public function loadLayout($ids = null, $generateBlocks = true, $generateXml = true)
    {
        parent::loadLayout($ids, $generateBlocks, $generateXml);
        $this->_initLayoutMessages('adminhtml/session');
        return $this;
    }

    public function massAddToArchiveAction()
    {
        $params = array( 'order' => array(
            'entity_id' => Mage::app()->getRequest()->getParam('order_ids', array()),
        ));

        $result = $this->addToArchiveAction($params, true);

        $this->getMessageArchiving($result,
            $this->__('%s order(s) have been moved into archive.', count($result['order'])),
            $this->__('Selected order(s) cannot be moved into archive.')
        );

        $this->_redirect('*/sales_order');

    }

    public function massRemoveFromArchiveAction()
    {
        $params = array('order' => array(
            'entity_id' => Mage::app()->getRequest()->getParam('order_ids', array()),
        ));
        $result = $this->removeFromArchiveAction($params, true);

        $this->getMessageArchiving($result,
            $this->__('%s order(s) have been removed from archive.', count($result['order'])),
            $this->__('Selected order(s) cannot be removed from archive.')
        );

        $this->_redirect('*/orderArchive');
    }

    public function massRemovePermanentlyAction()
    {
        $params = array('order' => array(
            'entity_id' => Mage::app()->getRequest()->getParam('order_ids', array()),
        ));
        $result = $this->removePermanently($params);

        $this->getMessageArchiving($result,
            $this->__('%s order(s) have been removed permanently.', $result['order']),
            $this->__('Selected order(s) cannot be removed permanently.')
        );
        $this->_redirectReferer();
    }

    /**
     * @param $params
     * @return array|Mage_Adminhtml_Controller_Action
     */
    public function addToArchiveAction($params = array(), $fromMassAction = false)
    {
        if(empty($params)) {
            $params = array('order' => array(
                'entity_id' => Mage::app()->getRequest()->getParam('order_id', array()),
            ));
        }
        $result = Mage::helper('amorderarchive/archive')
            ->addToArchive($params);
        if($fromMassAction == true) {
            return $result;
        }
        else {
            $this->getMessageArchiving($result,
                $this->__('The order has been moved into archive.'),
                $this->__('The order cannot be moved into archive.')
            );
            $this->_redirectReferer();
        }
    }

    public function removeFromArchiveAction($params = array(), $fromMassAction = false)
    {
        if(empty($params)) {
            $params = array('order' => array(
                'entity_id' => Mage::app()->getRequest()->getParam('order_id', array()),
            ));
        }
        $result = Mage::helper('amorderarchive/archive')->removeFromArchive($params);
        if($fromMassAction == true) {
            return $result;
        }
        else {
            $this->getMessageArchiving($result,
                $this->__('The order has been removed from archive.'),
                $this->__('The order cannot be removed from archive.')
            );
            $this->_redirectReferer();
        }
    }

    public function removePermanently($params)
    {
        return Mage::helper('amorderarchive/archive')
            ->removePermanently($params);
    }

    /**
     * Batch print shipping labels for whole shipments.
     * Push pdf document with shipping labels to user browser
     *
     * @return null
     */
    public function massPrintShippingLabelAction()
    {
        $request = $this->getRequest();
        $ids = $request->getParam('order_ids');
        $createdFromOrders = !empty($ids);
        $shipments = null;
        $labelsContent = array();
        switch ($request->getParam('massaction_prepare_key')) {
            case 'shipment_ids':
                $ids = $request->getParam('shipment_ids');
                array_filter($ids, 'intval');
                if (!empty($ids)) {
                    $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                        ->addFieldToFilter('entity_id', array('in' => $ids));
                }
                break;
            case 'order_ids':
                $ids = $request->getParam('order_ids');
                array_filter($ids, 'intval');
                if (!empty($ids)) {
                    $shipments = Mage::getResourceModel('sales/order_shipment_collection')
                        ->setOrderFilter(array('in' => $ids));
                }
                break;
        }

        if ($shipments && $shipments->getSize()) {
            foreach ($shipments as $shipment) {
                $labelContent = $shipment->getShippingLabel();
                if ($labelContent) {
                    $labelsContent[] = $labelContent;
                }
            }
        }

        if (!empty($labelsContent)) {
            $outputPdf = $this->_combineLabelsPdf($labelsContent);
            $this->_prepareDownloadResponse('ShippingLabels.pdf', $outputPdf->render(), 'application/pdf');
            return;
        }

        if ($createdFromOrders) {
            $this->_getSession()
                ->addError(Mage::helper('sales')->__('There are no shipping labels related to selected orders.'));
            $this->_redirect('*/orderArchive/index');
        } else {
            $this->_getSession()
                ->addError(Mage::helper('sales')->__('There are no shipping labels related to selected shipments.'));
            $this->_redirect('*/sales_order_shipment/index');
        }
    }

    protected function getMessageArchiving($result, $successMessage, $errorMessage)
    {
        if(array_key_exists('order', $result) && count($result['order']) > 0)
        {
            $this->_getSession()->addSuccess($successMessage);
        }
        else {
            $this->_getSession()->addError($errorMessage);
        }
    }
}