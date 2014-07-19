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
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Benjamin Klein <bk@symmetrics.de>
 * @author    Siegfried Schmitz <ss@symmetrics.de>
 * @copyright 2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql */
$connection  = $this->getConnection();
$regionTable = $installer->getTable('directory/country_region');

/**
 * Build the German regions to insert.
 */
$regions = array(
    array(
        'DE',
        'DE',
        'Nicht ausgewählt'
    )
);

/**
 * Add the regions we created.
 */
foreach ($regions as $row) {
    $connection->insert(
        $regionTable,
        array(
            'country_id' => $row[0],
            'code' => $row[1],
            'default_name' => $row[2]
        )
    );
}
