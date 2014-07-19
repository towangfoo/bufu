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
 * Base block class for rate buttons
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
abstract class Symmetrics_TrustedRating_Block_RateUs_Abstract extends Mage_Core_Block_Template
{
    /**
     * Where to show:
     * 
     *  - emails
     *  - shop frontend
     * 
     * @var string
     */
    protected $_ratePlace = Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_PLACE_FRONTEND;

    /**
     * Getter for Magento store instance
     *
     * @param int|Mage_Core_Model_Store $storeId Store ID or instance
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore($storeId = null)
    {
        return Mage::app()->getStore($storeId);
    }

    /**
     * Generates a HTML attribute ID value.
     *
     * @return string
     */
    public function getHtmlId()
    {
        $dataKey = 'html_id';
        if (!($blockId = $this->_getData($dataKey))) {
            $blockId = ($this->getBlockAlias()) ? $this->getBlockAlias() : $this->getNameInLayout();
            
            $this->setData($dataKey, $blockId);
        }
        
        return $blockId;
    }

    /**
     * Depending on where the rate buttons are shown the method generates a scheme-less image src
     *
     * @param string $rateType Either rate now or later
     *
     * @return string
     */
    public function getRateButtonSrc($rateType = null)
    {
        $rateType = $this->getRateType($rateType);
        $ratePlace = $this->getRatePlace();
        $withProtocol = ($ratePlace == Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_PLACE_EMAILS);
        $dataKey = '_rateus_button_src_' . $rateType . '_' . $ratePlace;
        if (!($btnSrc = $this->_getData($dataKey))) {
            $_xmlPath = Mage::helper('trustedrating')->getXmlPathRateusButtonImage($rateType, $ratePlace);
            $btnImageName = Mage::getStoreConfig($_xmlPath, $this->_getStore());
            $btnSrc = $this->getRateusBaseUrl($withProtocol) . $btnImageName;
            
            $this->setData($dataKey, $btnSrc);
        }
        
        return $btnSrc;
    }

    /**
     * Either we're in shop frontend or processing transaction mails
     *
     * @return string
     */
    public function getRatePlace()
    {
        return $this->_ratePlace;
    }
    
    /**
     * Validates and/or get default rate type.
     * Available types are:
     * 
     *  - Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_TYPE_RATE_NOW
     *  - Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_TYPE_RATE_Later
     * 
     * @param null|string $rateType Rate type.
     * 
     * @return string
     * @throws Mage_Core_Exception
     * @see Symmetrics_TrustedRating_Model_Trustedrating::$rateTypes
     */
    public function getRateType($rateType = null)
    {
        $rateType = (empty($rateType)) ?
            Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_TYPE_RATE_NOW :
            $rateType;
        if (!in_array($rateType, Symmetrics_TrustedRating_Model_Trustedrating::$rateTypes)) {
            $message = "Unknow rating type '%s'!";
            Mage::throwException($this->__($message, $rateType));
        }
        
        return $rateType;
    }
    
    /**
     * Get scheme-less base URL to the 'Rate Us' button images in media folder:
     * 
     *  '//magento-shop.net/media/trustedrating/buttons/'
     *
     * @param bool $withProtocol Flag to generate scheme-less URL
     * 
     * @return string
     */
    public function getRateusBaseUrl($withProtocol = false)
    {
        $dataKey = '_rateus_base_url';
        
        if (!($baseUrl = $this->_getData($dataKey))) {
            $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

            if (!$withProtocol) {
                $baseUrl = preg_replace('/^https?:/', '', $baseUrl);
            }
            $baseUrl .= Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_BUTTON_IMAGE_SUBPATH;
            $baseUrl .= DS;
            
            $this->setData($dataKey, $baseUrl);
        }
        
        return $baseUrl;
    }
    
    /**
     * Getter of configured Trusted Shops ID
     * 
     * @param mixed $storeId Store ID
     * 
     * @return string
     */
    public function getTsId($storeId = null)
    {
        return Mage::helper('trustedrating')->getTsId($storeId);
    }

    /**
     * Base validation if enabled or not
     *
     * @return bool
     */
    public function isActive()
    {
        return Mage::helper('trustedrating')->isTrustedRatingActive($this->_getStore());
    }

    /**
     * Setter of self::$ratePlace
     *
     * @param string $ratePlace Rate place defining whether we're in shop frontend or email context
     *
     * @return Symmetrics_TrustedRating_Block_RateUs_Abstract
     * @throws Mage_Core_Exception
     * @see Symmetrics_TrustedRating_Model_Trustedrating::$ratePlaces
     */
    public function setRatePlace($ratePlace)
    {
        if (!in_array($ratePlace, Symmetrics_TrustedRating_Model_Trustedrating::$ratePlaces)) {
            $message = "Unknow rating place '%s'!";
            Mage::throwException($this->__($message, $ratePlace));
        }
        
        return $this;
    }
}
