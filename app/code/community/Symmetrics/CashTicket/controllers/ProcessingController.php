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
 * Symmetrics_CashTicket_ProcessingController
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_ProcessingController extends Mage_Core_Controller_Front_Action
{
    /**
     * Standard expire ajax function
     *
     * @return void
     */
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            return null;
        }
    }

    /**
     * Get the checkout object
     *
     * @return object
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get the API object and
     * set transaction
     *
     * @param int $transactionId Transaction Id
     *
     * @return object
     */
    public function getApi($transactionId = null)
    {
        $cashTicketModel = Mage::getModel('cashticket/cashticket');
        $api = $cashTicketModel->getApi($transactionId);
        $api->setTransactionId($transactionId);

        return $api;
    }
    
    /**
     * Get the order object and
     * load by id
     *
     * @param int $orderId Order Id
     *
     * @return object
     */
    public function getOrder($orderId = null)
    {
        $order = Mage::getModel('sales/order'); 
        $order->loadByIncrementId($orderId);
        
        return $order;
    }

    /**
     * Action called when customer finishes
     * the checkout process
     *
     * @return object
     */
    public function redirectAction()
    {
        $checkout = $this->getCheckout();
        $order = $this->getOrder($checkout->getLastRealOrderId());
        $payment = $order->getPayment();
        $api = $this->getApi($payment->getAdditionalData());

        // get order total price and format it for the Cash-Ticket API
        $params = array(
            'amount' => $api->formatPrice(Mage::app()->getStore()->roundPrice($order->getGrandTotal()))
        );
        
        // set new order status and save info for order history
        $statusMessage = 'Customer was redirected to Cash-Ticket. Transaction ID: %s';
        $status = Mage::helper('cashticket')->__($statusMessage, $payment->getAdditionalData());
        $order->addStatusToHistory(
            $order->getStatus(), 
            $status
        );

        $order->save();

        // unset the quoteId
        $checkout->unsQuoteId();
            
        // redirect customer to the Cash-Ticket page
        $this->getResponse()->setRedirect($api->getCustomerRedirectUrl($params));
        
        return $this;
    }

    /**
     * Action called when customer
     * returns from the Cash-Ticket page
     *
     * @return object
     */
    public function successAction()
    {
        $checkout = $this->getCheckout();
        $order = $this->getOrder($checkout->getLastRealOrderId());
        $payment = $order->getPayment();
        $api = $this->getApi($payment->getAdditionalData());
        
        // get disposition state
        $response = $api->call('GetSerialNumbers', array());
        
        if (is_object($response) && $response->errCode == '0') {
            // s = cash tickets successfully assigned to disposition
            // @codingStandardsIgnoreStart
            if ($response->TransactionState == 'S') {
            // @codingStandardsIgnoreEnd
                $amount = Mage::app()->getStore()->roundPrice($order->getGrandTotal());
                // if intangible
                if ($api->getConfigValue('business_type') == 'I') {
                    // further debits not possible
                    // close transaction and debit the full amount
                    $cashTicketAmount = $amount;
                    $closeFlag = '1';
                // if tangible
                } else {
                    // further debits are possible
                    // enable manual debiting
                    $cashTicketAmount = '0.00';
                    $closeFlag = '0';
                }
                
                $params = array(
                    'amount' => $api->formatPrice($cashTicketAmount),
                    'close' => $closeFlag,
                );
                
                // call the debit function on Cash-Ticket
                $response = $api->call('Debit', $params);
                
                if (is_object($response) && $response->errCode == '0') {
                    if ($api->getConfigValue('business_type') == 'I') {
                        $message = 'Transaction successful. %s %s debited';
                        $currency = $api->getCurrency();
                        $statusMessage = Mage::helper('cashticket')->__($message, $cashTicketAmount, $currency);
                    } else {
                        $message = 'Cash-Ticket cards were successfully assigned to disposition. ';
                        $message .= 'The amount of %s %s can be debited.';
                        $statusMessage = Mage::helper('cashticket')->__($message, $amount, $api->getCurrency());
                    }
                    
                    // set the new order status and save comment
                    $newOrderStatus = $api->getDefaultConfigValue(
                        'order_status',
                        $order->getStoreId()
                    );
                    
                    if (empty($newOrderStatus)) {
                        $newOrderStatus = Mage_Sales_Model_Order::STATE_PROCESSING;
                    }
                    
                    $order->addStatusToHistory($newOrderStatus, $statusMessage);
                    $order->save();
                    
                    $checkout->getQuote()->setIsActive(false)->save();

                    // redirect to success page
                    $this->_redirect(
                        'checkout/onepage/success', 
                        array(
                            '_secure'=>true
                        )
                    );
                    
                    return $this;
                } else {
                    // debit was not successful
                    Mage::throwException($response->errMessage);
                    return $this;
                }
            } else {
                // do not process payment because something went wrong on the Cash-Ticket page
                $errorMessage = 'Error processing payment. It could have following reasons: ';
                $errorMessage .= 'no cards assigned to disposition, disposition already debited, ';
                $errorMessage .= 'disposition canceled by customer or expired by Cash-Ticket.';
                Mage::throwException(Mage::helper('cashticket')->__($errorMessage));
                return $this;
            }
        } else {
            Mage::throwException($response->errMessage);
            return $this;
        }
    }
    
    /**
     * Action called when customer hits 
     * cancel button on the Cash-Ticket
     * page
     *
     * @return string
     */
    public function errorAction()
    {
        // show error block
        $this->getResponse()->setBody(
            $this->getLayout()
            ->createBlock('cashticket/error')
            ->toHtml()
        );
        
        return $this;
    }
}