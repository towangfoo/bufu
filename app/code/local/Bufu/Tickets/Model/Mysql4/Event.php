<?php
/**
 * Mysql4 Resource model for an event
 *
 * @category   Que
 * @package    Que_Mytunes
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Model_Mysql4_Event extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
        $this->_init('bufu_tickets/event', 'event_id');
    }
}