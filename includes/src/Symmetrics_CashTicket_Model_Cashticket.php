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
 * Symmetrics_CashTicket_Model_CashTicket
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_Model_CashTicket extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Internal payment code.
     *
     * @var string
     */
    protected $_code = 'cashticket';

    /**
     * Flag for if gateway is used.
     *
     * @var boolean
     */
    protected $_isGateway = true;
    
    /**
     * Flag for if use authorization.
     *
     * @var boolean
     */
    protected $_canAuthorize = true;
    
    /**
     * Flag for if use capturing.
     *
     * @var boolean
     */
    protected $_canCapture = true;
    
    /**
     * Flag for if capture partial amounts.
     *
     * @var boolean
     */
    protected $_canCapturePartial = false;
    
    /**
     * Flag for if using refund.
     *
     * @var boolean
     */
    protected $_canRefund = false;
    
    /**
     * Flag for if can void.
     *
     * @var boolean
     */
    protected $_canVoid = false;
    
    /**
     * Flag for if using for internal.
     *
     * @var boolean
     */
    protected $_canUseInternal = false;
    
    /**
     * Flag for if using on checkout.
     *
     * @var boolean
     */
    protected $_canUseCheckout = true;
    
    /**
     * Flag for if saving creditcards .
     *
     * @var boolean
     */
    protected $_canSaveCc = false;
    
    /**
     * Flag for if validating validating.
     *
     * @var boolean
     */
    protected $_canValidate = true;
    
    /**
     * Define block for payment form.
     *
     * @var string
     */
    protected $_formBlockType = 'cashticket/form';

    /**
     * Define block for payment info.
     *
     * @var string
     */
    protected $_infoBlockType = 'cashticket/info';
    
    /**
     * Id of the transaction for current API call.
     *
     * @var string
     */
    protected $_transactionId = '';

    /**
     * Construct, init resource model.
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('cashticket/item');
    }
    
    /**
     * Get API object by transaction id
     *
     * @param int $transactionId Transaction Id
     *
     * @return object
     */
    public function getApi($transactionId)
    {
        $api = Mage::getModel('cashticket/api');
        $api->setTransactionId($transactionId);
        $api->setOrder($this->getQuote());

        return $api;
    }
    
    /**
     * Get checkout from session.
     *
     * @return object
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * Get session for Cash-Ticket.
     *
     * @return object
     */
    public function getSession()
    {
        return Mage::getSingleton('cashticket/session');
    }
    
    /**
     * Get quote object from checkout session.
     *
     * @return object
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Check if payment method is available.
     *
     * @param object $quote Quote
     *
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        // if quote is not set - something went wrong
        if (is_null($quote)) {
           return false;
        }
        
        if (!Mage::getStoreConfigFlag('payment/cashticket/active')) {
            return false;
        }

        // if cart value > 1000 - cancel
        // cash-ticket does not support orders 
        // with value > 1000 at this time
        if (Mage::app()->getStore()->roundPrice($quote->getGrandTotal()) <= 1000) {
            return true;
        }

        return false;
    }

    /**
     * Create disposition on Cash-Ticket.
     *
     * @param Varien_Object $payment Payment Object
     * @param int           $amount  Amount
     *
     * @return object
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        // generate random transaction number (number from 100 to 999 + orderid)
        $transactionId = rand(100, 999) . $payment->getOrder()->increment_id;
        $api = $this->getApi($transactionId);

        // override the amount to get the really order amount
        // needed for currency operations
        $amount = $api->formatPrice($this->getQuote()->getGrandTotal());
            
        $params = array(
            'amount' => $amount
        );

        // save transactionid in payment data
        $payment->setAdditionalData($transactionId);
        
        // get response from api
        $response = $api->call(
            'CreateDisposition', 
            $params
        );

        // if response could be parsed and no error happened
        if (is_object($response) && $response->errCode == '0') {
            return $this;
        } else {
            $message = 'There is an error occurred. Probably it`s a temporary error and ';
            $message .= 'the payment can be finished by a site reload. If this problem exist further, ';
            $message .= 'please contact our support.';
            Mage::throwException(Mage::helper('cashticket')->__($message));
        }
        
        return $this;
    }

    /**
     * Get URL which is called when customer finished
     * the checkout process.
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('cashticket/processing/redirect');
    }
}
