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
 * Buyer Protection Soap Interface.
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Service_Soap
{
    /**
     * Constant to define $_requestErrorCode on exception.
     *
     * @todo in v2 0 is used by Trusted Shops
     */
    const TS_SOAP_EXCEPTION_CODE = -9999;

    /**
     * Do not set positive values on error!
     * The Trusted Shop API returns a positive value on success.
     *
     * @var int|null
     */
    protected $_requestErrorCode = null;

    /**
     * Log file name.
     *
     * @var string
     */
    protected $_buyerProtectLogFile = 'ts_buyerprotect.log';

    /**
     * Optional order object, if requestForProtection() is called later without
     * order as an param.
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order = null;       
    
    /**
     * Order id in case requestForProtection() is called later.
     *
     * @var int
     */
    protected $_orderId = null;  

    /**
     * Check certificate status.
     *
     * @return void
     */
    public function checkCertificate()
    {
        $helper = Mage::helper('buyerprotect');
        $wsdl = $helper->getWsdlUrl('backend');
        $tsId = $helper->getTsUserId();
        
        $soapClient = new SoapClient($wsdl);
        
        $tsData = $soapClient->checkCertificate($tsId);
        
        return array(
            'language' => $tsData->certificationLanguage,
            'variation' => $tsData->typeEnum,
            'state' => $tsData->stateEnum
        );        
    }
    
    /**
     * SOAP request to Trusted Shops, a positive $errorCode determines a successful
     * request.
     *
     * @param Symmetrics_Buyerprotect_Model_Service_Soap_Data $buyerprotectModul SOAP data object.
     *
     * @return void
     */
    protected function _request(Symmetrics_Buyerprotect_Model_Service_Soap_Data $buyerprotectModul)
    {
        $soapClient = new SoapClient($buyerprotectModul->getWsdlUrl());

        $this->_requestErrorCode = $soapClient->requestForProtection(
            $buyerprotectModul->getTsId(),
            $buyerprotectModul->getTsProductId(),
            $buyerprotectModul->getAmount(),
            $buyerprotectModul->getCurrency(),
            $buyerprotectModul->getPaymentType(),
            $buyerprotectModul->getBuyerEmail(),
            $buyerprotectModul->getShopCustomerId(),
            $buyerprotectModul->getShopOrderId(),
            $buyerprotectModul->getOrderDate(),
            $buyerprotectModul->getWsUser(),
            $buyerprotectModul->getWsPassword()
        );

        return;
    }

    /**
     * Request V2: integrationhandbook Version 3.00
     * Has additional param for shop and module version.
     *
     * @param Symmetrics_Buyerprotect_Model_Service_Soap_Data $buyerprotectModul SOAP data object.
     *
     * @return void
     */
    protected function _requestV2(Symmetrics_Buyerprotect_Model_Service_Soap_Data $buyerprotectModul)
    {                                                               
        $soapClient = new SoapClient($buyerprotectModul->getWsdlUrl('frontend'));

        $this->_requestErrorCode = $soapClient->requestForProtectionV2(
            $buyerprotectModul->getTsId(),
            $buyerprotectModul->getTsProductId(),
            $buyerprotectModul->getAmount(),
            $buyerprotectModul->getCurrency(),
            $buyerprotectModul->getPaymentType(),
            $buyerprotectModul->getBuyerEmail(),
            $buyerprotectModul->getShopCustomerId(),
            $buyerprotectModul->getShopOrderId(),
            $buyerprotectModul->getOrderDate(),
            $buyerprotectModul->getShopSystemVersion(),
            $buyerprotectModul->getWsUser(),
            $buyerprotectModul->getWsPassword()
        );

        return;
    }

    /**
     * Validation if item is set.
     *
     * @param object $tsItem An item object.
     *
     * @return void
     */
    private function checkIfTsItemIsSet($tsItem)
    {
        if (!$tsItem) {
            Mage::log("$tsItem is empty!");
        }
    }

    /**
     * make a protection request to the Trusted Shops Soap Api.
     *
     * @param Mage_Sales_Model_Order $order order to make a Reqest from.
     *
     * @return Symmetrics_Buyerprotect_Model_Service_Soap_Data|null
     * @throw Symmetrics_Buyerprotect_Model_Service_Soap_Exception
     * @todo do some logging
     */
    public function requestForProtection(Mage_Sales_Model_Order $order = null)
    {
        if (!$order && !$this->_order) {
            Mage::log('Order object not set!');
            return;
        }

        if (!$order) {
            $order = $this->_order;
        } else {
            $this->_order = $order;
        }

        $orderItemsCollection = clone $order->getItemsCollection();
        /* @var $orderItemsCollection Mage_Sales_Model_Mysql4_Order_Item_Collection */

        $orderItemsCollection->addFieldToFilter('product_type', array('eq' => 'buyerprotect'));

        // Varien_Data_Collection::count() will do the load!
        if ($orderItemsCollection->count() >= 1) {
            $tsItem = null;
            
            // determine TS product type.
            foreach ($orderItemsCollection->getItems() as $item) {
                if ($item->getProductType() == Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT) {
                    $tsItem = $item;
                }
            }

            $this->checkIfTsItemIsSet($tsItem);

            /* @var $tsSoapDataObject Symmetrics_Buyerprotect_Model_Service_Soap_Data */
            $tsSoapDataObject = Mage::getModel('buyerprotect/service_soap_data');

            $tsSoapDataObject->init($order, $tsItem);

            if ($tsSoapDataObject->isActive()) {
                try {
                    $this->_requestV2($tsSoapDataObject);
                    Mage::log(
                        'SOAP return value: ' . $this->_requestErrorCode,
                        null,
                        $this->_buyerProtectLogFile,
                        true
                    );
                    Mage::log('SOAP request successful.', null, $this->_buyerProtectLogFile, true);
                } catch (SoapFault $soapFault) {
                    $this->_requestErrorCode = self::TS_SOAP_EXCEPTION_CODE;
                    Mage::log('SOAP request failed! See exception log!', null, $this->_buyerProtectLogFile, true);
                    Mage::logException($soapFault);
                }     
                Mage::log($tsSoapDataObject->getTsSoapData(), null, $this->_buyerProtectLogFile, true);
            }

            return $tsSoapDataObject;
        }

        return null;
    }
                 
    /**                                                                            
     * Load Order object, using in advance established order id.
     *
     * @return Symmetrics_Buyerprotect_Model_Service_Soap
     */                                                              
    public function loadOrder()
    {                                       
        $this->_order = Mage::getModel('sales/order')->load($this->_orderId);

        return $this;
    }

    /**
     * Set order id in case requestForProtection() is called later.
     *
     * @param int $orderId Order id.
     *
     * @return Symmetrics_Buyerprotect_Model_Service_Soap
     */
    public function setOrderId($orderId)
    {
        $this->_orderId = $orderId;

         return $this;
    }
}