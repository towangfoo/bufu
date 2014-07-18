<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Mage_BankPayment
 * @copyright  Copyright (c) 2008 Andrej Sinicyn
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_BankPayment_Model_BankPayment extends Mage_Payment_Model_Method_Abstract
{

    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'bankpayment';

    protected $_formBlockType = 'bankPayment/form';
    protected $_infoBlockType = 'bankPayment/info';

    public function getAccountHolder()
    {
        return $this->getConfigData('bankaccountholder');
    }

    public function getAccountNumber()
    {
        return $this->getConfigData('bankaccountnumber');
    }

    public function getSortCode()
    {
        return $this->getConfigData('sortcode');
    }

    public function getBankName()
    {
        return $this->getConfigData('bankname');
    }

    public function getIBAN()
    {
        return $this->getConfigData('bankiban');
    }

    public function getBIC()
    {
        return $this->getConfigData('bankbic');
    }

    public function getPayWithinXDays()
    {
        return intval($this->getConfigData('paywithinxdays'));
    }

    public function getCustomText()
    {
        return nl2br($this->getConfigData('customtext'));
    }

}
