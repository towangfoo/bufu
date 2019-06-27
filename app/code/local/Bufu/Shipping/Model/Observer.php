<?php
/**
 * Bufu_Shipping Events Observer
 *
 * @category   Bufu
 * @package    Bufu_Shipping
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Shipping_Model_Observer
{
    /**
     * Observer for event "checkout_cart_save_before"
     * Called when the cart is saved in frontend.
     * Set default shipping method to show a price as early as possible.
     *
     * @param  Varien_Event_Observer $observer
     * @return Bufu_Shipping_Model_Observer
     */
    public function checkout_cart_save_before(Varien_Event_Observer $observer)
    {
        /* @var $cart Mage_Checkout_Model_Cart */
        $cart = $observer->getEvent()->getCart();

        // hardcode shipping method
        $addressCountryId   = 'DE';
        $shippingMethodCode = 'tablerate_bestway';

        // prepare a shipping method with just the country set and calculate shipping price
        $quote = $cart->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCountryId($addressCountryId);
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates();
        $shippingAddress->setShippingMethod($shippingMethodCode);
        $quote->save();

        return $this;
    }
}
