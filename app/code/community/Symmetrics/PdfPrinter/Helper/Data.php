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
 * @package   Symmetrics_PdfPrinter
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Default helper class
 *
 * @category  Symmetrics
 * @package   Symmetrics_PdfPrinter
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_PdfPrinter_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @const string PDFPRINTER_CACHE_DIR cache directory under media dir
     */
    const PDFPRINTER_CACHE_DIR = 'pdfprinter';

    /**
     * Get currently selected store
     *
     * @return Mage_Core_Model_Store store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Get id of current store
     *
     * @return int id 
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Get request object
     *
     * @return mixed request
     */
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Get pdf cache directory
     *
     * @return string directory
     */
    public function getCacheDir()
    {
        return Mage::getBaseDir('media') . DS . self::PDFPRINTER_CACHE_DIR . DS;
    }

    /**
     * Convert a datetime string to unix timestamp
     *
     * @param string $timeString datetime in internal format
     *
     * @return int UTS time
     */
    public function convertToUts($timeString)
    {
        $zendDate = new Zend_Date($timeString, Varien_Date::DATETIME_INTERNAL_FORMAT);
        $timeUts = $zendDate->toString(Zend_Date::TIMESTAMP);

        return $timeUts;
    }

    /**
     * Get cache directory for pdf fonts
     *
     * @return string cache directory
     */
    public function getFontCacheDir()
    {
        $domPdfFontCacheDir = join(DS, array('lib', 'Symmetrics', 'dompdf', 'fonts'));
        $domPdfFontCacheDir = Mage::getBaseDir('var') . DS . $domPdfFontCacheDir;

        return $domPdfFontCacheDir;
    }
}