<?php
/**
 * @copyright (c) 2013, Pawel Kazakow <support@xonu.de>
 * @license http://xonu.de/license/ xonu.de EULA
 */


class Xonu_RoundingErrorFix_Model_Sales_Order_Payment extends Mage_Sales_Model_Order_Payment {

    protected function _formatAmount($amount, $asFloat = false)
    {
        // $amount = Mage::app()->getStore()->roundPrice($amount);
        $amount = round($amount,2);
        return !$asFloat ? (string)$amount : $amount;
    }

}