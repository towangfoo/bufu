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
 * Symmetrics_CashTicket_Adminhtml_CashticketController
 *
 * @category  Symmetrics
 * @package   Symmetrics_CashTicket
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
*/
class Symmetrics_CashTicket_Adminhtml_CashticketController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Default action called by pressing
     * the "send" button in order info
     * performed by AJAX
     *
     * @return object
     */
    public function defaultAction()
    {
        $postParams = $this->getRequest()->getParams();
        // get action variable from post
        $action = $postParams['cashticketAction'];
        // get order by id
        $order = $this->getOrder($postParams['orderId']);
        // get api and set the transaction id
        $api = $this->getApi($postParams['transactionId']);
        // get entered amount
        $amount = $api->formatPrice($postParams['amount']);

        if ($this->_checkAmount($amount, $action)) {
            return $this;
        }
        
        // switch between two functions (debit and modify) selected from order info box
        if ($action == 'debit') {
            // get disposition state from Cash-Ticket
            $response = $api->call('GetDispositionState', array());
            // if allow to debit
            // @codingStandardsIgnoreStart
            if (is_object($response) && $response->errCode == '0' && $response->Amount > 0 && $response->TransactionState == 'D') {
            // @codingStandardsIgnoreEnd
                    $params = array(
                        'amount' => $amount,
                        'close' => '0',
                    );
                    // call Cash-Ticket debit function with entered amount                     
                    $debitResponse = $api->call('Debit', $params);
                    if ($debitResponse->errCode == '0') {
                        // if everything was ok - save the new status and add info to order history
                        $statusMessage = 'Amount %s %s was successfully captured from customers Cash-Tickets.';
                        $status = Mage::helper('cashticket')->__($statusMessage, $amount, $api->getCurrency());
                        $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $status);
                        $order->save();
                        $successMessage = 'Amount of %s %s was successfully debited.';
                        $success = Mage::helper('cashticket')->__($successMessage, $amount, $api->getCurrency());
                        Mage::getSingleton('adminhtml/session')->addSuccess($success);
                        return $this;
                    } else {
                        $errorMessage = preg_replace('/&/', '', $debitResponse->errMessage);
                        $errorMessage = preg_replace('/;/', '', $errorMessage);
                        Mage::getSingleton('adminhtml/session')->addError($errorMessage);
                        return $this;
                    }
            } else {
                // if can not process debit
                $errorString = 'Error processing the Cash-Ticket request.';
                $error = Mage::helper('cashticket')->__($errorString);
                Mage::getSingleton('adminhtml/session')->addError($error);
                return $this;
            }
        } elseif ($action == 'modify') {
            // call Cash-Ticket modify function and modify the transaction amount
            $response = $api->call(
                'ModifyDisposition', 
                array(
                    'amount' => $amount
                )
            );
            
            $this->_checkStatus($order, $response, $amount, $api);
            
            return $this;
        }

        return $this;
    }
    
    /**
     * Check amount
     * 
     * @param string $amount Amount
     * @param string $action Action
     *
     * @return boolean
     */
     private function _checkAmount($amount, $action) 
     {
         if ($amount <= 0 && $action == 'modify') {
             // return error message
             $message = 'Debit amount must be greater then zero.';
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cashticket')->__($message));

             return true;
         }
         
         return false;
     }
    
    /**
     * Check status
     * 
     * @param object $order    Order object
     * @param object $response Response object
     * @param string $amount   Amount
     * @param object $api      Api object
     *
     * @return string
     */
     private function _checkStatus($order, $response, $amount, $api) 
     {
         if (is_object($response) && $response->errCode == '0') {
             // if everything was ok - save the new status and add info to order history 
             $statusString = sprintf('Disposition modified. New value: %s %s', $amount, $api->getCurrency());
             $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $statusString);
             $order->save();
             $successMessage = 'Disposition was successfully modified (%s %s).';
             $success = Mage::helper('cashticket')->__($successMessage, $amount, $api->getCurrency());
             Mage::getSingleton('adminhtml/session')->addSuccess($success);
         } else {
             $errorMessage = Mage::helper('cashticket')->__('Error processing the Cash-Ticket request.');
             Mage::getSingleton('adminhtml/session')->addError($errorMessage);
         }
     }

    /**
     * Get order object and load 
     * order by id
     *
     * @param int $orderId Order id
     *
     * @return object
     */    
    public function getOrder($orderId = null)
    {
        $order = Mage::getModel('sales/order');
        $order->load($orderId);
        
        return $order;
    }

    /**
     * Get API object and set 
     * the transaction
     *
     * @param int $transactionId Transaction Id
     *
     * @return object
     */
    public function getApi($transactionId = null)
    {
        $postParams = $this->getRequest()->getParams();
        $cashTicketModel = Mage::getModel('cashticket/cashticket');
        $api = $cashTicketModel->getApi($transactionId);
        $api->setTransactionId($transactionId);
        $api->setOrder($this->getOrder($postParams['orderId']));

        return $api;
    }
}