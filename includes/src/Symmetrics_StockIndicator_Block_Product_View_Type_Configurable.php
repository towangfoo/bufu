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
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * StockIndicator view for configurable product
 *
 * @category  Symmetrics
 * @package   Symmetrics_StockIndicator
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_StockIndicator_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @const Red state constant
     */
    const RED_STATE = 'red';

    /**
     * @const Yellow state constant
     */
    const YELLOW_STATE = 'yellow';

    /**
     * @const Green state constant
     */
    const GREEN_STATE = 'green';

    /**
     * Quantity configuration values for different states
     *
     * @var array quantity configuration
     */
    private $_quantityConfig = null;

    /**
     * Quantity config getter
     *
     * @param string $state state value
     *
     * @return array configuration
     */
    protected function getQuantityConfig($state)
    {
        if (!$this->_quantityConfig) {
            $this->_quantityConfig = Mage::getSingleton('stockindicator/config')
                ->getConfig();
        }

        return $this->_quantityConfig[$state];
    }

    /**
     * Get configured quantity for the red stockindicator
     *
     * @return int configured quantity
     */
    public function getRedConfigQuantity()
    {
        return $this->getQuantityConfig(self::RED_STATE);
    }

    /**
     * Get configured quantity for the yellow stockindicator
     *
     * @return int configured quantity
     */
    public function getYellowConfigQuantity()
    {
        return $this->getQuantityConfig(self::YELLOW_STATE);
    }

    /**
     * Get configured quantity for the green stockindicator
     *
     * @return int configured quantity
     */
    public function getGreenConfigQuantity()
    {
        return $this->getQuantityConfig(self::GREEN_STATE);
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
        if (null !== $product) {
            $stockItem = $product->getStockItem();
        } else {
            $stockItem =  $this->getProduct()->getStockItem();
        }

        $isInStock = $stockItem->getIsInStock();

        return (boolean) $isInStock;
    }

    /**
     * Get allowed products
     *
     * @return collection of saleable associated products
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $allProducts = $this->getProduct()->getTypeInstance(true)
                ->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
                if ($product->isSaleable()) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }

        return $this->getData('allow_products');
    }

    /**
     * Get available product quantity
     *
     * @param Mage_Catalog_Model_Product_Type_Abstract $product model instance
     *
     * @return $quantity
     */
    protected function _getAvailableProductQty($product)
    {
        $stockItem = $product->getStockItem();
        $quantity = (int) $stockItem->getQty() - $stockItem->getMinQty();

        return $quantity;
    }

    /**
     * Get associated product quantities as JSON for configurable product view
     *
     * @return strign json
     */
    public function getJsonProductQuantities()
    {
        $productQuantities = array();

        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();
            $productQuantity = $this->_getAvailableProductQty($product);
            $productQuantities[$productId] = $productQuantity;
        }
        // return result as JSON-encoded string
        $result = Mage::helper('core')->jsonEncode($productQuantities);

        return $result;
    }
}