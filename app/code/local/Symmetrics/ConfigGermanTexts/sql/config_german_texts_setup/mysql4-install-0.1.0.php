<?php
/**
 * @category Symmetrics
 * @package Symmetrics_ConfigGermanTexts
 * @author symmetrics gmbh <info@symmetrics.de>, Eugen Gitin <eg@symmetrics.de>, Sergej Braznikov <sb@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

// execute pages
foreach ($this->getConfigPages() as $name => $data) {
    if ($data['execute'] == 1) {
        $this->createCmsPage($data);
    }
}

// execute blocks
foreach ($this->getConfigBlocks() as $name => $data) {
    if ($data['execute'] == 1) {
        if ($name == 'symmetrics_footerlinks') {
            $this->updateFooterLinksBlock($data);
        }
        else {
            $this->createCmsBlock($data);
        }
    }
}

// execute emails
foreach ($this->getConfigEmails() as $name => $data) {
    if ($data['execute'] == 1) {
        $this->createEmail($data);
    }
}

// set some translations
$query = <<< EOF
    INSERT INTO {$this->getTable('core_translate')} (`string`, `store_id`, `translate`, `locale`) VALUES
    ('Mage_Sales::Logo for PDF print-outs (200x50)', 0, 'Logo for PDF print-outs', 'de_DE'),
    ('Mage_Checkout::You will receive an order confirmation email with details of your order and a link to track its progress.', 0, 'Sie erhalten in kürze eine Bestellbestätigung per Email. Wenn Sie aktuell eingeloggt sind und einen Kunden Account in unserem Shop haben, dann klicken Sie unten auf den Link, um eine Kopie Ihrer Bestellbestätigung zu drucken.', 'de_DE'),
    ('Mage_Checkout::Click <a href=""%s"" onclick=""this.target=''_blank''"">here to print</a> a copy of your order confirmation.', 0, '<a href=""%s"" onclick=""this.target=''_blank''"">Bestellbestätigung drucken</a>', 'de_DE'),
    ('Mage_Checkout::Your order # is: <a href=""%s"">%s</a>', 0, 'Ihre Auftragsnummer lautet: <a href=""%s"">%s</a>', 'de_DE'),
    ('Mage_Newsletter::Sign up for our newsletter:', 0, 'Abonnieren Sie unseren Newsletter (Abmeldung jederzeit möglich):', 'de_DE'),
    ('Mage_Customer::Sign Up for Newsletter', 0, 'In den Newsletter eintragen (Abmeldung jederzeit möglich)', 'de_DE'),
    ('Mage_Catalog::Availability: In stock.', 0, 'Verfügbarkeit: sofort lieferbar', 'de_DE'),
    ('Mage_Sales::Tax', 0, 'Zzgl. MwSt.', 'de_DE'),
    ('Mage_Checkout::Please agree to all Terms and Conditions before placing the order.', 0, 'Bitte bestätigen Sie die AGB und ggf. die Widerrufsbelehrung.', 'de_DE'),
    ('Mage_Checkout::Please agree to all Terms and Conditions before placing the orders.', 0, 'Bitte bestätigen Sie die AGB und ggf. die Widerrufsbelehrung.', 'de_DE'),
    ('Mage_Sales::Subtotal', 0, 'Zwischensumme (Netto)', 'de_DE');
EOF;

$installer->run($query);

// set imprint data
$imprintFields = $this->getConfigImprint();
$installer->setConfigData('general/impressum/company1', $imprintFields['company_name']);
$installer->setConfigData('general/impressum/company2', $imprintFields['company_sub']);
$installer->setConfigData('general/impressum/street', $imprintFields['street']);
$installer->setConfigData('general/impressum/zip', $imprintFields['zip']);
$installer->setConfigData('general/impressum/city', $imprintFields['city']);
$installer->setConfigData('general/impressum/telephone', $imprintFields['phone']);
$installer->setConfigData('general/impressum/email', $imprintFields['email']);
$installer->setConfigData('general/impressum/fax', $imprintFields['fax']);
$installer->setConfigData('general/impressum/web', $imprintFields['homepage']);
$installer->setConfigData('general/impressum/taxnumber', $imprintFields['tax_number']);
$installer->setConfigData('general/impressum/vatid', $imprintFields['sales_tax_id_number']);
$installer->setConfigData('general/impressum/court', $imprintFields['commercial_register']);
$installer->setConfigData('general/impressum/taxoffice', $imprintFields['tax_office']);
$installer->setConfigData('general/impressum/ceo', $imprintFields['holder_names']);
$installer->setConfigData('general/impressum/hrb', $imprintFields['hrb']);
$installer->setConfigData('general/impressum/bankaccount', $imprintFields['bank_account']);
$installer->setConfigData('general/impressum/bankcodenumber', $imprintFields['bank_id_code']);
$installer->setConfigData('general/impressum/bankaccountowner', $imprintFields['bank_account_owner']);
$installer->setConfigData('general/impressum/bankname', $imprintFields['bank_name']);
$installer->setConfigData('general/impressum/swift', $imprintFields['bank_swift']);
$installer->setConfigData('general/impressum/iban', $imprintFields['bank_iban']);
$installer->setConfigData('general/impressum/shopname', $imprintFields['shop_name']);
$installer->setConfigData('general/impressum/rechtlicheregelungen', $this->getTemplateContent($imprintFields['legal_info']));

// set some misc data
$installer->setConfigData('sales_pdf/invoice/put_order_id', '1');
$installer->setConfigData('sales_pdf/invoice/maturity', $imprintFields['invoice_maturity']);
$installer->setConfigData('sales_pdf/invoice/note', $imprintFields['invoice_note']);
$installer->setConfigData('sales_pdf/shipment/put_order_id', '1');
$installer->setConfigData('sales_pdf/creditmemo/put_order_id', '1');
$installer->setConfigData('sales/identity/logo', 'default/logo.jpg');
$installer->setConfigData('sales_pdf/invoice/customeridprefix', $imprintFields['invoice_customer_prefix']);
$installer->setConfigData('tax/display/shippingurl', 'lieferung');
