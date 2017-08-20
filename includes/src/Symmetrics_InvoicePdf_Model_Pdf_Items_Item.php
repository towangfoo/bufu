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
 * Class to handle Column items
 *
 * @category  Symmetrics
 * @package   Symmetrics_InvoicePdf
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_InvoicePdf_Model_Pdf_Items_Item
{
    /**
     * Variable to collect Items
     *
     * @var Varien_Object
     */
    protected $_columns;

    /**
     * column spacing
     */
    const COLUMN_SPACING = 1.2;

    /**
     * constructor to initialize the colums container
     *
     * @return void
     */
    public function __construct()
    {
        $this->_columns = new Varien_Object();
    }

    /**
     * add a Coumn
     *
     * @param string        $key      given key for column
     * @param string        $value    string to set for given column
     * @param float         $padding  padding of current column
     * @param string        $align    align of current column
     * @param float         $maxWidth max width for current column
     * @param Zend_Pdf_Font $font     font of current column
     * @param integer       $fontSize font size of current column
     *
     * @return Symmetrics_InvoicePdf_Model_Pdf_Items_Item
     */
    public function addColumn($key, $value, $padding = 0, $align = 'left', $maxWidth = 0, $font = null, $fontSize = 8)
    {
        $column = new Varien_Object();

        if ($font == null) {
            $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        }
        
        $column->setValue($value);
        $column->setPadding($padding);
        $column->setAlign($align);
        $column->setWidth($maxWidth);
        $column->setFont($font);
        $column->setFontSize($fontSize);

        $this->_columns->setData($key, $column);
        return $this;
    }

    /**
     * count the columns
     *
     * @return integer
     */
    public function columnCount()
    {
        return count($this->_columns->getData());
    }

    /**
     * clear all coumns
     *
     * @return void
     */
    public function clearColumns()
    {
        $this->_columns = new Varien_Object();
    }

    /**
     * get a column for a specified key
     *
     * @param sring $key key to get
     *
     * @return Varien_Object
     */
    public function getColumn($key)
    {
        return $this->_columns->getData($key);
    }

    /**
     * unset a column for specified key
     *
     * @param string $key key to get
     *
     * @return void
     */
    public function unsetColumn($key)
    {
        $this->_columns->unsetData($key);
    }

    /**
     * check if a column exists
     *
     * @param stirng $key key to check
     *
     * @return boolean
     */
    public function hasColumn($key)
    {
        return $this->_columns->hasData($key);
    }

    /**
     * get all collums of this item
     *
     * @return array
     */
    public function getAllColumns()
    {
        return $this->_columns->getData();
    }

    /**
     * calculate item height for his content
     *
     * @return float
     */
    public function calculateHeight()
    {
        $columnHeight = 0;
        $columns = $this->getAllColumns();
        
        foreach ($columns as $column) {
            $font = $column->getFont();
            $fontSize = $column->getFontSize();
            $value = $column->getValue();

            $textWidth = $this->_widthForStringUsingFontSize('T', $font, $fontSize);

            if (!is_array($value)) {
                if ($column->getWidth() != 0) {
                    $value = wordwrap($value, $column->getWidth()/$textWidth, "\n", false);
                    $value = explode("\n", $value);
                }
            }

            $heightMultiplyer = 1;
            if (is_array($value)) {
                $heightMultiplyer = count($value);
                $column->setValue($value);
            }

            $height = $this->_heightForFontUsingFontSize($font, $fontSize) * $heightMultiplyer * self::COLUMN_SPACING;
            if ($columnHeight < $height) {
                $columnHeight = $height;
            }
        }

        return $columnHeight;
    }

    /**
     * Returns the total height in points of the font using the specified font and
     * size.
     *
     * @param Zend_Pdf_Resource_Font $font     font to get size for
     * @param float                  $fontSize font size in points
     *
     * @return float
     */
    protected function _heightForFontUsingFontSize($font, $fontSize)
    {
        $height = $font->getLineHeight();
        $stringHeight = ($height / $font->getUnitsPerEm()) * $fontSize;

        return $stringHeight;
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
     * @param string                 $string   string to get size for
     * @param Zend_Pdf_Resource_Font $font     font to get size for
     * @param float                  $fontSize font size in points
     *
     * @return float
     */
    protected function _widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ? iconv(
            'UTF-8',
            'UTF-16BE//IGNORE',
            $string
        ) : @iconv(
            'UTF-8',
            'UTF-16BE',
            $string
        );

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
     * rount a Value up to given precision
     *
     * @param float $value     value to round up
     * @param int   $precision precision to round up
     *
     * @return float
     */
    public function roundUp($value, $precision = 0)
    {
        // If the precision is 0 then default the factor to 1, otherwise
        // use 10^$precision. This effectively shifts the decimal point to the
        // right.
        if ($precision == 0 ) {
            $precisionFactor = 1;
        } else {
            $precisionFactor = pow(10, $precision);
        }

        // ceil doesn't have any notion of precision, so by multiplying by
        // the right factor and then dividing by the same factor we
        // emulate a precision
        return ceil($value * $precisionFactor)/$precisionFactor;
    }
}