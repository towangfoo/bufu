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
 * @package    Mage_Debit
 * @copyright  Copyright (c) 2009 ITABS GbR - Rouven Alexander Rieker
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Debit_Model_Debit extends Mage_Payment_Model_Method_Abstract
{

    /**
    * unique internal payment method identifier
    * 
    * @var string [a-z0-9_]
    */
    protected $_code = 'debit';

    protected $_formBlockType = 'debit/form';
    protected $_infoBlockType = 'debit/info';

	
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setCcType($this->encrypt($data->getCcType()))		// BLZ
             ->setCcOwner($data->getCcOwner())						// Kontoinhaber
             ->setCcNumber($this->encrypt($data->getCcNumber()));	// Kontonummer
        return $this;
    }

    /**
     * Prepare info instance for save
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        $info->setCcNumberEnc($this->encrypt($info->getCcNumber()));
        return $this;
    }		
	
    public function getCODTitle()
    {
        return $this->getConfigData('title');
    }

    public function getCustomText()
    {
        return $this->getConfigData('customtext');
    }
    
    
    public function getEmailSettings()
    {
    	if($this->getConfigData('sendmail')) // send bank data via mail
    	{
    		if($this->getConfigData('sendmail_crypt')) // encrypt bank data
    		{
    			$return  = "<br />Kontoinhaber: ".$this->getAccountName();
    			$return .= "<br />Kontonummer: ".$this->emailEncrypt($this->getAccountNumber());
    			$return .= "<br />Bankleitzahl: ".$this->emailEncrypt($this->getAccountBLZ());
    		}
    		else // do not encrypt bank data
    		{
    			$return  = "<br />Kontoinhaber: ".$this->getAccountName();
    			$return .= "<br />Kontonummer: ".$this->getAccountNumber();
    			$return .= "<br />Bankleitzahl: ".$this->getAccountBLZ();
    			$return .= "<br />Kreditinstitut: ".$this->getAccountBankname();
    		}
    		return $return;
		}
		return false;
    }
    
       
    
	
	public function getAccountName()
	{
		$info = $this->getInfoInstance();
		return $info->getCcOwner();
	}
	
	public function getAccountNumber()
	{
		$info = $this->getInfoInstance();
		$return = $info->getCcNumberEnc();
		
		if(strlen(intval($return)) == 1) $return = $this->decrypt($this->decrypt($return)); // decrypt twice!
				
		return $return;
	}
	
	public function getAccountBLZ()
	{
		$info = $this->getInfoInstance();
		$return = $info->getCcType();
				
		if(strlen(intval($return)) == 1) $return = $this->decrypt($return); // decrypt
		
		return $return;
	}
	
	public function getAccountBankname()
	{
		$info = $this->getInfoInstance();
		$blz  = $this->getAccountBLZ();
		$name = '';
		$file = $this->getFilePath();		
		
		// Open file
	    $fp = fopen($file, 'r');
	    
		while ($data = fgetcsv($fp, 1024, ";")) {
			if ($data[0] == $blz) {
				$name = $data[1];
			}
	    }
		
		if($name == '') return 'existiert nicht';
		else return $name;
	}
	
	    /**
     * Get the path of the file "bankleitzahlen.csv"
     *
     * @return  string
     */
	private function getFilePath()
	{
		$f = dirname(__FILE__);			// Get the path of this file
		$f = substr($f, 0, -5);			// Remove the "Model" dir
		$f = $f.'etc/';					// Add the "etc" dir
		$f = $f.'bankleitzahlen.csv';	// Add the filename
		$f = str_replace("\\","/",$f);	// change slashes
		
		//echo $f; exit;
		
		return $f;
	}
	
	
	 /**
     * Encrypt data for mail
     *
     * @param   string $data
     * @return  string
     */
	protected function emailEncrypt($data)
	{
		$l     = strlen($data);		// string length
		$l3    = substr($data,-3);	// last 3 values
		$rest  = $l - 3;			
		$crypt = '';
		for($i=1; $i<=$rest; $i++)
		{
			$crypt .= '*';
		}
		$crypt .= $l3;				// add plain text values to crypted value
		
		return $crypt;		
	}
	
	
	
	 /**
     * Encrypt data
     *
     * @param   string $data
     * @return  string
     */
    public function encrypt($data)
    {
        if ($data) {
            return Mage::helper('core')->encrypt($data);
        }
        return $data;
    }
	
    /**
     * Decrypt data
     *
     * @param   string $data
     * @return  string
     */
    public function decrypt($data)
    {
        if ($data) {
            return Mage::helper('core')->decrypt($data);
        }
        return $data;
    }
}
