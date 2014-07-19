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
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */

/**
 * Default helper class, return config values
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @author    Ngoc Anh Doan <ngoc-anh.doan@cgi.com>
 * @copyright 2009-2014 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      https://github.com/symmetrics/trustedshops_trustedrating/
 * @link      http://www.symmetrics.de/
 * @link      http://www.de.cgi.com/
 */
class Symmetrics_TrustedRating_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Some constants to test if buyerprotect widget is available.
     */
    const BUYERPROTECT_MODULE_CONFIG_KEY = 'buyerprotect',
          BUYERPROTECT_MODULE_NAME = 'Symmetrics_Buyerprotect';
    
    /**
     * @const CONFIG_STATUS_PATH system config path to status settings
     */
    const CONFIG_STATUS_PATH = 'trustedrating/status';

    /**
     * SUPTRUSTEDSHOPS-122:
     */
    const XML_PATH_SHOW_WIDGET = 'show_widget';

    /**
     * Flag indicates if rating button image underneath media already exists
     *
     * @var bool
     */
    protected $_btnMediaDirExists;
    
    /**
     * List of store IDs which have TrustedRating IDs
     *
     * @var array
     */
    protected $_trustedRatingStores = null;

    /**
     * SUPTRUSTEDSHOPS-122: 
     *
     * @return boolean
     */
    public function canShowWidget()
    {
        return $this->getTsId() &&
            $this->getIsActive() &&
            $this->getModuleConfig(self::XML_PATH_SHOW_WIDGET);
    }
    
    /**
     * Get all stores having set Trustedrating ID.
     * 
     * @param bool $active Trigger to remove stores which are not active or
     *                     where Trustedrating is not active.
     * 
     * @return array
     */
    public function getAllTrustedRatingStores($active = true)
    {
        if (null == $this->_trustedRatingStores) {
            $stores = Mage::getModel('core/store')->getCollection();
            /* @var $stores Mage_Core_Model_Resource_Store_Collection */
            $tsRatingStoreIds = array();

            $stores->setWithoutDefaultFilter();
            if ($active) {
                $stores->addFieldToFilter('is_active', true);
            }

            foreach ($stores as $store) {
                if ($active && !Mage::getStoreConfigFlag('trustedrating/status/trustedrating_active', $store)) {
                    continue;
                }

                if (Mage::getStoreConfig('trustedrating/data/trustedrating_id', $store)) {
                    $tsRatingStoreIds[] = $store->getId();
                }
            }
            
            $this->_trustedRatingStores = $tsRatingStoreIds;
        }
        
        return $this->_trustedRatingStores;
    }

    /**
     * Get store config by node and key
     *
     * @param string $node node
     * @param string $key  key
     *
     * @return string
     */
    public function getConfig($node, $key)
    {
        return Mage::getStoreConfig($node . '/' . $key, Mage::app()->getStore());
    }

    /**
     * Get configured language of module
     *
     * @param null|int $storeId Store ID
     *
     * @return string
     */
    public function getLanguage($storeId = null)
    {
        $language = Mage::getStoreConfig(
            Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_TRUSTEDRATING_LANGUAGE,
            $storeId
        );

        if (!$language) {
            $language = Symmetrics_TrustedRating_Model_Trustedrating::DEFAULT_LANGUAGE;
        }

        return $language;
    }

    /**
     * Get module specific config from system configuration
     *
     * @param string $key config key
     *
     * @return mixed
     */
    public function getModuleConfig($key)
    {
        return $this->getConfig(self::CONFIG_STATUS_PATH, $key);
    }

    /**
     * Get the activity status from store config
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->getModuleConfig('trustedrating_active');
    }

    /**
     * Get the trusted rating id from store config
     * 
     * @param mixed $storeId ID of Store.
     *
     * @return string
     */
    public function getTsId($storeId = null)
    {
        if ((null == $storeId) && Mage::app()->getStore()->isAdmin()) {
            $excMessage = 'Can\'t determine TS ID in Admin scope without Store ID!';
            Mage::logException(new Exception($excMessage));
        }
        
        return Mage::getStoreConfig('trustedrating/data/trustedrating_id', $storeId);
    }

    /**
     * Get order ID by shipment ID
     *
     * @param int $shipmentId Shipment Id
     *
     * @return int
     */
    public function getOrderId($shipmentId)
    {
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);

        return $shipment->getData('order_id');
    }
    
    /**
     * Get customer email by shipment Id
     *
     * @param int $shipmentId Shipment Id
     *
     * @return string
     */
    public function getCustomerEmail($shipmentId)
    {

        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        $order = $shipment->getOrder();
        $email = $order->getCustomerEmail();

        return $email;
    }

    /**
     * Get language specific Trusted Shops rating URL
     *
     * @param null|string                    $rateType Rating type, now or laterg
     * @param null|int|Mage_Core_Model_Store $store    Store ID or language
     *
     * @return type
     */
    public function getRatingUrl($rateType = 'rate_now', $store = null)
    {
        $url = '';
        $path = 'default';
        $language = '';
        
        $path .= '/';
        if ($rateType == Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_TYPE_RATE_NOW) {
            $path .= Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_RATE_NOW_URL_PREFIX;
        } else {
            $path .= Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_RATE_LATER_URL_PREFIX;
        }
        $path .= '/';
        
        if (is_string($store) &&
            (strlen($store) == 2) &&
            in_array($store, Symmetrics_TrustedRating_Model_Trustedrating::$languages)) {
            $language = $store;
            $store = null;
        } else {
            if ($rateType == Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_TYPE_RATE_NOW) {
                $language = Mage::getStoreConfig(
                    Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_TRUSTEDRATING_LANGUAGE,
                    $store
                );
            } else {
                $language = Symmetrics_TrustedRating_Model_Trustedrating::DEFAULT_LANGUAGE;
            }
        }
        
        if (!$language) {
            $language = Symmetrics_TrustedRating_Model_Trustedrating::DEFAULT_LANGUAGE;
        }
        $path .= $language;
        
        $url .= Mage::getConfig()->getNode($path);
        if ($rateType == Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_TYPE_RATE_NOW) {
            $url .= '_' . $this->getTsId($store) . '.html';
        }
        
        return $url;
    }

    /**
     * Get number of days to passed by before TS system submits reminder emails
     *
     * @param null|int $store Store ID
     *
     * @return mixed
     */
    public function getRateLaterDaysInterval($store = null)
    {
        $path = Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_TRUSTEDRATING_RATE_LATER_DAYS_INTERVAL;
        
        return Mage::getStoreConfig($path, $store);
    }

    /**
     * Getter for TS' privacy link
     *
     * @param int|null $storeId Store ID
     *
     * @return mixed
     */
    public function getTsPrivacyUrl($storeId = null)
    {
        $xmlPath = Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_PRIVACY_URL_PREFIX .
            '/' . $this->getLanguage($storeId);
        
        return Mage::getStoreConfig($xmlPath, $storeId);
    }
    
    /**
     * Get system config key as XML PATH to save the settings
     * 
     * @param string $rateType  Either rate immediately or later
     * @param string $ratePlace Either in shop frontend or emails
     * 
     * @return null|string
     * @see Symmetrics_TrustedRating_Model_Trustedrating::$rateTypes
     * @see Symmetrics_TrustedRating_Model_Trustedrating::$ratePlaces
     */
    public function getXmlPathRateusButtonImage($rateType, $ratePlace)
    {
        if (!in_array($rateType, Symmetrics_TrustedRating_Model_Trustedrating::$rateTypes) ||
            !in_array($ratePlace, Symmetrics_TrustedRating_Model_Trustedrating::$ratePlaces)) {
            return null;
        }
        
        return implode(
            '/',
            array(
                Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_CONFIG_KEY,
                $rateType,
                $ratePlace,
            )
        );
    }
    
    /**
     * Get system dir to copy the rating button images to Magento's media dir
     * '/var/www/magento_shop/media/trustedrating/buttons'
     * 
     * @return bool
     */
    public function initTrustedRatingRateusButtonMediaDir()
    {
        if (is_null($this->_btnMediaDirExists)) {
            $ioF = new Varien_Io_File;
            $dir = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA). DS .
                Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_BUTTON_IMAGE_SUBPATH;
            
            $this->_btnMediaDirExists = $ioF->checkAndCreateFolder($dir);
        }
        
        return $this->_btnMediaDirExists;
    }
    
    /**
     * Check if TS ID is set and feature is enabled.
     * 
     * @param int|Mage_Core_Model_Store $store Store which should be checked
     * 
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isTrustedRatingActive($store = null)
    {
        if ((null == $store) && Mage::app()->getStore()->isAdmin()) {
            $excMessage = 'Can\'t determine TS settings in Admin scope without specific store!';
            Mage::throwException($excMessage);
        }
        $store = Mage::app()->getStore($store);
        
        return $store->getConfig(Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_TRUSTEDRATING_ACTIVE) &&
            $store->getConfig(Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_TRUSTEDRATING_ID);
    }

    /**
     * Get Base64 URL encoded string.
     *
     * @param string $data      Data to encode
     * @param bool   $urlEncode Using urlencode or not
     *
     * @return string
     * @see urlencode
     * @see base64_encode
     */
    public function tsDataEncode($data, $urlEncode = false)
    {
        $returnValue = base64_encode($data);
        
        return (!$urlEncode) ? $returnValue : urlencode($returnValue);
    }
}
