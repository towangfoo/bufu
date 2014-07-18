document.observe('dom:loaded', function() {
	var shopUrl = BLANK_IMG.substr (0, BLANK_IMG.length-13);
	var labels = $$('#product-attribute-specs-table .label');
	labels.each(function(label){
		if(label.innerHTML.match('Weight') !== null) {
			label.update('<a href="' + shopUrl + 'lieferung/">' + label.innerHTML + '</a>');
			throw $break;
		}
		else if(label.innerHTML.match('Gewicht') !== null) {
			label.update('<a href="' + shopUrl + 'lieferung/">' + label.innerHTML + '</a>');
			throw $break;
		}
	});  
});