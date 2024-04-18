function triggerDPDDropdown(near) {
	var shipping_method_code = near.closest("div.ship-wrapper").find('input[name="shipping_method"]').val();

    if (shipping_method_code) {
	    var shipping_method_name = shipping_method_code.split('.');

	    // DPD parcel
	    if (shipping_method_name[0] == 'dpd_parcel') {
	    	var selector = 'input[value^=\"dpd_parcel\"]';
	    	var el = $(selector);
		    var orig_value = el.val();
		    var dropdown = el.parent().parent().find('.select-parcel').first();
			var parcel_id = 'dpd_parcel_id=' + $(dropdown).val();
			var shipping_method_code = 'shipping_method_code=' + shipping_method_code;

			// dropdown.prop('disabled', false);

			$.ajax({
				url: 'index.php?route=journal3/checkout/setDPDParcelID',
				type: 'post',
				data: parcel_id + '&' + shipping_method_code,
				dataType: 'json',
				success: function(json) {
					if (json['error']){
						console.log(json['error']);
					}
				}
			});
		}

		// Timeframe
		if (shipping_method_name[0] == 'dpd_courier') {
			var selector = 'input[value^=\"dpd_courier.\"]';

			var el = $(selector);

		    var orig_value = el.val();
		    var dropdown = el.parent().parent().find('select[name="shipping_timeframe"]').first();

		    var timeframe = 'shipping_timeframe=' + $(dropdown).val();
		    var shipping_method_code = 'shipping_method_code=' + shipping_method_code;

		    // dropdown.prop('disabled', false);

		    $.ajax({
	            url: 'index.php?route=journal3/checkout/setDPDTimeframe',
	            type: 'post',
	            data: timeframe + '&' + shipping_method_code,
	            dataType: 'json',
	            // success: function(json) {

	            // }
	        });
		}
	}
}

$( window ).load(function() {
	$("input:radio[name=shipping_method]:first").click();

	// Parcel ID
	$("#content").on('change', 'select[name="dpd_parcel"]', function() {
		triggerDPDDropdown($(this));

		$('.section-shipping input[name="shipping_method"]').trigger('click');
	});

	// Timeframe
	$("#content").on('change', 'select[name="shipping_timeframe"]', function() {
		triggerDPDDropdown($(this));

		$('.section-shipping input[name="shipping_method"]').trigger('click');
	});
})