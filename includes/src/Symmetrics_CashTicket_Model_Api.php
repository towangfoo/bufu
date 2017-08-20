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
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_CashTicket_Model_Api
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_Model_Api extends Varien_Http_Adapter_Curl
{
    /**
     * Id of the Cash-Ticket transaction
     *
     * @var string
     */
    protected $_transactionId = '';

    /**
     * Use testing server or not 
     *
     * @var boolean
     */
    protected $_sandbox = null;
    
    /**
     * Current order
     *
     * @var object
     */
    protected $_order = null;
    
    /**
     * Configuration array
     *
     * @var array
     */
    protected $_config = null;

    /**
     * Main method for calling API functions
     * on Cash-Ticket
     *
     * @param string $functionName Function name
     * @param array  $params       Params
     *
     * @return object
     */
    public function call($functionName, array $params)
    {
        $callParams = '';

        // merge set params with standard params
        $params = array_merge(
            array(
                'currency' => $this->getConfigValue('currency_code'),
                'mid' => $this->getConfigValue('merchant_id'),
                'businesstype' => $this->getConfigValue('business_type'),
                'reportingcriteria' => $this->getConfigValue('reporting_criteria'),
                'okurl' => Mage::getUrl('cashticket/processing/success'),
                'nokurl' => Mage::getUrl('cashticket/processing/error'),
                'locale' => $this->getConfigValue('locale'),
                'outputFormat' => 'xml_v1',
                'mtid' => $this->getTransactionId()
            ), 
            $params
        );

        // format params for curl
        foreach ($params as $key => $value) {
            $callParams .= '&' . $key . '=' . urlencode($value);
        }

        try {
            // do curl post
            $this->write(
                Zend_Http_Client::POST, 
                $this->getCashTicketUrl() . $functionName . 'Servlet', 
                '1.1', 
                array(), 
                $callParams
            );

            // read response and convert it to XML
            $response = $this->read();
            $response = new Varien_Simplexml_Element($response);
        }
        catch (Exception $e) {
            Mage::throwException(Mage::helper('cashticket')->__('CURL connection failed. Reason: ') . $e->getMessage());
        }

        return $response;
    }
    
    /**
     * Writing a method to curl api
     *
     * @param string $method  Method
     * @param string $url     Url
     * @param string $httpVer httpVer
     * @param array  $headers Headers
     * @param string $body    Body                    
     *
     * @return string
     */
     public function write($method, $url, $httpVer = '1.1', $headers = array(), $body = '')
     {
         //unused vars, but cannot remove from parameters
         $method = '';
         $httpVer = '';
         $headers = array();
         
         // get keyring password
         $sslpass = $this->getConfigValue('sslcert_pass');
    
         // can not process if no keyring password is set
         if (empty($sslpass)) {
             $message = 'SSL Certificate Password is empty. Please set the passwort in the configuration settings.';
             Mage::throwException(Mage::helper('cashticket')->__($message));
         }
         // if testing
         if ($this->isSandbox()) {
             // set PEM certificate of the test server
             $pemCert = Mage::getBaseDir() . $this->getConfigValue('path_pem_test');
         } else {
             // set PEM certificate of the live server
             $pemCert = Mage::getBaseDir() . $this->getConfigValue('path_pem_live');
         }
         // if PEM certificate not found
         if (!file_exists($pemCert)) {
            Mage::throwException(Mage::helper('cashticket')->__('File with PEM Certificate does not exist'));
         }
         // if server certificate not found
         if (!file_exists(Mage::getBaseDir() . $this->getConfigValue('path_cert'))) {
             Mage::throwException(Mage::helper('cashticket')->__('File with Server Certificate does not exist'));
         }

         // set curl connection options
         curl_setopt($this->_getResource(), CURLOPT_URL, $url);
         curl_setopt($this->_getResource(), CURLOPT_POST, true);
         curl_setopt($this->_getResource(), CURLOPT_POSTFIELDS, $body);
         curl_setopt($this->_getResource(), CURLOPT_FOLLOWLOCATION, false);
         curl_setopt($this->_getResource(), CURLOPT_RETURNTRANSFER, true);
         curl_setopt($this->_getResource(), CURLOPT_TIMEOUT, 30);
         curl_setopt($this->_getResource(), CURLOPT_SSLCERT, $pemCert);
         curl_setopt($this->_getResource(), CURLOPT_SSLCERTTYPE, 'PEM');
         curl_setopt($this->_getResource(), CURLOPT_SSLCERTPASSWD, $sslpass);
         curl_setopt($this->_getResource(), CURLOPT_SSL_VERIFYPEER, 1);
         curl_setopt($this->_getResource(), CURLOPT_SSL_VERIFYHOST, 2);
         curl_setopt($this->_getResource(), CURLOPT_CAINFO, Mage::getBaseDir() . $this->getConfigValue('path_cert'));
     
         return $body;
     }
    
    /**
     * Exec curl function and
     * get the response
     *
     * @return resource
     */
    public function read()
    {
        $response = curl_exec($this->_getResource());
        
        return $response;
    }
    
    /**
     * Get Cash-Ticket API URL 
     *
     * @return string
     */
    public function getCashTicketUrl()
    {
        // if testing server
        if ($this->isSandbox()) {
            // return URL for the testing server
            return $this->getDefaultConfigValue('url_test');
        }
        
        // otherwise return live URL
        return $this->getDefaultConfigValue('url_live');
    }
    
    /**
     * Get URL to redirect the customer
     * after finishing the order
     *
     * @param array $params Params
     *
     * @return string
     */
    public function getCustomerRedirectUrl(array $params)
    {
        // if testing
        if ($this->isSandbox()) {
            // get test url
            $url = $this->getDefaultConfigValue('customer_url_test'); 
        } else {
            // get live url
            $url = $this->getDefaultConfigValue('customer_url_live');
        }
        
        $url .= 'GetCustomerPanelServlet?';
        $urlParams = '';
        
        // merge set and standard params
        $params = array_merge(
            array(
                'currency' => $this->getConfigValue('currency_code'),
                'mid' => $this->getConfigValue('merchant_id'),
                'locale' => $this->getConfigValue('locale'),
                'mtid' => $this->getTransactionId()
            ), 
            $params
        );
        
        // format params for curl call
        foreach ($params as $key => $value) {
            $urlParams .= '&' . $key . '=' . urlencode($value);
        }
        
        return $url . $urlParams;
    }

    /**
     * Set transactionId
     *
     * @param int $transId Id
     *
     * @return void
     */
    public function setTransactionId($transId)
    {
        $this->_transactionId = $transId;
    }

    /**
     * Get transactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->_transactionId;
    }

    /**
     * Set order
     *
     * @param object $order Order object
     *
     * @return void
     */
    public function setOrder($order) 
    {
        $this->_order = $order;
    }
    
    /**
     * Get order
     *
     * @return object
     */
    public function getOrder() 
    {
        return $this->_order;
    }
    
    /**
     * Check if woring on
     * the test server
     *
     * @return boolean
     */
    public function isSandbox()
    {
        // check if testing flag has been set
        if (is_null($this->_sandbox)) {
            // check if testing flag has been set in current 
            // configuration and set the flag for this api instance
            if ($this->getConfigValue('sandbox')) {
                $this->_sandbox = true;
            } else {
                $this->_sandbox = false;
            }
        }
        if ($this->_sandbox == true) {
            return true;
        }
        
        return false;
    }

    /**
     * Get value from active configuration
     * for current currency
     *
     * @param string $key Key
     *
     * @return string
     */
    public function getConfigValue($key)
    {
        $config = $this->getItemConfig();
        
        try {
            // if not set load from db
            if (is_null($config)) {
                // get model
                $model = Mage::getModel('cashticket/item');
                // load configuration for current currency
                $collection = $model->getItemsCollection($this->getCurrency());
                // if no active configurations found ..
                if ($collection->getSize() <= 0) {
                    // throw error
                    Mage::throwException(Mage::helper('cashticket')->__('No active configuration for this currency.'));
                }
                // if more then 1 active configuration found ..
                if ($collection->getSize() > 1) {
                    // throw error
                    $errorMessage = 'Multiply active configuration for this currency.';
                    Mage::throwException(Mage::helper('cashticket')->__($errorMessage));
                }
                // set the config with loaded data
                $this->_config = $model->getConfigItem($this->getCurrency());
                $value = $this->_config[$key];
             // if already set - load from model
            } elseif (is_array($config)) {
                $value = $config[$key];
            } else {
                $value = null;
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return;
        }
        
        return $value;
    }
    
    /**
     * Get configuration from XML
     *
     * @param string $key key
     *
     * @return string
     */
    public function getDefaultConfigValue($key)
    {
        return Mage::getStoreConfig('payment/cashticket/' . $key);
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    public function getCurrency()
    {
        $order = $this->getOrder();
        // get currency info from current order
        $currency = $order->getData('order_currency_code');
        // if no currency set for the order - get currency info from store
        if (empty($currency)) {
            $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
        }

        return $currency;
    }
    
    /**
     * Get config array
     *
     * @return array
     */
    public function getItemConfig()
    {
        return $this->_config;
    }
    
    /**
     * Format price for Cash-Ticket (format 0.00)
     *
     * @param string $amount Amount
     *
     * @return string
     */
    public function formatPrice($amount)
    {
        $formatted = number_format($amount, 2, '.', '');
        $formatted = sprintf('%.2f', $formatted);
        return $formatted;
    }
}