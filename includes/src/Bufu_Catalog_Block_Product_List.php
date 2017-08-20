<?php

class Bufu_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_List {

    /**
     * @BuschFunk
     * Neuerscheinungen in dieser Kategorie werden nur dann angezeigt, wenn sie
     * innerhalb des Zeitraums, der von "news_from_date" und "news_to_date" begrenzt wird.
     */
    protected function _beforeToHtml()
    {
        $neuerscheinungen_category_id = Mage::getStoreConfig('system/neuerscheinungen/category_id');

        $layer = Mage::getSingleton('catalog/layer');
        if ($layer->getCurrentCategory()->getId() == $neuerscheinungen_category_id) {
            $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $collection = $this->_getProductCollection();
            $collection->addAttributeToFilter('news_from_date', array('to' => $todayDate, 'date' => true));
            $collection->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left');

            $this->_productCollection = $collection;
        }
        return parent::_beforeToHtml();
    }

}