<?php
/**
 * Bufu_Catalog_Model_Layer
 */
class Bufu_Catalog_Model_Layer extends Mage_Catalog_Model_Layer
{

    const ARCHIVEMODE_SESSIONKEY = 'archive';

    /**
     * Initialize product collection
     * @Bufu: only show orderable products unless we are in archive mode
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $attributes = Mage::getSingleton('catalog/config')
            ->getProductAttributes();
        $collection->addAttributeToSelect($attributes)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            //->addStoreFilter()
            ;

        $session = Mage::getSingleton('checkout/session');

        if (true !== $session[self::ARCHIVEMODE_SESSIONKEY]) {
            $collection->addAttributeToFilter('bufu_orderable', 1);
        }

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->addUrlRewrite($this->getCurrentCategory()->getId());

        return $this;
    }

}
