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
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

/**
 * Return array with German, English and French codes
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_TrustedRating_Model_System_Rating
{
    /**
     * @const LANG_DE Option for German language.
     */
    const LANG_DE = 'de';

    /**
     * @const LANG_EN Option for English language.
     */
    const LANG_EN = 'en';

    /**
     * @const LANG_ES Option for Spanish language.
     */
    const LANG_ES = 'es';

    /**
     * @const LANG_FR Option for French language.
     */
    const LANG_FR = 'fr';

    /**
     * @const LANG_PL Option for Polish language.
     */
    const LANG_PL = 'pl';

    /**
     * options
     *
     * @var array
     */
    protected $_options = null;

    /**
     * gets the languages for trusted rating site as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'value' => self::LANG_DE,
                    'label' => Mage::helper('trustedrating')->__('German'),
                ),
                array(
                    'value' => self::LANG_EN,
                    'label' => Mage::helper('trustedrating')->__('English'),
                ),
                array(
                    'value' => self::LANG_ES,
                    'label' => Mage::helper('trustedrating')->__('Spanish'),
                ),
                array(
                    'value' => self::LANG_FR,
                    'label' => Mage::helper('trustedrating')->__('French')
                ),
                array(
                    'value' => self::LANG_PL,
                    'label' => Mage::helper('trustedrating')->__('Polish')
                )
            );
        }
        return $this->_options;
    }
}
