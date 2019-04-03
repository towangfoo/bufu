<?php
 /**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

class Amasty_Orderarchive_Block_Adminhtml_AjaxArchiving extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     *  Set template
     */
    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('*/ajax/archiving');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                    'id'      => 'amorderarchive_button_archiving',
                    'label'   => $this->helper('adminhtml')->__('Archiving'),
                    'onclick' => 'startArchiving();'
                )
            );

        return $button->toHtml();
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('amorderarchive/buttonArchiving.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

}