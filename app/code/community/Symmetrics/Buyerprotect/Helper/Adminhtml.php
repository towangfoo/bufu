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
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Standard helper for Admin scope.
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Helper_Adminhtml extends Mage_Core_Helper_Abstract
{
    /**
     * Constants for building path to seals.
     */
    const TS_SEAL_SKIN_PATH = 'images/buyerprotect/';
    const TS_SEAL_PREFIX = 'seal_rating_';
    const TS_SEAL_DEFAULT = 'seal_rating_en';
    const TS_SEAL_SUFFIX = '.jpg';

    /**
     * Determine country code according to ISO 3166-2
     *
     * http://www.iso.org/iso/country_codes/background_on_iso_3166/iso_3166-2.htm
     *
     * @param string|Mage_Core_Model_Locale $locale Locale object or represnting string.
     *
     * @see Zend_Locale
     *
     * @return string
     */
    public function getCountryCode($locale = null)
    {
        // Expecting locale format like 'en_US', 'en_UK', etc.
        if (!(is_string($locale) && (strlen($locale) == 5))) {
            if ($locale instanceof Mage_Core_Model_Locale) {
                $locale = $locale->getLocaleCode();
            } else {
                $locale = Mage::app()->getLocale()->getLocaleCode();
            }
        }

        // See Zend_Locale::_localeData
        $locale = explode('_', $locale);
        
        return strtolower($locale[1]);
    }

    /**
     * Build locale depending URL to TS seal image.
     *
     * @param array $params URL params for Mage_Core_Model_Design_Package::getSkinBaseUrl
     *
     * @return string
     */
    public function getTsSeal(array $params = array())
    {
        $sealResource = '';

        // Yet the seals are used in admin scope only.
        if (Mage::app()->getStore()->isAdmin()) {
            $seal = self::TS_SEAL_SKIN_PATH
                . self::TS_SEAL_PREFIX
                . $this->getCountryCode()
                . self::TS_SEAL_SUFFIX;

            // Fallback to default seal, see self::TS_SEAL_DEFAULT
            if (!is_file(Mage::getDesign()->getSkinBaseDir() . DS . $seal)) {
                $seal = self::TS_SEAL_SKIN_PATH
                . self::TS_SEAL_DEFAULT
                . self::TS_SEAL_SUFFIX;
            }

            $sealResource = Mage::getDesign()->getSkinUrl($seal, $params);
        }

        return $sealResource;
    }
}
