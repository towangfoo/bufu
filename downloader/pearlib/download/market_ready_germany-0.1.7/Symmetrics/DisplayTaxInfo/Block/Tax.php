<?php
/**
 * Symmetrics_DisplayTaxInfo_Block_Tax
 *
 * @category Symmetrics
 * @package Symmetrics_DisplayTaxInfo
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>, Sergej Braznikov <sb@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_DisplayTaxInfo_Block_Tax extends Mage_Core_Block_Abstract
{
    public static function getTaxInfo($product)
    {
    	if($product->getCanShowPrice() !== false) {
	        $tax = Mage::helper('tax');
	
	        $productTypeId = $product->getTypeId();
	        
	        if ($productTypeId != 'combined') { // use not for Symmetrics_CombinedProduct 
	            if ($tax->displayPriceIncludingTax()) {
	                $taxInfo = sprintf(Mage::helper('displaytaxinfo')->__('Incl. %1$s%% tax'), $product->getTaxPercent());
	            }
	            else {
	                $taxInfo = sprintf(Mage::helper('displaytaxinfo')->__('Excl. %1$s%% tax'), $product->getTaxPercent());
	            }
	
	            $shippingLink = sprintf(
	                Mage::helper('core')->__('Excl. <a href="%1$s">shipping</a>'),
	                Mage::getUrl('') . Mage::getStoreConfig('tax/display/shippingurl')
	            );
	
	            if ($productTypeId != 'virtual' && $productTypeId != 'downloadable') {
	                return '<span class="tax-details">' . $taxInfo . ', ' . $shippingLink . '</span>';
	            }
	            else {
	                return '<span class="tax-details">' . $taxInfo . '</span>';
	            }
	        }
    	}
    }
}
