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
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Bundle helper class
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Helper_Bundle
    extends Mage_Core_Helper_Abstract
{
    /**
    * Retrieve is Shipment Separately flag for Item
    *
    * @param Mage_Sales_Order_Item $orderItem given order Item
    * @param Varien_Object         $item      item to check
    *
    * @return bool
    */
    public function isShipmentSeparately($orderItem, $item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }
            
            return $this->_isShipmentItemSeparately($item);
        }

        $options = $orderItem->getProductOptions();
        if ($options) {
            if (isset($options['shipment_type'])
                && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Retrieve is Shipment Separately flag for parent Item
     *
     * @param Varien_Object $item item to check
     *
     * @return bool
     */
    protected function _isShipmentItemSeparately($item)
    {
        $parentItem = $item->getParentItem();
        if ($parentItem) {
            $options = $parentItem->getProductOptions();
            if ($options) {
                if (isset($options['shipment_type'])
                    && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            $options = $item->getProductOptions();
            if ($options) {
                if (isset($options['shipment_type'])
                    && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
    
    /**
     * Retrieve is Child Calculated
     *
     * @param Mage_Sales_Order_Item $orderItem given order Item
     * @param Varien_Object         $item      item to check
     *
     * @return bool
    */
    public function isChildCalculated($orderItem, $item = null)
    {
       if ($item) {
           if ($item->getOrderItem()) {
               $item = $item->getOrderItem();
           }
           return $this->_isChildItemCalculated($item);
       }

       $options = $orderItem->getProductOptions();
       if ($options) {
           if (isset($options['product_calculations'])
               && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
               return true;
           }
       }
       return false;
    }

    /**
    * Retrieve is parent Child Calculated
    *
    * @param Varien_Object $item item to check
    *
    * @return bool
    */
    protected function _isChildItemCalculated($item = null)
    {
       $parentItem = $item->getParentItem();
       if ($parentItem) {
           $options = $parentItem->getProductOptions();
           if ($options) {
               if (isset($options['product_calculations']) &&
                   $options['product_calculations'] ==
                   Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
                   return true;
               } else {
                   return false;
               }
           }
       } else {
           $options = $item->getProductOptions();
           if ($options) {
               if (isset($options['product_calculations']) &&
                   $options['product_calculations'] ==
                   Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD) {
                   return false;
               } else {
                   return true;
               }
           }
       }
    }
}
