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
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Torsten Walluhn <torsten.walluhn@cgi.com>
 * @copyright 2011-2012 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Observer model.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Torsten Walluhn <torsten.walluhn@cgi.com>
 * @copyright 2011-2012 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Model_Observer
{
    /**
     * @const CONFIGURATION_ORDER_IP
     */
    const CONFIGURATION_ORDER_IP = 'customer/privacy/order_ip';

    /**
     * @const
     */
    const CART_PRODUCT_ATTRIBUTE = 'checkout/cart/product_attribute';

    /**
     * Remove IP on checkout submit.
     *
     * @param Varien_Event_Observer $observer Observer object to get the order from.
     *
     * @return void
     */
    public function checkoutSubmit($observer)
    {
        if (!Mage::getStoreConfigFlag(self::CONFIGURATION_ORDER_IP)) {
            $observer->getOrder()->setRemoteIp('0');
        }
    }

    /**
     * Add a additional price information.
     *
     * @param Varien_Event_Observer $observer Observer object to get a block by
     *                                        toHtml method call.
     *
     * @return void
     */
    public function toHtmlAfter($observer)
    {
        $block = $observer->getBlock();
        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        $handles = $block->getLayout() ?
            $block->getLayout()->getUpdate()->getHandles() : array();

        $additionalInfoFlag = false;

        // Wishlist
        if (
            $block->getIdSuffix() == '-wishlist'
            || in_array('wishlist_index_index', $handles)
        ) {
            if ($block instanceof Mage_Wishlist_Block_Render_Item_Price) {
                $additionalInfoFlag = true;
            }

        // Bundle product type
        } elseif (
            $block instanceof Mage_Bundle_Block_Catalog_Product_Price
            && trim($html)
            && (
                $block->getDisplayMinimalPrice()
                || $block->getNameInLayout() == 'bundle.prices'
                || $block->getIdSuffix() == '_clone'
            )
        ) {
            $additionalInfoFlag = true;

        // Other product types
        } elseif (get_class($block) == 'Mage_Catalog_Block_Product_Price' && trim($html)) {
            $additionalInfoFlag = true;
        }

        if ($additionalInfoFlag) {
            $info = Mage::app()->getLayout()->createBlock('tweaksgerman/info')
                ->setProduct($block->getProduct())
                ->getInfo();
            $transport->setHtml($html . $info);
        }
    }

    /**
     * Add the selected attribute to the quote product collection for displaying it
     * on the quote checkout summary page.
     *
     * @param Varien_Event_Observer $observer Observer object to add the attribute.
     *
     * @return void
     */
    public function addAttributesToCart($observer)
    {
        if (Mage::getStoreConfig(self::CART_PRODUCT_ATTRIBUTE) !=
            Symmetrics_TweaksGerman_Model_System_Config_Source_Product_Attribute::NO_TEXT) {
            $observer->getAttributes()->setData(Mage::getStoreConfig(self::CART_PRODUCT_ATTRIBUTE), '');
        }
    }
}
