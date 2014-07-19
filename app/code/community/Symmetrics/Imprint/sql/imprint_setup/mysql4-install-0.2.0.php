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
 * @package   Symmetrics_Imprint
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Sergej Braznikov <sb@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
 
$installer = $this;
$installer->startSetup();

$prefixNew = 'general/imprint/';
$prefixOld = 'general/impressum/';
    
$configMap = array (
    'shopname' => 'shop_name',
    'company1' => 'company_first',
    'company2' => 'company_second',
    'street' => 'street',
    'zip' => 'zip',
    'city' => 'city',
    'telephone' => 'telephone',
    'fax' => 'fax',
    'email' => 'email',
    'web' => 'web',
    'taxnumber' => 'tax_number',
    'vatid' => 'vat_id',
    'court' => 'court',
    'taxoffice' => 'financial_office',
    'ceo' => 'ceo',
    'hrb' => 'register_number',
    'bankaccount' => 'bank_account',
    'bankcodenumber' => 'bank_code_number',
    'bankaccountowner' => 'bank_account_owner',
    'bankname' => 'bank_name',
    'swift' => 'swift',
    'iban' => 'iban',
    'rechtlicheregelungen' => 'business_rules',
);

$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->addFieldToFilter('path', array('like' => $prefixOld . '%'))
    ->load();

foreach ($configCollection as $configValue) {
    $scope = $configValue->getScope();
    $scopeId = $configValue->getScopeId();
    $oldPath = str_replace($prefixOld, '', $configValue->getPath());
    $value = $configValue->getValue();
    if (array_key_exists($oldPath, $configMap)) {
        $newPath = $configMap[$oldPath];
    } else {
        $newPath = $oldPath;
    }
    
    $installer->setConfigData($prefixNew . $newPath, $value, $scope, $scopeId);
}

$installer->endSetup();
