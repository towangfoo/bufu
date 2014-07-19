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
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Symmetrics_TweaksGerman_Block_Tax
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Block_Tax extends Mage_Core_Block_Abstract
{
    /**
     * @const SHOW_TAX_INFO System configuration path to wether display tax info or not.
     */
    const SHOW_TAX_INFO = 'catalog/frontend/display_taxinfo';
    
    /**
     * @const SHOW_SHIPPING_COSTS System configuration path to wether display shipping costs or not.
     */
    const SHOW_SHIPPING_COSTS = 'catalog/frontend/display_shippingcosts';
    /**
     * @const SHOW_SHIPPING_COSTS System configuration path to wether display shipping costs or not.
     */
    const SHIPPING_URL = 'tax/display/shippingurl';
    
    /**
     * Get shipping link
     *
     * @return string url appended to tax info
     */
    protected static function _getShippingLink()
    {
        $displayShipping = Mage::getStoreConfig(self::SHOW_SHIPPING_COSTS);
        
        if ($displayShipping == '0') {
            return '';
        } else if ($displayShipping == 'incl') {
            $pattern = Mage::helper('core')->__('Incl. <a href="%1$s">shipping</a>');
        } else {
            $pattern = Mage::helper('core')->__('Excl. <a href="%1$s">shipping</a>');   
        }
        
        $value = Mage::getUrl(Mage::getStoreConfig(self::SHIPPING_URL));
        $shippingLink = sprintf($pattern, $value);

        return $shippingLink;
    }

    /**
     * Compute tax info.
     *
     * @param Mage_Catalog_Model_Product $product Product instance.
     *
     * @return string tax info
     */
    protected static function _getTaxInfo($product)
    {
        $showPercentage = true;
        $tax = Mage::helper('tax');
        $helper = Mage::helper('tweaksgerman');
        if ($product->getTypeId() == 'bundle') {
            $showPercentage = false;
        }
        if ($showPercentage) {
            $taxPercent = $product->getTaxPercent();
            $locale = Mage::app()->getLocale()->getLocaleCode();
            // format number using shop locale
            $taxPercent = Zend_Locale_Format::getNumber($taxPercent, array('locale' => $locale));
            if ($taxPercent == 0) {
                $showPercentage = false;
            }
        }
        
        if ($showPercentage && Mage::getStoreConfigFlag(self::SHOW_TAX_INFO)) {
            if ($tax->displayPriceIncludingTax()) {
                $taxInfo = sprintf($helper->__('Incl. %1$s%% VAT'), $taxPercent);
            } else {
                $taxInfo = sprintf($helper->__('Excl. %1$s%% VAT'), $taxPercent);
            }
        } else {
            if ($tax->displayPriceIncludingTax()) {
                $taxInfo = $helper->__('Incl. VAT');
            } else {
                $taxInfo = $helper->__('Excl. VAT');
            }
        }

        return $taxInfo;
    }

    /**
     * Get tax info as HTML.
     *
     * @param Mage_Catalog_Model_Product $product Product instance.
     *
     * @return string|null result is null if product type is configured or combined.
     */
    public static function getTaxInfo($product)
    {
        if ($product->getCanShowPrice() === false) {
            return null;
        }

        $productTypeId = $product->getTypeId();
        if ($productTypeId == 'combined') {
            // ignore Symmetrics_CombinedProduct
            return null;
        }
        
        Mage::getStoreConfigFlag(self::SHOW_TAX_INFO);

        // produce tax info
        $ignoreTypeIds = array('virtual', 'downloadable');
        $taxInfo = self::_getTaxInfo($product);
        $shippingLink = self::_getShippingLink();
        if (in_array($productTypeId, $ignoreTypeIds) || empty($shippingLink)) {
            $result = '<span class="tax-details">' . $taxInfo . '</span>';
        } else {
            // product type is not in ingore list, so we append a shipping link
            $result = '<span class="tax-details">' . $taxInfo . ', ' . $shippingLink . '</span>';
        }

        return $result;
    }
}
