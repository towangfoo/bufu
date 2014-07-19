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
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$attributeType = 'catalog_product';
$code = 'generate_meta';
$label = 'Generate Meta Tags';

$data = array(
    'label' => $label,
    'global' => 0,
    'is_global' => 0,
    'input' => 'boolean',
    'frontend_label' => 'Generate meta data',
    'visible' => true, // useless for 1.4.0.0
    'required' => true,
    'user_defined' => true,
    'is_searchable' => false,
    'is_visible_in_advanced_search' => false,
    'is_comparable' => false,
    'is_visible_on_front' => false,
    'used_in_product_listing' => false,
    'html_allowed_on_front' => false,
    'note' => 'Auto generate new meta tags from product name and categories',
    'default_value' => 1
);

$installer->addAttribute($attributeType, $code, $data);

$attribute = Mage::getModel('catalog/resource_eav_attribute');
$attribute->loadByCode($attributeType, $code);

if ($attribute->getId()) {
    $attribute->setDefaultValue($data['default_value']);
    $attribute->setIsGlobal($data['is_global']);

    try {
        $attribute->save();
    } catch (Exception $e) {
        Mage::log($e->getMessage());
    }
} else {
    Mage::log('Attribute could not be loaded');
}

$installer->endSetup();