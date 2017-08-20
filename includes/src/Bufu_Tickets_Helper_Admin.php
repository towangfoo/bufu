<?php
/**
 *
 * @category   Que
 * @package    Que_Mytunes
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Helper_Admin extends Mage_Core_Helper_Abstract
{

    /**
     * Checks if a product has ticke options.
     *
     * @return boolean
     */
    public function hasTicketOptions(Mage_Sales_Model_Order_Item $product)
    {
        $options = $product->getProductOptions();
        return isset($options['info_buyRequest']['bufu_tickets']);
    }

    /**
     * Get date from a product which hasTicketOptions() === true !!!
     *
     * @return string $date
     */
    public function getDateFromTicketProduct(Mage_Sales_Model_Order_Item $product)
    {
        $options = $product->getProductOptions();
        $eventId = $options['info_buyRequest']['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_EVENT_ID];
        $event = Mage::getModel('bufu_tickets/event')->load($eventId);
        return $event->getEventLocalDate();
    }

    /**
     * Get title from a product which hasTicketOptions() === true !!!
     *
     * @return string $date
     */
    public function getTitleFromTicketProduct(Mage_Sales_Model_Order_Item $product)
    {
        $options = $product->getProductOptions();
        $eventId = $options['info_buyRequest']['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_EVENT_ID];
        $event = Mage::getModel('bufu_tickets/event')->load($eventId);
        return $event->getEventTitle();
    }

    /**
     * Get time from a product which hasTicketOptions() === true !!!
     *
     * @return string $date
     */
    public function getTimeFromTicketProduct(Mage_Sales_Model_Order_Item $product)
    {
        $options = $product->getProductOptions();
        $eventId = $options['info_buyRequest']['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_EVENT_ID];
        $event = Mage::getModel('bufu_tickets/event')->load($eventId);
        return $event->getEventLocalTime();
    }

    /**
     * Get location from a product which hasTicketOptions() === true !!!
     *
     * @return string $location
     */
    public function getLocationFromTicketProduct(Mage_Sales_Model_Order_Item $product)
    {
        $options = $product->getProductOptions();
        $eventId = $options['info_buyRequest']['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_EVENT_ID];
        $event = Mage::getModel('bufu_tickets/event')->load($eventId);
        return $event->getEventLocation();
    }

    /**
     * Get tarif string from a product which hasTicketOptions() === true !!!
     *
     * @return string $location
     */
    public function getTypeFromTicketProduct(Mage_Sales_Model_Order_Item $product)
    {
        $options = $product->getProductOptions();
        $option = $options['info_buyRequest']['bufu_tickets'][Bufu_Tickets_Helper_Data::OPTION_TYPE];
        if ($option === Bufu_Tickets_Helper_Data::TICKET_TYPE_NORMAL) {
            return "Normalpreis";
        }
        if ($option === Bufu_Tickets_Helper_Data::TICKET_TYPE_SPECIAL) {
            return "Ermäßigter Preis";
        }
    }
}