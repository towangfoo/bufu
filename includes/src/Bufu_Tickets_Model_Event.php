<?php
/**
 * Ticket Event model
 *
 * @category    Que
 * @package     Que_Mytunes
 * @author      Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Model_Event extends Mage_Core_Model_Abstract
{

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('bufu_tickets/event');
        parent::_construct();
    }

    public function getPriceNormal(Mage_Catalog_Model_Product $product = null)
    {
        $basePrice = 0;
        if (!is_null($product)) {
            $basePrice = (float) $product->getPrice();
        }
        return $this->getData('price_normal') + $basePrice;
    }

    public function getPriceSpecial(Mage_Catalog_Model_Product $product = null)
    {
        $basePrice = 0;
        if (!is_null($product)) {
            $basePrice = (float) $product->getPrice();
        }
        return $this->getData('price_special') + $basePrice;
    }

    /**
     * Get only the localised date.
     *
     * @return string dd.mm.YY
     */
    public function getEventLocalDate()
    {
        $date = Mage::helper('bufu_tickets')->getLocalTime($this->getEventDate());
        $parts = explode(" ", $date);
        return $parts[0];
    }

    /**
     * Get only the localised time.
     *
     * @param boolean $removeSeconds
     *
     * @return string HH:MM[:SS]
     */
    public function getEventLocalTime($removeSeconds = true)
    {
        $date = Mage::helper('bufu_tickets')->getLocalTime($this->getEventDate(), true, $removeSeconds);
        $parts = explode(" ", $date);
        return $parts[1];
    }

    /**
     * Check for the date of an event, update status to STATUS_ABENDKASSE, when
     * it is upcoming, but not save the status in the DB (only for frontend display)
     */
    public function updateUpcomingStatus()
    {
        // how many days to the concert to treat it as upcoming
        // TODO: value could depend on the day of week of the concert
        $daysLeftToBeUpcoming = 3;

        if (!in_array($this->getIsAvailable(), array(
            Bufu_Tickets_Helper_Data::STATUS_ABENDKASSE,
            Bufu_Tickets_Helper_Data::STATUS_SOLDOUT
        ))) {
            $dateTestUpcoming = mktime(0,1,0,date('m'), date('d') + $daysLeftToBeUpcoming, date('Y'));
            $dateConcert = strtotime($this->getEventLocalDate());
            if ($dateTestUpcoming > $dateConcert) {
                $this->setIsAvailable(Bufu_Tickets_Helper_Data::STATUS_ABENDKASSE);
            }
        }
    }

    /**
     * @return boolean
     */
    public function getIsSpecialPriceAvailable()
    {
        return $this->getData('is_special_price_available') === "1";
    }

}