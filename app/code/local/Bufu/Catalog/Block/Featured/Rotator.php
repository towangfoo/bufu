<?php

/*
 * Bufu_Catalog_Block_Featured_Rotator
 */
class Bufu_Catalog_Block_Featured_Rotator extends Mage_Catalog_Block_Product_Abstract
{

    /**
     * retrieve products for featured rotator
     *
     * @param integer $limit
     * @param boolean $shuffle
     *
     * @return array of Mage_Catalog_Model_Product
     */
    public function getProducts($limit = 10, $shuffle = false)
    {
        $category = Mage::getModel('catalog/category')->load(
            Mage::app()->getStore()->getRootCategoryId()
        );
        $result = $category->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('bufu_orderable', '1')
            ->addAttributeToSort('date_created', 'DESC')
            ->setPageSize($limit,1);

        if (true === $shuffle) {
            $result->getSelect()->order('RAND()');
        }

        return $result;
    }

}