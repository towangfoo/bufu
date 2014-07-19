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
 * Order related ratings.
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
abstract class Symmetrics_TrustedRating_Block_RateUs_Order extends Symmetrics_TrustedRating_Block_RateUs_Abstract
{
    /**
     * Current sales order instance
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Default rate place
     *
     * @var string
     */
    protected $_ratePlace = Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_PLACE_FRONTEND;

    /**
     * Overriding to determine correct store in admin scope by sales order instance
     *
     * @param int|null $storeId Store ID
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore($storeId = null)
    {
        if (!$storeId && Mage::app()->getStore()->isAdmin()) {
            $storeId = $this->getOrder()->getStoreId();
        }
        
        $store = parent::_getStore($storeId);
        if ($store->isAdmin()) {
            $message = "Couldn't determine correct store scope in Admin area!";
            Mage::throwException($this->__($message));
        }

        return $store;
    }

    /**
     * Number of days to passed by before reminder mails from the TS system are send
     *
     * @return int
     */
    public function getDaysIntervall()
    {
        return Mage::helper('trustedrating')->getRateLaterDaysInterval($this->_getStore());
    }
    
    /**
     * Getter of self::$_order
     * 
     * @return Mage_Sales_Model_Order
     */
    abstract public function getOrder();
    
    /**
     * Get customer email from order.
     * 
     * @return string
     */
    public function getOrderEmail()
    {
        return $this->getOrder()->getCustomerEmail();
    }
    
    /**
     * Get sales order ID
     * 
     * @return string
     */
    abstract public function getOrderId();
    
    /**
     * Get sales order increment ID
     * 
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }

    /**
     * Assemble rate URL to Trusted Shops
     *
     * @param string $rateType Rate now or later
     *
     * @return string
     */
    public function getRatingOrderUrl($rateType = 'rate_now')
    {
        $ratingUrl = '';
        
        $ratingUrl .= Mage::helper('trustedrating')->getRatingUrl($rateType, $this->_getStore());
        if ($rateType == Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_TYPE_RATE_NOW) {
            $ratingUrl .= '&'; // According to 'Integration Handbook v2.5':
                               // Only for Rate Shop Now, parameters must be added to the URL
                               // by "&" (not "?") because the link is rewritten by the Trusted Shops
                               // system.
        } else {
            $ratingUrl .= '?';
        }
        $ratingUrl .= http_build_query($this->getRatingOrderParams($rateType));
        
        return $ratingUrl;
    }

    /**
     * Assemble rating parameter passing to Trusted Shops
     *
     * @param string $rateType Rate type either rating now or later
     *
     * @return array
     */
    public function getRatingOrderParams($rateType = 'rate_now')
    {
        $helper = Mage::helper('trustedrating');
        $params = array(
            Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_RATING_PARAM_NAME_CUSTOMER_EMAIL =>
                $helper->tsDataEncode($this->getOrderEmail()),
            Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_RATING_PARAM_NAME_ORDER_ID =>
                $helper->tsDataEncode($this->getOrderIncrementId()),
        );
        
        if (is_string($rateType) &&
            ($rateType == Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_TYPE_RATE_LATER)) {
            $params[Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_RATING_PARAM_NAME_TS_ID] =
                $helper->getTsId($this->_getStore());
            $params[Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_RATING_PARAM_NAME_RATE_LATER_DAYS] =
                $helper->getRateLaterDaysInterval($this->_getStore());
        }
        
        return $params;
    }
}
