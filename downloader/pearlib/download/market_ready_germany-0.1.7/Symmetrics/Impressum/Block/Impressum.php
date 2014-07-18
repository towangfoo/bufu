<?php
/**
 * Symmetrics_Impressum_Block_Impressum
 *
 * @category Symmetrics
 * @package Symmetrics_Impressum
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_Impressum_Block_Impressum extends Mage_Core_Block_Abstract 
{
	protected $_data;

	public function __construct()
	{
		parent::_construct();
		
		$storeId = $this->getStoreId();
		
		$this->setData(Mage::getStoreConfig('general/impressum', $storeId));
	}

    /**
     * Getting StoreId to get proper store related
     * information in order comments
     *
     * @return int
     */
	protected function getStoreId()
	{
	    $orderId = $this->getRequest()->getParam('order_id', 0);
	    
	    if($orderId > 0) {
	        return Mage::getSingleton('sales/order')->load($orderId)->getStoreId();
	    }
	    else {
	        return null;
	    }
	}
	
	protected function _toHtml() 
	{
		$html = $this->getValueFromDB($this->getValue());
		return $html;
	}
	
	public function getValueFromDB($param, $path='')
	{
		if (!empty($path)) {
			$this->_data = Mage::getStoreConfig($path);
		}

		switch($param) {
			case 'emailfooter':
				$value = $this->getEmailFooter();
			break;
			case 'address':
				$value = $this->getAddressBlock();				
			break;
			case 'communication':
				$value = $this->getCommunicationBlock();
			break;
			case 'legal':
				$value = $this->getLegalBlock();
			break;
			case 'tax':
				$value = $this->getTaxBlock();
			break;
			case 'bank':
				$value = $this->getBankBlock();
			break;
			case 'web_href':
				$value = '<a href="http://'.$this->getValueFromDB('web').'" title="'.$this->getValueFromDB('company1').'">'.$this->getValueFromDB('web').'</a>';
			break;
			case 'email_href':
				$value = '<a href="mailto:'.$this->getValueFromDB('email').'" title="'.$this->getValueFromDB('company1').'">'.$this->getValueFromDB('email').'</a>';
			break;
			default:
				$value = $this->_data[$param];
		}
		
		return $value;
	}

	public function getEmailFooter()
	{
		$out = '';
		
		$out .= '<p><strong>'.$this->getValueFromDB('shopname').'</strong></p>';
		$out .= '<p>';
		$out .= $this->getValueFromDB('company1').'<br />';
		if ($this->getValueFromDB('company2') != '') {
			$out .= $this->getValueFromDB('company2').'<br />';
		}
		$out .= $this->getValueFromDB('street').'<br />';
		$out .= $this->getValueFromDB('zip').' '.$this->getValueFromDB('city').'<br />';
		$out .= '</p>';
		
		$out .= '<p>';
		$out .= $this->getCommunicationBlock();
		$out .= '</p>';
		
		return $out;
	}

	public function getAddressBlock()
	{
		$out = '';
		$out .= $this->getValueFromDB('company1').'<br />';
		$out .= $this->getValueFromDB('company2').'<br />';
		$out .= $this->getValueFromDB('street').'<br />';
		$out .= $this->getValueFromDB('zip').' '.$this->getValueFromDB('city');
		return $out;
	}

	public function getCommunicationBlock()
	{
		$out = '';
		$out .= $this->__('Telephone:').' '.$this->getValueFromDB('telephone').'<br />';
		$out .= $this->__('Fax:').' '.$this->getValueFromDB('fax').'<br />';
		$out .= $this->__('Web:').' '.$this->getValueFromDB('web_href').'<br />';
		$out .= $this->__('E-Mail:').' '.$this->getValueFromDB('email_href');
		return $out;
	}

	public function getLegalBlock()
	{
		$out = '';
		$out .= $this->__('CEO:').' '.$this->getValueFromDB('ceo').'<br />';
		$out .= $this->__('Register court:').' '.$this->getValueFromDB('court').'<br />';
		if ($this->getValueFromDB('hrb')) {
			$out .= $this->__('Register number:').' '.$this->getValueFromDB('hrb').'<br />';
		}
		if ($this->getValueFromDB('rechtlicheregelungen')) {
			$out .= '<p>'.$this->getValueFromDB('rechtlicheregelungen').'</p>';
		}
		return $out;
	}
	
	public function getTaxBlock()
	{
		$out = '';
		$out .= $this->__('Financial office:').' '.$this->getValueFromDB('taxoffice').'<br />';
		if ($this->getValueFromDB('taxnumber')) {
			$out .= $this->__('Tax number:').' '.$this->getValueFromDB('taxnumber').'<br />';
		}
		if ($this->getValueFromDB('vatid')) {
			$out .= $this->__('VAT-ID:').' '.$this->getValueFromDB('vatid').'<br />';
		}
		return $out;
	}
	
	public function getBankBlock()
	{
		$out = '';
		$out .= $this->__('Account owner:').' '.$this->getValueFromDB('bankaccountowner').'<br />';
		$out .= $this->__('Account:').' '.$this->getValueFromDB('bankaccount').'<br />';
		$out .= $this->__('Bank number:').' '.$this->getValueFromDB('bankcodenumber').'<br />';
		$out .= $this->__('Bank name:').' '.$this->getValueFromDB('bankname').'<br />';
		if ($this->getValueFromDB('swift')) {
			$out .= $this->__('SWIFT:').' '.$this->getValueFromDB('swift').'<br />';
		}
		if ($this->getValueFromDB('iban')) {
			$out .= $this->__('IBAN:').' '.$this->getValueFromDB('iban').'<br />';
		}
		return $out;
	}
	
	public function getImpressumData()
	{
		return $this->_data;
	}
}
