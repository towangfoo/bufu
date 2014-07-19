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
 * rendering class to draw the info box block to the invoice
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Model_Pdf_Items_Invoice_Block
    extends Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
{
    /**
     * method to draw the info box block to the invoice
     *
     * @return void
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $helper = Mage::helper('invoicepdf');
        $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
        /* @var $tableRowItem Symmetrics_InvoicePdf_Model_Pdf_Items_Item */

        $font = Mage::helper('invoicepdf')->getFont('bold');
        $fontSize = 10;

        $infoTextHeadLine = $helper->getSalesPdfInvoiceConfigKey('infoboxhl', $order->getStore());
        if (!empty($infoTextHeadLine)) {
            $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
            $infoTextHeadLine = explode("\n", $infoTextHeadLine);
            $tableRowItem->addColumn('note', $infoTextHeadLine, 10, 'left', 0, $font, $fontSize + 2);
            $this->addRow($tableRowItem);
        }

        $font = Mage::helper('invoicepdf')->getFont();
        $infoText = $helper->getSalesPdfInvoiceConfigKey('infobox', $order->getStore());
        if (!empty($infoText)) {
            $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
            $infoText = explode("\n", $infoText);
            $tableRowItem->addColumn('note', $infoText, 10, 'left', 0, $font, $fontSize);
            $this->addRow($tableRowItem);
        }

        $page = $pdf->insertTableRow($page, $this, false, true);
        $this->setPage($page);
        $this->clearRows();
    }
}