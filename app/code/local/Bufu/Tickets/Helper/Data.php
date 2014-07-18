<?php
/**
 *
 * @category   Que
 * @package    Que_Mytunes
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * The product attribute set id of "Ticket"
     * 28 for local
     * 30 for konsum.buschfunk.com
     */
    const PRODUCT_ATTRIBUTE_SET_ID_TICKET = 30;

    /**
     * The different types of tickets
     */
    const TICKET_TYPE_NORMAL = 'normal';
    const TICKET_TYPE_SPECIAL = 'special';

    /**
     * The custom options for quote items
     */
    const OPTION_TYPE = 'bufu_tickets_type';
    const OPTION_EVENT_ID = 'bufu_tickets_event_id';
    const OPTION_PRICE = 'bufu_tickets_price';

    /**
     * Status for tickets
     */
     const STATUS_AVAILABLE = 1;
     const STATUS_SOMELEFT = 2;
     const STATUS_ABENDKASSE = 3;
     const STATUS_SOLDOUT = 0;

    /**
     * Get events attached to a product
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return Bufu_Tickets_Model_Mysql4_Cvent_Collection
     */
    public function getEvents(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getModel('bufu_tickets/event')->getCollection()
            ->addFieldToFilter('product_id', $product->getId())
            // only select dates that have not yet passed
            ->addFieldToFilter('event_date', array(
                'from' => date('Y-m-d 00:00:00'),
                'date' => true, // specifies conversion of comparison values
            ))
            ->setOrder('event_date', 'ASC');
        return $collection;
    }

    /**
     * Is the current product of the attribute set "Ticket"?
     *
     * Mage_Catalog_Model_Product $product = null
     *
     * @return boolean
     */
    public function isATicket(Mage_Catalog_Model_Product $product = null)
    {
        if ($product == null) {
            return false;
        }
        return (int) $product->getAttributeSetId() === Bufu_Tickets_Helper_Data::PRODUCT_ATTRIBUTE_SET_ID_TICKET;
    }

    /**
     * get formatted price
     *
     * @return string
     */
    public function getFormattedPrice($price) {
        return str_replace(".", ",", sprintf('%.2f', $price));
    }

    /**
     * Get local time from UTC time.
     *
     * @param string $utcTime "YYYY-mm-dd HH:MM:SS"
     *
     * @return $localTime "dd.mm.YYYY HH:MM[:SS]"
     */
    public function getLocalTime($utcTime, $convertToLocal = true, $removeSeconds = true)
    {
        $local = Mage::getModel('core/date')->date(null,strtotime($utcTime));
        if ($convertToLocal) {
            $parts = explode(" ", $local);
            $dateParts = explode("-", $parts[0]);
            $localDate = $dateParts[2] . "." . $dateParts[1] . "." . $dateParts[0];
            $local = $localDate . " " . $parts[1];
        }
        if ($removeSeconds) {
            $local = substr($local, 0, -3);
        }
        return $local;
    }

    /**
     * Get GMT time from local time.
     *
     * @param string $utcTime "YYYY-mm-dd HH:MM:SS" | "dd.mm.YYYY HH:MM:SS"
     * @param boolean $convertToIso = true
     *
     * @return $utcTime "YYYY-mm-dd HH:MM:SS"
     */
    public function getUtcTime($localTime, $convertToIso = true)
    {
        if ($convertToIso) {
            $parts = explode(" ", $localTime);
            $dateParts = explode(".", $parts[0]);
            if (strlen($dateParts[2]) == 2) {
                // TODO: This is will fail with years bigger than 2099 ...
                $dateParts[2] = "20" . $dateParts[2];
            }
            $isoDate = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];
            $localTime = $isoDate;
            if (isset($parts[1]))
                $localTime .= " " . $parts[1];
        }
        return Mage::getModel('core/date')->gmtDate(null,strtotime($localTime));
    }

    /**
     * Save events for a product to DB.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $events from POST data
     */
    public function saveEvents(Mage_Catalog_Model_Product $product, $events)
    {
        foreach ($events as $item) {
            // new event model
            $event = Mage::getModel('bufu_tickets/event');

            if (isset($item['event_id']) && isset($item['delete_event']) && $item['delete_event'] == "1") {
                // delete event
                $event->load($item['event_id'])->delete();
            }
            else {
                // create or update event
                if (isset($item['delete_event'])) unset($item['delete_event']);
                if (empty($item['event_id'])) unset($item['event_id']);
                $event->setData($item);
                if (isset($item['special_price_available'])) {
                    $event->setIsSpecialPriceAvailable(1);
                } else {
                    $event->setIsSpecialPriceAvailable(0);
                }
                // store UTC date!
                $event->setEventDate($this->getUtcTime($event['event_date']));
                $event->setProductId($product->getId());
                $event->save();
            }
        }
    }

    /**
     * Checks if a product has ticke options.
     *
     * @return boolean
     */
    public function hasTicketOptions(Mage_Sales_Model_Quote_Item $product)
    {
        $option = $product->getProduct()->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID);
        return $option !== null;
    }

    /**
     * Get date from a product which hasTicketOptions() === true !!!
     *
     * @return string $date
     */
    public function getDateFromTicketProduct(Mage_Sales_Model_Quote_Item $product)
    {
        $event = Mage::getModel('bufu_tickets/event')->load($product->getProduct()->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID)->getValue());
        return $event->getEventLocalDate();
    }

    /**
     * Get location from a product which hasTicketOptions() === true !!!
     *
     * @return string $location
     */
    public function getLocationFromTicketProduct(Mage_Sales_Model_Quote_Item $product)
    {
        $event = Mage::getModel('bufu_tickets/event')->load($product->getProduct()->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID)->getValue());
        return $event->getEventLocation();
    }

    /**
     * Get tarif string from a product which hasTicketOptions() === true !!!
     *
     * @return string $location
     */
    public function getTypeFromTicketProduct(Mage_Sales_Model_Quote_Item $product)
    {
        $option = ($product->getProduct()->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_TYPE)->getValue());
        if ($option === Bufu_Tickets_Helper_Data::TICKET_TYPE_NORMAL) {
            return "Normalpreis";
        }
        if ($option === Bufu_Tickets_Helper_Data::TICKET_TYPE_SPECIAL) {
            return "Ermäßigter Preis";
        }
    }

    /**
     * Get title from a product which hasTicketOptions() === true !!!
     *
     * @return string $date
     */
    public function getTitleFromTicketProduct(Mage_Sales_Model_Quote_Item $product)
    {
        $event = Mage::getModel('bufu_tickets/event')->load($product->getProduct()->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID)->getValue());
        return $event->getEventTitle();
    }
}