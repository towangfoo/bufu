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
 * Frontend renderer for  displaying link to TS' language specific online documentation.
 *
 * @category  Symmetrics
 * @package   Symmetrics_Buyerprotect
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2010-2013 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Buyerprotect_Block_Adminhtml_System_Config_Documentation
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    /**
     * @const TS' URL to online documentation
     */
    const ONLINE_DOC_URL = 'https://www.trustedshops.com/docs/magento/buyer_protection_%s.htm';
    
    /**
     * Prepare and add documentation link element.
     *
     * @param Varien_Data_Form_Element_Abstract $element Form element instance
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $linkText = $this->__('Trusted Shops documentation');
        $linkAttribs = array(
            // de, en, es, fr and pl
            'href' => sprintf(self::ONLINE_DOC_URL, substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2)),
            'target' => '_blank',
            'value' => $linkText,
            'title' => $linkText
        );
        $element->addElement(new Varien_Data_Form_Element_Link($linkAttribs));
        
        return parent::render($element);
    }
}