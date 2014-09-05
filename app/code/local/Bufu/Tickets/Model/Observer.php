<?php
/**
 * Bufu_Tickets Events Observer
 *
 * @category   Bufu
 * @package    Bufu_Tickets
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Model_Observer
{
    /**
     * Observer for event "catalog_product_prepare_save"
     * Called when saving a product after editing in Backend.
     * Checks for availability of module specific POST data
     * and saves that stuff to DB.
     *
     * Backend observer.
     *
     * @param  Varien_Object $observer
     * @return Bufu_Tickets_Model_Observer
     */
    public function catalog_product_prepare_save($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if($myData = $request->getPost('bufu_tickets')) {
            Mage::helper('bufu_tickets')->saveEvents($product, $myData['events']);
        }

        return $this;
    }

	public function checkout_cart_product_add_after(Varien_Event_Observer $observer)
	{
		/* @var $item Mage_Sales_Model_Quote_Item */
		$item = $observer->getQuoteItem();
		if ($item->getParentItem()) {
			$item = $item->getParentItem();
		}

		$request = Mage::app()->getRequest();
		$ticketParams = $request->getParam("bufu_tickets");

		if(!$ticketParams)
			return $this;

		// load event
		$event = Mage::getModel('bufu_tickets/event')->load($ticketParams['event_id']);
		if(!$event) {
			throw new Mage_Core_Exception("Event not found");
		}

		if ((int) $ticketParams['nr_normal'] > 0) {
			// add normal tickets
			$type = Bufu_Tickets_Helper_Data::TICKET_TYPE_NORMAL;
			$price = $event->getPriceNormal();
			$item->setQty((int) $ticketParams['nr_normal']);
		}

		if ((int) $ticketParams['nr_special'] > 0) {
			// add special tickets
			$type = Bufu_Tickets_Helper_Data::TICKET_TYPE_SPECIAL;
			$price = $event->getPriceSpecial();
			$item->setQty((int) $ticketParams['nr_special']);
		}

		$item->setCustomPrice($price);
		$item->setOriginalCustomPrice($price);
		$item->getProduct()->setIsSuperMode(true);

		$item->addOption(array(
			'item' => $item,
			'code' => Bufu_Tickets_Helper_Data::OPTION_TYPE,
			'value' => $type
		));
		$item->addOption(array(
			'item' => $item,
			'code' => Bufu_Tickets_Helper_Data::OPTION_EVENT_ID,
			'value' => $event->getId()
		));
		$item->addOption(array(
			'item' => $item,
			'code' => Bufu_Tickets_Helper_Data::OPTION_PRICE,
			'value' => $price
		));

		return $this;
	}
}