<?php
/**
 * Ticket Event collection
 *
 * @category   Que
 * @package    Que_Mytunes
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Model_Mysql4_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize collection
     *
     */
    protected function  _construct()
    {
        $this->_init('bufu_tickets/event');
    }

}