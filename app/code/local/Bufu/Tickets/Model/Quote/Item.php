<?php
/**
 * Quote item model with updated pricing for tickets
 *
 * @package    Bufu_Tickets
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    /**
     * Overriden method of Mage_Sales_Model_Quote_Item_Abstract.
     * This is where the price calculation for a quote item is done.
     *
     * TODO: It seems hacky to do this right here. Is there a better more elegant way of doing this?
     * Should be possible without rewriting/overriding Mage classes!
     *
     * @return Bufu_Tickets_Model_Quote_Item $this
     */
    public function calcRowTotal() {
        $product = $this->getProduct();
        // update prices for tickets - take finalPrice as setting
        if ($this->isATicket()) {
            // set prices to the price given in backend
            $price = (float) $product->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_PRICE)->getValue();
            $this->setPrice($this->getStore()->roundPrice($price));
            // $this->setCustomPrice($this->getStore()->roundPrice($price));
        }

        // always do this!!!
        parent::calcRowTotal();
    }

	/**
	 * Return whether a quote item is a ticket
	 *
	 * @return boolean
	 */
	public function isATicket()
	{
		$product = $this->getProduct();
		$optionEventId = $product->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID);
		return ($optionEventId !== null);
	}
}
