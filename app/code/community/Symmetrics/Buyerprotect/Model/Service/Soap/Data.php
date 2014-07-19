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
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_Buyerprotect_Model_Service_Soap_Data contains data for SOAP API
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Service_Soap_Data extends Varien_Object
{
    /**
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order = null;

    /**
     * Initialize Data
     *
     * @param Mage_Sales_Model_Order      $order         Sales order object
     * @param Mage_Sales_Model_Order_Item $tsProductItem TS product id
     *
     * @return Symmetrics_Buyerprotect_Model_Service_Soap_Data
     */
    public function init(Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Item $tsProductItem)
    {
        $this->setTsProductItem($tsProductItem);
        $this->setTsProductId($tsProductItem->getSku());
        $this->_order = $order;

        $this->_initTsSoapData();

        return $this;
    }

    /**
     * Keys of data:
     *
     * returnValue: return_value
     * tsId: ts_id
     * tsProductId: ts_product_id
     * amount: amount
     * currency: currency
     * paymentType: payment_type
     * buyerEmail: buyer_email
     * shopCustomerID: shop_customer_id
     * shopOrderID: shop_order_id
     * orderDate: order_date
     * wsUser: ws_user
     * wsPassword: ws_password
     *
     * @return void
     * @throw Symmetrics_Buyerprotect_Exception
     */
    protected function _initTsSoapData()
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $this->_order;
        /* @var $payment Mage_Sales_Model_Order_Payment */
        $payment = $order->getPayment();
        /* @var $helper Symmetrics_Buyerprotect_Helper_Data */
        $helper = $this->getHelper();

        $tsStoreConfigPaths = $helper->getTsStoreConfigPaths();
        $paymentCode = $payment->getMethod();
        $availableCodes = $helper->getAvailableTsPaymentCodes();
        $allTsProductTypes = $helper->getAllTsProductTypes();

        $this->setAvailablePaymentCodes($availableCodes);

        if (!is_array($availableCodes) || !array_key_exists($paymentCode, $availableCodes)) {
            throw Mage::exception(
                'Symmetrics_Buyerprotect', "'$paymentCode' is not a supported payment by Trusted Shops!"
            );
        }

        if (!in_array($this->getTsProductItem()->getProductId(), $allTsProductTypes)) {
            throw Mage::exception(
                'Symmetrics_Buyerprotect', "{$this->getTsProductId()} is not a valid TS product type!"
            );
        }

        $tsSoapData = array(
            'is_active' => $helper->getStoreConfig($tsStoreConfigPaths['is_active']),
            'ts_id' => $helper->getStoreConfig($tsStoreConfigPaths['ts_id']),
            'ws_user' => $helper->getStoreConfig($tsStoreConfigPaths['ws_user']),
            'ws_password' => $helper->getStoreConfig($tsStoreConfigPaths['ws_password']),
            'wsdl_url' => $helper->getWsdlUrl(),
            'buyer_email' => $order->getCustomerEmail(),
            'amount' => $order->getGrandTotal(),
            'shop_order_id' => $order->getRealOrderId(),
            'order_date' => $this->getTsOrderDate($order->getCreatedAt()),
            'payment_type' => $this->getPaymentMethodByCode($paymentCode),
            'ts_product_id' => $this->getTsProductId(),
            'currency' => $helper->getCurrencyCode(),
            'shop_customer_id' => $order->getCustomerId()
        );

        $this->setData($tsSoapData);

        return;
    }

    /**
     * Returns an TS array whith formated keys.
     * 11 required fields for TS SOAP service. Note the format of the keys.
     *
     * @see self::_initTsSoapData()
     * @return array
     */
    public function getTsSoapData()
    {
        if (!($formatedTsSoapData = $this->getFormatedTsSoapData())) {
            $formatedTsSoapData = array(
                'tsId' => $this->getTsId(),
                'tsProductId' => $this->getTsProductId(),
                'amount' => $this->getAmount(),
                'currency' => $this->getCurrency(),
                'paymentType' => $this->getPaymentType(),
                'buyerEmail' => $this->getBuyerEmail(),
                'shopCustomerID' => $this->getShopCustomerId(),
                'shopOrderID' => $this->getShopOrderId(),
                'oderDate' => $this->getOrderDate(),
                'shopSystemVersion' => $this->getShopSystemVersion(),
                'wsUser' => $this->getWsUser(),
                'wsPasssword' => $this->getWsPassword()
            );

            $this->setFormatedTsSoapData($formatedTsSoapData);
        }

        return $formatedTsSoapData;
    }

    /**
     * Returns a TS formated date time:
     *
     * 2010-10-10T10:10:10
     *
     * @param string $dateTime Order date
     *
     * @return string
     */
    public function getTsOrderDate($dateTime)
    {
        return str_replace(' ', 'T', $dateTime);
    }

    /**
     * Returns the payment method
     *
     * @param string $paymentCode Payment code
     * 
     * @return string
     */
    public function getPaymentMethodByCode($paymentCode)
    {
        if (!($availableCodes = $this->getAvailablePaymentCodes())) {
            $availableCodes = $this->getHelper()->getAvailableTsPaymentCodes();
        }

        return $availableCodes[$paymentCode];
    }

    /**
     * Returns helper object
     *
     * @return Symmetrics_Buyerprotect_Helper_Data
     */
    public function getHelper()
    {
        if (!($helper = $this->getData('helper'))) {
            $helper = Mage::helper('buyerprotect');

            $this->setHelper($helper);
        }

        return $helper;
    }

    /**
     * URL of SOAP server      
     *                     
     * @param string $type SOAP API type [backend|frontend].                    
     *                     
     * @return string
     */
    public function getWsdlUrl($type = 'backend')
    {
        return $this->getHelper()->getWsdlUrl($type);
    }

    /**
     * Get Magento version.
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return Mage::getVersion();
    }

    /**
     * Get module version
     *
     * @return string
     */
    public function getModulVersion()
    {
        $name = Symmetrics_Buyerprotect_Helper_Data::BUYERPROTECT_MODUL_NAME;

        return Mage::getConfig()->getNode("modules/$name/version");
    }

    /**
     * Get both versions: Magento and module. It's for requestForProtectionV2().
     *
     * @return string
     */
    public function getShopSystemVersion()
    {
        if (!($version = $this->getData('shop_system_version'))) {
            $version  = 'Magento ' . $this->getMagentoVersion() . ' - '
                      . 'MC Trusted Shops Käuferschutz ' . $this->getModulVersion();
            
            $this->setData('shop_system_version', $version);
        }

        return $version;
    }

    /**
     * Check service is active
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getIsActive();
    }
}
