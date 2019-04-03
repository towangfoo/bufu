<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

class Amasty_Orderarchive_Helper_Email_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @var array sender name and email
     */
    protected $sender;

    /**
     * @var Mage_Core_Model_Email_Template
     */
    protected $mailer;

    public function __construct()
    {
        $this->sender = array('name'  => Mage::getStoreConfig('trans_email/ident_general/name'),
                              'email' => Mage::getStoreConfig('trans_email/ident_general/email')
        );

        $this->mailer = Mage::getModel('core/email_template');
    }

    /**
     * @param string $recipientEmail
     * @param int $templateId
     * @param array $templateParams
     *
     * @return boolean true - email successful sent, false - error
     */
    public function send($recipientEmail, $templateId, array $templateParams)
    {

        $this->mailer->sendTransactional(
            $templateId,
            $this->sender,
            $recipientEmail,
            '',
            $templateParams
        );

        return $this->mailer->getSentSuccess();
    }

    /**
     * @param int $countOrders count archived orders
     * @param DateTime $dateStart Date time start archiving
     * @param DateTime $dateEnd Date time end archiving
     *
     * @return bool
     */
    public function sendEmailArchiving($countOrders, $dateStart)
    {
        $recipientEmail = Mage::getStoreConfig('amorderarchive/email/recipient');
        $templateId = Mage::getStoreConfig('amorderarchive/email/template');

        $dataStart = DateTime::createFromFormat('Y-m-d H:i:s', $dateStart);
        $dateEnd = new DateTime();
        $duration = $dateEnd->diff($dataStart);
        if (($duration->format('%i') == 0 && $duration->format('%s') == 0)) {
            $durationText = 'took less then a second';
        } elseif(($duration->format('%i') == 0 && $duration->format('%s') != 0)) {
            $durationText = $duration->format(' %s ' . Mage::helper('core/translate')->__('second(s).'));
        } else {
            $durationText = $duration->format('%i' . Mage::helper('core/translate')->__('minute(s)') . ' %s ' . Mage::helper('core/translate')->__('second(s).'));
        }

        $params = array(
            'datetime_start'   => $dateStart,
            'count_orders' => $countOrders,
            'duration' => $durationText,
            'subject' => 'Orders have been archived.',
        );

        return $this->send($recipientEmail, $templateId, $params);
    }

}