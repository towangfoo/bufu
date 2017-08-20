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
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
 
/**
 * Buyerprotection Product Type Class
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerportect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Type_Buyerprotect extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Constant to get protuct type
     */
    const TYPE_BUYERPROTECT = 'buyerprotect';

    /**
     * Checks if correct values are set for stock table 'cataloginventory_stock_item'
     *
     * @param Mage_CatalogInventory_Model_Stock_Item $stockItem Stock item object
     *
     * @return bool
     */
    protected static function _checkStockItem(Mage_CatalogInventory_Model_Stock_Item $stockItem)
    {
        if ($stockItem->getUseConfigMaxSaleQty() != 0
            || $stockItem->getUseConfigMinSaleQty() != 0
            || $stockItem->getUseConfigManageStock() != 0
            || $stockItem->getManageStock()!= 0
            // method getMinSaleQty() is implemented and does not return the DB value
            // as expected
            || $stockItem->getData('min_sale_qty') != 1
            || $stockItem->getMaxSaleQty() != 1
            ) {
            return false;
        }

        return true;
    }

    /**
     * Check if Product ist virtual, this always returns true
     *
     * @param Mage_Catalog_Model_Product $product product type instance
     *
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isVirtual($product = null)
    {
        // return True if this product is virtual and false if this product isn't virtual product
        return true;
    }

    /**
     * Static getter for self::$_tsProductIds
     *
     * @param bool $toArray Data of Mage_Core_Model_Config_Element to array
     *
     * @return array
     */
    public static function getAllTsProductIds($toArray = false)
    {
        $tsProducts = array();

        /* @var $tsProducts Mage_Core_Model_Config_Element */
        $tsProducts = Mage::getConfig()->getXpath('global/data/ts_products');
        $tsProducts = $tsProducts[0];

        return $toArray ? $tsProducts->asArray() : $tsProducts;
    }

    /**
     * Before save the product
     *
     * @param Mage_Catalog_Model_Product $product product to save
     *
     * @return null
     */
    public function beforeSave($product = null)
    {
        parent::beforeSave($product);
        $product->setVisibility(1);
        /** @todo calculate brutto price */
    }

    /**
     * Sets correct values if check fails
     *
     * @param Mage_CatalogInventory_Model_Stock_Item $stockItem Stock item object
     *
     * @see self::_checkStockItem()
     * @return void
     */
    public static function checkStockItem(Mage_CatalogInventory_Model_Stock_Item $stockItem)
    {
        if (!self::_checkStockItem($stockItem)) {
            $stockItem->setUseConfigMaxSaleQty(0);
            $stockItem->setUseConfigMinSaleQty(0);
            $stockItem->setUseConfigManageStock(0);
            $stockItem->setManageStock(0);
            $stockItem->setMinSaleQty(1);
            $stockItem->setMaxSaleQty(1);

            $stockItem->save();
        }

        return;
    }
}
