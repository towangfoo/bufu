<?php
/**
 * The event items grid in the catalog product edit tab "Tickets Events"
 *
 * @category   Que
 * @package    Que_Mytunes
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Block_Adminhtml_Catalog_Product_Edit_Tab_Event_Grid extends Mage_Adminhtml_Block_Widget
{
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('bufu/tickets/product/edit/tab/event/grid.phtml');
    }

    /**
     * Get HTML for add-item button
     */
    public function getAddEventButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('bufu_tickets')->__('Add Event'),
                'id' => 'bufu_tickets_events_btn_add',
                'class' => 'add',
            ));
        return $addButton->toHtml();
    }

    /**
     * Get existing event data
     *
     * @return array of JSONString
     */
    public function getExistingEvents()
    {
        $product = $this->getProduct();
        if (!$product->getId())
            return array();

        $events = Mage::helper('bufu_tickets')->getEvents($product);
        if (!$events || count($events) == 0) {
            return array();
        }

        $result = array();
        foreach ($events as $event) {
            $resultItem = array(
                'event_id' => (int) $event->getId(),
                // NOTE: datetime is stored in UTC, convert to local time
                'date' => Mage::helper('bufu_tickets')->getLocalTime($event->getEventDate()),
                'title' => $event->getEventTitle(),
                'location' => $event->getEventLocation(),
                'desc' => $event->getEventDesc(),
                'price_normal' => $event->getPriceNormal(),
                'price_special' => $event->getPriceSpecial(),
                'availability' => (int) $event->getIsAvailable(),
                'specialPriceAvailable' => $event->getIsSpecialPriceAvailable()
            );

            $result[] = new Varien_Object($resultItem);
        }

        return $result;
    }

    /**
     * Get default values for event items.
     *
     * @return Varien_Object
     */
    public function getEventItemDefaults()
    {
        return new Varien_Object(array(
            'availability' => 1,
            'priceNormal'  => '',
            'priceSpecial' => '',
        ));
    }

    /**
     * Get model of the product that is being edited
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Is the current product of the attribute set "Ticket"?
     *
     * @return boolean
     */
    public function productIsATicket()
    {
        return Mage::helper('bufu_tickets')->isATicket($this->getProduct());
    }

    /**
     * Retrive config object
     *
     * @return Varien_Config
     */
    public function getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }

        return $this->_config;
    }
}
