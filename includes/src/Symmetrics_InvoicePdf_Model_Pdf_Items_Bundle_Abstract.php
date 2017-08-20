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
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 Symmetrics Gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Abstract class to render Bundles 
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
abstract class Symmetrics_InvoicePdf_Model_Pdf_Items_Bundle_Abstract
    extends Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
{
    /**
     * Getting all available childs for Invoice, Shipmen or Creditmemo item
     *
     * @param Varien_Object $item item to get childs
     * 
     * @return array
     */
    public function getChilds($item)
    {
        $_itemsArray = array();

        if ($item instanceof Mage_Sales_Model_Order_Invoice_Item) {
            $_items = $item->getInvoice()->getAllItems();
        } else if ($item instanceof Mage_Sales_Model_Order_Shipment_Item) {
            $_items = $item->getShipment()->getAllItems();
        } else if ($item instanceof Mage_Sales_Model_Order_Creditmemo_Item) {
            $_items = $item->getCreditmemo()->getAllItems();
        }

        if ($_items) {
            foreach ($_items as $_item) {
                $parentItem = $_item->getOrderItem()->getParentItem();
                if ($parentItem) {
                    $_itemsArray[$parentItem->getId()][$_item->getOrderItemId()] = $_item;
                } else {
                    $_itemsArray[$_item->getOrderItem()->getId()][$_item->getOrderItemId()] = $_item;
                }
            }
        }

        if (isset($_itemsArray[$item->getOrderItem()->getId()])) {
            return $_itemsArray[$item->getOrderItem()->getId()];
        } else {
            return null;
        }
    }

    /**
     * Retrieve Selection attributes
     *
     * @param Varien_Object $item Item to get attributes
     * 
     * @return mixed
     */
    public function getSelectionAttributes($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }
        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }
        return null;
    }

    /**
     * Retrieve Order Item
     *
     * @return Mage_Sales_Order_Item
     */
    public function getOrderItem()
    {
        if ($this->getItem() instanceof Mage_Sales_Order_Item) {
            return $this->getItem();
        } else {
            return $this->getItem()->getOrderItem();
        }
    }

    /**
     * Retrieve Value HTML
     *
     * @param Mage_Sales_Order_Item $item item to get Html
     *
     * @return string
     */
    public function getValueHtml($item)
    {
        /* @var $helper Symmetrics_InvoicePdf_Helper_Bundle */
        $helper = Mage::helper('invoicepdf/bundle');
        $result = strip_tags($item->getName());
        
        if (!$helper->isShipmentSeparately($this->getOrderItem(), $item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }
        if (!$helper->isChildCalculated($this->getOrderItem(), $item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result .= " " . strip_tags($this->getOrderItem()->getOrder()->formatPrice($attributes['price']));
            }
        }
        return $result;
    }

    /**
     * Can show price info for item
     *
     * @param Mage_Sales_Order_Item $item item to check
     * 
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        /* @var $helper Symmetrics_InvoicePdf_Helper_Bundle */
        $helper = Mage::helper('invoicepdf/bundle');
        
        if (($item->getOrderItem()->getParentItem() && $helper->isChildCalculated($this->getOrderItem()))
                || (!$item->getOrderItem()->getParentItem() && !$helper->isChildCalculated($this->getOrderItem()))) {
            return true;
        }
        return false;
    }
}