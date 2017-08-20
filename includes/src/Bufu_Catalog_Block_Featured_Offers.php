<?php

/*
 * Bufu_Catalog_Block_Featured_Offers
 */
class Bufu_Catalog_Block_Featured_Offers extends Mage_Catalog_Block_Product_Abstract
{

    /**
     * retrieve products for featured offers
     *
     * @param integer $limit
     * @param integer $minimum
     *
     * @return Mage_Wishlist_Model_Mysql4_Product_Collection
     * @throws Mage_Catalog_Exception on incorrect input
     */
    public function getProducts($limit = 5, $minimum = 2)
    {
        if ($minimum > $limit) {
            throw new Mage_Catalog_Exception("minimum collection size can't be bigger than collection size limit");
        }

        $category = Mage::getModel('catalog/category')->load(
            Mage::app()->getStore()->getRootCategoryId()
        );
        $result = $category->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('bufu_monthly_offer_from', array('neq' => date("0000-00-00 00:00:00")))
            ->addAttributeToFilter('bufu_monthly_offer_from', array('lteq' => date("Y-m-d 00:00:00")))
            ->addAttributeToFilter('bufu_monthly_offer_to', array('gteq' => date("Y-m-d 00:00:00")))
            ->addAttributeToSort('date_created', 'DESC')
            ->setPageSize($limit,1);

        // not enough products found, take a collection off all products
        if (count($result) < $minimum) {
            unset($result);
            $result = $category->getProductCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToSort('date_created', 'DESC')
                ->addAttributeToSort('bufu_monthly_offer_to', 'DESC')
                ->setPageSize($minimum,1);
        }

        return $result;
    }

}