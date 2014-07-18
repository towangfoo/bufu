<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Bufu_Catalog_OrderableController extends Mage_Core_Controller_Front_Action
{
    /**
     * switch between normal and archive mode
     */
    public function switchAction()
    {
        $active = (int) $this->getRequest()->getPost('active');

        $session = Mage::getSingleton('checkout/session');

        if (1 === $active) {
            $session[Bufu_Catalog_Model_Layer::ARCHIVEMODE_SESSIONKEY] = true;
            $session->addSuccess($this->__('Not orderable products are shown'));
        }
        else {
            $session[Bufu_Catalog_Model_Layer::ARCHIVEMODE_SESSIONKEY] = false;
            $session->addSuccess($this->__('Not orderable products are not shown'));
        }

        $this->_redirectReferer();
    }

}
