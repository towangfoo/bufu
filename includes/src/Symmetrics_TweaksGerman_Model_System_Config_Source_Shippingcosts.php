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
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_TweaksGerman_Model_System_Config_Source_Shippingcosts constructs the
 * dropdown for the back-end configuration, in which you can choose if the 
 * 'shipping-costs' - link shall be displayed incl., excl., or not at all 
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Model_System_Config_Source_Shippingcosts
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var array $_options cached options
     */
    protected $_options;

    /**
     * Return option array
     *
     * @return array options
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('tweaksgerman');
        if (!$this->_options) {
            $this->_options = array(
                array('value' => '0', 'label' => $helper->__('No')),
                array('value' => 'incl', 'label' => $helper->__('incl. Shipping costs')),
                array('value' => 'excl', 'label' => $helper->__('excl. Shipping costs'))
            );
        }

        return $this->_options;
    }

    /**
     * Get all options as array
     *
     * @return array options
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
