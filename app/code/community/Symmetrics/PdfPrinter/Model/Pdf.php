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
 * PDF model
 *
 * @category  Symmetrics
 * @package   Symmetrics_PdfPrinter
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_PdfPrinter_Model_Pdf extends Mage_Core_Model_Abstract
{
    /**
     * @var Symmetrics_PdfPrinter_Helper_Data $_helper Helper object
     */
    protected $_helper;

    /**
     * @var Mage_Cms_Model_Page $_cmsPage CMS page object
     */
    protected $_cmsPage;
    /**
     * @var string $_cmsType page|block
     */
    protected $_cmsType = 'page';

    /**
     * @const string FILE_EXTENSION extension for PDF files
     */
    const FILE_EXTENSION = '.pdf';

    /**
     * Parse html content for pdf generation
     *
     * @return binary content
     */
    public function parseContents()
    {
        $content = $this->_cmsPage->getContent();
        
        $processor = Mage::getModel('cms/template_filter');
        $content = $processor->filter($content);
        
        $html = Mage::getSingleton('core/layout')
            ->createBlock('pdfprinter/pdf')
            ->setPdfContent($content)
            ->toHtml();

        $pdfContent = $this->htmlToPdf($html);

        return $pdfContent;
    }

    /**
     * Get cms page by identifier
     *
     * @param string $identifier CMS page identifier
     *
     * @return bool
     */
    public function loadPage($identifier)
    {
        $pageModel = Mage::getModel('cms/page');
        $pageId = $pageModel->checkIdentifier($identifier, $this->getHelper()->getStoreId());
        if (!is_null($pageId) && $pageId != false) {
            $this->_cmsPage = $pageModel->load($pageId);
            $this->_cmsType = 'page';
            return true;
        }
        
        return false;
    }

    /**
     * Get CMS page object
     *
     * @return Mage_Cms_Model_Page object
     */
    public function getPage()
    {
        return $this->_cmsPage;
    }

    /**
     * Check if cms page is already cached
     *
     * @return bool|string  cache filename on success
     */
    public function checkCache()
    {
        $fileName = $this->buildFileName();
        if (file_exists($fileName)) {
            return $fileName;
        }

        return false;
    }

    /**
     * Generate pdf from html
     *
     * @param string $html content to convert
     *
     * @return binary (PDF content)
     */
    public function htmlToPdf($html)
    {
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Write pdf content into file if no cache were found
     *
     * @param binary $pdf PDF content
     *
     * @return void
     */
    public function cachePdf($pdf)
    {
        if (!$this->checkCache()) {
            $fileName = $this->buildFileName();
            file_put_contents($fileName, $pdf);
        }
    }

    /**
     * Generate file name
     *
     * @return string file name
     */
    protected function buildFileName()
    {
        if (!isset($this->_cmsPage)) {
            throw new Exception('CMS page not loaded.');
        }
        $updated = $this->getHelper()->convertToUts($this->_cmsPage->getUpdateTime());
        $pageName = $this->_cmsPage->getIdentifier();
        $cacheDir = $this->getHelper()->getCacheDir();
        $fileName = $cacheDir . $this->_cmsType . '_' . $pageName . '_' . $updated . self::FILE_EXTENSION;

        return $fileName;
    }

    /**
     * Return helper object
     *
     * @return Symmetrics_PdfPrinter_Helper_Data helper
     */
    public function getHelper()
    {
        if (!isset($this->_helper)) {
            $this->_helper = Mage::helper('pdfprinter');
        }

        return $this->_helper;
    }
}