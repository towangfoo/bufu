<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Adminhtml_BasicController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() 
    {
        Mage::helper('watchlog')->checkWarning();
        $this->loadLayout()
                ->_setActiveMenu('watchlog/watchlog')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Watchlog'), Mage::helper('adminhtml')->__('Watchlog'));

        return $this;
    }
    
    protected function _isAllowed() 
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/watchlog');
    }
    
    public function indexAction() 
    {
        $this->_title($this->__('Watchlog'));
        $this->_title($this->__('Manager Watchlog'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function purgeAction() 
    {
        try {
            $observer = Mage::getModel('watchlog/observer');
            $observer->purgeData();

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('The history was successfully purged')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}