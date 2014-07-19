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
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 * @link      http://www.de.cgi.com/
 */

/**
 * SUPTRUSTEDSHOPS-129: System config source class for selecting 'Rate Us' button image sizes.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_TrustedRating_Model_System_Config_Source_RateUs_Button_Size
{
    /**
     * List of available 'Rate Us' button image sizes (width = 140, 150, 160, 170, 180, 190, 290).
     *
     * @var array
     */
    protected $_options;
    
    /**
     * Method to locate 'Rate Us' button images and determine available sizes.
     * 
     * @param string $lang Language specific buttons
     * 
     * @return void
     * @see self::toOptionArray()
     * @todo Complete implementation, currently method is unused and this source model is static
     */
    protected function _listImages($lang = Symmetrics_TrustedRating_Model_Trustedrating::DEFAULT_LANGUAGE)
    {
        Mage::getDesign()->getSkinBaseDir(array('_area' => 'frontend')) . DS .
            Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_BUTTON_IMAGE_SUBPATH . DS .
            strtoupper($lang);
    }
    
    /**
     * Getter for system config options
     * 
     * @return array
     * @see self::$_options
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            // Width is part of button image names.
            $sizes = array(140, 150, 160, 170, 180, 190, 290);
            
            $this->_options[] = array(
                'value' => null,
                'label' => Mage::helper('trustedrating')->__('Please select a size')
            );
            foreach ($sizes as $size) {
                $this->_options[] = array('value' => $size, 'label' => $size);
            }
        }
        
        return $this->_options;
    }
}
