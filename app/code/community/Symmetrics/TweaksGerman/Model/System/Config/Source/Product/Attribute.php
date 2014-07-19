<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <torsten.walluhn@cgi.com>
 * @copyright 2012 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_TweaksGerman_Model_System_Config_Source_Product_Attribute helps to construct
 * dropdown element for the back-end configuration filled with product attributes.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Torsten Walluhn <torsten.walluhn@cgi.com>
 * @copyright 2012 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Model_System_Config_Source_Product_Attribute
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var Constant value to specify the option for disable the attribute output.
     */
    const NO_TEXT = '-------';

    /**
     * Retrieve Catalog Config Singleton.
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getProductAttributes()
    {
        return Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter()
            ->setOrder('frontend_label');
    }

    /**
     * Retrieve all options.
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(array(
                'label' => Mage::helper('catalog')->__('No Text'),
                'value' => self::NO_TEXT
            ));
            foreach ($this->_getProductAttributes() as $attribute) {
                $this->_options[] = array(
                    'label' => Mage::helper('catalog')->__($attribute->getFrontendLabel()),
                    'value' => $attribute->getAttributeCode()
                );
            }
        }
        return $this->_options;
    }

    /**
     * Return option array
     *
     * @return array options
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
