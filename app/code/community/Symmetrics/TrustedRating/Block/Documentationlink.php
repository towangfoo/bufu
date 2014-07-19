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
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

/**
 * Generate documentation links for backend
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_TrustedRating_Block_Documentationlink extends Mage_Core_Block_Template
{
    /**
     * Default documentation language.
     */
    const DEFAULT_DOCUMENTATION_LANGUAGE = 'en';
    
    /**
     * @const TS' URL to online documentation
     */
    const ONLINE_DOC_URL = 'https://www.trustedshops.com/docs/magento/seller_rating_%s.htm';
    
    /**
     * List of supported documentation languages
     *
     * @var array
     */
    public static $availableLanguages = array('de', 'en', 'es', 'fr', 'pl');

    /**
     * Get language specific online documentation link.
     *
     * @return string
     */
    public function getLinkTarget()
    {
        $docLanguage = substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
        
        if (!in_array($docLanguage, self::$availableLanguages)) {
            $docLanguage = self::DEFAULT_DOCUMENTATION_LANGUAGE;
        }
        
        return sprintf(self::ONLINE_DOC_URL, $docLanguage);
    }
}
