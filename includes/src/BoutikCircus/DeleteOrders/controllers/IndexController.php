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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

class BoutikCircus_DeleteOrders_IndexController extends Mage_Adminhtml_Sales_OrderController
{
	/**
	 * Class Constructor
	 * call the parent Constructor
	 */

	public function __constuct()
	{
		parent::__construct();
	}

    /**
     * Delete selected orders
     */
    public function indexAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
        $countDeleteOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('deleteorders/order')->load($orderId);
            if ($order->canDelete()) {
                $order->delete()
                    ->delete();
                $countDeleteOrder++;
            }
        }
        if ($countDeleteOrder>0) {
            $this->_getSession()->addSuccess($this->__('%s order(s) successfully deleted', $countDeleteOrder));
        }
        else {
            // selected orders is not available for delete
            $this->_getSession()->addError($this->__('Please cancel your order before delete it. Only order without invoice, shipment or creditmemo could be deleted.', $countDeleteOrder));
        }

	$this->_redirect('adminhtml/sales_order/', array());
	
    }
}
