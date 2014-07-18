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
$installer->setConfigData('cataloginventory/stock_indicator/availability_0', '0');
$installer->setConfigData('cataloginventory/stock_indicator/availability_1', '20');
$installer->setConfigData('cataloginventory/stock_indicator/availability_2', '50');
$installer->setConfigData('cataloginventory/stock_indicator/availability_3', '80');