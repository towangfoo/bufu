<?php

/**
 * Overrides a magento class to make tablerates calculation use prices including taxes.
 *
 * @see http://www.magentocommerce.com/boards/vi/viewthread/86251/
 *
 * @author Steffen Mücke <mail@quellkunst.de>
 */
class Bufu_Shipping_Model_Carrier_Tablerate extends Mage_Shipping_Model_Carrier_Tablerate
{
	/**
	 * Collect the shipping rate for a shipping request.
	 *  - Lines 46 to 51 have been added to use price including taxes as base for deciding which rate from the table to use.
	 *  - A check for ticket items in the quote. If ticket items contained, set a minimum shipping rate, configured in Adminhtml
	 * Rest of the method is cut+paste as it sadly is monolithic.
	 *
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!$this->getConfigFlag('active')) {
			return false;
		}

		// exclude Virtual products price from Package value if pre-configured
		if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				if ($item->getParentItem()) {
					continue;
				}
				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if ($child->getProduct()->isVirtual()) {
							$request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
						}
					}
				} elseif ($item->getProduct()->isVirtual()) {
					$request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
				}
			}
		}

		// If displayed prices include tax, consider this when calculating package value
		// @see http://www.magentocommerce.com/boards/vi/viewthread/86251/
		if (Mage::helper('tax')->priceIncludesTax()) {
			foreach ($request->getAllItems() as $item) {
				$request->setPackageValue($request->getPackageValue() + $item->getTaxAmount());
			}
		}

		$ticketItemContained = false;

		// Free shipping by qty
		$freeQty = 0;
		if ($request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
					continue;
				}

				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
							$freeQty += $item->getQty() * ($child->getQty() - (is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0));
						}
					}
				} elseif ($item->getFreeShipping()) {
					$freeQty += ($item->getQty() - (is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0));
				}

				if($item->isATicket())
					$ticketItemContained = true;
			}
		}

		if (!$request->getConditionName()) {
			$request->setConditionName($this->getConfigData('condition_name') ? $this->getConfigData('condition_name') : $this->_default_condition_name);
		}

		 // Package weight and qty free shipping
		$oldWeight = $request->getPackageWeight();
		$oldQty = $request->getPackageQty();

		$request->setPackageWeight($request->getFreeMethodWeight());
		$request->setPackageQty($oldQty - $freeQty);

		$result = Mage::getModel('shipping/rate_result');
		$rate = $this->getRate($request);

		$request->setPackageWeight($oldWeight);
		$request->setPackageQty($oldQty);

		if (!empty($rate) && $rate['price'] >= 0) {
			$method = Mage::getModel('shipping/rate_result_method');

			$method->setCarrier('tablerate');
			$method->setCarrierTitle($this->getConfigData('title'));

			$method->setMethod('bestway');
			$method->setMethodTitle($this->getConfigData('name'));

			if ($request->getFreeShipping() === true || ($request->getPackageQty() == $freeQty)) {
				$shippingPrice = 0;
			} else {
				$shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
			}

			// when a Ticket is in the order ...
			if ($ticketItemContained) {
				// get ticket shipping price from adminhtml
				$ticketsMinShipping = floatval(str_replace(",", ".", $this->getConfigData("bufu_tickets_price")));

				// if ticket shipping price is higher than current shipping price, use that
				if ($ticketsMinShipping > $shippingPrice) {
					$shippingPrice = $ticketsMinShipping;
					// also set the label accordingly
					$method->setMethodTitle($this->getConfigData("bufu_tickets_shipping_label"));
				}
			}

			$method->setPrice($shippingPrice);
			$method->setCost($rate['cost']);

			$result->append($method);
		}

		return $result;
	}
}
