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
 * Block class for rating buttons in transaction mails
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
class Symmetrics_TrustedRating_Block_RateUs_Order_Email extends Symmetrics_TrustedRating_Block_RateUs_Order
{
    /**
     * Override parent's default rate place
     *
     * @var string
     */
    protected $_ratePlace = Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_PLACE_EMAILS;
    
    /**
     * Default template.
     *
     * @var string
     */
    protected $_template = 'trustedrating/rateus/order/email.phtml';
    
    /**
     * In transaction mails the store is passed by template definition and processing
     * 
     * @param type $storeId Store ID
     * 
     * @return Mage_Core_Model_Store
     */
    protected function _getStore($storeId = null)
    {
        if ($this->hasData('store')) {
            return $this->getData('store');
        } else {
            return parent::_getStore($storeId);
        }
    }
    
    /**
     * In transaction mails the order is passed by template processing
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = $this->_getData('order');
        }
        
        return $this->_order;
    }

    /**
     * Get order ID
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->getOrder()->getId();
    }

    /**
     * Get HTML link to Trusted Shops' privacy conditions
     *
     * @return string
     */
    public function getTsPrivacyLink()
    {
        $link = '';
        $translator = Mage::helper('trustedrating');
        /* @var $translator Symmetrics_TrustedRating_Helper_Data */
        
        $link .= '<a href="' . $translator->getTsPrivacyUrl($this->_getStore()) . '"';
        $link .= ' title="' . $translator->__('Trustedshops Privacy') . '">';
        $link .= $translator->__('Details');
        $link .= '</a>';
        
        return $link;
    }
}
