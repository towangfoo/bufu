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
}