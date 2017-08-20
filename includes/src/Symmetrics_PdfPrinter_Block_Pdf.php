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
class Symmetrics_PdfPrinter_Block_Pdf extends Mage_Core_Block_Template
{
    /**
     * @var string $_pdfcontent CMS content for PDF
     */
    protected $_pdfcontent;

    /**
     * Set template path
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('pdfprinter/body.phtml');

        parent::_construct();
    }

    /**
     * Get previously set template content for template
     *
     * @return binary content
     */
    public function getPdfContent()
    {
        return $this->_pdfcontent;
    }

    /**
     * Set PDF content so it can be gotten by the template
     *
     * @param binary $pdfContent binary PDF content
     *
     * @return Symmetrics_PdfPrinter_Block_Pdf object
     */
    public function setPdfContent($pdfContent)
    {
        $this->_pdfcontent = $pdfContent;

        return $this;
    }
}