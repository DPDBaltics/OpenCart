<?php
// Heading
$_['heading_title']    = '<img src="view/image/dpd_logo.png" title="DPD Extension" alt="DPD Extension" style="height: 22px; margin-right: 15px; vertical-align: bottom;" /> DPD Extension';
$_['text_module']      = 'Modules';
$_['text_success']     = 'Success: You have modified DPD module!';
$_['text_success_labels_no_changed'] = 'You have successfully change the printing labels amount!';
$_['text_courier_success'] = 'Pick-up order was created success. Courier will arrive at: <strong>%s - %s</strong>';
$_['text_error_response'] = 'DPD Response:';
$_['error_courier_shipment'] = 'Pick-up order was not created! You have entered wrong date or time. Please correct entered data and save your order!';
$_['text_edit']        = '<img src="view/image/dpd_logo.png" title="DPD Extension" alt="DPD Extension" style="height: 22px; margin-right: 15px; vertical-align: middle;" />  Edit DPD Setting Module';
$_['text_success_dpd_canceled'] = 'Success: You have canceled selected orders for DPD shipment!';
$_['text_dpd_order_successfully_canceled'] = 'DPD Shipment was canceled for this order!';
$_['text_order_error'] = 'Error in order:';
$_['text_one_shipment'] = 'All products in same shipment';
$_['text_separate_shipment'] = 'Each product in separate shipment';
$_['text_separate_quantity_shipment'] = 'Each product quantity in separate shipment';

// Tabs strings
$_['tab_general'] = 'General';
$_['tab_collection_request'] = 'Collection request';
$_['tab_company'] = 'Warehouse settings';
$_['tab_parcel_configuration'] = 'Parcels Configuration';

// Entries
$_['entry_request'] = 'Request';
$_['entry_pickup_title'] = 'Where we should pick up your packages?';
$_['entry_pickup_name'] = 'Sender name: <span data-toggle="tooltip" title="" data-original-title="Enter the Pick up name. Maximum lenght 140 symbols" class="custom-tooltip"></span>';
$_['entry_pickup_address'] = 'Sender street address: <span data-toggle="tooltip" title="" data-original-title="Enter the Pick up street address. Maximum lenght 35 symbols" class="custom-tooltip"></span>';
$_['entry_pickup_postcode'] = 'Sender postcode: <span data-toggle="tooltip" title="" data-original-title="Enter the Pick up street postcode without country code. Maximum lenght 8 numbers" class="custom-tooltip"></span>';
$_['entry_pickup_city'] = 'Sender city:';
$_['entry_pickup_country'] = 'Sender country:';
$_['entry_pickup_contact'] = 'Contact person phone number:';
$_['entry_pickup_contact_email'] = 'Contact person email address:';
$_['entry_pickup_recipient_title'] = 'Where we should deliver the packages?';
$_['entry_placeholder_weight'] = 'Total weight in kg';
$_['entry_pickup_recipient_name'] = 'Recipient name: <span data-toggle="tooltip" title="" data-original-title="Enter the Recipient name. Maximum lenght 70 symbols" class="custom-tooltip"></span>';
$_['entry_pickup_recipient_address'] = 'Recipient street address: <span data-toggle="tooltip" title="" data-original-title="Enter the recipient street address. Maximum lenght 35 symbols" class="custom-tooltip"></span>';
$_['entry_pickup_recipient_postcode'] = 'Recipient postcode: <span data-toggle="tooltip" title="" data-original-title="Enter the recipient street postcode without country code. Maximum lenght 8 numbers" class="custom-tooltip"></span>';
$_['entry_pickup_recipient_city'] = 'Recipient city:';
$_['entry_pickup_recipient_country'] = 'Recipient country:';
$_['entry_parcels_title'] = 'Provide us a details about the parcels / pallets';
$_['entry_pickup_parcels_information'] = 'Enter the amount of parcels/pallets:';
$_['entry_placeholder_parcels'] = 'Enter number of parcels';
$_['entry_placeholder_pallets'] = 'Enter number of pallets';
$_['entry_pickup_parcels_additional_information'] = 'Additional information <span data-toggle="tooltip" data-html="true" title="" data-original-title="(order number)"></span>';

$_['entry_dpd_setting_api_username'] = 'Username:';
$_['entry_dpd_setting_api_password'] = 'Password:';
$_['entry_dpd_setting_api_url'] = 'API Url:';
$_['entry_dpd_setting_price_calculation'] = 'Shipping price calculation (For Courier)';
$_['entry_dpd_setting_price_calculation_parcels'] = 'Shipping price calculation (For Pickup points)';
$_['entry_dpd_setting_google_map_api_key'] = 'Google Map API key:<br /><a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><small>Get your key</small></a><span data-toggle="tooltip" title="" data-original-title="Google Maps will be used to pick up postcode from customer shipping address" class="custom-tooltip"></span>';
$_['entry_dpd_tracking_number'] = 'DPD Tracking number:';
$_['entry_dpd_label_size'] = 'Default label format';
$_['entry_dpd_parcel_distribution'] = 'Parcel distribution:';
$_['entry_help_manifest'] = 'Manifest has been closed. Print version of manifest is not required by DPD. If you would like to print manifest, you should go <a href="%s" target="_blank">here</a> and select Manifest section';
$_['entry_dpd_rod_services'] = 'Enable ROD / Document return service:';
$_['entry_dpd_return_services'] = 'Enable return labels printing?  <span data-toggle="tooltip" title="" data-original-title="If it will be enabled, when you print DPD Shipment label, you will get both label: one for package, second for customer if he would like to return the goods" class="custom-tooltip"></span>';
$_['info_warehouses'] = 'All fields must be filled. In this place you can fill information about your warehouse. You can add more than 1 warehouse, from where DPD can pick up orders.';
$_['entry_pickup_parcels_date'] = 'Provide a date when it should be pick up:';

$_['text_action'] = 'Action';
$_['text_print'] = 'Print';
$_['text_current_manifest_day'] = 'Manifest closed at';
$_['text_select'] = '-- Select country --';
$_['text_per_item'] = 'Per Item';
$_['text_per_order'] = 'Per Order';
$_['text_per_weight'] = 'Weight based';
$_['entry_name'] = 'Name';
$_['entry_address'] = 'Address';
$_['entry_postcode'] = 'Postcode';
$_['entry_city'] = 'City';
$_['entry_contact_person_name'] = 'Contact person';
$_['entry_warehouse_phone'] = 'Warehouse phone number';
$_['entry_warehouse_working_hours'] = '17:00';
$_['entry_warehouse_pickup_time'] = '12:30';
$_['entry_select_warehouse'] = 'Select warehouse: <span data-toggle="tooltip" title="" data-original-title="You can select different addresses for packages pick up" class="custom-tooltip"></span>';
$_['entry_select_warehouse_return'] = 'Select warehouse: <span data-toggle="tooltip" title="" data-original-title="You can select one of the warehouse where courier should bring the return products" class="custom-tooltip"></span>';
$_['entry_pallets_no'] = 'Count of pallets:';
$_['entry_parcels_no'] = 'Count of parcels:';
$_['entry_comment_for_courier'] = 'Comment for courier:';
$_['entry_pickuptime'] = 'Pickup time:';

$_['column_name'] = 'Warehouse name';
$_['column_address'] = 'Address';
$_['column_postcode'] = 'Postcode';
$_['column_city'] = 'City';
$_['column_country'] = 'Country';
$_['column_contact_person'] = 'Contact person';
$_['column_phone'] = 'Phone';

// Buttons
$_['button_warehouse_add'] = 'Add warehouse';
$_['button_request_dpd'] = 'Create Collection request';
$_['button_request_dpd_courier'] = 'Request courier';

// Errors
$_['error_dpd_setting_google_map_api_key'] = 'Google Map API key is required!';
$_['error_dpd_setting_api_username'] = 'DPD Username required!';
$_['error_dpd_setting_api_password'] = 'DPD Password required!';
$_['error_dpd_setting_api_url'] = 'API URL required!';
$_['error_dpd_setting_parcel_distribution'] = 'You should select the type';
$_['error_non_dpd_orders_selected'] = 'Please select orders which DPD Labels you would like to print!';
$_['error_non_dpd_orders_canceled'] = 'Please select orders which you would like to cancel!';
$_['error_non_dpd_orders'] = 'Orders with IDs <strong>%s</strong> delivery not DPD, so we cannot print labels';
$_['error_non_tracking_numbers'] = 'Selected orders doesnt have tracking numbers, so we cannot cancel it.';
$_['error_shipping_method_no_dpd'] = 'Shipping method is not DPD';
$_['error_labels_number_empty'] = 'Error: Please provide number of labels how many you need. It should be more than 0!';
$_['error_weight'] = 'Enter weight value';
$_['error_parcels'] = 'Enter amount of parcels';
$_['text_from'] = 'From';
$_['text_until'] = 'Until';
$_['text_success_note_added'] = 'Success: Note for document return labels added!';
$_['text_success_collection_requested'] = 'Your data was send to DPD and collections will be pick up. For more details call to DPD';
$_['error_pickup_name'] = 'Field is required! Pick up name must be from 1 to 140 symbols';
$_['error_pickup_address'] = 'Field is required! Pick up street address must be from 1 to 35 symbols';
$_['error_pickup_postcode'] = 'Field is required! Pick up postcode must be from 1 to 8 digits';
$_['error_pickup_city'] = 'Field is required! Pick up city must be from 1 to 25 symbols';
$_['error_pickup_country'] = 'Field is required!';
$_['error_recipient_name'] = 'Field is required! Receiver name must be from 1 to 70 symbols';
$_['error_recipient_pickup_address'] = 'Field is required! Receiver street address must be from 1 to 35 symbols';
$_['error_recipient_pickup_postcode'] = 'Field is required! Receiver postcode must be from 1 to 8 digits';
$_['error_recipient_pickup_city'] = 'Field is required! Receiver city must be from 1 to 25 symbols';
$_['error_recipient_pickup_country'] = 'Field is required!';