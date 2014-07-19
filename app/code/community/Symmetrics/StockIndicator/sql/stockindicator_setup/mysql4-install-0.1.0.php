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
 * @package   Symmetrics_StockIndicator
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * StockIndicator database migration
 *
 * @category  Symmetrics
 * @package   Symmetrics_StockIndicator
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Andreas Timm <at@symmetrics.de>
 * @author    Ngoc Anh Doan <nd@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->setConfigData('cataloginventory/stock_indicator/red', '0');
$installer->setConfigData('cataloginventory/stock_indicator/yellow', '20');
$installer->setConfigData('cataloginventory/stock_indicator/green', '40');

$installer->endSetup();