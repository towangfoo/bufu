<?php

/*
 * Mage_CheckoutNotice_Block_Show
 */

class Mage_CheckoutNotice_Block_Show extends Mage_Core_Block_Template
{

    protected $_notice;

    function __construct()
    {
        parent::__construct();

        $this->_notice = new Mage_CheckoutNotice_Model_CheckoutNotice();

        $this->setTemplate('checkoutNotice/show.phtml');
    }

    public function getSaveAction()
    {
        return $this->getUrl('checkoutNotice/checkoutNotice/save');
    }

    public function getSaveJsonAction()
    {
        return $this->getUrl('checkoutNotice/checkoutNotice/saveJson');
    }

    /**
     * get notice
     *
     * @return string | false
     */
    public function getNotice()
    {
        return $this->_notice->getNotice();
    }

    /**
     * get form key name
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage_CheckoutNotice_Model_CheckoutNotice::CHECKOUT_NOTICE_FORMKEY;
    }

}