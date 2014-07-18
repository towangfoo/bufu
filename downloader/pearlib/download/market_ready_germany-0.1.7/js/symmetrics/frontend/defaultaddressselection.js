/**
  * checked the radiobutton "shipping to this adress"
  */
document.observe('dom:loaded', function() {
    $('billing:use_for_shipping_yes').writeAttribute('checked', true);
});
