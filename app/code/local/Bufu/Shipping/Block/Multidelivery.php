<?php

class Bufu_Shipping_Block_Multidelivery extends Mage_Core_Block_Template {

  /**
   * Check if the quote contains at least one product, that is not yet available.
   * @return boolean
   */
  public function hasBothPreorderedAndAvailableProducts()
  {
    $preorder = $available = 0;

    $cart = Mage::getSingleton('checkout/session')->getQuote();
    foreach ($cart->getAllItems() as $item) {
       $product = $item->getProduct();
       if ($product->isSaleable()) {
          if ($product->isPreorderable()) {
              $preorder++;
          }
          else {
              $available++;
          }
       }
    }

    return ($preorder > 0) && ($available > 0);
  }

}
