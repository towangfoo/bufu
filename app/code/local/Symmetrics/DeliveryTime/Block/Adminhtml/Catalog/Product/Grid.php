<?php
/**
 * Symmetrics_DeliveryTime_Block_Catalog_Product_Grid
 *
 * @category Symmetrics
 * @package Symmetrics_DeliveryTime
 * @author symmetrics gmbh <info@symmetrics.de>, Sergej Braznikov <sb@symmetrics.de>
 * @copyright symmetrics gmbh
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Symmetrics_DeliveryTime_Block_Adminhtml_Catalog_Product_Grid
	extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    public function setCollection($collection)
    {
        $collection->addAttributeToSelect('delivery_time');
        parent::setCollection($collection);
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('delivery_time',
            array(
                'header'=> Mage::helper('deliverytime')->__('Delivery time'),
                'width' => '100px',
                'type'  => 'text',
                'index' => 'delivery_time',
            ));

        parent::_prepareColumns();
    }
    
    public function getColumns()
    {
        $columns = parent::getColumns();
        $new_columns_order = array();

        foreach ($columns as $column_id => $column) {
            if ($column_id =='qty') {
                $new_columns_order[$column_id] = $column;   
                $new_columns_order['delivery_time'] = $columns['delivery_time'];    
            } elseif ($column_id != 'delivery_time') {
                $new_columns_order[$column_id] = $column;   
            }
        }

        return $new_columns_order;
    }
}
