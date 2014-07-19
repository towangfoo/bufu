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
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2011 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Backend Model to create and delete regions for hidden region dropdowns by the
 * specified contries.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TweaksGerman
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Torsten Walluhn <tw@symmetrics.de>
 * @copyright 2011 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_TweaksGerman_Model_System_Config_Backend_Region
    extends Mage_Core_Model_Config_Data
{

    /**
     * Constant value to store the default name for hidden region selects.
     */
    const DEFAULT_CONTRY_NAME = 'Not selected.';

    /**
     * Cleanup the region collection to remove the join of the translated title.
     *
     * @param Mage_Directory_Model_Mysql4_Region_Collection $collection Region collection to modify.
     *
     * @return Mage_Directory_Model_Mysql4_Region_Collection
     */
    protected function _cleanupRegionCollection($collection)
    {
        $select = $collection->getSelect();
        $fromPart = $select->getPart('from');
        if (array_key_exists('rname', $fromPart)) {
            unset($fromPart['rname']);
        }
        $select->setPart('from', $fromPart);

        $columnsPart = $select->getPart('columns');
        foreach ($columnsPart as $index => $column) {
            if ($column[0] == 'rname') {
                unset($columnsPart[$index]);
            }
        }
        $select->setPart('columns', $columnsPart);
        $collection->addFieldToFilter('default_name', self::DEFAULT_CONTRY_NAME);
        return $collection;
    }

    /**
     * Cleanup all regions which is not in countries array.
     *
     * @param array $countries Contries to not remove the regions.
     *
     * @return null
     */
    protected function _cleanupRegions($countries)
    {
        $region = Mage::getModel('directory/region');
        $collection = $this->_cleanupRegionCollection($region->getCollection());
        $resource = Mage::getSingleton('core/resource');
        $regionTable = $resource->getTableName('directory/country_region');
        $writeAdapter = $resource->getConnection('directory_write');

        $collection->addFieldToFilter('country_id', array('nin' => $countries));
        $idsToDelete = $collection->getAllIds();

        $writeAdapter->delete(
            $regionTable,
            array(
                'region_id IN(?)' => $idsToDelete,
            )
        );
    }

    /**
     * Before save event of the hide_region configuration to inject 'Not selected.' countries.
     *
     * @return Symmetrics_TweaksGerman_Model_System_Config_Backend_Region
     */
    protected function _beforeSave()
    {
        $countries = $this->getValue();
        $this->_cleanupRegions($countries);

        $region = Mage::getModel('directory/region');
        $collection = $this->_cleanupRegionCollection($region->getCollection());
        $resource = Mage::getSingleton('core/resource');
        $regionTable = $resource->getTableName('directory/country_region');
        $writeAdapter = $resource->getConnection('directory_write');

        foreach ($countries as $country) {
            if ($collection->getItemByColumnValue('country_id', $country)) {
                continue;
            }
            $writeAdapter->insert(
                $regionTable,
                array(
                    'country_id' => $country,
                    'code' => $country,
                    'default_name' => self::DEFAULT_CONTRY_NAME,
                )
            );
        }

        return $this;
    }
}
