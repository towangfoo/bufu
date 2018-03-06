function bufu_tickets_cart_checkQty(ipt, max) {
    var v = parseInt(ipt.value, 10);
    if (isNaN(v)) v = 1;
    if (v > max) ipt.value = max;
}
