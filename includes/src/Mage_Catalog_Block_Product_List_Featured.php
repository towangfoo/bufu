<?php
/*
 * Mage_Catalog_Block_Product_Featured
 */
class Mage_Catalog_Block_Product_List_Featured extends Mage_Catalog_Block_Product_List {

    protected $_limit = 4;

    protected $_featureCategories = array();

    protected $_collection = array();

    protected $_attr_to_add = array();

    /**
     * add custom attributes to select
     *
     * @param string $name
     * @return void
     */
    public function addAttribute($name) {
        array_push($this->_attr_to_add, $name);
    }

    /**
     * set limit of products to show
     *   (initial value = 4)
     *
     * @param integer $limit
     * @return void
     */
    public function setProductLimit($limit) {
        $this->_limit = $limit;
    }

    /**
     * set categories where to search for featured products
     *
     * @param string $categories
     * @param string $delim
     * @return void
     */
    public function setFeaturedCategories($categories, $delim = ",") {
        $this->_featureCategories = explode($delim, $categories);
    }

    /**
     * get categories where to search for featured products
     *
     * @return array
     */
    public function getFeaturedCategories() {
        if (count($this->_featureCategories) < 1) {
            throw new Mage_Catalog_Exception("No categories of featured products specified!");
        }
        else return $this->_featureCategories;
    }

    /**
     * get collection of featured products for a category
     *
     * @param integer $categoryId
     * @return Mage_Catalog_Model_Resource_Eav_Product_Collection $collection
     */
    public function getFeaturedProductCollection($categoryId) {
        if (!isset($this->_collection[$categoryId])) {
            $category = Mage::getModel('catalog/category')->load($categoryId);

            $collection = Mage::getResourceModel('catalog/product_collection');

            $attributes = Mage::getSingleton('catalog/config')
                ->getProductAttributes();

            $collection->addAttributeToSelect($attributes);

            foreach($this->_attr_to_add as $attr) {
                $collection->addAttributeToSelect($attr);
            }

            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToFilter('bufu_feature_on_landingpage', 1)
                ->addAttributeToFilter('bufu_orderable', 1)
                ->addCategoryFilter($category)
                ->addStoreFilter()
                ->getSelect()->order(array('updated_at DESC','created_at DESC'))->limit($this->_limit);

            $this->_collection[$categoryId] = $collection;
        }

        return $this->_collection[$categoryId];
    }

    /**
     * returns name of category
     *
     * @param integer $categoryId
     * @return string
     */
    public function getCategoryName($categoryId) {
        return Mage::getModel('catalog/category')->load($categoryId)->getName();
    }

}
?>