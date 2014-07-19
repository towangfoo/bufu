<?php
/**
 * Symmetrics_InvoicePdf_Model_Pdf_Creditmemo
 *
 * @category Symmetrics
 * @package Symmetrics_InvoicePdf
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_InvoicePdf_Model_Pdf_Creditmemo extends Symmetrics_InvoicePdf_Model_Pdf_Invoice
{
	public function __construct()
	{
		parent::__construct();
		$this->setMode('creditmemo');
	}
}