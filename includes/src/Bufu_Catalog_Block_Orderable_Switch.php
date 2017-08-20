<?php
/**
 * Bufu_Catalog_Block_Orderable_Switch
 */
class Bufu_Catalog_Block_Orderable_Switch extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 0,
            'cache_tags'     => array('Bufu_Catalog_Block_Orderable_Switch'),
            'cache_key'      => 'Bufu_Catalog_Block_Orderable_Switch'
        ));
    }

}