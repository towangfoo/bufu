<?php

/**
 * Diese Klasse ist für BuschFunk angepasst !!
 *      - spezielle Versandkosten wenn alle Produkte vom Attribute Set "Ticket" sind
 *      - sonst kostenloser Versand ab 60 EUR
 *
 * @author Steffen Mücke <mail@quellkunst.de>
 */

class Mxperts_ShipCalc_Model_Carrier_ShippingMethod extends Mage_Shipping_Model_Carrier_Abstract
{

  protected $_code = 'shipcalcmodule'; // Unser Versandkostencode/Alias

  /**
   * Einstellungen für Ticketversand
   */
  protected $ticketAttrSetName = 'Ticket';
  protected $ticketCarrierMethodTitle = "Ticketversand";
  protected $ticketShippingPrice = 3.5;

  /**
   * kostenloser Versan ab (Netto) EUR
   */
  protected $freeShippingStartingFrom = 60;

  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    // Wenn das Modul inaktiv ist -> abbrechen
    if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active'))
        return false;

      $handling = 0;

      if(Mage::getStoreConfig('carriers/'.$this->_code.'/handling') >0) {
          $handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
      }

      if(Mage::getStoreConfig('carriers/'.$this->_code.'/handling_type') == 'P' && $request->getPackageValue() > 0) {
          $handling = $request->getPackageValue()*$handling;
        }

      $method = Mage::getModel('shipping/rate_result_method'); // Instanz der Mage_Shipping_Model_Rate_Result_Method (app\code\core\Mage\Shipping\Model\Rate\Result\Method.php)

      $method->setCarrier($this->_code);
    $method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));

    $method->setMethod('shipcalc');
    $method->setMethodTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/methodtitle'));

    // Formel nach http://www.magentocommerce.com/boards/viewthread/30924/P15/
    $sess = Mage::getSingleton('checkout/session');
    $items = $sess->getQuote()->getAllItems();

    $price=0;


    // attribute set id von "Ticket" laden
    $attributeSetId = Mage::getModel('eav/entity_attribute_set')
        ->load($this->ticketAttrSetName, 'attribute_set_name')
        ->getAttributeSetId();

    $cartContainsTickets = false;
    $allItemsAreTickets = true;
    foreach($items as $item) {
        $price += ($item->getQty()*$item->getBaseCalculationPrice())+$item->getTaxAmount();

        // testen ob mind 1 Ticket im Cart
        if (!$cartContainsTickets && $item->getProduct()->getAttributeSetId() == $attributeSetId) {
            $cartContainsTickets = true;
        }

        // testen ob Produkt ein Ticket ist
        if($allItemsAreTickets && $item->getProduct()->getAttributeSetId() !== $attributeSetId) {
            $allItemsAreTickets = false;
        }
    }

    // nur tickets in der bestellung - custom Gebühr für Ticket-Versand
    if($allItemsAreTickets) {
        $method->setCarrierTitle("");
        $method->setMethodTitle($this->ticketCarrierMethodTitle);
        $price=$this->ticketShippingPrice;
    }

    // mind. 1 Ticket im Cart - custom Versandgebühr
    else if ($cartContainsTickets) {
        $method->setCarrierTitle("");
        $method->setMethodTitle($this->ticketCarrierMethodTitle);
        $price=$this->ticketShippingPrice;
    }

    // ab 60 EUR versandkostenfrei (in D)
    else if($price>=$this->freeShippingStartingFrom) {
        $method->setCarrierTitle("");
        $price=0;
    }

    // default  -do not use this module
    else {
        return false;
    }

    $method->setCost($handling+$price);
    $method->setPrice($handling+$price);

    $result = Mage::getModel('shipping/rate_result'); // Instanz der Klasse Mage_Shipping_Model_Rate_Result (app\code\core\Mage\Shipping\Model\Rate\Result.php)
    $result->append($method);

    return $result;

  }
}
