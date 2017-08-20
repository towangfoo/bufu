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

/**
 * Abstract Pdf Rendering class
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 * @SuppressWarnings(PHPMD)
 */
abstract class Symmetrics_InvoicePdf_Model_Pdf_Abstract extends Varien_Object
{
    /**
     * Zend PDF object
     *
     * @var Zend_Pdf
     */
    protected $_pdf;

    /**
     * pointer for current height on the pdf page
     *
     * @var float
     */
    protected $_height;

    /**
     * pointer for current width on the pdf page
     *
     * @var float
     */
    protected $_width;

    /**
     * Counter for product positions
     *
     * @var integer
     */
    protected $_posCount;

    /**
     * Item renderers with render type key
     *
     * model    => the model name
     * renderer => the renderer model
     *
     * @var array
     */
    protected $_renderers = array();

    const PDF_INVOICE_PUT_ORDER_ID = 'put_order_id';
    const PDF_SHIPMENT_PUT_ORDER_ID = 'sales_pdf/shipment/put_order_id';
    const PDF_CREDITMEMO_PUT_ORDER_ID = 'sales_pdf/creditmemo/put_order_id';

    const PAGE_POSITION_LEFT = 60;
    const PAGE_POSITION_RIGHT = 555;
    const PAGE_POSITION_TOP = 790;
    const PAGE_POSITION_BOTTOM = 90;
    
    /**
     * @const FOOTER_SPACING Vertical padding between footer blocks.
     */
    const FOOTER_SPACING = 30;

    const MAX_LOGO_WIDTH = 500;
    const MAX_LOGO_HEIGHT = 50;

    /**
     * Abstract function to render the PDF
     *
     * @return Zend_Pdf
     */
    abstract public function getPdf();

    /**
     * Cunstructor to initialize the PDF object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setPdf(new Zend_Pdf());
        $this->_height = self::PAGE_POSITION_TOP;
        $this->_width = self::PAGE_POSITION_LEFT;

        $this->_posCount = 0;
    }

    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param string                 $string   string to calculate width for
     * @param Zend_Pdf_Resource_Font $font     Font to calculate height
     * @param float                  $fontSize Font size in points
     *
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ?
            iconv('UTF-8', 'UTF-16BE//IGNORE', $string) : @iconv('UTF-8', 'UTF-16BE', $string);

        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;

    }

    /**
     * Returns the total height in points of the font using the specified font and
     * size.
     *
     * @param Zend_Pdf_Resource_Font $font     Font to calculate height
     * @param float                  $fontSize Font size in points
     *
     * @return float
     */
    public function heightForFontUsingFontSize($font, $fontSize)
    {
        $height = $font->getLineHeight();
        $stringHeight = ($height / $font->getUnitsPerEm()) * $fontSize;

        return $stringHeight;
    }

    /**
     * Before getPdf processing
     *
     * @return void
     */
    protected function _beforeGetPdf()
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
    }

    /**
     * After getPdf processing
     *
     * @return void
     */
    protected function _afterGetPdf()
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(true);
    }

    /**
     * Set PDF object
     *
     * @param Zend_Pdf $pdf pdf top set
     *
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected function _setPdf(Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Retrieve PDF object
     *
     * @throws Mage_Core_Exception
     *
     * @return Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->_pdf instanceof Zend_Pdf) {
            Mage::throwException(Mage::helper('sales')->__('Please define PDF object before using'));
        }

        return $this->_pdf;
    }

    /**
     * make a new line with given font, size and spaceing
     *
     * @param Zend_Pdf_Font $font        font for new line
     * @param float         $fontSize    size for new line
     * @param boolean       $invert      invert the new line (if true it will be upwards)
     * @param float         $spacingSize spacing of the font
     *
     * @return void
     */
    protected function _newLine($font, $fontSize, $invert = false, $spacingSize = 1.2)
    {
        if ($invert) {
            $this->_height += $this->heightForFontUsingFontSize($font, $fontSize) * $spacingSize;
        } else {
            $this->_height -= $this->heightForFontUsingFontSize($font, $fontSize) * $spacingSize;
        }
    }

    /**
     * Set regular font
     *
     * @param Zend_Pdf_Page $object Page to set font for
     * @param integer       $size   Size to set
     *
     * @return Zend_Pdf_Font
     */
    protected function _setFontRegular($object, $size = 10)
    {
        $font = Mage::helper('invoicepdf')->getFont();
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * set bold font
     *
     * @param Zend_Pdf_Page $object Page to set font for
     * @param integer       $size   Size to set
     *
     * @return Zend_Pdf_Font
     */
    protected function _setFontBold($object, $size = 10)
    {
        $font = Mage::helper('invoicepdf')->getFont('bold');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * set italic font
     *
     * @param Zend_Pdf_Page $object Page to set font for
     * @param integer       $size   Size to set
     *
     * @return Zend_Pdf_Font
     */
    protected function _setFontItalic($object, $size = 10)
    {
        $font = Mage::helper('invoicepdf')->getFont('italic');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Init the given renderer
     *
     * @param string $type type of renderer to init
     *
     * @return void
     */
    protected function _initRenderer($type)
    {
        $node = Mage::getConfig()->getNode('global/invoicepdf/'.$type);
        foreach ($node->children() as $renderer) {
            $this->_renderers[$renderer->getName()] = array(
                'model'     => (string)$renderer,
                'renderer'  => null
            );
        }
    }

    /**
     * Retrieve renderer model
     *
     * @param string $type Type of renderer to get
     *
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        if (!isset($this->_renderers[$type])) {
            Mage::throwException(Mage::helper('sales')->__('Invalid renderer model'));
        }

        if (is_null($this->_renderers[$type]['renderer'])) {
            $this->_renderers[$type]['renderer'] = Mage::getSingleton($this->_renderers[$type]['model']);
        }

        return $this->_renderers[$type]['renderer'];
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param Varien_Object $settings settings to get
     * Allowed properties are
     *  $settings->setPageSize($pageSize);
     * '$pageSize' is a Size from Zend_Pdf_Page
     *  $settings->setDrawTableHeader(true);
     * to draw the table header
     *
     * @return Zend_Pdf_Page
     */
    public function newPage(Varien_Object $settings)
    {
        $helper = Mage::helper('invoicepdf');
        $pageSize = ($settings->hasPageSize()) ? $settings->getPageSize() : Zend_Pdf_Page::SIZE_A4;
        $page = $this->_getPdf()->newPage($pageSize);
        $this->insertAddressFooter($page, $settings->getStore());
        $pdf = $this->_getPdf();

        $this->_height = self::PAGE_POSITION_TOP;
        /* @var $pdf Zend_Pdf */
        $pdf->pages[] = $page;
        if (count($pdf->pages) > 1 && ($settings->hasDrawTableHeader()) ? $settings->getDrawTableHeader() : false) {
            $this->insertTableHeader($page);
        }

        // Draw fold marks
        if ($helper->getSalesPdfInvoiceConfigKey('displayfoldmarks', $settings->getStore())) {
            $foldMarkHeight = $page->getHeight() / 3;
            $page->drawLine(20, $foldMarkHeight, 25, $foldMarkHeight);
            $foldMarkHeight *= 2;
            $page->drawLine(20, $foldMarkHeight, 25, $foldMarkHeight);
            $page->drawLine(20, $page->getHeight() / 2, 25, $page->getHeight() / 2);
        }
        // draw page number
        $font = $this->_setFontRegular($page, 8);
        $pageText = Mage::helper('invoicepdf')->__('Page %d', count($pdf->pages));
        $pageTextSize = $this->widthForStringUsingFontSize($pageText, $font, 8);
        $pageTextHeight = $this->heightForFontUsingFontSize($font, 8);
        $page->drawText(
            $pageText,
            self::PAGE_POSITION_RIGHT - $pageTextSize,
            self::PAGE_POSITION_BOTTOM + $pageTextHeight * 0.4,
            'UTF-8'
        );

        return $page;
    }

    /**
     * Insert the store logo to the Pdf
     *
     * @param Zend_Pdf_Page         &$page Page to insert logo
     * @param Mage_Core_Model_Store $store Store to get the logo
     *
     * @return Zend_Pdf_Page
     */
    protected function insertLogo(&$page, $store = null)
    {
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        if ($image) {
            $image = Mage::getStoreConfig('system/filesystem/media', $store) . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $size = getimagesize($image);
                $imageWidth = $size[0];
                $imageHeight = $size[1];

                // calcualte image ratio
                $imageRatio = 1;
                if ($imageWidth > $imageHeight) {
                    $imageRatio = $imageWidth / $imageHeight;
                } elseif ($imageHeight > $imageWidth) {
                    $imageRatio = $imageHeight / $imageWidth;
                }

                // calculate new image size
                if ($imageHeight > self::MAX_LOGO_HEIGHT or $imageWidth > self::MAX_LOGO_WIDTH) {
                    if ($imageHeight > self::MAX_LOGO_HEIGHT) {
                        $imageHeight = self::MAX_LOGO_HEIGHT;
                        $imageWidth = round(self::MAX_LOGO_HEIGHT * $imageRatio);
                    }
                    if ($imageWidth > self::MAX_LOGO_WIDTH) {
                        $imageWidth = self::MAX_LOGO_WIDTH;
                        $imageHeight = round(self::MAX_LOGO_WIDTH * $imageRatio);
                    }
                }

                $image = Zend_Pdf_Image::imageWithPath($image);
                $logoPosition = Mage::getStoreConfig('sales/identity/logoposition', $store);

                switch($logoPosition) {
                    case 'center':
                        $startLogoAt = self::PAGE_POSITION_LEFT;
                        $startLogoAt += ((self::PAGE_POSITION_RIGHT - self::PAGE_POSITION_LEFT) / 2 );
                        $startLogoAt -= $imageWidth / 2;
                        break;
                    case 'right':
                        $startLogoAt = self::PAGE_POSITION_RIGHT - $imageWidth;
                        break;
                    default:
                        $startLogoAt = self::PAGE_POSITION_LEFT;
                        break;
                }

                $imageTopLeft = $startLogoAt;
                $imageTop = self::PAGE_POSITION_TOP - $imageHeight;
                $imageBottomRight = $imageTopLeft + $imageWidth;
                $imageBottom = $imageTop + $imageHeight;

                $page->drawImage($image, $imageTopLeft, $imageTop, $imageBottomRight, $imageBottom);
            }
        }

        return $page;
    }

    /**
     * insert a address footer item
     *
     * @param Zend_Pdf_Page $page        page to insert the item
     * @param string        $key         key of item
     * @param string        $value       value of item
     * @param float         $itemSpacing Spacing between key and value
     *
     * @return void
     */
    protected function _insertAddressFooterItem($page, $key, $value = null, $itemSpacing = 0)
    {
        $fontSize = 5;
        $font = $this->_setFontRegular($page, $fontSize);
        $this->_newLine($font, 5);

        $page->drawText(
            $key,
            $this->_width,
            $this->_height,
            'UTF-8'
        );

        if ($value) {
            $page->drawText(
                $value,
                $this->_width + $itemSpacing,
                $this->_height,
                'UTF-8'
            );
        }
    }

    /**
     * Insert the store address to the Pdf
     *
     * @param Zend_Pdf_Page         &$page Page to insert address
     * @param Mage_Core_Model_Store $store Store to get the address
     *
     * @return Zend_Pdf_Page
     */
    protected function insertAddressFooter(&$page, $store = null)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

        $this->_height = self::PAGE_POSITION_BOTTOM;
        $this->_width = 20;
        $heightCount = 0;
        $fontSize = 5;
        $font = $this->_setFontRegular($page, $fontSize);

        $page->setLineWidth(0.4);
        //$page->drawLine($this->_width, $this->_height, self::PAGE_POSITION_RIGHT, $this->_height);
        $page->setLineWidth(0);

        if (Mage::helper('invoicepdf')->getSalesPdfInvoiceConfigFlag('showfooter', $store)) {
            $config = null;
            if (Mage::getConfig()->getNode('modules/Symmetrics_Imprint')) {
                $data = Mage::getStoreConfig('general/imprint', $store);
                $config = Mage::getModel('Mage_Core_Model_Config_System')->load('Symmetrics_Imprint');
                $moduleName = 'imprint';
            } else if (Mage::getConfig()->getNode('modules/Symmetrics_Impressum')) {
                $data = Mage::getStoreConfig('general/impressum', $store);
                $config = Mage::getModel('Mage_Core_Model_Config_System')->load('Symmetrics_Impressum');
                $moduleName = 'impressum';
            } else {
                $data = explode("\n", Mage::getStoreConfig('sales/identity/address', $store));
            }

            $itemCollector = array();
            foreach ($data as $key => $value) {
                if ($value == '') {
                    continue;
                } else {
                    if ($config) {
                        /* get labels from fields in system.xml */
                        $element = $config->getNode('sections/general/groups/' . $moduleName . '/fields/' . $key);
                        $element = $element[0];
                        $elementData = $element->asArray();
                        if (isset($elementData['hide_in_invoice_pdf'])) {
                            /* don`t show this field */
                            continue;
                        } else {
                            $label = Mage::helper($moduleName)->__($elementData['label']) . ':';
                            $itemCollector[$label] = $value;
                        }

                    } else {
                        array_push($itemCollector, $value);
                    }

                    $heightCount++;
                    if ($heightCount % 6 == 0 || (count($data) - 1 == $heightCount)) {
                        $keyWidth = 0;
                        $itemWidth = 0;

                        // Calculate Column width
                        foreach ($itemCollector as $itemKey => $itemValue) {
                            $itemKey = strip_tags($itemKey);
                            $itemValue = strip_tags($itemValue);
                            if ($config) {
                                if ($keyWidth < $this->widthForStringUsingFontSize($itemKey, $font, $fontSize)) {
                                    $keyWidth = $this->widthForStringUsingFontSize($itemKey, $font, $fontSize);
                                }
                            }
                            if ($itemWidth < $this->widthForStringUsingFontSize($itemValue, $font, $fontSize)) {
                                $itemWidth = $this->widthForStringUsingFontSize($itemValue, $font, $fontSize);
                            }
                        }

                        foreach ($itemCollector as $itemKey => $itemValue) {
                            if ($config) {
                                $this->_insertAddressFooterItem(
                                    $page,
                                    trim(strip_tags($itemKey)),
                                    trim(strip_tags($itemValue)),
                                    ($keyWidth + 15)
                                );
                            } else {
                                $this->_insertAddressFooterItem($page, trim(strip_tags($itemValue)));
                            }
                        }
                        $this->_width += $keyWidth + $itemWidth + self::FOOTER_SPACING;
                        $this->_height = self::PAGE_POSITION_BOTTOM;
                        $itemCollector = array();
                    }
                }
            }
        }
        $this->_setFontRegular($page);
        $this->_width = self::PAGE_POSITION_LEFT;

        return $page;
    }

    /**
     * Format address
     *
     * @param string $address address to format
     *
     * @return array
     */
    protected function _formatAddress($address)
    {
        $return = array();
        foreach (explode('|', $address) as $str) {
            foreach (Mage::helper('core/string')->str_split($str, 65, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }

    /**
     * Insert a Order information row
     *
     * @param Zend_Pdf_Page &$page given Page to insert row
     * @param string        $key   key to write
     * @param string        $value value to write
     *
     * @return void
     */
    protected function _insertOrderInfoRow(&$page, $key, $value)
    {
        $font = $this->_setFontRegular($page, 8);


        $keyPos = self::PAGE_POSITION_RIGHT - 170;
        $keyWidth = $this->widthForStringUsingFontSize($key, $font, 8);
        $valuePos = self::PAGE_POSITION_RIGHT - 10 - $this->widthForStringUsingFontSize($value, $font, 8);
        $valueWidth = $this->widthForStringUsingFontSize($value, $font, 8);

        $keyRightPos = $keyPos + $keyWidth + 4;
        $avilValueSpace = self::PAGE_POSITION_RIGHT - $keyRightPos - 10;

        $textWidth = $this->widthForStringUsingFontSize('T', $font, 8);
        $value = wordwrap($value, $avilValueSpace/$textWidth, "\n", false);
        $value = explode("\n", $value);

        $value = array_reverse($value);

        $count = 0;
        foreach ($value as $item) {
            if ($count > 0) {
                $this->_newLine($font, 8, true);
            }

            $page->drawText(
                $item,
                self::PAGE_POSITION_RIGHT - 10 - $this->widthForStringUsingFontSize($item, $font, 8),
                $this->_height,
                'UTF-8'
            );
            $count++;
        }

        $page->drawText(
            $key,
            self::PAGE_POSITION_RIGHT - 170,
            $this->_height,
            'UTF-8'
        );
        $this->_newLine($font, 8, true);
    }

    /**
     * Inserts the Order Information to given page
     *
     * @param Zend_Pdf_Page          &$page      given page to insert order info
     * @param Mage_Sales_Model_Order $order      order to get info from
     * @param boolean                $putOrderId print order id
     *
     * @return void
     */
    protected function _insertOrderInfo(&$page, $order, $putOrderId)
    {
        $this->_height = 570;
        $storeId = $order->getStoreId();

        /* @var $helper Symmetrics_InvoicePdf_Helper_Data */
        $helper = Mage::helper('invoicepdf'); //->getSalesPdfInvoiceConfig($order->getStoreId());

        $this->_insertOrderInfoRow(
            $page,
            Mage::helper('sales')->__('Order Date: '),
            Mage::helper('core')->formatDate(
                $order->getCreatedAtStoreDate(),
                'medium',
                false
            )
        );

        $customerid = $order->getCustomerId();
        if (!empty($customerid)) {
            $customerid = $helper->getSalesPdfInvoiceConfigKey('customeridprefix', $storeId) . $customerid;
        } else {
            $customerid = '-';
        }
        $this->_insertOrderInfoRow(
            $page,
            $helper->__('Customer #:'),
            $customerid
        );

        if ($helper->getSalesPdfInvoiceConfigFlag('showcustomerip', $storeId)) {
            $this->_insertOrderInfoRow(
                $page,
                $helper->__('Customer IP:'),
                $order->getRemoteIp()
            );
        }

        /* Payment */
        if ($helper->getSalesPdfInvoiceConfigFlag('showpayment', $storeId)) {
            $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true)
                ->toPdf();

            $payment = explode('{{pdf_row_separator}}', $paymentInfo);
            foreach ($payment as $key => $value) {
                if (strip_tags(trim($value)) == '') {
                    unset($payment[$key]);
                } else {
                    $payment[$key] = strip_tags(trim($value));
                }
            }
            reset($payment);
            $payment = implode(' ', $payment);

            $this->_insertOrderInfoRow(
                $page,
                Mage::helper('sales')->__('Payment Method:'),
                $payment
            );
        }

        if ($helper->getSalesPdfInvoiceConfigFlag('showcarrier', $storeId)) {
            $this->_insertOrderInfoRow(
                $page,
                Mage::helper('sales')->__('Shipping Method:'),
                $order->getShippingDescription()
            );
        }

        if ($putOrderId) {
            $this->_insertOrderInfoRow(
                $page,
                $helper->__('Order # '),
                $order->getRealOrderId()
            );
        }
    }

    /**
     * insert billing address to given page
     *
     * @param Zend_Pdf_Page                  &$page          page to insert the billing address
     * @param Mage_Sales_Model_Order_Address $billingAddress billing address to get data
     *
     * @return void
     */
    protected function _insertBillingAddress(&$page, $billingAddress)
    {
        $billingAddress = $this->_formatAddress($billingAddress->format('pdf'));
        $font = $this->_setFontRegular($page, 7);
        $greyScale9 = new Zend_Pdf_Color_GrayScale(0.5);
        $page->setFillColor($greyScale9);

        $this->_height = 675;
        $this->_width = self::PAGE_POSITION_LEFT;
        $senderAddress = Mage::helper('invoicepdf')->getSalesPdfInvoiceConfigKey('senderaddress', null);
        if ($senderAddress) {
            $page->drawText(
                $senderAddress,
                $this->_width,
                $this->_height,
                'UTF-8'
            );
            // $this->_height -= 15;
            $this->_newLine($font, 7, false, 1.8);
        }

        $font = $this->_setFontRegular($page, 9);
        $black = new Zend_Pdf_Color_GrayScale(0);
        $page->setFillColor($black);

        foreach ($billingAddress as $addressItem) {
            $page->drawText(
                $addressItem,
                $this->_width,
                $this->_height,
                'UTF-8'
            );

            //$this->_height -= 12;
            $this->_newLine($font, 9);
        }
    }

    /**
     * Set a Subject to given page
     *
     * @param Zend_Pdf_Page &$page page to set the title
     * @param string        $title title to set
     *
     * @return void
     */
    protected function setSubject(&$page, $title)
    {
        $this->_setFontBold($page, 16);
        $black = new Zend_Pdf_Color_GrayScale(0);
        $page->setFillColor($black);

        $page->drawText(
            $title,
            self::PAGE_POSITION_LEFT,
            525,
            'UTF-8'
        );
        $this->_setFontRegular($page);
    }

    /**
     * draw the oder info to given page
     *
     * @param Zend_Pdf_Page          &$page      page to draw the order info
     * @param Mage_Sales_Model_Order $order      order to get info from
     * @param boolean                $putOrderId put the order id
     *
     * @return void
     */
    protected function insertOrder(&$page, $order, $putOrderId = true)
    {
        /* @var $order Mage_Sales_Model_Order */

        $this->_insertOrderInfo($page, $order, $putOrderId);

        /* Billing Address */
        $this->_insertBillingAddress($page, $order->getBillingAddress());


        $this->_height = 515;
        $this->_width = self::PAGE_POSITION_LEFT;
        $this->insertTableHeader($page);
    }

    /**
     * insert the table header to given page
     *
     * @param Zend_Pdf_Page &$page page to insert the table header
     *
     * @return void
     */
    protected function insertTableHeader(&$page)
    {

        $fontSize = 9;
        $font = $this->_setFontRegular($page, $fontSize);
        $fontHeight = $this->heightForFontUsingFontSize($font, $fontSize);

        $columHeight = $fontHeight + 5;
        $greyScale9 = new Zend_Pdf_Color_GrayScale(0.9);
        $fillType = Zend_Pdf_Page::SHAPE_DRAW_FILL;

        $page->setFillColor($greyScale9);
        $page->drawRectangle(
            $this->_width,
            $this->_height,
            self::PAGE_POSITION_RIGHT,
            $this->_height - $columHeight,
            $fillType
        );

        $this->_newLine($font, $fontSize, false, 1);
        $black = new Zend_Pdf_Color_GrayScale(0);
        $page->setFillColor($black);

        $page->drawText(
            Mage::helper('invoicepdf')->__('Pos'),
            $this->_width + 3,
            $this->_height,
            'UTF-8'
        );
        $page->drawText(
            Mage::helper('invoicepdf')->__('No.'),
            $this->_width + 45,
            $this->_height,
            'UTF-8'
        );
        $page->drawText(
            Mage::helper('invoicepdf')->__('Description'),
            $this->_width + 110,
            $this->_height,
            'UTF-8'
        );

        $singlePrice = Mage::helper('invoicepdf')->__('Price');
        $page->drawText(
            $singlePrice,
            self::PAGE_POSITION_RIGHT - 160 - $this->widthForStringUsingFontSize($singlePrice, $font, $fontSize),
            $this->_height,
            'UTF-8'
        );

        $amountLabel = Mage::helper('invoicepdf')->__('Amount');
        $page->drawText(
            $amountLabel,
            self::PAGE_POSITION_RIGHT - 110 - $this->widthForStringUsingFontSize($amountLabel, $font, $fontSize),
            $this->_height,
            'UTF-8'
        );

        $taxLabel = Mage::helper('invoicepdf')->__('Tax amount');
        $page->drawText(
            $taxLabel,
            self::PAGE_POSITION_RIGHT - 60 - $this->widthForStringUsingFontSize($taxLabel, $font, $fontSize),
            $this->_height,
            'UTF-8'
        );

        $totalLabel = Mage::helper('invoicepdf')->__('Total');
        $page->drawText(
            $totalLabel,
            self::PAGE_POSITION_RIGHT - 10 - $this->widthForStringUsingFontSize($totalLabel, $font, $fontSize),
            $this->_height,
            'UTF-8'
        );

        $this->_newLine($font, $fontSize, false, 1.8);
    }

    /**
     * Insert a table Row
     *
     * @param Zend_Pdf_Page                                  &$page           Page to insert table row
     * @param Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract $data            data to insert in the row
     * @param boolean                                        $drawTableHeader flag to draw the table header
     * @param boolean                                        $drawBorder      flag to draw a border around the row
     * @param float                                          $borderSize      size of the border
     *
     * @return Zend_Pdf_Page
     */
    public function insertTableRow(
        &$page,
        Symmetrics_InvoicePdf_Model_Pdf_Items_Abstract $data,
        $drawTableHeader = false,
        $drawBorder = false,
        $borderSize = 1)
    {
        // TODO: Fist calc high
        $neededHeight = $data->calculateHeight();

        if ($this->_height - $neededHeight < self::PAGE_POSITION_BOTTOM) {
            $settings = new Varien_Object();
            $settings->setDrawTableHeader($drawTableHeader);
            $page = $this->newPage($settings);
        }

        // Draw Pos. Nr.

        $tableTop = $this->_height + 8;

        $rowFont = $this->_setFontBold($page, 8);
        if ($drawBorder) {
            $this->_newLine($rowFont, $borderSize * 3);
        }

        if ($data->hasTriggerPosNumber()) {
            $this->_posCount++;
            $page->drawText($this->_posCount, self::PAGE_POSITION_LEFT + 3, $this->_height, 'UTF-8');
        }


        // check if height is abialable
        foreach ($data->getAllRows() as $row) {
            $columns = $row->getAllColumns();
            foreach ($columns as $column) {
                $font = $column->getFont();
                $fontSize = $column->getFontSize();
                $value = $column->getValue();
                $align = $column->getAlign();
                $padding = $column->getPadding();

                if ($align == 'right') {
                    $padding = self::PAGE_POSITION_RIGHT - $padding;
                    $padding -= $this->widthForStringUsingFontSize($value, $font, $fontSize);
                } else {
                    $padding = $this->_width + $padding;
                }

                $currentPos = 0;
                $height = $this->_height;

                $page->setFont($font, $fontSize);
                if (is_array($value)) {
                    foreach ($value as $valueRow) {
                        $page->drawText($valueRow, $padding, $height, 'UTF-8');
                        $height -= $this->heightForFontUsingFontSize($font, $fontSize)
                            * Symmetrics_InvoicePdf_Model_Pdf_Items_Item::COLUMN_SPACING;
                    }
                } else {
                    $page->drawText($value, $padding, $height, 'UTF-8');
                }
            }
            $this->_height -= $row->calculateHeight();
        }

        if ($drawBorder) {
            $page->setLineWidth($borderSize);
            $page->drawRectangle(
                self::PAGE_POSITION_LEFT,
                $tableTop,
                self::PAGE_POSITION_RIGHT,
                $this->_height + 8,
                Zend_Pdf_Page::SHAPE_DRAW_STROKE
            );
        }

        $this->_height -= 10;

        return $page;
    }

    /**
     * Draw Item process
     *
     * @param Varien_Object          $item  item to draw
     * @param Zend_Pdf_Page          $page  page to draw on
     * @param Mage_Sales_Model_Order $order order do draw from
     *
     * @return Zend_Pdf_Page
     */
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getOrderItem()->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw();

        return $renderer->getPage();
    }

    /**
     * Insert the totals block to given page
     *
     * @param Zend_Pdf_Page            &$page  page to insert the block
     * @param Mage_Core_Model_Abstract $source source to set
     *
     * @return Zend_Pdf_Page
     */
    public function insertTotals(&$page, $source)
    {
        $renderer = Mage::getModel('invoicepdf/pdf_items_totals');
        $renderer->setSource($source);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw();

        return $renderer->getPage();
    }
}