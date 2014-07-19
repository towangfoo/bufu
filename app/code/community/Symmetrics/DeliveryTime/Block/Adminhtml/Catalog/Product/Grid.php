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
 * @package   Symmetrics_DeliveryTime
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Sergej Braznikov <sb@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/**
 * Overwrite Catalog/Product grid in backend to add delivery time column
 *
 * @category  Symmetrics
 * @package   Symmetrics_DeliveryTime
 * @author    symmetrics gmbh <info@symmetrics.de>
 * @author    Sergej Braznikov <sb@symmetrics.de>
 * @author    Yauhen Yakimovich <yy@symmetrics.de>
 * @copyright 2009-2010 symmetrics gmbh
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */
class Symmetrics_DeliveryTime_Block_Adminhtml_Catalog_Product_Grid
    extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /**
     * Set collection
     *
     * @param object $collection attribute collection used by grid
     *
     * @return void
     */
    public function setCollection($collection)
    {
        $collection->addAttributeToSelect('delivery_time');
        parent::setCollection($collection);
    }

    /**
     * Prepare columns
     *
     * @return void
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $column = array(
            'header' => Mage::helper('deliverytime')->__('Delivery time'),
            'width' => '100px',
            'type' => 'text',
            'index' => 'delivery_time',
        );

        // add column specifying the proper position, right after quantity
        $this->addColumnAfter('delivery_time', $column, 'qty');

        // sort all columns, so that a new column order can take place
        $this->sortColumnsByOrder();
    }
}
