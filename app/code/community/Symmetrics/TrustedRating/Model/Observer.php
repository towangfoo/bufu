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
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

/**
 * Observer model
 *
 * SUPTRUSTEDSHOPS-129: Submission of reminder emails from the Magento host is replaced by
 * Trusted Shops' 'Rate later' service thus the event listener checkSendRatingEmail and the
 * used methods are superfluous.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_TrustedRating_Model_Observer
{
    /**
     * Config path to email template id
     *
     * @var string
     * @deprecated since v0.2.4
     */
    const XML_PATH_CONFIG_EMAIL_TEMPLATE = 'trustedrating/trustedrating_email/template';

    /**
     * Email identity path
     *
     * @var string
     */
    const XML_PATH_EMAIL_IDENTITY = 'sales_email/order/identity';

    /**
     * Change the status (active, inactive) by sending an api call to Trusted Shops
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function changeTrustedRatingStatus($observer)
    {
        $storeId = $observer->getStore();
        $soapUrl = Mage::helper('trustedrating')->getConfig('soapapi', 'url');
        $sendData = $this->_getSendData($storeId);
        $returnValue = $this->_callTrustedShopsApi($sendData, $soapUrl);
        $returnString = Mage::helper('trustedrating')->__('TrustedShops return value: ');
        Mage::getSingleton('core/session')->addNotice($returnString . $returnValue);
    }

    /**
     * Checks the shippings which will get an email
     *
     * @return void
     */
    public function checkSendRatingEmail()
    {
        $model = Mage::getModel('trustedrating/trustedrating');
        if ($shipmentIds = $model->checkShippings()) {
            $this->_sendTrustedRatingMails($shipmentIds);
        }
    }

    /**
     * Send mail and save entry to db
     *
     * @param array $shipmentIds Shipment IDs
     *
     * @return void
     */
    private function _sendTrustedRatingMails($shipmentIds)
    {
       foreach ($shipmentIds as $shipmentId) {
           $orderId = $this->getHelper()->getOrderId($shipmentId);
           $order = Mage::getModel('sales/order')->load($orderId);
           $this->_sendTransactionalMail($order);
           $this->_saveShipmentIdToTable($shipmentId);
       }
    }

    /**
     * Generates the rating url by given order object.
     *
     * @param Mage_Sales_Model_Order $order Order object.
     *
     * @return string
     */
    protected function _getRatingUrl($order)
    {
        $trustedRating = Mage::getSingleton('trustedrating/trustedrating');
        $storeId = $order->getStoreId();
        $tsId = $trustedRating->getTsId($storeId);
        $params = array(
            'buyerEmail' => base64_encode($order->getCustomerEmail()),
            'shopOrderID' => base64_encode($order->getRealOrderId())
        );
        $ratingUrl = $trustedRating->getEmailRatingLink($storeId) . '_' . $tsId . '.html'
                   . '&'    // Do not change, it's necessary for the TS systems to
                            // handle the query parameters correctly.
                   . http_build_query($params);

        return $ratingUrl;
    }

    /**
     * Generates the image url by given order object.
     *
     * @param Mage_Sales_Model_Order $order Order object.
     *
     * @return string
     */
    protected function _getImageUrl($order)
    {
        $language = Mage::getStoreConfig(
            Symmetrics_TrustedRating_Model_Trustedrating::CONFIG_LANGUAGE,
            $order->getStoreId()
        );
        
        return Mage::getDesign()->getSkinUrl(
            'images/bewerten_' . $language . '.gif', array('_area' => 'frontend')
        );
    }

    /**
     * Send transactional email to customer.
     *
     * @param Mage_Sales_Model_Order $order Order object.
     *
     * @return void
     */
    public function _sendTransactionalMail($order)
    {
        Mage::getModel('core/email_template')->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_CONFIG_EMAIL_TEMPLATE, $order->getStoreId()),
            Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY),
            $order->getCustomerEmail(),
            $order->getCustomerName(),
            array(
                'ratingUrl' => $this->_getRatingUrl($order),
                'imageUrl' => $this->_getImageUrl($order)
            ),
            $order->getStoreId()
        );
    }

    /**
     * Save shipping ID of customers which got an email to table
     *
     * @param int $shipmentId Shipping Id
     *
     * @return void
     */
    private function _saveShipmentIdToTable($shipmentId)
    {
        $mailModel = Mage::getModel('trustedrating/mail');
        $mailModel->setShippmentId($shipmentId)
            ->save();
    }

    /**
     * Collect the data for sending to Trusted Shops
     *
     * @param int $storeId storeID
     *
     * @return array
     */
    private function _getSendData($storeId)
    {
        $sendData = array();

        $sendData['tsId'] = Mage::getStoreConfig('trustedrating/data/trustedrating_id', $storeId);
        $sendData['activation'] = $this->isActive();
        $sendData['wsUser'] = $this->getHelper()->getConfig('soapapi', 'wsuser');
        $sendData['wsPassword'] = $this->getHelper()->getConfig('soapapi', 'wspassword');
        $sendData['partnerPackage'] = $this->getHelper()->getConfig('soapapi', 'partnerpackage');

        return $sendData;
    }

    /**
     * Call the SOAP API of Trusted Shops
     *
     * @param array $sendData data to send
     * @param array $soapUrl  soap url
     *
     * @return string
     */
    private function _callTrustedShopsApi($sendData, $soapUrl)
    {
        $returnValue = 'SOAP_ERROR';
        try {
            $client = new SoapClient($soapUrl);
            $returnValue = $client->updateRatingWidgetState(
                $sendData['tsId'],
                $sendData['activation'],
                $sendData['wsUser'],
                $sendData['wsPassword'],
                $sendData['partnerPackage']
            );
        } catch (SoapFault $fault) {
            $errorText = 'SOAP Fault: (faultcode: ' . $fault->faultcode;
            $errorText.= ', faultstring: ' . $fault->faultstring . ')';
            Mage::log($errorText);
        }

        return $returnValue;
    }

    /**
     * Return helper object
     *
     * @return Symmetrics_TrustedRating_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('trustedrating');
    }

    /**
     * Check wether module is active or not
     *
     * @return boolean
     */
    public function isActive()
    {
        return (bool)$this->getHelper()->getIsActive();
    }
}
