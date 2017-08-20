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
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

include('Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php');

/**
 * Overwriting sales order invoice controller in adminhtml
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Adminhtml_Sales_Order_InvoiceController
    extends Mage_Adminhtml_Sales_Order_InvoiceController
{
    /**
     * Action to print invoice as PDF
     *
     * @return void
     */
    public function printAction()
    {
        if (($invoiceId = $this->getRequest()->getParam('invoice_id'))) {
            if (($invoice = Mage::getModel('sales/order_invoice')->load($invoiceId))) {
                if ($invoice->getStoreId()) {
                    Mage::app()->setCurrentStore($invoice->getStoreId());
                }
                
                $pdf = Mage::getModel('invoicepdf/pdf_invoice')->getPdf(array($invoice));

                $this->_prepareDownloadResponse(
                    'invoice' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf', 
                    $pdf->render(), 
                    'application/pdf'
                );
            }
        } else {
            $this->_forward('noRoute');
        }
    }
}
