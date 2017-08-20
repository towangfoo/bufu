<?php
/*
 * Mage_CheckoutNotice_CheckoutNoticeController
 */
class Mage_CheckoutNotice_CheckoutNoticeController extends Mage_Core_Controller_Front_Action
{

    /**
     * save checkout notice
     *
     *  @return null
     */
    public function saveAction()
    {
        $session = $this->getSession();
        $notice  = $this->getRequest()->getPost(Mage_CheckoutNotice_Model_CheckoutNotice::CHECKOUT_NOTICE_FORMKEY);

        if (false === $this->getNotice()) {
            $session->addSuccess($this->__("Your notice has been saved"));
        }
        else {
            $session->addSuccess($this->__("Your notice has been updated"));
        }

        // save notice
        $session[Mage_CheckoutNotice_Model_CheckoutNotice::CHECKOUT_NOTICE_SESSIONKEY] = $notice;

        $this->_redirectReferer();
    }

    /**
     * save checkout notice
     *
     *  @return null
     */
    public function saveJsonAction()
    {
        $session = $this->getSession();
        $notice  = $this->getRequest()->getPost(Mage_CheckoutNotice_Model_CheckoutNotice::CHECKOUT_NOTICE_FORMKEY);

        if (!$notice) {
            $result = array();
            $result['success'] = false;
            $result['error'] = true;
            $result['error_message'] = $this->__("Your message could not be saved");
            $result['field'] = Mage_CheckoutNotice_Model_CheckoutNotice::CHECKOUT_NOTICE_FORMKEY;
            $this->getResponse()->setBody(Zend_Json::encode($result));
            return;
        }

        // save notice
        $checkoutNotice = new Mage_CheckoutNotice_Model_CheckoutNotice();
        $checkoutNotice->setNotice($notice);

        $result = array();
            $result['success'] = true;
            $result['error'] = false;
          //  $result['error_message'] = "Your message could not be saved";
          //  $result['field'] = self::CHECKOUT_NOTICE_FORMKEY;

        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
    }

    /**
     * get notice
     *
     * @return string | false
     */
    protected function getNotice()
    {
        $notice = new Mage_CheckoutNotice_Model_CheckoutNotice();
        return $notice->getNotice();
    }

    /**
     * get session instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

}