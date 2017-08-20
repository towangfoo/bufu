<?php
/*
 * Mage_BufuObserver_Model_Review
 */
class Mage_BufuObserver_Model_Review
{

    public function __construct()
    {
    }

    /**
     * save checkoutNotice
     * @param   Varien_Event_Observer $observer
     * @return  Bufu_Review_Model_Observer
     */
    public function bufu_review_controller_product_post($observer)
    {
        $postData = $observer->getData();

        // get config data
        $notification_send = Mage::getStoreConfig('catalog/review/notification_send');

        if ($notification_send) {
            // mail
            $emailTemplate  = Mage::getModel('core/email_template')
                ->load(Mage::getStoreConfig('catalog/review/notification_template'))
                ->setSenderName(Mage::getStoreConfig('catalog/review/notification_sendername'))
                ->setSenderEmail(Mage::getStoreConfig('catalog/review/notification_sender'));
            $variables = array(
                'title' => $postData['data']['title'],
                'detail' => $postData['data']['detail'],
                'nickname' => $postData['data']['nickname']
            );
            $resl = $emailTemplate->send(
                Mage::getStoreConfig('catalog/review/notification_email'),
                null, $variables
            );
        }

        return $this;
    }
}