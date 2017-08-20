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
 * System config model to configure button rate image sizes. It also will copy button rate images from skin to media
 * for better access in admin scope while processing transaction mails
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
class Symmetrics_TrustedRating_Model_System_Config_Backend_RateUs_Button extends Mage_Core_Model_Config_Data
{
    /**
     * XML attribute name for rate type.
     */
    const ATTRIBUTE_RATE_TYPE = 'rate_type';

    /**
     * Magento's base design package name
     */
    const BASE_DESIGN_PACKAGE = 'base';

    /**
     * Default scope value
     *
     * @see Mage_Adminhtml_Block_System_Config_Form::SCOPE_DEFAULT
     */
    const SCOPE_DEFAULT = Mage_Adminhtml_Block_System_Config_Form::SCOPE_DEFAULT;

    /**
     * XML path sub keys to configure button image sizes for the corresponding 'places'
     */
    const SYSTEM_CONFIG_FIELD_SIZE_IN_EMAILS = 'size_in_emails',
          SYSTEM_CONFIG_FIELD_SIZE_IN_FRONTEND = 'size_in_frontend';

    /**
     * Map button image sizes to the corresponding 'places' (shop frontend or emails)
     *
     * @var array
     */
    public static $fieldPlaceMaps = array(
        self::SYSTEM_CONFIG_FIELD_SIZE_IN_EMAILS =>
            Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_PLACE_EMAILS,
        self::SYSTEM_CONFIG_FIELD_SIZE_IN_FRONTEND =>
            Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_PLACE_FRONTEND,
    );

    /**
     *
     * @var Symmetrics_TrustedRating_Helper_Data
     */
    protected $_helper;

    /**
     * Assemble store and setting specific button image file names
     *
     * @param Varien_Object &$storeData Data object to consider for image file name
     *
     * @return string
     */
    protected function _assembleTrustedratingRateusButtonImageName(Varien_Object &$storeData)
    {
        $imageName = '';
        
        $imageName .= $this->getFieldConfig()->getAttribute(self::ATTRIBUTE_RATE_TYPE);
        $imageName .= '_';
        $imageName .= $storeData->getData('language');
        $imageName .= '_';
        $imageName .= $this->getValue();
        
        return $imageName . Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_BUTTON_IMAGE_SUFFIX;
    }

    /**
     * Copying rating button images from skin to media folder
     *
     * @param string      $image    Button image file name
     * @param null|string $language For language specific rating button images
     *
     * @return void
     */
    protected function _copyTrustedratingRateusButtonImageToMediaDir($image, $language = null)
    {
        $src = '';
        $dest = '';
        
        $src .= $this->_getTrustedratingRateusButtonImageSkinImageDir($language);
        $src .= DS;
        $src .= $image;
        
        $dest .= $this->_getTrustedratingRateusButtonImageMediaDir();
        $dest .= DS;
        $dest .= $image;

        if (!is_file($dest) && is_file($src)) {
            $ioF = new Varien_Io_File;
            Mage::getSingleton('adminhtml/session')->addNotice(
                $this->getHelper()->__(
                    '%s has been copied to the media folder (%s)!',
                    $image,
                    'media/' . Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_BUTTON_IMAGE_SUBPATH
                )
            );
            
            $ioF->cp($src, $dest);
        }
    }

    /**
     * Hook into system config save event and save to custom XML path of button image names
     * as well copying button image files to media folder
     *
     * @return Symmetrics_TrustedRating_Model_System_Config_Backend_RateUs_Button
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $scopeCode = ($this->getStoreCode()) ? $this->getStoreCode() : $this->getWebsiteCode();
            $this->getHelper()->initTrustedRatingRateusButtonMediaDir();
            $type = $this->getFieldConfig()->getAttribute(self::ATTRIBUTE_RATE_TYPE);
            $place = self::$fieldPlaceMaps[$this->getField()];

            foreach ($this->_getTsStoreData($this->getScope(), $scopeCode) as $storeId => $data) {
                $imageName = $this->_assembleTrustedratingRateusButtonImageName($data);

                $xmlPath = $this->getHelper()->getXmlPathRateusButtonImage($type, $place);
                $this->_saveStoreConfig($xmlPath, $imageName, $storeId);
                $this->_copyTrustedratingRateusButtonImageToMediaDir($imageName, $data->getLanguage());
            }
        }
        
        return parent::_afterSave();
    }

    /**
     * Get Magento media dir to place the rating button images
     *
     * @return string
     */
    protected function _getTrustedratingRateusButtonImageMediaDir()
    {
        $dataKey = '_trustedrating_btn_image_media_dir';
        if (!($mediaDir = $this->_getData($dataKey))) {
            $mediaDir = '';
            $mediaDir .= Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA);
            $mediaDir .= DS;
            $mediaDir .= Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_BUTTON_IMAGE_SUBPATH;
            
            $this->setData($dataKey, $mediaDir);
        }
        
        return $mediaDir;
    }

    /**
     * Getting skin folder containing rating button images. It is common to copy base/default folder to custom
     * design/theme thus we have to consider it here.
     *
     * @param string $language ISO language code
     *
     * @return string
     */
    protected function _getTrustedratingRateusButtonImageSkinImageDir($language)
    {
        $language = empty($language) ? Symmetrics_TrustedRating_Model_Trustedrating::DEFAULT_LANGUAGE : $language;
        $dataKey = '_trustedrating_btn_image_skin_image_dir_' . $language;
        if (!($skinDir = $this->_getData($dataKey))) {
            $skinDirParams = array(
                '_area' => 'frontend',
            );
            
            $btnImageSubpath = DS . 'images' . DS .
                Symmetrics_TrustedRating_Model_Trustedrating::RATEUS_BUTTON_IMAGE_SUBPATH .
                DS . strtoupper($language);
            
            // Custom design package and theme
            $skinDir = '';
            $skinDir .= Mage::getDesign()->getSkinBaseDir($skinDirParams);
            $skinDir .= $btnImageSubpath;
            
            // Custom design package and 'default' theme
            if (!is_dir($skinDir)) {
                $skinDirParams['_theme'] = 'default';
                
                $skinDir = '';
                $skinDir .= Mage::getDesign()->getSkinBaseDir($skinDirParams);
                $skinDir .= $btnImageSubpath;
            }
            
            // We finally fall back to Magento's 'base' design package
            if (!is_dir($skinDir)) {
                $skinDirParams['_package'] = self::BASE_DESIGN_PACKAGE;
                
                $skinDir = '';
                $skinDir .= Mage::getDesign()->getSkinBaseDir($skinDirParams);
                $skinDir .= $btnImageSubpath;
            }
            
            $this->setData($dataKey, $skinDir);
        }
        
        return $skinDir;
    }

    /**
     * Getting Store specific TrustedRating settings
     *
     * @param string $scope Config scope
     * @param string $code  Scope code
     *
     * @return array
     */
    protected function _getTsStoreData($scope = self::SCOPE_DEFAULT, $code = '')
    {
        $dataKey = '_ts_store_data_scope::' . $scope;
        if (!($data = $this->_getData($dataKey))) {
            foreach ($this->_getTsStores($scope, $code) as $store) {
                /* @var $store Mage_Core_Model_Store */
                $data[$store->getId()] = new Varien_Object(
                    array(
                        'language' => $store->getConfig(
                            Symmetrics_TrustedRating_Model_Trustedrating::XML_PATH_TRUSTEDRATING_LANGUAGE
                        ),
                        'store_code' => $store->getCode(),
                    )
                );
            }
        }
        
        return $data;
    }
    
    /**
     * List of affected stores with active TrustedRating feature.
     *
     * @param string $scope Configuration scope in backend
     * @param string $code  Store code
     *
     * @return array
     */
    protected function _getTsStores($scope = self::SCOPE_DEFAULT, $code = '')
    {
        $dataKey = '_ts_stores_scope::' . $scope;
        if (!($stores = $this->_getData($dataKey))) {
            $_stores = null;
            $stores = array();

            switch ($scope) {
                case Mage_Adminhtml_Block_System_Config_Form::SCOPE_WEBSITES:
                    $_stores = Mage::app()->getWebsite($code)->getStores();
                    break;
                case Mage_Adminhtml_Block_System_Config_Form::SCOPE_STORES:
                    $_stores = array(Mage::app()->getStore($code));
                    break;
                case Mage_Adminhtml_Block_System_Config_Form::SCOPE_DEFAULT:
                default:
                    $_stores = Mage::app()->getStores();
                    break;
            }

            foreach ($_stores as $store) {
                /** @var $store Mage_Core_Model_Store */
                if ($this->getHelper()->isTrustedRatingActive($store)) {
                    $stores[] = $store;
                }
            }
            
            $this->setData($dataKey, $stores);
        }
        
        return $stores;
    }
    
    /**
     * Wrapper to save just store specific settings
     * 
     * @param string                    $path    XML PATH to use as key
     * @param mixed                     $value   Value to save
     * @param Mage_Core_Model_Store|int $storeId Store ID
     * 
     * @return Symmetrics_TrustedRating_Model_System_Config_Backend_RateUs_Button
     */
    protected function _saveStoreConfig($path, $value, $storeId)
    {
        Mage::getConfig()->saveConfig($path, $value, 'stores', $storeId);
        
        return $this;
    }
    
    /**
     * Getter for default module helper
     * 
     * @return Symmetrics_TrustedRating_Helper_Data
     */
    public function getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('trustedrating');
        }
        
        return $this->_helper;
    }
}
