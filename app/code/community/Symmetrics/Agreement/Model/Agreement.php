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
 * @package   Symmetrics_Agreement
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2009-2011 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Rewrite agreement model to enable {{block}} parsing
 *
 * @category  Symmetrics
 * @package   Symmetrics_Agreement
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @copyright 2009-2011 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_Agreement_Model_Agreement extends Mage_Checkout_Model_Agreement
{
    /**
     * Add parsing filter to agreement box content.
     *
     * @return string
     */
    public function getContent()
    {
        $content = $this->getData('content');
        $processor = Mage::getModel('cms/template_filter');
        $html = $processor->filter($content);
        
        return $html;
    }
    
    /**
     * Add parsing filter to agreement checkbox text.
     *
     * @return string
     */
    public function getCheckboxText()
    {
        $content = $this->getData('checkbox_text');
        $processor = Mage::getModel('cms/template_filter');
        $html = $processor->filter($content);
        
        return $html;
    }
}
