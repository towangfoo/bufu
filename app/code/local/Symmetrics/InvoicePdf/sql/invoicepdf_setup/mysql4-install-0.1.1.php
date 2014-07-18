<?php
/**
 * Symmetrics_InvoicePdf
 *
 * @category Symmetrics
 * @package Symmetrics_InvoicePdf
 * @author symmetrics gmbh <info@symmetrics.de>, Andreas Timm <at@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;
$installer->startSetup();

$this->_conn->addColumn($this->getTable('sales_flat_quote'), 'invoicepdf_add_totals', 'text');

$installer->endSetup();