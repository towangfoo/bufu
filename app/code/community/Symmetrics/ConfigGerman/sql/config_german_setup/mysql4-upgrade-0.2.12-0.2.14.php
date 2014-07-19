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

$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->addFieldToFilter('value', array('like' => '%' . 'symmetrics' . '%'))
    ->load();

foreach ($configCollection as $configValue) {
    if ($configValue->getPath() == 'trans_email/ident_general/name') {
        $installer->setConfigData('trans_email/ident_general/name', $configData['default']['contact_name']);
    } else if ($configValue->getPath() == 'trans_email/ident_general/email') {
        $installer->setConfigData('trans_email/ident_general/email', $configData['default']['contact_email']);
    } else if ($configValue->getPath() == 'design/footer/copyright') {
        $installer->setConfigData('design/footer/copyright', $configData['default']['copyright']);
    } else if ($configValue->getPath() == 'trans_email/ident_sales/email') {
        $installer->setConfigData('trans_email/ident_sales/email', $configData['default']['contact_sales_email']);
    } else if ($configValue->getPath() == 'trans_email/ident_support/email') {
        $installer->setConfigData('trans_email/ident_support/email', $configData['default']['contact_support_email']);
    } else if ($configValue->getPath() == 'trans_email/ident_custom1/email') {
        $installer->setConfigData('trans_email/ident_custom1/email', $configData['default']['contact_custom1_email']);
    } else if ($configValue->getPath() == 'trans_email/ident_custom2/email') {
        $installer->setConfigData('trans_email/ident_custom2/email', $configData['default']['contact_custom2_email']);
    } else if ($configValue->getPath() == 'contacts/email/recipient_email') {
        $installer->setConfigData('contacts/email/recipient_email', $configData['default']['contact_recipient']);
    } else if ($configValue->getPath() == 'sales/identity/address') {
        $installer->setConfigData('sales/identity/address', $configData['default']['invoice_address']);
    } else if ($configValue->getPath() == 'customer/create_account/email_domain') {
        $installer->setConfigData('customer/create_account/email_domain', $configData['default']['email_domain']);
    }
}

$installer->endSetup();
