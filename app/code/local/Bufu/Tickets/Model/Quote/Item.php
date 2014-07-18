<?php
/**
 * Quote item model with updated pricing for tickets
 *
 * @category   Que
 * @package    Que_Mytunes
 * @author     Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Tickets_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    /**
     * Overriden method of Mage_Sales_Model_Quote_Item_Abstract.
     * This is where the price calculation for a quote item$product = $this->getProduct(); is done.
     *
     * TODO: It seems hacky to do this right here. Is there a better more elegant way of doing this?
     * Should be possible without rewriting/overriding Mage classes!
     *
     * @return Que_Mytunes_Model_Quote_Item $this
     */
    public function calcRowTotal() {
        $product = $this->getProduct();
        $optionEventId = $product->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_EVENT_ID);
        // update prices for tickets - take finalPrice as setting
        if ($optionEventId !== null) {
            // set prices to the price given in backend
            $price = (float) $product->getCustomOption(Bufu_Tickets_Helper_Data::OPTION_PRICE)->getValue();
            // custom options price add here
            $basePrice = (float) $this->getPrice() + 0.07 * $this->getPrice();
            $this->setCustomPrice($this->getStore()->roundPrice($price + $basePrice));
        }

        // always do this!!!
        parent::calcRowTotal();
    }
}
