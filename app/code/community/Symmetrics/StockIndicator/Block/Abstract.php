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
 * @package   Symmetrics_StockIndicator
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Abstract StockIndicator block class
 *
 * @category  Symmetrics
 * @package   Symmetrics_StockIndicator
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
abstract class Symmetrics_StockIndicator_Block_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @const string RED_STATE red state
     */
    const RED_STATE = 'red';

    /**
     * @const string YELLOW_STATE yellow state
     */
    const YELLOW_STATE = 'yellow';

    /**
     * @const string GREEN_STATE green state
     */
    const GREEN_STATE = 'green';

    /**
     * Default alignment of the 'Ampel' indicator. Used as css class to get it
     * horizontal of vertical
     *
     * @var string  horizontal | vertical
     */
    protected $_alignment = 'horizontal';

    /**
     * Options and data for the indicator:
     *
     * - Css classes
     * - Indicator product state (red | yellow | green)
     * - HTML tag, default is div (<div></div>)
     * - HTML attribute 'title'
     * - Stock indicator as HTML block
     *
     * Array keys:
     *
     * - html_tag
     * - css_class
     * - product_state
     * - state_title
     * - stockindicator_as_html
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Inherited classes can overwrite class::$attributes within this method
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->init();
    }

    /**
     * Initialize:
     *
     * Sets some default options and checks if this module is enabled
     *
     * @return void
     */
    public function init()
    {
        /* @var Symmetrics_StockIndicator_Model_Config $stockIndicatorConfig */
        $stockIndicatorConfig = Mage::getSingleton('stockindicator/config');

        // check if enabled
        $isIndicatorEnabled = $stockIndicatorConfig->isEnabled();
        if (!$isIndicatorEnabled) {
            return;
        }

        if ($this->isProductInStock()) {
            $this->setIsProductInStock(true);
            $this->_initProductStateByQuantity();
        } else {
            $this->setIsProductInStock(false);
            // set RED state
            $this->setProductState(self::RED_STATE);
            $this->setStateTitle($this->__('Currently out of stock!'));
        }

        // Default css classes
        $this->addCssClass('stock-indicator ' . $this->getAlignment() . ' ' . $this->getProductState());
    }

    /**
     * Initialize product state by quantity
     *
     * @return void
     */
    private function _initProductStateByQuantity()
    {
        /* @var Symmetrics_StockIndicator_Model_Config $stockIndicatorConfig */
        $stockIndicatorConfig = Mage::getSingleton('stockindicator/config');

        // Array for the following foreach statement
        $states = array(self::RED_STATE, self::YELLOW_STATE, self::GREEN_STATE);

        $configQuantities = $stockIndicatorConfig->getConfig();
        $productQuantity = $this->getProductStockQuantity();

        $stateFlag = false;

        // Sets state and HTML title attribute of product
        // based on quantity matching against configuration values
        foreach ($states as $state) {
            if ($productQuantity >= $configQuantities[$state]) {
                $stateFlag = true;
                $this->setProductState($state);
                switch ($state) {
                    case self::RED_STATE:
                        $this->setStateTitle($this->__('Currently out of stock!'));
                        break;
                    case self::YELLOW_STATE:
                        $this->setStateTitle($this->__('Only a few available!'));
                        break;
                    case self::GREEN_STATE:
                        $this->setStateTitle($this->__('In stock'));
                        break;
                }
            }
        }

        if (!$stateFlag) {
            $this->setProductState(self::RED_STATE);
            $this->setStateTitle($this->__('Currently out of stock!'));
        }
    }

    /**
     * Add extra css class to the StockIndicator
     *
     * @param string $class string with additional css class names
     *
     * @return Symmetrics_StockIndicator_Block_Abstract
     */
    public function addCssClass($class)
    {
        $class = trim($class);
        ($this->getCssClass()) ? $this->setCssClass($this->getCssClass() . " $class") : $this->setCssClass($class);

        return $this;
    }

    /**
     * If no tag is set, div gets set as default HTML tag
     *
     * @return string HTML tag
     */
    public function getHtmlTag()
    {
        if (!$this->getOption('html_tag')) {
            $this->setHtmlTag('div');
        }

        return $this->getOption('html_tag');
    }

    /**
     * Css classes for HTML
     *
     * @return string css classes
     */
    public function getCssClass()
    {
        return $this->getOption('css_class');
    }

    /**
     * Current stock state of product which is defined in the backend and depends
     * on the stock|quantity
     *
     * @return string red | yellow | green
     */
    public function getProductState()
    {
        return $this->getOption('product_state');
    }

    /**
     * HTML attribute - title - which will shown on hover indicator
     *
     * @return string HTML title
     */
    public function getStateTitle()
    {
        return $this->getOption('state_title');
    }

    /**
     * Getter method for the options array
     *
     * @param string $key array index
     *
     * @return string|bool
     * @see Symmetrics_StockIndicator_Block_Abstract::_options
     */
    public function getOption($key)
    {
        $key = trim($key);
        return isset($this->_options[$key]) ? $this->_options[$key] : false;
    }

    /**
     * Alignment of indicator, default is 'horizontal'
     *
     * @see Symmetrics_StockIndicator_Block_Abstract::_alignment
     * @see Symmetrics_StockIndicator_Block_Abstract::setAlignment()
     * @return string alignment
     */
    public function getAlignment()
    {
        return $this->_alignment;
    }

    /**
     * Gets product ID
     *
     * @return int product ID
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * Test stock availability
     *
     * @param Mage_Catalog_Model_Product $product specific product
     *
     * @return boolean true if product is in stock
     */
    public function isProductInStock($product = null)
    {
        if ($product === null) {
            $product = $this->getProduct();
        }

        $isInStock = $product ? $product->getStockItem()->getIsInStock() : false;

        return (boolean) $isInStock;
    }

    /**
     * Gets current stock of the product
     *
     * @param Mage_Catalog_Model_Product $product specific product
     *
     * @return int stock/quantity
     */
    public function getProductStockQuantity($product = null)
    {
        if (null !== $product) {
            $stockItem = $product->getStockItem();
        } else {
            $stockItem = $this->getProduct()->getStockItem();
            if (is_null($stockItem->getQty())) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')
                    ->loadByProduct($this->getProduct());
            }
        }

        $minQty = (int) $stockItem->getMinQty();

        return (int) $stockItem->getQty() + ($minQty < 0 ? $minQty : - $minQty);
    }

    /**
     * Css class(es) used for the HTML code
     *
     * @param string $tag css class(es)
     *
     * @return Symmetrics_StockIndicator_Block_Abstract
     */
    public function setHtmlTag($tag)
    {
        return $this->setOption('html_tag', strtolower($tag));
    }

    /**
     * Css class(es) used for the HTML code
     *
     * @param string $class css class(es)
     *
     * @return Symmetrics_StockIndicator_Block_Abstract
     */
    public function setCssClass($class)
    {
        return $this->setOption('css_class', $class);
    }

    /**
     * Product's indicator state which gets added as css class
     *
     * @param string $state red|yellow|green
     *
     * @return Symmetrics_StockIndicator_Block_Abstract
     */
    public function setProductState($state)
    {
        return $this->setOption('product_state', $state);
    }

    /**
     * HTML attribute: title - part of the HTML code
     *
     * @param string $title HTML title
     *
     * @return Symmetrics_StockIndicator_Block_Abstract
     */
    public function setStateTitle($title)
    {
        return $this->setOption('state_title', $title);
    }

    /**
     * Setter for the options array
     *
     * @param string $index key index
     * @param string $value array value
     *
     * @return Symmetrics_StockIndicator_Block_Abstract
     * @see Symmetrics_StockIndicator_Block_Abstract::_options
     */
    public function setOption($index, $value)
    {
        $this->_options[trim($index)] = trim($value);

        return $this;
    }

    /**
     * This attribute defines as css class whether the indicator is displayed
     * horizontal or vertical
     *
     * @param string $alignment css class: horizontal|vertical
     *
     * @return Symmetrics_StockIndicator_Block_Abstract
     */
    public function setAlignment($alignment)
    {
        $this->_alignment = strtolower(trim($alignment));

        return $this;
    }
}