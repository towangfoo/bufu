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
 * @package   Symmetrics_SetMeta
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @copyright 2010-2012 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * This observer model generates meta data from product name and categories
 *
 * @category  Symmetrics
 * @package   Symmetrics_SetMeta
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eric Reiche <er@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2010-2012 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_SetMeta_Model_Observer extends Varien_Object
{
    /**
     * @var Symmetrics_SetMeta_Helper_Data $_helper Cached helper object.
     */
    protected $_helper;

    /**
     * Update meta tags on product save.
     *
     * @param Varien_Event_Observer $observer Event observer instance.
     *
     * @return void
     */
    public function handleProductSaveBefore($observer)
    {
        $helper = $this->_getHelper();
        $product = $observer->getEvent()->getProduct();

        if (!$product instanceof Mage_Catalog_Model_Product
            /* || !$product->getId() */
        ) {
            throw new Exception('Product not set.');
        }
        if ($product->getGenerateMeta() != '1') {
            return;
        }

        $helper->updateMetaData($product);
    }

    /**
     * Is called for mass editing of products.
     * WARNING!
     * This code isn't used anymore, it doesn't work this way since Mangeto 1.5.
     * Feel free to adapt this to your needs.
     *
     * @return void
     */
    public function handleProductMassEdit()
    {
        $helper = $this->_getHelper();
        // Take product id list from session.
        $productsIds = Mage::getSingleton('adminhtml/session')->getProductIds();

        // Ignore non-array values.
        if (!is_array($productsIds)) {
            return;
        }

        $attributesData = Mage::app()->getRequest()->getParam('attributes');

        if (!isset($attributesData['generate_meta'])
            || $attributesData['generate_meta'] != 1
        ) {
            return;
        }

        // obtain collection of products by store id and product ids.
        $products = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId($helper->getStoreId())
            ->addAttributeToSelect('name')
            ->addIdFilter($productsIds)
            ->load();

        // update meta data for all of them
        foreach ($products as $product) {
            $product = Mage::getModel('catalog/product');
            $product = $product->setStoreId($helper->getStoreId())
                ->load($product->getId());
            $helper->updateMetaData($product);
        }
    }

    /**
     * Load helper object.
     *
     * @return Symmetrics_SetMeta_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('setmeta');
        }

        return $this->_helper;
    }
}
