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
 * @package   Symmetrics_ConfigGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Eugen Gitin <eg@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

$configData = Mage::getConfig()->getNode('default/config_german')->asArray();

$installer = $this;
$installer->startSetup();

$taxTables = array(
    'tax_calculation_rule',
    'tax_class',
    'tax_calculation_rate',
    'tax_calculation'
);

foreach ($taxTables as $table) {
    /* truncate table (not delete) */
    $this->_conn->delete($this->getTable($table));
    $data = $this->getInsertData($table);

    /* insert new tax settings */
    foreach ($data as $insert) {
        $this->_conn->insert($this->getTable($table), $insert);
    }
}

$condition = 'scope = \'default\' AND scope_id = 0 AND path = \'catalog/category/root_id\'';
$this->_conn->delete($this->getTable('core_config_data'), $condition);

/* set different config data */
$installer->setConfigData('general/locale/code', 'de_DE');
$installer->setConfigData('general/locale/timezone', 'Europe/Berlin');
$installer->setConfigData('general/store_information/name', $configData['default']['shop_name']);
$installer->setConfigData('general/store_information/phone', $configData['default']['shop_phone']);
$installer->setConfigData('general/store_information/address', $configData['default']['invoice_address']);
$installer->setConfigData('currency/options/base', 'EUR');
$installer->setConfigData('currency/options/default', 'EUR');
$installer->setConfigData('currency/options/allow', 'EUR');
$installer->setConfigData('general/country/allow', 'DE');
$installer->setConfigData('catalog/category/root_id', '0');
$installer->setConfigData('catalog/custom_options/date_fields_order', 'd,m,y');
$installer->setConfigData('catalog/custom_options/time_format', '24h');
$installer->setConfigData('general/country/default', 'DE');
$installer->setConfigData('general/country/allow', 'DE');
$installer->setConfigData('general/locale/firstday', '1');
$installer->setConfigData('general/locale/weekend', '0,6');
$installer->setConfigData('web/secure/use_in_frontend', '1');
// NOTE: The following session configuration highly depends on hosting and
//       productive environment. Please, use them with care and uncomment
//       only if you know what you are doing. They are still recommended for
//       security reasons, but beware that this must be tested individually.
//$installer->setConfigData('web/session/use_remote_addr', '1');
//$installer->setConfigData('web/session/use_http_via', '1');
//$installer->setConfigData('web/session/use_http_x_forwarded_for', '1');
//$installer->setConfigData('web/session/use_http_user_agent', '1');
$installer->setConfigData('web/cookie/cookie_lifetime', '0');
$installer->setConfigData('design/head/default_title', $configData['default']['shop_name']);
$installer->setConfigData('design/head/default_description', $configData['default']['meta_description']);
$installer->setConfigData('design/head/default_keywords', $configData['default']['meta_keywords']);
$installer->setConfigData('design/head/default_robots', $configData['default']['meta_robots']);
$installer->setConfigData('design/header/logo_alt', $configData['default']['shop_name']);
$installer->setConfigData('design/header/welcome', $configData['default']['welcome_msg']);
$installer->setConfigData('design/footer/copyright', $configData['default']['copyright']);
$installer->setConfigData('trans_email/ident_general/name', $configData['default']['contact_name']);
$installer->setConfigData('trans_email/ident_general/email', $configData['default']['contact_email']);
$installer->setConfigData('trans_email/ident_sales/name', $configData['default']['contact_sales_name']);
$installer->setConfigData('trans_email/ident_sales/email', $configData['default']['contact_sales_email']);
$installer->setConfigData('trans_email/ident_support/name', $configData['default']['contact_support_name']);
$installer->setConfigData('trans_email/ident_support/email', $configData['default']['contact_support_email']);
$installer->setConfigData('trans_email/ident_custom1/name', $configData['default']['contact_custom1_name']);
$installer->setConfigData('trans_email/ident_custom1/email', $configData['default']['contact_custom1_email']);
$installer->setConfigData('trans_email/ident_custom2/name', $configData['default']['contact_custom2_name']);
$installer->setConfigData('trans_email/ident_custom2/email', $configData['default']['contact_custom2_email']);
$installer->setConfigData('contacts/email/recipient_email', $configData['default']['contact_recipient']);
$installer->setConfigData('contacts/email/sender_email_identity', 'general');
$installer->setConfigData('sitemap/generate/enabled', '1');
$installer->setConfigData('sendfriend/email/check_by', '1');
$installer->setConfigData('newsletter/subscription/success_email_identity', 'support');
$installer->setConfigData('newsletter/subscription/confirm', '1');
$installer->setConfigData('customer/create_account/email_domain', $configData['default']['email_domain']);
$installer->setConfigData('customer/address/prefix_show', 'req');
$installer->setConfigData('customer/address/prefix_options', $configData['default']['prefix_options']);
$installer->setConfigData('sales/identity/address', $configData['default']['invoice_address']);
$installer->setConfigData('tax/classes/shipping_tax_class', '4');
$installer->setConfigData('tax/calculation/based_on', 'billing');
$installer->setConfigData('tax/calculation/price_includes_tax', '1');
$installer->setConfigData('tax/calculation/shipping_includes_tax', '1');
$installer->setConfigData('tax/calculation/apply_after_discount', '1');
$installer->setConfigData('tax/calculation/discount_tax', '1');
$installer->setConfigData('tax/defaults/country', 'DE');
$installer->setConfigData('tax/defaults/region', '79');
$installer->setConfigData('tax/defaults/postcode', $configData['default']['zip']);
$installer->setConfigData('tax/display/type', '2');
$installer->setConfigData('tax/display/shipping', '2');
$installer->setConfigData('tax/cart_display/subtotal', '2');
$installer->setConfigData('tax/cart_display/full_summary', '1');
$installer->setConfigData('tax/cart_display/shipping', '2');
$installer->setConfigData('tax/cart_display/price', '2');
$installer->setConfigData('tax/cart_display/zero_tax', '1');
$installer->setConfigData('tax/cart_display/grandtotal', '0');
$installer->setConfigData('tax/sales_display/price', '2');
$installer->setConfigData('tax/sales_display/subtotal', '2');
$installer->setConfigData('tax/sales_display/shipping', '2');
$installer->setConfigData('tax/sales_display/grandtotal', '0');
$installer->setConfigData('tax/sales_display/full_summary', '1');
$installer->setConfigData('tax/sales_display/zero_tax', '1');
$installer->setConfigData('checkout/options/enable_agreements', '1');
$installer->setConfigData('checkout/cart_link/use_qty', '0');
$installer->setConfigData('shipping/origin/country_id', 'DE');
$installer->setConfigData('shipping/origin/region_id', '79');
$installer->setConfigData('shipping/origin/postcode', $configData['default']['zip']);
$installer->setConfigData('shipping/origin/city', $configData['default']['city']);
$installer->setConfigData('google/googlebase/target_country', 'DE');
$installer->setConfigData('payment/free/title', 'Keine Zahlungsinformationen benötigt');
$installer->setConfigData('payment/checkmo/title', 'Scheck / Zahlungsanweisung');
/* disallow reorder */
$installer->setConfigData('sales/reorder/allow', '0');

/* shipping method codes */
$shippingMethods = array(
    'dhl',
    'ups',
    'usps',
    'fedex',
    'flatrate',
    'tablerate',
    'freeshipping'
);

$errorMsg = 'Diese Versandmethode ist derzeit nicht verfügbar. ';
$errorMsg .= 'Bitte kontaktieren Sie uns wenn sie diese Methode verwenden möchten.';

/* set default error message for shipping methods */
foreach ($shippingMethods as $method) {
    $installer->setConfigData('carriers/' . $method . '/specificerrmsg', $errorMsg);
}

/* add weight attribute */
$attributeParameters = array(
    'label' => 'Gewicht',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,    
    'required' => true,
    'user_defined' => true,    
    'default' => '1'
);
$installer->addAttribute('catalog_product', 'weight', $attributeParameters);
// Unfortunately the following fields are not processed by addAttribute method.
// The code bellow will update default values, used in addAttribute.
$attributeParameters = array(
    'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'is_visible' => true,
    'is_filterable' => true,
    'is_searchable' => true,
    'is_comparable' => true,
    'is_visible_on_front' => true,
    'is_visible_in_advanced_search' => true,
    'used_in_product_listing' => true,    
);
$installer->updateAttribute('catalog_product', 'weight', $attributeParameters);

$installer->endSetup();
