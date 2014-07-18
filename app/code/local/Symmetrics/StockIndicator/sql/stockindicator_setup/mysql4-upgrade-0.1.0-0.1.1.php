<?php
/**
 * StockIndicator database migration
 *
 * @category Symmetrics
 * @package Symmetrics_StockIndicator
 * @author symmetrics gmbh <info@symmetrics.de>, Andreas Timm <at@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software 
 */
$installer = $this;
$installer->deleteConfigData('cataloginventory/stock_indicator/availability_0');
$installer->deleteConfigData('cataloginventory/stock_indicator/availability_1');
$installer->deleteConfigData('cataloginventory/stock_indicator/availability_2');
$installer->deleteConfigData('cataloginventory/stock_indicator/availability_3');

$installer->setConfigData('cataloginventory/stock_indicator/red', '0');
$installer->setConfigData('cataloginventory/stock_indicator/yellow', '20');
$installer->setConfigData('cataloginventory/stock_indicator/green', '40');