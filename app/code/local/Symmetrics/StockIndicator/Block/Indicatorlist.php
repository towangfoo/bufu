<?php
/**
 * @category Symmetrics
 * @package Symmetrics_StockIndicator
 * @author symmetrics gmbh <info@symmetrics.de>, Andreas Timm <at@symmetrics.de>, Eric Reiche <er@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software 
 */
/*
 * This class extends the Block class for product listing and calls the static class to get the Trafficlight color
 */
class Symmetrics_StockIndicator_Block_Indicatorlist extends Mage_Catalog_Block_Product_List
{
	
	protected $productId;

    public function getAvailabilityClass()
    {
    	if(!isset($this->productId)) {
    		return Symmetrics_StockIndicator_Block_StaticIndicator::getColor($this->getProduct());
        }
        else {
    		return Symmetrics_StockIndicator_Block_StaticIndicator::getColor($this->productId);
        }
    }
    
    function setProductIdAvail($productId)
    {
    	$this->productId = $productId;
    }
}