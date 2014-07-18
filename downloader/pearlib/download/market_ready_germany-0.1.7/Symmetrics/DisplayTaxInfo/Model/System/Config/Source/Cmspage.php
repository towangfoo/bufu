<?php 
/**
 * Symmetrics_DisplayTaxInfo_Block_Tax
 *
 * @category Symmetrics
 * @package Symmetrics_DisplayTaxInfo
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>, Sergej Braznikov <sb@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_DisplayTaxInfo_Model_System_Config_Source_Cmspage extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getModel('cms/page')->getCollection()
                            ->addFieldToFilter('is_active', 1)
                            ->setOrder('title', 'ASC')
                            ->toOptionArray();
        }
        return $this->_options;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
