<?php
/**
 * Symmetrics_InvoicePdf_Model_Pdf_Items_Default
 *
 * @category Symmetrics
 * @package Symmetrics_InvoicePdf
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_InvoicePdf_Model_Pdf_Items_Default extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    public function draw($position = 1)
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = array();
        
        $fontSize = 9;

        // draw Position Number
        $lines[0]= array(array(
            'text'  => $position,
            'feed'  => $pdf->margin['left'] + 20,
            'align' => 'right',
            'font_size' => $fontSize
        ));
        
        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 10),
            'feed'  => $pdf->margin['left'] + 45,
            'font_size' => $fontSize
        );
        
        // draw Product name
        $lines[0][]= array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 40, true, true),
            'feed' => $pdf->margin['left'] + 110,
            'font_size' => $fontSize
        );

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty() * 1,
            'feed'  => $pdf->margin['right'] - 110,
            'align' => 'right',
            'font_size' => $fontSize
        );

        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, false, true),
                    'font' => 'bold',
                    'feed' => $pdf->margin['left'] + 110
                );

                // draw options value
                if ($option['value']) {
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => Mage::helper('core/string')->str_split($value, 60, true, true),
                            'feed' => $pdf->margin['left'] + 120
                        );
                    }
                }
            }
        }
        
        // draw Price
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getPrice()),
            'feed'  => $pdf->margin['right'] - 160,
            'align' => 'right',
            'font_size' => $fontSize
        );

        // draw Tax
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => $pdf->margin['right'] - 65,
            'align' => 'right',
            'font_size' => $fontSize
        );

        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => $pdf->margin['right'] - 10,
            'align' => 'right',
            'font_size' => $fontSize
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 15
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}
