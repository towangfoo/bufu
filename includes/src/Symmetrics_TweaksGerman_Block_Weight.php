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
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Block class for weight.phtml template
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2011 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Block_Weight extends Mage_Core_Block_Template
{
    /**
     * @const string DELIVERY_URL_CONFIG_PATH system config path delivery cms page
     */
    const DELIVERY_URL_CONFIG_PATH = 'checkout/cart/deliveryurl';

    /**
     * @var attray attributeLabelCache Attribute Label Cache.
     */
    private $_attributeLabelCache = array();

    /**
     * Get translation for attribute
     *
     * @param string $code Attribute code
     *
     * @return string label
     */
    public function getAttributeLabel($code)
    {
        $cacheKey = $code . '-' . $this->getStoreId();
        if (!array_key_exists($cacheKey, $this->_attributeLabelCache)) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $code);
            $attibuteLabels = $attribute->getStoreLabels();
            if (array_key_exists($this->getStoreId(), $attibuteLabels)) {
                $attibuteLabel = $attibuteLabels[$this->getStoreId()];
            } else {
                $attibuteLabel = $attribute->getFrontendLabel();
            }
            $this->_attributeLabelCache[$cacheKey] = $attibuteLabel;
        }

        return $this->_attributeLabelCache[$cacheKey];
    }

    /**
     * Get link for weight attribute from system configuration
     *
     * @return string link
     */
    public function getWeightLink()
    {
        $pageIdentifier = Mage::getStoreConfig(self::DELIVERY_URL_CONFIG_PATH, $this->getStore());

        return Mage::getUrl($pageIdentifier);
    }

    /**
     * Get weight info as html
     *
     * @param Mage_Catalog_Model_Product $product product object
     *
     * @return string
     */
    public function getWeightInfo($product)
    {
        // $catalogProduct = Mage::getModel('catalog/product')->load($product->getId());
        $weight = $product->getWeight();
        if (!is_numeric($weight)) {
            $weight = 0;
        }

        if ($weight == 0) {
            return;
        }
        $storeId = Mage::app()->getStore()->getId();
        $countrycode = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);

        $weight = Zend_Locale_Format::toNumber($weight, array('precision' => 2, 'locale' => $countrycode));
        $label = $this->getAttributeLabel('weight');

        return '<span class="weight-details">' . $label . ' ' . $weight . 'kg</span>';
    }

    /**
     * Get delivery information as HTML.
     *
     * @param Mage_Catalog_Model_Product $product product object.
     *
     * @return string
     */
    public function getDeliveryInfo($product)
    {
        $delivery = $product->getDeliveryTime();
        if (is_null($delivery) || empty($delivery)) {
            return;
        }
        $label = $this->getAttributeLabel('delivery_time');

        return '<span class="delivery-time-details">' . $label . ' ' . $delivery . '</span>';
    }

    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Get id of current store
     *
     * @return int id
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }
}
