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
 * rendering class to draw bundle products to the invoice
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010-2012 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Model_Pdf_Items_Bundle_Invoice
    extends Symmetrics_InvoicePdf_Model_Pdf_Items_Bundle_Abstract
{
    /**
     * method to draw bundle products to the invoice
     *
     * @return void
     */
    public function draw()
    {
        $order = $this->getOrder();
        $item  = $this->getItem();
        $pdf   = $this->getPdf();
        $page  = $this->getPage();
        $items = $this->getChilds($item);

        $taxHelper = Mage::helper('tax');
        /* @var $taxHelper Mage_Tax_Helper_Data */
        $checkoutHelper = Mage::helper('checkout');
        /* @var $checkoutHelper Mage_Checkout_Helper_Data */

        $_prevOptionId = '';
        $font = Mage::helper('invoicepdf')->getFont();

        foreach ($items as $_item) {
            $attributes = $this->getSelectionAttributes($_item);
            $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');

            // draw SKUs
            if (!$_item->getOrderItem()->getParentItem()) {
                $sku = $this->getSku($_item);
                $sku = Mage::helper('invoicepdf')->getSplitSku($sku);
                $tableRowItem->addColumn("sku", $sku, 45, 'left', 50, $font);
            }

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($_item->getOrderItem()->getParentItem()) {
                $feed = 30;
                $maxWidth = 335;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = 110;
                $maxWidth = 260;
                $name = $_item->getName();
            }
            $tableRowItem->addColumn("name", $name, $feed, 'left', $maxWidth, $font);

            // draw prices
            if ($this->canShowPriceInfo($_item)) {
                if ($taxHelper->displaySalesPriceInclTax()) {
                    $price = $checkoutHelper->getPriceInclTax($_item);
                    $rowTotal = $checkoutHelper->getSubtotalInclTax($_item);
                } elseif ($taxHelper->displaySalesPriceExclTax()) {
                    $price = $_item->getPrice();
                    $rowTotal = $_item->getRowTotal();
                } else {
                    throw new Mage_Core_Exception('invalid Tax Settings');
                }
                $price = $order->formatPriceTxt($price);
                $tableRowItem->addColumn("price", $price, 160, 'right', 0, $font);

                $qty = $_item->getQty()*1;
                $tableRowItem->addColumn("qty", $qty, 110, 'right', 0, $font);

                $tax= $order->formatPriceTxt($_item->getTaxAmount());
                $tableRowItem->addColumn("tax", $tax, 60, 'right', 0, $font);

                $rowTotal = $order->formatPriceTxt($rowTotal);
                $tableRowItem->addColumn("rowTotal", $rowTotal, 10, 'right', 0, $font);
            }

            // draw Option Labels
            if ($_item->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $tableRowOptionItem = Mage::getModel('invoicepdf/pdf_items_item');
                    /* @var $tableRowOptionItem Symmetrics_InvoicePdf_Model_Pdf_Items_Item */

                    $labelFont = Mage::helper('invoicepdf')->getFont('bold');
                    $tableRowOptionItem->addColumn(
                        'option_label',
                        $attributes['option_label'],
                        20,
                        'left',
                        0,
                        $labelFont,
                        7
                    );

                    $this->addRow($tableRowOptionItem);

                    $_prevOptionId = $attributes['option_id'];
                }
            }
            $this->addRow($tableRowItem);

        }
        // Draw custom options.
        $this->_drawCustomOption($_item);

        $this->setTriggerPosNumber(true);

        $page = $pdf->insertTableRow($page, $this);
        $this->setPage($page);
        $this->clearRows();
    }

    /**
     * Draw a custom option for an order parent item.
     *
     * @param Mage_Sales_Model_Order_Invoice_Item $item Order item to draw the custom options for.
     *
     * @return null|void
     */
    protected function _drawCustomOption($item)
    {
        if ($item->getOrderItem()->getParentItem()) {
            $options = $this->getItemOptions();
            if ($options) {
                foreach ($options as $option) {
                    $tableRowOptionItem = Mage::getModel('invoicepdf/pdf_items_item');
                    /* @var $tableRowOptionItem Symmetrics_InvoicePdf_Model_Pdf_Items_Item */
                    // Draw options label.
                    $labelFont = Mage::helper('invoicepdf')->getFont('bold');
                    $tableRowOptionItem->addColumn('option_label', $option['label'], 20, 'left', 0, $labelFont, 7);

                    $this->addRow($tableRowOptionItem);

                    if ($option['value']) {
                        $tableRowOptionItem = Mage::getModel('invoicepdf/pdf_items_item');
                        $_printValue = isset($option['print_value'])
                            ? $option['print_value'] : strip_tags($option['value']);

                        $valueFont = Mage::helper('invoicepdf')->getFont();
                        $tableRowOptionItem->addColumn('option_value', $_printValue, 30, 'left', 0, $valueFont, 6);
                        $this->addRow($tableRowOptionItem);
                    }
                }
            }
        }
    }
}
