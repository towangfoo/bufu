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
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Default Modul observer
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Observer
{
    /**
     * Checking if products of
     * Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT
     * were added and get sure only of that type is in cart.
     *
     * @param Varien_Event_Observer $observer current event observer
     *
     * @return void
     */
    public function addProductToCart($observer)
    {
        // phpmd hack unused parameter.
        unset($observer);

        $frontController = Mage::app()->getFrontController();
        $request = $frontController->getRequest();
        /* @var $cart Mage_Checkout_Model_Cart */
        $cart = Mage::getSingleton('checkout/cart')->setStore(Mage::app()->getStore());
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        $helper = Mage::helper('buyerprotect');
        $tsProductsInCart = $helper->getTsProductsInCart();

        if ($request->getParam('trusted_shops')) {

            // cart is empty
            if (!($cartProductIds = $cart->getProductIds())) {
                return;
            }

            $requestedProductId = $request->getParam('trusted_shops-product');

           /**
            * cart is not empty but the only item is a type of
            * Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT
            * and is identical to $requestedProductId.
            */
            if ((count($cartProductIds) < 2) && in_array($requestedProductId, $cartProductIds)) {
                return;
            }

            /**
             * Get rid off all previous added products of
             * Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT.
             * This way it get sure that only one item of this product type is in cart.
             */
            if ($tsProductsInCart) {
                foreach ($tsProductsInCart as $cartItemId => $tsProductId) {
                    $cart->removeItem($cartItemId);
                }
            }

            // add Buyerprotection Product to cart
            $cart->addProductsByIds(array($requestedProductId));
            $this->_saveCart($cart);
        } else {
            if ($tsProductsInCart) {
                foreach ($tsProductsInCart as $cartItemId => $tsProductId) {
                    $cart->removeItem($cartItemId);
                }
            }
        }

        return;
    }

    /**
     * This is a copy from Mage_Checkout_Model_Cart::save method, because a original
     * method call a "quote collectTotals", that throws Shopping Cart Price Rules procedure
     * with a Payment Method condition.
     *
     * @param Mage_Checkout_Model_Cart $cart Checkout cart model object
     *
     * @return void
     */
    private function _saveCart($cart)
    {
        $cart->getQuote()->getBillingAddress();
        $cart->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $cart->getQuote()->save();
        $cart->getCheckoutSession()->setQuoteId($cart->getQuote()->getId());
        /**
         * Cart save usually called after chenges with cart items.
         */
        Mage::dispatchEvent('checkout_cart_save_after', array('cart' => $cart));
    }

    /**
     * Init Symmetrics_Buyerprotect_Model_Service_Soap if the corresponding
     * product is in cart and register it to customer session for later use.
     *
     * @param Varien_Event_Observer $observer Varien observer object
     *
     * @return void
     */
    public function registerTsSoapModel($observer)
    {
        $helper = Mage::helper('buyerprotect');
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */

        if ($helper->hasTsProductsInCart()) {
            $order = $observer->getEvent()->getOrder();
            /* @var $order Mage_Sales_Model_Order */
            $customerSession = Mage::getSingleton('customer/session');
            /* @var $customerSession Mage_Customer_Model_Session */

            $customerSession->setTsSoap(true);
            $customerSession->setOrderId($order->getId());
        }

        return;
    }

    /**
     * Request for buyer protection service of Trusted Shops if
     * Symmetrics_Buyerprotect_Model_Service_Soap is in customer session on
     * checkout success.
     *
     * @param Varien_Event_Observer $observer Varien observer object
     *
     * @return void
     */
    public function requestTsProtection($observer)
    {
        // phpmd hack unused parameter.
        unset($observer);

        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */

        if ($customerSession->getTsSoap()) {
            $tsSoap = Mage::getModel('buyerprotect/service_soap');
            $tsSoap->setOrderId($customerSession->getOrderId());

            /* @var $tsSoap Symmetrics_Buyerprotect_Model_Service_Soap */
            $tsSoap->loadOrder();
            try {
                Mage::log('start SOAP request');
                $tsSoap->requestForProtection();
                Mage::log('end SOAP request');
            } catch (Exception $e) {
                Mage::log('SOAP request failed! See exception log!', null, null, true);
                Mage::logException($e);
            }
        }

        return;
    }

    /**
     * Observer to prevent discount rules to the product type.
     *
     * @param Varien_Event_Observer $observer current event observer
     *
     * @todo implement code
     *
     * @return null
     */
    public function quoteCalculateDiscountItem($observer)
    {
        $event = $observer->getEvent();

        /* @var $item Mage_Sales_Model_Quote_Item */
        $item = $event->getItem();
        if ($item->getProductType() == Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT) {
            $result = $event->getResult();
            $result->setDiscountAmount(0);
            $result->setBaseDiscountAmount(0);
        }
    }

    /**
     * Observer to check correct values of stock table 'cataloginventory_stock_item'
     * for product type Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT.
     *
     * @param Varien_Event_Observer $observer Varien observer object
     *
     * @return void
     */
    public function hookIntoCataloginventoryStockItemSaveAfter($observer)
    {
        $stockItem = $observer->getEvent()->getItem();
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */

        if (!$stockItem) {
            return;
        }

        $typeIdentifier = $stockItem->getProductTypeId();

        if ($typeIdentifier == Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT) {
            Symmetrics_Buyerprotect_Model_Type_Buyerprotect::checkStockItem($stockItem);
        }

        return;
    }

    /**
     * Check certificate status.
     * admin_system_config_changed_section_buyerprotection
     *
     * @param Varien_Event_Observer $observer Varien observer object.
     *
     * @return void
     */
    public function checkCertificate($observer)
    {
        $params = Mage::app()->getRequest()->getParams();
        if (isset($params['groups']['data']['fields']['trustedshops_id']['value'])) {
            $tsId = trim($params['groups']['data']['fields']['trustedshops_id']['value']);
        }
        if (!isset($tsId) || is_null($tsId)) {
            return;
        }
        $helper = Mage::helper('buyerprotect');
        $website = $observer->getWebsite();
        $store = $observer->getStore();

        if (!empty($store)) {
            $scope = 'stores';
            $scopeId = Mage::getModel('core/store')->load($store, 'code')->getId();
        } elseif (!empty($website)) {
            $scope = 'websites';
            $scopeId = Mage::getModel('core/website')->load($website, 'code')->getId();
        } else {
            $scope = 'default';
            $scopeId = 0;
        }
        $pattern = '!^X[A-Za-z0-9]{32}$!imsU';
        if (!preg_match($pattern, $tsId)) {
            Mage::getSingleton('core/session')->addNotice(
                $helper->__('Invalid Trusted Shops ID. Disabled buyer protection.')
            );

            Mage::helper('buyerprotect')->setConfigData(
                Symmetrics_Buyerprotect_Helper_Data::XML_PATH_TS_BUYERPROTECT_IS_ACTIVE,
                0,
                $scope,
                $scopeId
            );
        } else {
            $tsData = Mage::getModel('buyerprotect/service_soap')->checkCertificate();

            if ($tsData['variation'] == 'CLASSIC') {
                $variation = Symmetrics_Buyerprotect_Model_System_Config_Source_Variation::CLASSIC_VALUE;
            } else {
                $variation = Symmetrics_Buyerprotect_Model_System_Config_Source_Variation::EXCELLENCE_VALUE;
                $productsModel = Mage::getModel('buyerprotect/products');
                $productsModel->recreateProducts(false, $website, $store);
            }

            Mage::helper('buyerprotect')->setConfigData(
                Symmetrics_Buyerprotect_Helper_Data::XML_PATH_TS_BUYERPROTECT_VARIATION,
                $variation,
                $scope,
                $scopeId
            );

            $returnString = 'Checking Trusted Shops ID: ' . $tsId . ' | Set variation to ' . $tsData['variation'];
            Mage::getSingleton('core/session')->addNotice($returnString);
        }
    }
}
