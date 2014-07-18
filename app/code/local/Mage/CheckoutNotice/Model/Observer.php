<?php
/*
 * Mage_CheckoutNotice_Model_Observer
 */
class Mage_CheckoutNotice_Model_Observer
{

    public function __construct()
    {
    }

    /**
     * save checkoutNotice
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CheckoutNotice_Model_Notice
     */
    public function checkout_type_onepage_save_order($observer)
    {
        // do this only once - install the attribute
        //$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        //$AttrCode = 'checkoutNotice';
        //$setup->addAttribute('11', $AttrCode, $settings);

        $order = $observer->getOrder();
        $checkoutNoticeClass = new Mage_CheckoutNotice_Model_CheckoutNotice();
        $checkoutNotice = $checkoutNoticeClass->getNotice();
        if ($checkoutNotice) {
            $order->setData('checkoutNotice', $checkoutNotice);
            $checkoutNoticeClass->clear();
        }

        return $this;
    }
}