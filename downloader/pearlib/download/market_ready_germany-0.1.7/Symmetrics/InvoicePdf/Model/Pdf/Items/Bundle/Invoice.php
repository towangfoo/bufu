<?php
/**
 * Symmetrics_InvoicePdf_Model_Pdf_Items_Bundle_Invoice
 *
 * @category Symmetrics
 * @package Symmetrics_InvoicePdf
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_InvoicePdf_Model_Pdf_Items_Bundle_Invoice extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Invoice
{
    public function draw($position = 1)
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $this->_setFontRegular();
        $items = $this->getChilds($item);
        
        $fontSize = 9;
        
        $_prevOptionId = '';
        $drawItems = array();

        foreach ($items as $_item) {
            $line   = array();

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId   = $attributes['option_id'];
            }
            else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = array(
                    'lines'  => array(),
                    'height' => 15
                );
            }
            
            if ($_item->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[] = array(
                        'font'  => 'bold',
                        'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 70, true, true),
                        'feed'  => $pdf->margin['left'] + 110,
                        'font_size' => $fontSize
                    );

                    $drawItems[$optionId] = array(
                        'lines'  => array($line),
                        'height' => 15
                    );

                    $line = array();

                    $_prevOptionId = $attributes['option_id'];
                }
            }

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($_item->getOrderItem()->getParentItem()) {
                $feed = $pdf->margin['left'] + 130;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = $pdf->margin['left'] + 130;
                $name = $_item->getName();
            }
            $line[] = array(
                'text'  => Mage::helper('core/string')->str_split($name, 35, true, true),
                'feed'  => $pdf->margin['left'] + 110,
                'font_size' => $fontSize
            );

            if (!$_item->getOrderItem()->getParentItem()) {

                // draw SKUs
                $line[] = array(
                    'text'  => Mage::helper('core/string')->str_split($item->getSku(), 10),
                    'feed'  => $pdf->margin['left'] + 45,
                    'font_size' => $fontSize
                );
                
                // draw Position Number
                $line[]= array(
                    'text'  => $position,
                    'feed'  => $pdf->margin['left'] + 20,
                    'align' => 'right',
                    'font_size' => $fontSize
                );
            }

            // draw prices
            if ($this->canShowPriceInfo($_item)) {
                $price = $order->formatPriceTxt($_item->getPrice());
                $line[] = array(
                    'text'  => $price,
                    'feed'  => $pdf->margin['right'] - 160,
                    'align' => 'right',
                    'font_size' => $fontSize
                );
                $line[] = array(
                    'text'  => $_item->getQty()*1,
                    'feed'  => $pdf->margin['right'] - 110,
                    'align' => 'right',
                    'font_size' => $fontSize
                );

                $tax = $order->formatPriceTxt($_item->getTaxAmount());
                $line[] = array(
                    'text'  => $tax,
                    'feed'  => $pdf->margin['right'] - 65,
                    'align' => 'right',
                    'font_size' => $fontSize
                );

                $row_total = $order->formatPriceTxt($_item->getRowTotal());
                $line[] = array(
                    'text'  => $row_total,
                    'feed'  => $pdf->margin['right'] - 10,
                    'align' => 'right',
                    'font_size' => $fontSize
                );
            }

            $drawItems[$optionId]['lines'][] = $line;
        }

        // custom options
        $options = $item->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $lines = array();
                    $lines[][] = array(
                        'text'  => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                        'font'  => 'italic',
                        'feed'  => 35
                    );

                    if ($option['value']) {
                        $text = array();
                        $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 50, true, true) as $_value) {
                                $text[] = $_value;
                            }
                        }

                        $lines[][] = array(
                            'text'  => $text,
                            'feed'  => 40
                        );
                    }

                    $drawItems[] = array(
                        'lines'  => $lines,
                        'height' => 15
                    );
                }
            }
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, array('table_header' => false));

        $this->setPage($page);
    }
}
