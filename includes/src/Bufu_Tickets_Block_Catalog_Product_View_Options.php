<?php
/**
 * Block to display ticket options on product detail page.
 *
 * @category   Que
 * @package    Que_Mytunes
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Block_Catalog_Product_View_Options
    extends Mage_Catalog_Block_Product_View
{

    /**
     * Get event data for the current product
     *
     * @return Bufu_Tickets_Model_Mysql4_Event_Collection
     */
    public function getEvents()
    {
        return Mage::helper('bufu_tickets')->getEvents($this->getProduct());
    }

}
