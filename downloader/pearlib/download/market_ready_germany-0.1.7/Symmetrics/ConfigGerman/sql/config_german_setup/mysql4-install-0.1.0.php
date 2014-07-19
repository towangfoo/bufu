<?php
$configData = Mage::getConfig()->getNode('default/config_german')->asArray();

$installer = $this;
$installer->startSetup();

$query = "DELETE FROM {$this->getTable('tax_calculation_rule')};";
$installer->run($query);

$query = <<< EOF
INSERT INTO {$this->getTable('tax_calculation_rule')} VALUES(3, 'Produkte mit 19% MwSt.', 1, 0);
INSERT INTO {$this->getTable('tax_calculation_rule')} VALUES(4, 'Produkte mit 7% MwSt.', 2, 0);
INSERT INTO {$this->getTable('tax_calculation_rule')} VALUES(5, 'Versand mit 19% MwSt.', 3, 0);
EOF;
$installer->run($query);

$query = "DELETE FROM {$this->getTable('tax_class')};";
$installer->run($query);

$query = <<< EOF
INSERT INTO {$this->getTable('tax_class')} VALUES(1, 'Umsatzsteuerpfichtige Güter 19%', 'PRODUCT');
INSERT INTO {$this->getTable('tax_class')} VALUES(2, 'Umsatzsteuerpfichtige Güter 7%', 'PRODUCT');
INSERT INTO {$this->getTable('tax_class')} VALUES(3, 'inkl. Mehrwertsteuer', 'CUSTOMER');
INSERT INTO {$this->getTable('tax_class')} VALUES(4, 'Versand', 'PRODUCT');
EOF;
$installer->run($query);

$query = "DELETE FROM {$this->getTable('tax_calculation_rate')};";
$installer->run($query);

$query = <<< EOF
INSERT INTO {$this->getTable('tax_calculation_rate')} VALUES(3, 'DE', 0, '*', '19% Steuer', 19.0000);
INSERT INTO {$this->getTable('tax_calculation_rate')} VALUES(4, 'DE', 0, '*', '0% Steuer', 0.0000);
INSERT INTO {$this->getTable('tax_calculation_rate')} VALUES(5, 'DE', 0, '*', '7% Steuer', 7.0000);
EOF;
$installer->run($query);

$query = "DELETE FROM {$this->getTable('tax_calculation')};";
$installer->run($query);

$query = <<< EOF
INSERT INTO {$this->getTable('tax_calculation')} VALUES(3, 3, 3, 1);
INSERT INTO {$this->getTable('tax_calculation')} VALUES(3, 5, 3, 4);
INSERT INTO {$this->getTable('tax_calculation')} VALUES(5, 4, 3, 2);
EOF;
$installer->run($query);

$query = "DELETE FROM {$this->getTable('core_config_data')} WHERE `scope`='default' AND `scope_id`=0 AND `path`='catalog/category/root_id';";
$installer->run($query);

$installer->setConfigData('general/locale/code', 'de_DE');
$installer->setConfigData('general/locale/timezone', 'Europe/Berlin');
$installer->setConfigData('currency/options/base', 'EUR');
$installer->setConfigData('currency/options/default', 'EUR');
$installer->setConfigData('currency/options/allow', 'EUR');
$installer->setConfigData('general/country/allow', 'DE');
$installer->setConfigData('catalog/category/root_id', '0');
$installer->setConfigData('general/country/default', 'DE');
$installer->setConfigData('general/country/allow', 'DE');
$installer->setConfigData('general/locale/firstday', '1');
$installer->setConfigData('general/locale/weekend', '0,6');
$installer->setConfigData('design/package/ua_regexp', 'a:0:{}');
$installer->setConfigData('design/head/default_title', $configData['default']['shop_name']);
$installer->setConfigData('design/head/title_prefix', '');
$installer->setConfigData('design/head/title_suffix', '');
$installer->setConfigData('design/head/default_description', $configData['default']['meta_description']);
$installer->setConfigData('design/head/default_keywords', $configData['default']['meta_keywords']);
$installer->setConfigData('design/head/default_robots', $configData['default']['meta_robots']);
$installer->setConfigData('design/head/includes', '');
$installer->setConfigData('design/header/logo_src', 'images/logo.gif');
$installer->setConfigData('design/header/logo_alt', $configData['default']['shop_name']);
$installer->setConfigData('design/header/welcome', $configData['default']['welcome_msg']);
$installer->setConfigData('design/footer/copyright', $configData['default']['copyright']);
$installer->setConfigData('design/footer/absolute_footer', '');
$installer->setConfigData('design/watermark/image_size', '');
$installer->setConfigData('design/watermark/image_position', 'stretch');
$installer->setConfigData('design/watermark/small_image_size', '');
$installer->setConfigData('design/watermark/small_image_position', 'stretch');
$installer->setConfigData('design/watermark/thumbnail_size', '');
$installer->setConfigData('design/watermark/thumbnail_position', 'stretch');
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
$installer->setConfigData('contacts/contacts/enabled', '1');
$installer->setConfigData('contacts/email/recipient_email', $configData['default']['contact_recipient']);
$installer->setConfigData('contacts/email/sender_email_identity', 'general');
$installer->setConfigData('catalog/review/allow_guest', '1');
$installer->setConfigData('catalog/frontend/list_mode', 'grid-list');
$installer->setConfigData('catalog/frontend/grid_per_page_values', '9,15,30');
$installer->setConfigData('catalog/frontend/grid_per_page', '9');
$installer->setConfigData('catalog/frontend/list_per_page_values', '5,10,15,20,25');
$installer->setConfigData('catalog/frontend/list_per_page', '10');
$installer->setConfigData('catalog/productalert/allow_price', '0');
$installer->setConfigData('catalog/productalert/allow_stock', '0');
$installer->setConfigData('catalog/productalert/email_identity', 'general');
$installer->setConfigData('catalog/productalert_cron/frequency', 'D');
$installer->setConfigData('crontab/jobs/catalog_product_alert/schedule/cron_expr', '0 0 * * *');
$installer->setConfigData('crontab/jobs/catalog_product_alert/run/model', 'productalert/observer::process');
$installer->setConfigData('catalog/productalert_cron/time', '00,00,00');
$installer->setConfigData('catalog/productalert_cron/error_email', '');
$installer->setConfigData('catalog/productalert_cron/error_email_identity', 'general');
$installer->setConfigData('catalog/recently_products/scope', 'website');
$installer->setConfigData('catalog/recently_products/viewed_count', '5');
$installer->setConfigData('catalog/recently_products/compared_count', '5');
$installer->setConfigData('catalog/price/scope', '0');
$installer->setConfigData('catalog/search/min_query_length', '1');
$installer->setConfigData('catalog/search/max_query_length', '128');
$installer->setConfigData('catalog/search/max_query_words', '10');
$installer->setConfigData('catalog/search/search_type', '1');
$installer->setConfigData('catalog/search/use_layered_navigation_count', '2000');
$installer->setConfigData('catalog/navigation/max_depth', '0');
$installer->setConfigData('catalog/seo/search_terms', '1');
$installer->setConfigData('catalog/seo/site_map', '1');
$installer->setConfigData('catalog/seo/product_url_suffix', '.html');
$installer->setConfigData('catalog/seo/category_url_suffix', '.html');
$installer->setConfigData('catalog/seo/product_use_categories', '1');
$installer->setConfigData('catalog/seo/title_separator', '-');
$installer->setConfigData('catalog/downloadable/order_status', 'complete');
$installer->setConfigData('catalog/downloadable/downloads_number', '0');
$installer->setConfigData('catalog/downloadable/shareable', '0');
$installer->setConfigData('catalog/downloadable/samples_title', 'Samples');
$installer->setConfigData('catalog/downloadable/links_title', 'Links');
$installer->setConfigData('catalog/downloadable/links_target_new_window', '1');
$installer->setConfigData('catalog/downloadable/content_disposition', 'inline');
$installer->setConfigData('catalog/downloadable/disable_guest_checkout', '1');
$installer->setConfigData('sitemap/category/changefreq', 'daily');
$installer->setConfigData('sitemap/category/priority', '0.5');
$installer->setConfigData('sitemap/product/changefreq', 'daily');
$installer->setConfigData('sitemap/product/priority', '1');
$installer->setConfigData('sitemap/page/changefreq', 'daily');
$installer->setConfigData('sitemap/page/priority', '0.25');
$installer->setConfigData('sitemap/generate/enabled', '1');
$installer->setConfigData('sitemap/generate/time', '00,00,00');
$installer->setConfigData('sitemap/generate/frequency', 'D');
$installer->setConfigData('crontab/jobs/sitemap_generate/schedule/cron_expr', '0 0 * * *');
$installer->setConfigData('crontab/jobs/sitemap_generate/run/model', 'sitemap/observer::scheduledGenerateSitemaps');
$installer->setConfigData('sitemap/generate/error_email', '');
$installer->setConfigData('sitemap/generate/error_email_identity', 'general');
$installer->setConfigData('sendfriend/email/enabled', '1');
$installer->setConfigData('sendfriend/email/allow_guest', '0');
$installer->setConfigData('sendfriend/email/max_recipients', '5');
$installer->setConfigData('sendfriend/email/max_per_hour', '5');
$installer->setConfigData('sendfriend/email/check_by', '0');
$installer->setConfigData('newsletter/subscription/un_email_identity', 'support');
$installer->setConfigData('newsletter/subscription/success_email_identity', 'general');
$installer->setConfigData('newsletter/subscription/confirm_email_identity', 'support');
$installer->setConfigData('newsletter/subscription/confirm', '1');
$installer->setConfigData('newsletter/sending/set_return_path', '0');
$installer->setConfigData('customer/online_customers/online_minutes_interval', '');
$installer->setConfigData('customer/account_share/scope', '1');
$installer->setConfigData('customer/create_account/default_group', '1');
$installer->setConfigData('customer/create_account/email_domain', $configData['default']['email_domain']);
$installer->setConfigData('customer/create_account/email_identity', 'general');
$installer->setConfigData('customer/create_account/confirm', '0');
$installer->setConfigData('customer/password/forgot_email_identity', 'support');
$installer->setConfigData('customer/address/street_lines', '2');
$installer->setConfigData('customer/address/prefix_show', 'req');
$installer->setConfigData('customer/address/prefix_options', $configData['default']['prefix_options']);
$installer->setConfigData('customer/address/middlename_show', '0');
$installer->setConfigData('customer/address/suffix_show', '');
$installer->setConfigData('customer/address/suffix_options', '');
$installer->setConfigData('customer/address/dob_show', '');
$installer->setConfigData('customer/address/taxvat_show', '');
$installer->setConfigData('wishlist/general/active', '1');
$installer->setConfigData('wishlist/email/email_identity', 'general');
$installer->setConfigData('sales_email/order/enabled', '1');
$installer->setConfigData('sales_email/order/identity', 'sales');
$installer->setConfigData('sales_email/order/copy_to', '');
$installer->setConfigData('sales_email/order/copy_method', 'bcc');
$installer->setConfigData('sales_email/order_comment/enabled', '1');
$installer->setConfigData('sales_email/order_comment/identity', 'sales');
$installer->setConfigData('sales_email/order_comment/copy_to', '');
$installer->setConfigData('sales_email/order_comment/copy_method', 'bcc');
$installer->setConfigData('sales_email/invoice/enabled', '1');
$installer->setConfigData('sales_email/invoice/identity', 'sales');
$installer->setConfigData('sales_email/invoice/copy_to', '');
$installer->setConfigData('sales_email/invoice/copy_method', 'bcc');
$installer->setConfigData('sales_email/invoice_comment/enabled', '1');
$installer->setConfigData('sales_email/invoice_comment/identity', 'sales');
$installer->setConfigData('sales_email/invoice_comment/copy_to', '');
$installer->setConfigData('sales_email/invoice_comment/copy_method', 'bcc');
$installer->setConfigData('sales_email/shipment/enabled', '1');
$installer->setConfigData('sales_email/shipment/identity', 'sales');
$installer->setConfigData('sales_email/shipment/copy_to', '');
$installer->setConfigData('sales_email/shipment/copy_method', 'bcc');
$installer->setConfigData('sales_email/shipment_comment/enabled', '1');
$installer->setConfigData('sales_email/shipment_comment/identity', 'sales');
$installer->setConfigData('sales_email/shipment_comment/copy_to', '');
$installer->setConfigData('sales_email/shipment_comment/copy_method', 'bcc');
$installer->setConfigData('sales_email/creditmemo/enabled', '1');
$installer->setConfigData('sales_email/creditmemo/identity', 'sales');
$installer->setConfigData('sales_email/creditmemo/copy_to', '');
$installer->setConfigData('sales_email/creditmemo/copy_method', 'bcc');
$installer->setConfigData('sales_email/creditmemo_comment/enabled', '1');
$installer->setConfigData('sales_email/creditmemo_comment/identity', 'sales');
$installer->setConfigData('sales_email/creditmemo_comment/copy_to', '');
$installer->setConfigData('sales_email/creditmemo_comment/copy_method', 'bcc');
$installer->setConfigData('tax/classes/shipping_tax_class', '4');
$installer->setConfigData('tax/calculation/based_on', 'origin');
$installer->setConfigData('tax/calculation/price_includes_tax', '1');
$installer->setConfigData('tax/calculation/shipping_includes_tax', '0');
$installer->setConfigData('tax/calculation/apply_after_discount', '0');
$installer->setConfigData('tax/calculation/discount_tax', '1');
$installer->setConfigData('tax/calculation/apply_tax_on', '0');
$installer->setConfigData('tax/defaults/country', 'DE');
$installer->setConfigData('tax/defaults/region', '79');
$installer->setConfigData('tax/defaults/postcode', $configData['default']['zip']);
$installer->setConfigData('tax/display/column_in_summary', '2');
$installer->setConfigData('tax/display/full_summary', '1');
$installer->setConfigData('tax/display/shipping', '2');
$installer->setConfigData('tax/display/type', '2');
$installer->setConfigData('tax/display/zero_tax', '1');
$installer->setConfigData('tax/weee/enable', '0');
$installer->setConfigData('tax/weee/display_list', '0');
$installer->setConfigData('tax/weee/display', '0');
$installer->setConfigData('tax/weee/display_sales', '0');
$installer->setConfigData('tax/weee/display_email', '0');
$installer->setConfigData('tax/weee/discount', '0');
$installer->setConfigData('tax/weee/apply_vat', '0');
$installer->setConfigData('tax/weee/include_in_subtotal', '0');
$installer->setConfigData('checkout/options/onepage_checkout_disabled', '0');
$installer->setConfigData('checkout/options/guest_checkout', '1');
$installer->setConfigData('checkout/options/enable_agreements', '1');
$installer->setConfigData('checkout/cart/delete_quote_after', '30');
$installer->setConfigData('checkout/cart/redirect_to_cart', '1');
$installer->setConfigData('checkout/cart/grouped_product_image', 'itself');
$installer->setConfigData('checkout/cart/configurable_product_image', 'parent');
$installer->setConfigData('checkout/cart_link/use_qty', '0');
$installer->setConfigData('checkout/sidebar/display', '1');
$installer->setConfigData('checkout/sidebar/count', '3');
$installer->setConfigData('checkout/payment_failed/reciever', 'general');
$installer->setConfigData('checkout/payment_failed/identity', 'general');
$installer->setConfigData('checkout/payment_failed/copy_to', '');
$installer->setConfigData('checkout/payment_failed/copy_method', 'bcc');
$installer->setConfigData('shipping/origin/country_id', 'DE');
$installer->setConfigData('shipping/origin/region_id', '79');
$installer->setConfigData('shipping/origin/postcode', $configData['default']['zip']);
$installer->setConfigData('shipping/origin/city', $configData['default']['city']);
$installer->setConfigData('shipping/option/checkout_multiple', '1');
$installer->setConfigData('shipping/option/checkout_multiple_maximum_qty', '100');
$installer->setConfigData('dev/restrict/allow_ips', '');
$installer->setConfigData('dev/debug/profiler', '0');
$installer->setConfigData('dev/translate_inline/active', '0');
$installer->setConfigData('dev/translate_inline/active_admin', '0');
$installer->setConfigData('dev/log/active', '0');
$installer->setConfigData('dev/log/file', 'system.log');
$installer->setConfigData('dev/log/exception_file', 'exception.log');
$installer->setConfigData('dev/js/deprecation', '0');
$installer->setConfigData('sales/totals_sort/subtotal', '10');
$installer->setConfigData('sales/totals_sort/discount', '20');
$installer->setConfigData('sales/totals_sort/shipping', '30');
$installer->setConfigData('sales/totals_sort/weee', '50');
$installer->setConfigData('sales/totals_sort/tax', '90');
$installer->setConfigData('sales/totals_sort/grand_total', '100');
$installer->setConfigData('sales/reorder/allow', '1');
$installer->setConfigData('sales/identity/address', $configData['default']['invoice_address']);
$installer->setConfigData('sales/minimum_order/active', '0');
$installer->setConfigData('sales/minimum_order/amount', '');
$installer->setConfigData('sales/minimum_order/description', '');
$installer->setConfigData('sales/minimum_order/error_message', '');
$installer->setConfigData('sales/minimum_order/multi_address', '0');
$installer->setConfigData('sales/minimum_order/multi_address_description', '');
$installer->setConfigData('sales/minimum_order/multi_address_error_message', '');
$installer->setConfigData('sales/gift_messages/allow_order', '0');
$installer->setConfigData('sales/gift_messages/allow_items', '0');
$installer->setConfigData('catalog/custom_options/date_fields_order', 'd,m,y');
$installer->setConfigData('catalog/custom_options/time_format', '24h');
$installer->setConfigData('google/googlebase/target_country', 'DE');
$installer->setConfigData('payment/free/title', 'Keine Zahlungsinformationen benötigt');
$installer->setConfigData('payment/checkmo/title', 'Scheck / Zahlungsanweisung');

$errorMsg = 'Diese Versandmethode ist derzeit nicht verfügbar. Bitte kontaktieren Sie uns wenn sie diese Methode verwenden möchten.';

$installer->setConfigData('carriers/dhl/specificerrmsg', $errorMsg);
$installer->setConfigData('carriers/ups/specificerrmsg', $errorMsg);
$installer->setConfigData('carriers/usps/specificerrmsg', $errorMsg);
$installer->setConfigData('carriers/fedex/specificerrmsg', $errorMsg);
$installer->setConfigData('carriers/flatrate/specificerrmsg', $errorMsg);
$installer->setConfigData('carriers/tablerate/specificerrmsg', $errorMsg);
$installer->setConfigData('carriers/freeshipping/specificerrmsg', $errorMsg);

$installer->addAttribute('catalog_product', 'weight', array(
    'label' => 'Gewicht',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => true,
    'user_defined' => true,
    'searchable' => true,
    'comparable' => true,
    'visible_on_front' => true,
    'visible_in_advanced_search' => true,
    'default' => '1'
));

$installer->endSetup();