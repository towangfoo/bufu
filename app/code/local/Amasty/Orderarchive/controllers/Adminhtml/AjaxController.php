<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

class Amasty_Orderarchive_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action
{
    public function archivingAction()
    {
        /** @var Amasty_Orderarchive_Model_Observer $observer */
        $observer = Mage::getModel('amorderarchive/observer');
        $result = $observer->amorderarchiveArchiving();

        if ($result) {
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('amorderarchive')->__('Orders Archive was started.'));
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amorderarchive')->__('Orders Archive is not enabled.'));
        }

        $this->_redirect('adminhtml/system_config/edit/section/amorderarchive');

        return true;
    }

    protected function _isAllowed()
    {
        return Mage::getStoreConfig('amorderarchive/general/turnedon');
    }

}