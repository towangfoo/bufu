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
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2011 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * TS products model.
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2011 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_Products
    extends Mage_Core_Helper_Abstract
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttriCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttriTypes = array();

    /**
     * Get scope from post data.
     *
     * @param string $website Current website scope.
     * @param string $store   Current store scope.
     *
     * @return array
     */
    public function getScopeId($website, $store)
    {
        if (!empty($store)) {
            $scope['type'] = 'stores';
            $scope['id'] = Mage::getModel('core/store')->load($store, 'code')->getId();
        } elseif (!empty($website)) {
            $scope['type'] = 'websites';
            $scope['id'] = Mage::getModel('core/website')->load($website, 'code')->getId();
        } else {
            $scope['type'] = 'default';
            $scope['id'] = 0;
        }
        return $scope;
    }

    /**
     * Recreate Trusted Shops Excellence Buyer Protection products.
     *
     * @param bool   $deleteOld Delete old products or check if there are already products?
     * @param string $website   Current website scope.
     * @param string $store     Current store scope.
     *
     * @return void
     */
    public function recreateProducts($deleteOld, $website, $store)
    {
        // todo: implement translations according to the result of this method
        //       or something.
        $this->getScopeId($website, $store);

        $helper = Mage::helper('buyerprotect');
        $productCollection = $helper->getAllTsProducts();

        if ($deleteOld) {
            $createNew = $this->deleteProducts($productCollection);
        } else {
            $createNew = true;
        }
        if ($createNew) {
            $this->createProducts();
        }
    }

    /**
     * Create a specific TS product.
     *
     * @return void
     */
    public function createProducts()
    {
        $tsProductsIds = Symmetrics_Buyerprotect_Model_Type_Buyerprotect::getAllTsProductIds();
        $tsProductsData = array();
        $preTaxValue = 1.19;
        $currency = new Zend_Currency('de_DE');

        foreach ($tsProductsIds as $tsProduct) {
            $tsProductsData['price'] = (double) $tsProduct->net * (double) $preTaxValue;
            preg_match('/^TS080501_([0-9]*)_.*/', $tsProduct->id, $matches);
            $tsProductName = "Käuferschutz bis " . $currency->toCurrency($matches[1]);
            $tsProductsData['name'] = $tsProductName;
            $tsProductsData['description'] = $tsProductName . $currency->toCurrency($tsProductsData['price']);
            $tsProductsData['short_description'] = $tsProductName . $currency->toCurrency($tsProductsData['price']);

            $this->createBuyerprotectProduct($tsProduct->id, $tsProductsData);
        }
    }

    /**
     * Delete a given product collection.
     *
     * @param Mage_Catalog_Model_Product_Collection $productCollection Product collection to delete.
     *
     * @return void
     */
    public function deleteProducts($productCollection)
    {
        foreach ($productCollection as $product) {
            $product->delete();
        }
    }

    /**
     * Create a BuyerProtect product from given data.
     *
     * @param string $sku         sku for new product
     * @param array  $productData Required indexes:
     *                            $productData['price'] (pre-tax)
     *                            $productData['name']
     *                            $productData['description']
     *                            $productData['short_description']
     *
     * @return void
     */
    public function createBuyerprotectProduct($sku, $productData)
    {
        $defaultSetId = $this->getDefaultAttributeSetId('catalog_product');
        $productModel = Mage::getModel('catalog/product');
        /* @var $productModel Mage_Catalog_Model_Product */

        // Sku already exists
        if ($productModel->getIdBySku($sku)) {
            return;
        }

        $productModel->setStoreId(0)
            ->setWebsiteIds($this->_getWebsiteIds())
            ->setAttributeSetId($defaultSetId)
            ->setTypeId('buyerprotect')
            ->setStatus(1)
            ->setSku($sku);

        foreach ($productModel->getTypeInstance(true)->getEditableAttributes($productModel) as $attribute) {
            $_attrCode = $attribute->getAttributeCode();
            if ($this->_isAllowedAttribute($attribute) && isset($productData[$_attrCode])) {
                $productModel->setData(
                    $attribute->getAttributeCode(),
                    $productData[$_attrCode]
                );
            }
        }

        $errors = $productModel->validate();
        if (is_array($errors)) {
            return $errors;
        }

        $stockData = array();
        $stockData['use_config_manage_stock'] = 0;
        $stockData['manage_stock'] = 0;
        $productModel->setStockData($stockData);

        try {
            $productModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        /**************
         * stock part *
         **************/

        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('cataloginventory/stock_item');

        $stockItem->loadByProduct($productModel);

        // note: the product id has to be set
        $stockItem->setProductId($productModel->getId());
        $stockItem->setStockId(Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID);
        $stockItem->setQty(999999999999);
        $stockItem->setIsInStock(1);
        $stockItem->setMinSaleQty(1);
        $stockItem->setMaxSaleQty(1);
        $stockItem->setUseConfigManageStock(0);
        $stockItem->setManageStock(0);

        try {
            $stockItem->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return;
    }


    /**
     * Retrieve Default Attribute Set for Entity Type
     *
     * @param string|int $entityType Attribute set type.
     *
     * @return int
     */
    public function getDefaultAttributeSetId($entityType)
    {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core/read');
        $select = $connection->select()
            ->from($resource->getTableName('eav/entity_type'), 'default_attribute_set_id')
            ->where(is_numeric($entityType) ? 'entity_type_id=?' : 'entity_type_code=?', $entityType);
        return $resource->getConnection('core/read')->fetchOne($select);
    }

    /**
     * Get all website IDs.
     *
     * @return array
     */
    protected function _getWebsiteIds()
    {
        return Mage::getModel('core/website')->getCollection()
            ->addFieldToFilter('website_id', array('gt' => 0))
            ->getAllIds();
    }

    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute  attribute to check
     * @param array                                    $attributes array of attributes
     *
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        if (is_array($attributes)
            && !( in_array($attribute->getAttributeCode(), $attributes)
                  || in_array($attribute->getAttributeId(), $attributes))) {
            return false;
        }

        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttriTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttriCodes);
    }
}