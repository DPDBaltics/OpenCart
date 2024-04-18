var dpd_gmap_api_key = window.wow_gmap_api_key;
var dpd_gmap_base_url = 'https://maps.googleapis.com/maps/api/geocode/json';

var dpd_parcel_modal;
var dpd_parcel_map = {};

(function($) {

	function onSearchLocationClick(event) {
		if(event) {
			event.preventDefault();
		}

		var address = $('input[name="dpd-modal-address"]').val() + ' ' + $('input[name="dpd-modal-city"]').val();

		if(address) {
			setGeocodedAddress(address);
		}
	}

	/**
	 * Attempt to get postal code from Google Geocoder JSON response.
	 * 
	 * @param  {object} 	 data [Google Geocode JSON response]
	 * @return {string|null}      [Postal code]
	 */
	function getCoordinatesFromJson(data) {
		var coords;
		
		try {
			coords = data.results[0].geometry.location;
		} catch(ex) {}

		return coords;
	}

	function setGeocodedAddress(address) {
		if(!dpd_gmap_api_key) {
			console.log('Postal code geocoder is missing Google Maps API key');
			return;
		}

		// Build URL query
		var params = jQuery.param({
		    address: address,
		    type: 'street_address',
		    key: dpd_gmap_api_key
		});

		var url = dpd_gmap_base_url + '?' + params;

		// Send request to Google Geocoder
		$.getJSON(url, function(data) {
			if(data.status == 'OK') {
				var coords = getCoordinatesFromJson(data);
				if(coords) {
					dpd_parcel_map.my_location = coords;
					dpd_parcel_map.my_marker.setPosition(dpd_parcel_map.my_location);
					dpd_parcel_map.map.setZoom(15);
					dpd_parcel_map.map.panTo(dpd_parcel_map.my_location);
					setTimeout("dpd_parcel_map.map.setZoom(14)", 1000);
				} else {
					console.log('Cannot geocode coordinates from address');
				}
			} else {
				console.log('Google Maps Geocoder Error', data);
				// el.val('');
			}
		});
	}

	function onSelectTerminalClick(event) {
		event.preventDefault();

		var code = $(this).data('terminal-code');
		var terminal_title = $(this).data('terminal-title');
		var vId = $(this).data('terminal-shipping-id');

		

		$.ajax({
	      url: 'index.php?route=checkout/checkout/dpd_set_terminal',
	      dataType: 'json',
	      // method: 'POST',
	      data: {
	      	code: code,
	      	api_token: ' ',
	      },
	      success: function(response) {
	      	console.log('Terminal id: ' + response.shipping_parcel_id);
	      },
	      error: function(xhr, options, error) {
	        console.error('DPD Parcel store: ' + error);
	      },
	      complete: function() {
			if(vId){
				$("#shipping_method_form").find('input:radio').prop('checked',false);	
				$("#shipping_method_form").find('input:radio').each(function() {
					if($.trim($( this ).attr( 'id' )) == 'dpd_parcel.dpd_parcel_'+vId){
						$( this ).click();
					}
				});
			}
	      	$('.dpd-selected-parcel').html(terminal_title);
	      	$('#dpd-close-parcel-modal').trigger('click');
	      	
	      }
	    });
	}

	function showMarkerInfo(marker) {
		var terminal = marker.dpd_terminal;

		var title = dpd_parcel_map.marker_info.find('h3');
		var address = dpd_parcel_map.marker_info.find('.info-address');
		var hours = dpd_parcel_map.marker_info.find('.info-hours');

		var mon = dpd_parcel_map.marker_info.find('.mon');
		var tue = dpd_parcel_map.marker_info.find('.tue');
		var wed = dpd_parcel_map.marker_info.find('.wed');
		var thu = dpd_parcel_map.marker_info.find('.thu');
		var fri = dpd_parcel_map.marker_info.find('.fri');
		var sat = dpd_parcel_map.marker_info.find('.sat');
		var sun = dpd_parcel_map.marker_info.find('.sun');

		var phone = dpd_parcel_map.marker_info.find('.info-phone');
		var email = dpd_parcel_map.marker_info.find('.info-email');
		var btn = dpd_parcel_map.marker_info.find('.select-terminal');


		title.html(terminal.company);
		address.html(terminal.street + ', ' + terminal.pcode + ', ' + terminal.city  || '-');

		monday = terminal.mon.split('|');
		tuesday = terminal.tue.split('|');
		wednesday = terminal.wed.split('|');
		thursday= terminal.thu.split('|');
		friday = terminal.fri.split('|');
		saturday = terminal.sat.split('|');
		sunday = terminal.sun.split('|');

		mon.find( ".morning" ).html(monday[0]);
		mon.find( ".afternoon" ).html(monday[1]);

		tue.find( ".morning" ).html(tuesday[0]);
		tue.find( ".afternoon" ).html(tuesday[1]);

		wed.find( ".morning" ).html(wednesday[0]);
		wed.find( ".afternoon" ).html(wednesday[1]);

		thu.find( ".morning" ).html(thursday[0]);
		thu.find( ".afternoon" ).html(thursday[1]);

		fri.find( ".morning" ).html(friday[0]);
		fri.find( ".afternoon" ).html(friday[1]);

		sat.find( ".morning" ).html(saturday[0]);
		sat.find( ".afternoon" ).html(saturday[1]);

		sun.find( ".morning" ).html(sunday[0]);
		sun.find( ".afternoon" ).html(sunday[1]);
		

		// if (terminal.sat == '00:00-00:00') {
		// 	sat.hide();
		// } else {
		// 	sat.append(terminal.sat || '-');
		// }

		// if (terminal.sun == '00:00-00:00') {
		// 	sun.hide();
		// } else {
		// 	sun.append(terminal.sun || '-');
		// }

		
		
		if(terminal.phone) {
			phone.html('<a href="tel:' + terminal.phone + '">' + terminal.phone + '</a>');
		} else {
			phone.html('');
		}

		if(terminal.email) {
			email.html('<a href="mailto:' + terminal.email + '">' + terminal.email + '</a>');
		} else {
			email.html('');
		}

		if(!terminal.phone && !terminal.email) {
			email.html('-');
		}

		btn.data('terminal-code', terminal.code);
		btn.data('terminal-title', terminal.company + ', ' + terminal.street);
		btn.data('terminal-shipping-id', $('#dptid').val());

		dpd_parcel_map.marker_info.show();

		dpd_parcel_map.map.panTo(marker.getPosition());
	}

	/**
	 * Create marker objects and show them on map
	 * 
	 * @param {array} data
	 */
	function setTerminalMarkers(data) {
		// Remove existing markers if any
		if(dpd_parcel_map.markers.length > 0) {
			for(var i = 0; i < dpd_parcel_map.markers.length; i++) {
				dpd_parcel_map.markers[i].setMap(null);
			}

			dpd_parcel_map.markers = [];
		}

		var marker, item;
		// Create and load marker objects
		for(var i = 0; i < data.length; i++) {
			item = data[i];
			try {
				// Create marker
				marker = new google.maps.Marker({
		          position: {lat: parseFloat(item.latitude), lng: parseFloat(item.longitude)},
		          map: dpd_parcel_map.map,
		          icon: 'admin/view/image/point.png'
		        });

				// Store terminal properties in marker
				marker['dpd_terminal'] = $.extend({}, item);

				google.maps.event.addListener(marker, 'click', function() {
					if(this.hasOwnProperty('dpd_terminal')) {
					  showMarkerInfo(this);
					}
				});

				// Add to markers array
		        dpd_parcel_map.markers.push(marker);
			} catch(e) {
				console.log('Cannot create marker for terminal', item);
			}

		}

		//var markerCluster = new MarkerClusterer(dpd_parcel_map.map, dpd_parcel_map.markers,
            // {imagePath: 'catalog/view/javascript/dpd/m'});
	}

	/**
	 * Manage AJAX interceptor
	 * 
	 * @param  {boolean} enabled
	 * @return {void}
	 */
	function useAjaxInterceptor(enabled) {
		if(enabled) {
			$(document).ajaxStop(function() {
				init();
			});
		} else {
			$(document).off('ajaxStop');
		}
	}

	/**
	 * Get terminals from API
	 * 
	 * @return {void}
	 */
	function loadTerminalMarkers() {
		// Temporart detach AJAX interceptor
		useAjaxInterceptor(false);

		$.ajax({
	      url: 'index.php?route=checkout/checkout/dpd_terminals',
	      data: 'api_token=""',
	      dataType: 'json',
	      success: function(response) {
	      	setTerminalMarkers(response);
	      },
	      error: function(xhr, options, error) {
	        console.error('DPD Parcel store: ' + error);
	      },
	      complete: function() {
	      	// Need delay to reattach AJAX interceptor
	      	setTimeout(function() {
	      		useAjaxInterceptor(true);
	      	}, 1000);
	      }
	    });
	}

	function initMap() {
		onSearchLocationClick(null);

		// TODO use geolocation for current position
		dpd_parcel_map.my_location = {lat:54.8897105, lng: 23.9258975};
		dpd_parcel_map.markers = [];
		dpd_parcel_map.marker_info.hide();

		// Initialize map
		dpd_parcel_map.map = new google.maps.Map(document.getElementById('dpd-parcel-modal-map'), {
			center: dpd_parcel_map.my_location,
			zoom: 15,
			gestureHandling: 'greedy', // Disable "use ctrl + scroll to zoom the map" message
			disableDefaultUI: true,
			zoomControl: true,
			zoomControlOptions: {
				position: google.maps.ControlPosition.LEFT_CENTER
			},
        });

		dpd_parcel_map.my_marker = new google.maps.Marker({
          position: dpd_parcel_map.my_location,
          map: dpd_parcel_map.map
        });

		loadTerminalMarkers();
	}

	/**
	 * Setup modal window
	 * 
	 * @return {void}
	 */
	function bindModal() {

		// Open the modal on button click
		$('#dpd-show-parcel-modal').off('click').click(function(event) {
			event.preventDefault();
		    initMap();

		    dpd_parcel_modal.css('display', 'block');
		    $('body').css('overflow', 'hidden');
			$('#dptid').val($(this).attr('data-tId'));

		    $(this).parent().find('input[name="shipping_method"]').prop('checked', true)
		})

		// Close the modal on X click
		$('#dpd-close-parcel-modal').off('click').click(function(event) {
			event.preventDefault();
		    dpd_parcel_modal.css('display', 'none');
		    $('body').css('overflow', 'auto');
		})

		// When the user clicks anywhere outside of the modal, close it
		dpd_parcel_modal.off('click').click(function(event) {
		    if (event.target == dpd_parcel_modal.get(0)) {
		        dpd_parcel_modal.css('display', 'none');
				$('body').css('overflow', 'auto');
		    }
		})

		dpd_parcel_map.marker_info = $('#dpd-parcel-modal-info');
		dpd_parcel_map.marker_info.find('.select-terminal').off('click').click(onSelectTerminalClick);

		dpd_parcel_modal.find('.search-location').off('click').click(onSearchLocationClick);
	}

	/**
	 * Setup parcel modal
	 * 
	 * @return {void}
	 */
	function init() {
		dpd_parcel_modal = $('#dpd-parcel-modal');

		// if(!$('#dpd-selected-parcel').html()) {
		// 	$('[name="shipping_method"]:checked').prop('checked', false)
		// }
		
		// Exit script if modal isn't available
		if(dpd_parcel_modal.length == 0) {
			return;
		}

		bindModal();
	}

	/**
	 * Initialize on jQuery ready
	 * 
	 */
	$(document).ready(function() {
		// Watch for AJAX calls to finish and bind events
		useAjaxInterceptor(true);
	})
})(jQuery)
