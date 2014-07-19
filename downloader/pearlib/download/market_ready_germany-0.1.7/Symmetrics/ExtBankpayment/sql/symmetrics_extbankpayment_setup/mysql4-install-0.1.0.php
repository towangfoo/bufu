<?php
/**
 * @category Symmetrics
 * @package Symmetrics_Bankpayment
 * @author symmetrics gmbh <info@symmetrics.de>, Sergej Braznikov <sb@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

$installer->setConfigData('payment/bankpayment/active', '1');
$installer->setConfigData('payment/bankpayment/title', 'Vorkasse');

$installer->endSetup();
