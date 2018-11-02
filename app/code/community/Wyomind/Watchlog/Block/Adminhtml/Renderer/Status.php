<?php
/**
 * Copyright © 2017 Wyomind. All rights reserved.
 */
class Wyomind_Watchlog_Block_Adminhtml_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $watchlogHelper = Mage::helper('watchlog');
        $class = 'critical';
        $label = $watchlogHelper->__('Failed');
        
        if ($row->getType() == 1) {
            $class = 'notice';
            $label = $watchlogHelper->__('Success');
        }
        if ($row->getType() == 2) {
            $class = 'minor';
            $label = $watchlogHelper->__('Blocked');
        }
        
        return "<span class='grid-severity-" . $class . "' title='" . $row->getUseragent() . "'>"
                    . "<span>" . $label . "</span>"
                . "</span>";
    }
}