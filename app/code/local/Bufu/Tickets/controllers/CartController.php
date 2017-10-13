<?php
/**
 * Shopping cart controller
 *
 * @category   Que
 * @package    Que_Mytunes
 * @author     Steffen Mücke <mail@quellkunst.de>
 *
 * @derived from Mage_Checkout_CartController
 */
class Bufu_Tickets_CartController extends Mage_Core_Controller_Front_Action
{

    /**
     * keep track of whether we already added related products.
     */
    protected $_addedRelated = false;

    /**
     * Add product to shopping cart action.
     * Use options from POST bufu_tickets to setup product(s).
     * Add separate products for normal and special price tickets.
     */
    public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }

        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();

        if (!isset($params['bufu_tickets'])) {
            $this->_goBack();
        }

        $bufuOptions = $params['bufu_tickets'];
        $params['bufu_tickets'] = array();

        try {
            $product = false;

            // add normal price ticket as a product
            if ((int) $bufuOptions['nr_normal'] > 0) {
                $normalProduct = $this->_initProduct(Bufu_Tickets_Helper_Data::TICKET_TYPE_NORMAL);
                if (!$normalProduct) {
                    $this->_goBack();
                    return;
                }

                $params['qty'] = $bufuOptions['nr_normal'];
                $params['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_TYPE] = $normalProduct->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_TYPE)->getValue();
                $params['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_EVENT_ID] = $normalProduct->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID)->getValue();

                $cart->addProduct($normalProduct, $params);
                $this->_addRelatedOnce();

                /**
                 * @todo remove wishlist observer processAddToCart
                 */
                Mage::dispatchEvent('checkout_cart_add_product_complete',
                    array('product' => $normalProduct, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );

                $product = $normalProduct;
            }

            // add special price ticket as a product
            if ((int) $bufuOptions['nr_special'] > 0) {
                $specialProduct = $this->_initProduct(Bufu_Tickets_Helper_Data::TICKET_TYPE_SPECIAL);
                if (!$specialProduct) {
                    $this->_goBack();
                    return;
                }

                $params['qty'] = $bufuOptions['nr_special'];
                $params['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_TYPE] = $specialProduct->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_TYPE)->getValue();
                $params['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_EVENT_ID] = $specialProduct->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID)->getValue();

                $cart->addProduct($specialProduct, $params);
                $this->_addRelatedOnce();

                /**
                 * @todo remove wishlist observer processAddToCart
                 */
                Mage::dispatchEvent('checkout_cart_add_product_complete',
                    array('product' => $specialProduct, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );

                $product = $specialProduct;
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = Mage::helper('checkout/cart')->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        }
        catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('checkout')->__('Cannot add the item to shopping cart.'));
            $this->_goBack();
        }
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct($type = null)
    {
        $productId = (int) $this->getRequest()->getParam('product');
        $bufuOptions = $this->getrequest()->getParam('bufu_tickets');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                if ($type != null) {
                    // set updated price according to event and type!
                    $event = Mage::getModel('bufu_tickets/event')->load($bufuOptions['event_id']);

                    $price = 0;
                    if ($type == Bufu_Tickets_Helper_Data::TICKET_TYPE_NORMAL) {
                        $price = $event->getPriceNormal();
                    } else if ($type == Bufu_Tickets_Helper_Data::TICKET_TYPE_SPECIAL) {
                        $price = $event->getPriceSpecial();
                    }
                    // does not seem to work, gets overrun by Quote_Item
                    $product->setPrice($price);

                    // add bufu_ticket options
                    $product->addCustomOption(Bufu_Tickets_Helper_Data::OPTION_TYPE, $type);
                    $product->addCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID, $bufuOptions['event_id']);
                    $product->addCustomOption(Bufu_Tickets_Helper_Data::OPTION_PRICE, $price);
                }

                return $product;
            }
        }
        return false;
    }

    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack()
    {
        if ($returnUrl = $this->getRequest()->getParam('return_url')) {
            // clear layout messages in case of external url redirect
            if ($this->_isUrlInternal($returnUrl)) {
                $this->_getSession()->getMessages(true);
            }
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()) {

            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }

    protected function _addRelatedOnce()
    {
        if ($this->_addedRelated === false) {
            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }
            $this->_addedRelated = true;
        }
    }
}
