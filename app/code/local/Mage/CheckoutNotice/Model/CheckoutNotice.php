<?php
/*
 * Mage_CheckoutNotice_Model_CheckoutNotice
 */

class Mage_CheckoutNotice_Model_CheckoutNotice
{
    const CHECKOUT_NOTICE_FORMKEY       = 'checkoutNotice:notice';
    const CHECKOUT_NOTICE_SESSIONKEY    = 'checkoutNotice';

    /**
     * get notice
     *
     * @return string | false
     */
    public function getNotice()
    {
        $session = Mage::getSingleton('checkout/session');
        if (isset($session[self::CHECKOUT_NOTICE_SESSIONKEY])) {
            return $session[self::CHECKOUT_NOTICE_SESSIONKEY];
        }
        else return false;
    }

    /**
     * set notice
     *
     * @param  string $notice
     * @return void
     */
    public function setNotice($notice)
    {
        $notice = trim($notice);

        if (strlen($notice)>0) {
            // save notice
            $session = Mage::getSingleton('checkout/session');
            $session[self::CHECKOUT_NOTICE_SESSIONKEY] = $notice;
        }
    }

    public function clear()
    {
        $session = Mage::getSingleton('checkout/session');
        if(isset($session[self::CHECKOUT_NOTICE_SESSIONKEY]))
            unset($session[self::CHECKOUT_NOTICE_SESSIONKEY]);
    }

}