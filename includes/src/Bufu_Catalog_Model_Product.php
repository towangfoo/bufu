<?php
/*
 * Bufu_Catalog_Model_Product
 */
class Bufu_Catalog_Model_Product extends Mage_Catalog_Model_Product
{

    /*
     * override is saleable check by checking Bufu option first
     *
     * @return boolean
     */
    public function isSaleable()
    {
        $orderable = $this->load($this->getId())->getBufuOrderable();

        if ($orderable !== "1")
            return false;
        else
            return parent::isSaleable();
    }

    /**
     * is the product preorderable but not yet released
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return boolean
     */
    public function isPreorderable()
    {
        $orderable_from = $this->getBufuOrderableFrom();
        if (null === $orderable_from)
            return false;

        $date = date("Y-m-d H:i:s");
        return $orderable_from > $date ;
    }

}
?>
