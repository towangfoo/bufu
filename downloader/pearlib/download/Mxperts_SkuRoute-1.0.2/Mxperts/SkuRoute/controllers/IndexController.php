<?php
/**
 * @category Mxperts
 * @package Mxperts_SkuRoute
 * @authors TMEDIA cross communications <info@tmedia.de>, Johannes Teitge <teitge@tmedia.de>, Igor Jankovic <jankovic@tmedia.de>, Daniel Sasse <d.sasse1984@googlemail.com>
 * @copyright TMEDIA cross communications, Doris Teitge-Seifert
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * 
 *
 * Initial-Release V 1.0.0 - 8-8-2009 
 *    
 * Changes V 1.0.1 - 8-8-2009
 *   - add fix, if magento run sin subdirectory
 *   
 * Changes V 1.0.2 - 8-8-2009
 *   - add 301 to Head     
 *  
 */
require_once "Mage/Cms/controllers/IndexController.php";
  
class Mxperts_SkuRoute_IndexController extends Mage_Cms_IndexController 
{
     public function noRouteAction($coreRoute = null)  
     {  
       $sku = (strpos($_SERVER["REQUEST_URI"],"/") == 0) ? substr($_SERVER["REQUEST_URI"],1) : $_SERVER["REQUEST_URI"];
       if ($pos = strrpos($sku,"/")) { $sku = substr($sku,$pos+1); }         
       $product = Mage::getModel('catalog/product'); 
       if ($product_id = $product->getIdBySku($sku)) {
         $product->load($product_id);
         Header("Location: ".$product->getProductUrl(), true, 301);
         exit();          
       } else {        
        parent::noRouteAction($coreRoute);
       }        
     } 
} 