<?php
/**
 * @category Symmetrics
 * @package Symmetrics_StockIndicator
 * @author symmetrics gmbh <info@symmetrics.de>, Andreas Timm <at@symmetrics.de>, Eric Reiche <er@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software 
 */

/*
 * This class returns the traffic light color depending on the availability
 */
class Symmetrics_StockIndicator_Block_StaticIndicator
{
	public static function getColor($productId)
    {
        if (Mage::getStoreConfig('cataloginventory/stock_indicator/indicator_show') == 1) {
            if(!is_numeric($productId)) {
            	$product = $productId;
            }
            else {
            	$product = Mage::getModel('catalog/product')->load($productId);
            }
            $qty = $product->getData('stock_item')->getData('qty');
            $config = Mage::getStoreConfig('cataloginventory/stock_indicator');
            $color = 'red';
            $keys = array('red', 'yellow', 'green');
            foreach ($keys as $key) {
                if ($qty >= $config[$key]) {
                    $color = $key;
                }
            }
        }
        else {
            $color = false;
        }
        return $color;
    }
}