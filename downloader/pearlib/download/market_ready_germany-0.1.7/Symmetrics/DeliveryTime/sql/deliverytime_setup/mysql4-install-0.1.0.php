<?php
$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$installer->addAttribute('catalog_product', 'delivery_time', array(
    'label' => 'Lieferzeit',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'searchable' => true,
    'comparable' => true,
    'visible_on_front' => true,
    'visible_in_advanced_search' => true,
    'default' => '2-3 Tage',
));
$installer->endSetup();
