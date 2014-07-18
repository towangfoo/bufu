<?php
/**
 * BuschFunk Adminhtml
 * for usage of own template theme
 */
class Bufu_Adminhtml_Block_Page extends Mage_Adminhtml_Block_Page
{

    public function __construct()
    {
        parent::__construct();
        Mage::getDesign()->setTheme('buschfunk');
    }

}
