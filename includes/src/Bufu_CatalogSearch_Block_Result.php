<?php
/**
 * Bufu_CatalogSearch_Block_Result
 */
class Bufu_CatalogSearch_Block_Result extends Mage_CatalogSearch_Block_Result
{

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        }
        return $this->_productCollection;
    }

}
