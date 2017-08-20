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
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Default helper class
 * 
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    Symmetrics GmbH <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Helper_Data
    extends Mage_Core_Helper_Abstract
{
    /**
     * const to store Sales Pdf Invoice Config xPath
     */
    const SALES_PDF_INVOICE = 'sales_pdf/invoice';

    /**
     * const to store Sales Pdf Invoice Config xPath
     */
    const MAX_SKU_LENGTH = 14;
    
    /**
     * Get the config from sales pdf invoice setings
     *
     * @param integer $store store to get from
     *
     * @return array
     */
    public function getSalesPdfInvoiceConfig($store)
    {
        return Mage::getStoreConfig(self::SALES_PDF_INVOICE, $store);
    }

    /**
     * Get the config from sales pdf invoice setings for given key
     *
     * @param string  $key   key to get
     * @param integer $store store to get from
     *
     * @return mixed
     */
    public function getSalesPdfInvoiceConfigKey($key, $store)
    {
        return Mage::getStoreConfig(self::SALES_PDF_INVOICE . '/' . $key, $store);
    }

    /**
     * Get the config from sales pdf invoice setings flag for given key
     *
     * @param string  $key   key to get
     * @param integer $store store to get from
     *
     * @return mixed
     */
    public function getSalesPdfInvoiceConfigFlag($key, $store)
    {
        return Mage::getStoreConfigFlag(self::SALES_PDF_INVOICE . '/' .  $key, $store);
    }
    
    /**
     * Return uploaded or default font.
     * 
     * @param string $type  Type (normal / bold / italic)
     * @param string $store Store
     *
     * @return string
     */
    public function getFont($type = 'normal', $store = '')
    {
        if (Mage::getStoreConfigFlag(self::SALES_PDF_INVOICE . '/use_font_' . $type, $store)) {
            $fontFile = Mage::getStoreConfig(self::SALES_PDF_INVOICE . '/font_' . $type, $store);
            
            if ($this->_checkFileExist($this->_getFontFileDir() . $fontFile)) {
                return Zend_Pdf_Font::fontWithPath($this->_getFontFileDir() . $fontFile);
            } 
        } 
        switch($type) {
            case 'bold':
                $font = Zend_Pdf_Font::FONT_HELVETICA_BOLD;
                break;
            case 'italic':
                $font = Zend_Pdf_Font::FONT_HELVETICA_ITALIC;
                break;
            default:
                $font = Zend_Pdf_Font::FONT_HELVETICA;
                break;
        }
        return Zend_Pdf_Font::fontWithName($font);
    }
    
    /**
     * Returns fonts directory.
     *
     * @return string
     */
    protected function _getFontFileDir() 
    {
        return Mage::getBaseDir('media') . '/fonts/';
    }
    
    /**
     * Check if file exist.
     * 
     * @param string $file Path and filename.
     *
     * @return boolean
     */
    protected function _checkFileExist($file) 
    {
        $ioFile = new Varien_Io_File();
        
        if (!$ioFile->fileExists($file)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Split sku to array if length > maxlength.
     * 
     * @param string $sku SKU.
     *
     * @return string|array
     */
    public function getSplitSku($sku) 
    {
        if (strlen($sku) > self::MAX_SKU_LENGTH) {
            return str_split($sku, self::MAX_SKU_LENGTH);
        }
        
        return $sku;
    }
}