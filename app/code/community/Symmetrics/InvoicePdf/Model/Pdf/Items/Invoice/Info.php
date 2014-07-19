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
 * rendering class to draw the info text block to the invoice
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Model_Pdf_Items_Invoice_Info
    extends Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract
{
    /**
     * method to draw the info text block to the invoice
     *
     * @return void
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $helper = Mage::helper('invoicepdf');
        $font = $helper->getFont();
        $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
        /* @var $tableRowItem Symmetrics_InvoicePdf_Model_Pdf_Items_Item */
        
        $infoText = $helper->getSalesPdfInvoiceConfigKey('infotxt', $order->getStore());
        if (!empty($infoText)) {
            $tableRowItem = Mage::getModel('invoicepdf/pdf_items_item');
            $infoText = explode("\n", $infoText);
            $tableRowItem->addColumn('note', $infoText, 0, 'left', 0, $font, 10);
            $this->addRow($tableRowItem);
        }

        $page = $pdf->insertTableRow($page, $this);
        $this->setPage($page);
        $this->clearRows();
    }
}