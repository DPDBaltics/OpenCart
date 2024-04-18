var dpd_parcel_method_code = 'dpd_parcel';

(function($) {

	function buildTerminalList() {
		var wrap = $('#dpd-parcel-list-wrap');
		if(wrap.length == 0) {
			var parent = $('select[name="shipping_method"]').closest('.form-group');
			$('' +
				'<div class="form-group required" id="dpd-parcel-list-wrap">' +
				'<label class="col-sm-2 control-label">DPD Pickup points</label>' +
				'<div class="col-sm-10">' +
				'<select class="form-control" id="dpd-parcel-list"></select>' +
				'</div>' +
				'</div>').insertAfter(parent);
		}

		var list = $('#dpd-parcel-list');
		list.empty();

		// Temporart detach AJAX interceptor
		useAjaxInterceptor(false);

		$.ajax({
			type: 'POST',
			url: 'index.php?route=extension/shipping/dpd_parcel/dpd_terminals&user_token=' + window.url_token,
			dataType: 'json',
			data: {
				country_id: $('#input-shipping-country').val(),
			},
			success: function(response) {
				var terminal;
				var value = $('[name="shipping_parcel_id"]').val();

				for(var i = 0; i < response.length; i++) {
					terminal = response[i];

					list.append('<option ' + (value && value == terminal.code ? 'selected="selected"' : '') + ' value="' + terminal.code + '">' + terminal.company + ', ' + terminal.street + '</option>');
				}

				list.off('change').change(function() {
					var code = $(this).val();
					$('[name="shipping_parcel_id"]').val(code);
				})
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

	function onShippingMethodChange(e) {
		if($(this).val().indexOf(dpd_parcel_method_code) > -1) {
			buildTerminalList();
		} else {
			$('#dpd-parcel-list-wrap').remove();
		}
	}

	function init() {
		$('select[name="shipping_method"]').off('change').change(onShippingMethodChange);

		if($('select[name="shipping_method"]:visible').length > 0) {
			$('select[name="shipping_method"]').trigger('change');
		}
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

	$(document).ready(function() {
		$('#button-save').click(function() {
			// Temporart detach AJAX interceptor
			useAjaxInterceptor(false);

			$.ajax({
				type: 'POST',
				url: 'index.php?route=extension/shipping/dpd_parcel/dpd_set_order_terminal&user_token=' + window.url_token,
				dataType: 'json',
				data: {
					code: $('[name="shipping_parcel_id"]').val(),
					order_id: $('[name="order_id"]').val(),
				},
				success: function(response) {
					console.log('Terminal id: ' + response.shipping_parcel_id);
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
		})

		// Watch for AJAX calls to finish and bind events
		useAjaxInterceptor(true);
	});

})(jQuery)