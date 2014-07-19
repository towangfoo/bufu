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
 * This script will add delivery-time attribute to the default set of the product
 *
 * @category  Symmetrics
 * @package   Symmetrics_DeliveryTime
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
$installer = $this;

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$entityType = 'catalog_product';
$attributeCode = 'delivery_time';
$groupCode = 'general';

$attributeId = $installer->getAttributeId($entityType, $attributeCode);

if ($attributeId) {
    $attributeSetId = $installer->getDefaultAttributeSetId($entityType);
    try {
        $attributeGroupId = $installer->getAttributeGroupId($entityType, $attributeSetId, $groupCode);
    } catch (Exception $exception) {
        $attributeGroupId = $installer->getDefaultAttributeGroupId($entityType, $attributeSetId);
    }

    $installer->addAttributeToSet($entityType, $attributeSetId, $attributeGroupId, $attributeId);    
}

$installer->endSetup();

//EOF
