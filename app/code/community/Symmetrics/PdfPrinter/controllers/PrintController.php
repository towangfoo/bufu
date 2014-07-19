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
 * Print controller
 *
 * @category  Symmetrics
 * @package   Symmetrics_PdfPrinter
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_PdfPrinter_PrintController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Symmetrics_PdfPrinter_Helper_Data $_helper Helper object
     */
    protected $_helper;

    /**
     * Constructor
     * set autoloader for DomPDF
     *
     * @param Zend_Controller_Request_Abstract  $request    Request object
     * @param Zend_Controller_Response_Abstract $response   Response Object
     * @param array                             $invokeArgs Arguments to pass-through
     *
     * @return void
     */
    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array()
    )
    {
        if (!defined("DOMPDF_FONT_CACHE")) {
            $domPdfFontCacheDir = Mage::helper('dompdf')->getFontCacheDir();
            define("DOMPDF_FONT_CACHE", $domPdfFontCacheDir);
        }
        require_once 'Symmetrics/dompdf/dompdf_config.inc.php';
        spl_autoload_unregister(array(Varien_Autoload::instance(), 'autoload'));
        spl_autoload_register('DOMPDF_autoload');
        Varien_Autoload::register();

        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     * Index action of print controller
     *
     * @return Symmetrics_PdfPrinter_PrintController controller
     */
    public function indexAction()
    {
        if ($pageIdentifier = $this->getHelper()->getRequest()->getParam('identifier')) {
            $pdfModel = Mage::getModel('pdfprinter/pdf');
            if (!$pdfModel->loadPage($pageIdentifier)) {
            $this->_forward('no-route');
            } else {
                $pdfCache = $pdfModel->checkCache();
                if ($pdfCache === false) {
                    $pdfContent = $pdfModel->parseContents($this->getLayout());
                    $pdfModel->cachePdf($pdfContent);
                }
                $pdfCache = $pdfModel->checkCache();
                if ($pdfCache === false) {
                    throw new Exception('PDF File could not be cached');
                }
                if (!isset($pdfContent)) {
                    $pdfContent = file_get_contents($pdfCache);
                }
                $this->_prepareDownloadResponse($pageIdentifier . '.pdf', $pdfContent, 'application/pdf');
            }
        } else {
            $this->_forward('no-route');
        }

        return $this;
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

    /**
     * Declare headers and content file in responce for file download
     *
     * @param string $fileName      name of the file which the user can download
     * @param string $content       set to null to avoid starting output,
     *                              $contentLength should be set explicitly in
     *                              that case
     * @param string $contentType   content type, in this case application/pdf
     * @param int    $contentLength explicit content length, if strlen($content)
     *                              isn't applicable
     *
     * @return Symmetrics_PdfProductSheet_ProductController controller
     */
    protected function _prepareDownloadResponse(
        $fileName,
        $content,
        $contentType = 'application/octet-stream',
        $contentLength = null
    )
    {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate,post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
            ->setHeader('Last-Modified', date('r'));
        if (!is_null($content)) {
            $this->getResponse()->setBody($content);
        }

        return $this;
    }
}