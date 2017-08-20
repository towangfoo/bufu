<?php
/**
 * @copyright (c) 2013, Pawel Kazakow <support@xonu.de>
 * @license http://xonu.de/license/ xonu.de EULA
 */

class Xonu_RoundingErrorFix_Model_Api_Standard extends Mage_Paypal_Model_Api_Standard {
    /**
     * Filter amounts in API calls
     * @param float|string $value
     * @return string
     */
    protected function _filterAmount($value)
    {
        // return sprintf('%.2F', $value); // original line would round e. g. 30.605 to 30.60
        return sprintf('%.2F', round($value, 2)); // the modified line would round 30.605 to 30.61
    }


    public function getStandardCheckoutRequest()
    {
        // calculate the rounded request total
        $request = parent::getStandardCheckoutRequest();
        $requestBaseGrandTotal = round($request['amount'] + $request['tax'] - $request['discount_amount'], 2);

        // get the rounded order total
        $orderObj = Mage::getSingleton('sales/order')
                    ->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
        $orderBaseGrandTotal = round($orderObj->getBaseGrandTotal(), 2);

        // get the rounded rounding error
        $roundingError = round($orderBaseGrandTotal - $requestBaseGrandTotal, 2); // -0.01 or +0.01

        // fix the rounding error
        if($roundingError) {
            $order = array(); // create an array from order data resembling the structure or the request array
            $roundingDeltas = array(); // save the rounding error

            $order['amount'] = round($orderObj->getBaseSubtotal() + $orderObj->getBaseShippingAmount(), 2);
            $roundingDeltas['amount'] = ($orderObj->getBaseSubtotal() + $orderObj->getBaseShippingAmount()) - $order['amount'];

            $order['tax'] = round($orderObj->getBaseTaxAmount(), 2);
            $roundingDeltas['tax'] = $orderObj->getBaseTaxAmount() - $order['tax'];

            $order['shipping'] = round($orderObj->getBaseShippingAmount(), 2);
            $roundingDeltas['shipping'] = $orderObj->getBaseShippingAmount() - $order['shipping'];

            // not contained in the request but useful to determine if there is a rounding error in shipping
            $order['shipping_incl_tax'] = round($orderObj->getBaseShippingAmount() + $orderObj->getBaseShippingTaxAmount(), 2);
            $roundingDeltas['shipping_incl_tax'] = ($orderObj->getBaseShippingAmount() + $orderObj->getBaseShippingTaxAmount()) - $order['shipping_incl_tax'];

            $orderTotalItemCount = $orderObj->getTotalItemCount();

            // hide rounding error in shipping
            if($roundingDeltas['shipping_incl_tax'] && $order['shipping'] > 0) {
                if(isset($request['amount_'.($orderTotalItemCount+1)])) { // ensure that the shipping item is there
                    $request['amount_'.($orderTotalItemCount+1)] += $roundingError;
                }
                $request['shipping'] += $roundingError;
                $request['amount'] += $roundingError;

            // hide rounding error in the last cart item
            } elseif($roundingDeltas['amount'] && $order['amount'] > 0) {
                if(isset($request['amount_'.($orderTotalItemCount+1)])) {
                    $request['amount_'.($orderTotalItemCount+1)] += $roundingError;
                }
                $request['amount'] += $roundingError;
            } else {
                // hide rounding error in tax
                if($order['tax'] > 0) {
                    $request['tax'] += $roundingError;
                    $request['tax_cart'] += $roundingError;
                } else {
                    // do not correct rounding error in this unexpected situation
                }
            }

        }
        return $request;
    }
}