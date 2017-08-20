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
 * Block class for rating buttons in checkout
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
class Symmetrics_TrustedRating_Block_RateUs_Order_Checkout extends Symmetrics_TrustedRating_Block_RateUs_Order
{
    /**
     * Default template.
     *
     * @var string
     */
    protected $_template = 'trustedrating/rateus/order/checkout.phtml';
    
    /**
     * Instantiate sales order by session last order ID and return instance
     * 
     * @return Mage_Sales_Model_Order
     * @see self::getOrderId()
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
        }
        
        return $this->_order;
    }

    /**
     * Get ID from recent order
     *
     * @return string
     */
    public function getOrderId()
    {
        return Mage::getSingleton('checkout/session')->getLastOrderId();
    }

    /**
     * Additional validation if displaying in shop frontend is allowed
     *
     * @return bool
     */
    public function isActive()
    {
        return parent::isActive() &&
            Mage::getStoreConfig(
                Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_RATEUS_SHOW_IN_FRONTEND,
                $this->_getStore()
            );
    }
}
