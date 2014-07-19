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
 * This setup script will add the delivery time attribute
 *
 * @category  Symmetrics
 * @package   Symmetrics_DeliveryTime
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Sergej Braznikov <sb@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
$installer = $this;

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$initialData = array(
    'label' => 'Lieferzeit', // frontend_label
    'input' => 'text',       // frontend_input
    'required' => false,     // is_required
    'user_defined' => true,  // is_user_defined
    'default' => '2-3 Tage', // default_value
);

$installer->addAttribute('catalog_product', 'delivery_time', $initialData);

// Unfortunately the following fields are not processed by addAttribute method.
// The code bellow will update default values, used in addAttribute.
$additionalData = array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'is_visible' => true,
    'is_filterable' => true,
    'is_searchable' => true,
    'is_comparable' => true,
    'is_visible_on_front' => true,
    'is_visible_in_advanced_search' => true,
    'used_in_product_listing' => true,
    'is_html_allowed_on_front' => true,
);

$installer->updateAttribute('catalog_product', 'delivery_time', $additionalData);

$installer->endSetup();