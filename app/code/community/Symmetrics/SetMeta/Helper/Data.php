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
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Default helper class.
 *
 * @category  Symmetrics
 * @package   Symmetrics_SetMeta
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2011-2012 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_SetMeta_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var int $_storeId Current store id
     */
    protected $_storeId = null;

    /**
     * Get store ID from request.
     *
     * @return int Store ID.
     */
    public function getStoreId()
    {
        if ($this->_storeId == null) {
            $request = Mage::app()->getRequest();
            $this->_storeId = $request->getParam('store');
        }

        return $this->_storeId;
    }

    /**
     * Get SKU from request.
     *
     * @return boolean|string SKU or false.
     */
    public function getSku()
    {
        $request = Mage::app()->getRequest();
        $productParams = $request->getParam('product');
        if (isset($productParams['sku'])) {
            return $productParams['sku'];
        }

        return false;
    }


    /**
     * Get product by loading a new one. If there is an SKU specified in request,
     * then we use it, else we load product by supplied product id.
     *
     * @param int $productId Product id.
     *
     * @return Mage_Catalog_Model_Product Product instance.
     */
    public function getProduct($productId)
    {
        // have SKU in request?
        $productSku = $this->getSku();
        if ($productSku !== false) {
            // load product by SKU and store ID from request data
            $product = $this->loadProductBySku($productSku);
        } else {
            // otherwise just load it by supplied product id
            $product = $this->loadProductById($productId);
        }

        return $product;
    }

    /**
     * Obtain product instance by SKU and store id
     *
     * @param string $sku Product SKU.
     *
     * @return Mage_Catalog_Model_Product Product instance.
     */
    public function loadProductBySku($sku)
    {
        $productId = Mage::getSingleton('catalog/product')
            ->getIdBySku($sku);
        $product = $this->loadProductById($productId);

        return $product;
    }

    /**
     * Obtain product instance by product id and store id
     *
     * @param int $productId Product ID.
     *
     * @return Mage_Catalog_Model_Product Product instance.
     */
    public function loadProductById($productId)
    {
        $product = Mage::getModel('catalog/product')
            ->setStoreId($this->getStoreId())
            ->load($productId);

        return $product;
    }

    /**
     * Load product, generate meta data and store it in the product.
     *
     * @param Mage_Catalog_Model_Product $product Product instance.
     *
     * @return void
     */
    public function updateMetaData($product)
    {
        $productName = $product->getName();
        $categoryArray = $this->getCategoryNames($product);

        // compute meta content by prepending product name
        array_unshift($categoryArray, $productName);
        $metaContent = implode(', ', $categoryArray);
        $product->setMetaTitle($productName)
            ->setMetaKeyword($metaContent)
            ->setMetaDescription($metaContent)
            ->setGenerateMeta(0);
    }

    /**
     * Get category names of the product
     *
     * @param Mage_Catalog_Model_Product $product Product instance.
     *
     * @return array of category names
     */
    public function getCategoryNames($product)
    {
        $categories = $product->getCategoryIds();

        $categoryArray = array();
        foreach ($categories as $categoryId) {
            $categoryArray[] = $this->getCategoryName($categoryId);
        }

        return $categoryArray;
    }

    /**
     * Gets the category name by category id and store id.
     *
     * @param string $categoryId Category id.
     *
     * @return string Category name
     */
    public function getCategoryName($categoryId)
    {
        $storeId = Mage::app()->getRequest()->getParam('store');
        $categoryObject = Mage::getModel('catalog/category')
            ->setStoreId($storeId)
            ->load($categoryId);
        $categoryName = $categoryObject->getName();

        return $categoryName;
    }
}
