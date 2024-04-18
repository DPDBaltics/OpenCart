var dpd_gmap_api_key = window.wow_gmap_api_key;
var dpd_gmap_base_url = 'https://maps.googleapis.com/maps/api/geocode/json';

(function($) {
	/**
	 * Attempt to get postal code from Google Geocoder JSON response.
	 * 
	 * @param  {object} 	 data [Google Geocode JSON response]
	 * @return {string|null}      [Postal code]
	 */
	function getPostalCodeFromJson(data) {
		var code, item;

		try {
			for(var i = 0; i < data[0].address_components.length; i++) {
				item = data[0].address_components[i];
				
				if(data[0].address_components[i].types.indexOf('postal_code') > -1) {
					code = data[0].address_components[i].short_name;
					break;
				}
			}
		} catch(ex) {}

		return code;
	}

	/**
	 * Try to geocode address to get postal code and assign it as element value.
	 * 
	 * @param {string} address [Adress query]
	 * @param {object} el      [Element for setting geocoded postal code]
	 */
	function setGeocodedPostalCode(address, el) {
		if(!dpd_gmap_api_key) {
			console.log('Postal code geocoder is missing Google Maps API key');
			return;
		}

		// Build URL query
		var params = jQuery.param({
		    address: address,
		    type: 'postal_code',
		    key: dpd_gmap_api_key
		});

		var url = dpd_gmap_base_url + '?' + params;

		el.prop('readonly', true);
		// Send request to Google Geocoder
		$.getJSON(url, function(data) {
			if(data.status == 'OK') {
				var code = getPostalCodeFromJson(data.results);
				if(code) {
					el.val(code);
				}
			} else {
				console.log('Google Maps Geocoder Error', data);
				el.val('');
			}

			el.prop('readonly', false);
		});
	}

	/**
	 * Handle address-part input change.
	 * 
	 * @return {void}
	 */
	function onAddressInputChange() {
		// Find closest wrapper
		// var form = $(this).closest('.panel-body');
		
		// var el_city = form.find('input[name="city"]');
		// var el_address_1 = form.find('input[name="address_1"]');
		// var el_address_2 = form.find('input[name="address_2"]');
		// var el_postcode = form.find('input[name="postcode"]');

		var el_city = $('input[name="city"]');
		var el_address_1 = $('input[name="address_1"]');
		var el_address_2 = $('input[name="address_2"]');
		var el_postcode = $('input[name="postcode"]');

		var query = "";
		query += el_address_1.val() ? el_address_1.val() + ' ' : '';
		query += el_address_2.val() ? el_address_2.val() + ' ' : '';
		query += el_city.val() ? el_city.val() + ' ' : '';

		if(el_address_1.val() && el_city.val() && el_postcode.get(0)) {
			setGeocodedPostalCode(query, el_postcode);
		}
	}

	/**
	 * Initialize on jQuery ready
	 * 
	 */
	$(document).ready(function() {
		// Watch for AJAX calls to finish and bind events
		$(document).ajaxStop(function() {
		  $('input[name="city"], input[name="address_1"], input[name="address_2"]').off('change', onAddressInputChange).change(onAddressInputChange);
		});
	})
})(jQuery)