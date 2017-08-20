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
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2011 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Source Model to get the WSDL API for Trusted Shops protectionservice 
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2011 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Model_System_Config_Source_Variation
{
    /**
     * @const CLASSIC_VALUE Value for classic option.
     */
    const CLASSIC_VALUE = 1;
    
    /**
     * @const EXCELLENCE_VALUE Value for excellence option.
     */
    const EXCELLENCE_VALUE = 2;
    
    /**
     * Generate option array for Trusted Shops product variation.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $returnArray = array(
            array(
                'value' => self::CLASSIC_VALUE,
                'label' => Mage::helper('buyerprotect')->__('Classic')
            ),

            array(
                'value' => self::EXCELLENCE_VALUE,
                'label' => Mage::helper('buyerprotect')->__('Excellence')
            ),
        );

        return $returnArray;
    }
}
