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
 * rendering class to draw additional info
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Model_Pdf_Items_Invoice_Additional
    extends Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
{
    /**
     * method to draw the additional info block to the invoice
     *
     * @return void
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $height = $this->getHeight();

        $helper = Mage::helper('invoicepdf');
        $font = $helper->getFont();
        $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
        /* @var $tableRowItem Symmetrics_InvoicePdf_Model_Pdf_Items_Item */

        if ($helper->getSalesPdfInvoiceConfigKey('displaymaturity', $order->getStore())) {
            $maturitySetting = $helper->getSalesPdfInvoiceConfigKey('maturity', $order->getStore());
            if (!$maturity = $helper->getSalesPdfInvoiceConfigKey('altmaturitytext', $order->getStore())) {
                if ($maturitySetting != 0) {
                    $maturity = $helper->__('Invoice maturity: %s days', $maturitySetting);
                } else {
                    $maturity = $helper->__('Invoice maturity: immediatly');
                }
            }
        }

        $paddingLegt = 10;
        $maxWidth = 380;

        if (!empty($maturity)) {
            $tableRowItem->addColumn('maturity', $maturity, $paddingLegt, 'left', $maxWidth, $font, 10);
        }
        
        $this->addRow($tableRowItem);
        $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
        
        if ($helper->getSalesPdfInvoiceConfigKey('displayinvoicedate', $order->getStore())) {
            $notice = $helper->__('Invoice date is equal to delivery date');
            $tableRowItem->addColumn('notice', $notice, $paddingLegt, 'left', $maxWidth, $font, 10);
        }

        $this->addRow($tableRowItem);

        $note = $maturitySetting = $helper->getSalesPdfInvoiceConfigKey('note', $order->getStore());
        if (!empty($note)) {
            $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
            $tableRowItem->addColumn('note', $note, $paddingLegt, 'left', $maxWidth, $font, 10);
            $this->addRow($tableRowItem);
        }

        $page = $pdf->insertTableRow($page, $this);
        $this->setHeight($height);
        $this->setPage($page);
        $this->clearRows();
    }
}