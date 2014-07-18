<?php
/**
 * Symmetrics_InvoicePdf_Model_Source_LogoPosition
 *
 * @category Symmetrics
 * @package Symmetrics_InvoicePdf
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_InvoicePdf_Model_Source_LogoPosition
{
    public function toOptionArray()
    {
        return array(
            'left' => Mage::helper('invoicepdf')->__('Left'),
            'center' => Mage::helper('invoicepdf')->__('Center'),
            'right' => Mage::helper('invoicepdf')->__('Right')
        );
    }
}
