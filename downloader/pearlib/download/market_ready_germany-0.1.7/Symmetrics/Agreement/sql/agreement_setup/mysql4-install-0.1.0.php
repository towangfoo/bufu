<?php

$installer = $this;
$installer->startSetup();

// creating agreements for the order process. they will be shown at the end of the checkout.
$data['name'] = 'AGB';
$data['content'] = '{{block type="cms/block" block_id="sym_agb"}}';
$data['checkbox_text'] = 'Ich habe die Allgemeinen Geschäftsbedingungen gelesen und stimme diesen ausdrücklich zu.';
$this->createAgreement($data);

$data['name'] = 'Widerrufsbelehrung';
$data['content'] = '{{block type="cms/block" block_id="sym_widerruf"}}';
$data['checkbox_text'] = 'Ich habe die Widerrufsbelehrung gelesen.';
$this->createAgreement($data);

unset($data);

// creating agreements pages
$data['title'] = 'AGB';
$data['root_template'] = 'one_column';
$data['identifier'] = 'agb';
$data['content'] = '{{block type="cms/block" block_id="sym_agb"}}';
$this->createCmsPage($data);

$data['title'] = 'Widerrufsbelehrung';
$data['root_template'] = 'one_column';
$data['identifier'] = 'widerruf';
$data['content'] = '{{block type="cms/block" block_id="sym_widerruf"}}';
$this->createCmsPage($data);

// checking if blocks sym_agb and sym_widerruf are already in the db. they 
// could be created by the customer or by the Symmetrics_ConfigGermanTexts module
$agreementBlock = $installer->getConnection()->fetchRow("
    SELECT COUNT(block_id) AS counter FROM {$installer->getTable('cms_block')} WHERE identifier='sym_agb'
");

if ($agreementBlock['counter'] == 0) {
    // if block not found -> create block
    $data['title'] = 'AGB';
    $data['identifier'] = 'sym_agb';
    $data['content'] = '<h2>AGB</h2><p>[MUSTER]</p>';
    $this->createCmsBlock($data);
}

$agreementBlock = $installer->getConnection()->fetchRow("
    SELECT COUNT(block_id) AS counter FROM {$installer->getTable('cms_block')} WHERE identifier='sym_widerruf'
");

if ($agreementBlock['counter'] == 0) {
    // if block not found -> create block
    $data['title'] = 'Widerrufsbelehrung';
    $data['identifier'] = 'sym_widerruf';
    $data['content'] = '<h2>Widerrufsbelehrung</h2><p>[MUSTER]</p>';
    $this->createCmsBlock($data);
}

$installer->setConfigData('checkout/options/enable_agreements', '1');

$installer->endSetup();