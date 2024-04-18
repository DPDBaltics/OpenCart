<?php
class ControllerExtensionModuleDpdSettings extends Controller {
    private $error = array();

    private $required_field = [
        'dpd_setting_api_username',
        'dpd_setting_api_password',
        'dpd_setting_api_url',
        'dpd_setting_parcel_distribution',
        'dpd_setting_label_size',
        // 'dpd_setting_google_map_api_key'
    ];

    public function index() {
        $data['MODULE_DPD_SETTINGS_VERSION'] = "1.3";
        $this->load->language('extension/module/dpd_settings');

        // Load countries
        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();

        $this->document->setTitle(preg_replace("/<img[^>]+\>/i", "", $this->language->get('heading_title')));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            unset($this->request->post['collectionRequest']);

            $this->request->post['dpd_setting_module_version'] = $data['MODULE_DPD_SETTINGS_VERSION'];
            $this->model_setting_setting->editSetting('dpd_setting', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        // Printing manifest
        $this->load->model('extension/shipping/dpdlivehandler');
        $data['manifest_list'] = $this->model_extension_shipping_dpdlivehandler->getManifest();

        $data['manifest_print'] = $this->url->link('extension/module/dpd_settings/print_manifest', 'user_token=' . $this->session->data['user_token'], true);

        // Load languages string
        $language_strings = [
            'heading_title',
            'tab_company',
            'tab_parcel_configuration',
            'tab_general',
            'tab_collection_request',
            'button_save',
            'button_cancel',
            'button_remove',
            'button_warehouse_add',
            'column_name',
            'column_address',
            'column_postcode',
            'column_city',
            'column_country',
            'column_contact_person',
            'column_phone',
            'text_edit',
            'text_select',
            'text_one_shipment',
            'text_separate_shipment',
            'text_separate_quantity_shipment',
            'text_action',
            'text_print',
            'text_current_manifest_day',
            'text_enabled',
            'text_disabled',
            'entry_dpd_setting_api_username',
            'entry_dpd_setting_api_password',
            'entry_dpd_setting_api_url',
            'entry_dpd_setting_price_calculation',
            'entry_dpd_setting_price_calculation_parcels',
            'entry_dpd_setting_google_map_api_key',
            'entry_name',
            'entry_address',
            'entry_postcode',
            'entry_city',
            'entry_contact_person_name',
            'entry_warehouse_phone',
            'info_warehouses',
            'entry_dpd_label_size',
            'entry_dpd_parcel_distribution',
            'text_per_item',
            'text_per_order',
            'text_per_weight',
            'entry_pickup_title',
            'entry_request',
            'entry_pickup_name',
            'entry_pickup_address',
            'entry_pickup_postcode',
            'entry_pickup_city',
            'entry_pickup_country',
            'entry_pickup_contact',
            'entry_pickup_contact_email',
            'entry_pickup_recipient_title',
            'entry_pickup_recipient_name',
            'entry_pickup_recipient_address',
            'entry_pickup_recipient_postcode',
            'entry_pickup_recipient_city',
            'entry_pickup_recipient_country',
            'entry_parcels_title',
            'entry_pickup_parcels_information',
            'entry_pickup_parcels_additional_information',
            'entry_placeholder_parcels',
            'entry_placeholder_pallets',
            'entry_dpd_rod_services',
            'entry_dpd_return_services',
            'entry_pickup_parcels_date',
            'entry_placeholder_weight'
        ];

        // Load language data
        foreach ($language_strings as $key) {
            $data[$key] = preg_replace("/<img[^>]+\>/i", "", $this->language->get($key));
        }

        // Warehouses
        if (isset($this->request->post['dpd_setting_warehouse'])) {
            $data['dpd_setting_warehouses'] = $this->request->post['dpd_setting_warehouse'];
        } else {
            $data['dpd_setting_warehouses'] = $this->config->get('dpd_setting_warehouse');
        }

        // Breadcrumbs
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => preg_replace("/<img[^>]+\>/i", "", $this->language->get('heading_title')),
            'href' => $this->url->link('extension/module/dpd_settings', 'user_token=' . $this->session->data['user_token'], true)
        );

        // Actions
        $data['action'] = $this->url->link('extension/module/dpd_settings', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();

        $data['collectionRequest'] = str_replace("&amp;", "&", $this->url->link('extension/module/dpd_settings/collectionReqesut', 'user_token=' . $this->session->data['user_token'], true));

        // Default error warning
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $variables = [
            'dpd_setting_api_username',
            'dpd_setting_api_password',
            'dpd_setting_api_url',
            'dpd_setting_google_map_api_key',
            'dpd_setting_label_size',
            'dpd_setting_price_calculation',
            'dpd_setting_price_calculation_parcels',
            'dpd_setting_parcel_distribution',
            'dpd_setting_rod_service',
            'dpd_setting_return_service',
        ];

        // Form data
        foreach ($variables as $key) {
            // Saving data
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } else {
                $data[$key] = $this->config->get($key);
            }
        }

        // CR time custom for OC 3
        $data['dpd_setting_cr_time'] = date('Y-m-d', strtotime('+1 day'));

        // Required
        $required_variables = $this->required_field;
        // Error messages
        foreach ($required_variables as $key) {
            if (isset($this->error[$key])) {
                $data['error_'.$key] = $this->error[$key];
            } else {
                $data['error_'.$key] = '';
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/dpd_settings', $data));
    }

    public function print_manifest() {
        $pdf_content = $this->request->get['pdf_content'];

        if ($pdf_content) {
            $this->load->model('extension/shipping/dpdlivehandler');
            return $this->model_extension_shipping_dpdlivehandler->printManifest($pdf_content);
        }
    }

    public function getTracking() {
        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');

        $order_id = $this->request->post['order_id'];

        if (isset($order_id) && $order_id) {
            $requestResult = $this->model_extension_shipping_dpdlivehandler->getParcelStatus($order_id);
            $order_barcode = $this->model_extension_shipping_dpdlivehandler->getOrderBarcode($order_id);

            $success_message = '';
            $error_message = '';
            $json = array();

            if ($order_barcode) {
                // New
                foreach ($requestResult as $result) {
                    if (isset($result['status']) && strtolower($result['status']) == 'ok') {
                        if ($result['parcel_status'] != '') {
                            if ($result['parcel_status'] == 'Pickup scan') {
                                $status = 'Parcel pickup made';
                            } else if ($result['parcel_status'] == 'HUB-scan') {
                                $status = 'Parcel is in DPD warehouse';
                            } else if ($result['parcel_status'] == 'Out for delivery') {
                                $status = 'Parcel is out for delivery';
                            } else if ($result['parcel_status'] == 'Infoscan') {
                                $status = 'Additional information added';
                            } else if ($result['parcel_status'] == 'Delivered') {
                                $status = 'Parcel is delivered successfully';
                            } else if ($result['parcel_status'] == 'Delivery obstacle') {
                                $status = 'Delivery obstacle';
                            } else {
                                $status = 'The parcel is not scanned by DPD';
                            }

                            $success_message .=  $result['barcode'] . ' ' . $status . '<br />';

                            $this->model_extension_shipping_dpdlivehandler->setOrderHistory($order_id, $success_message);
                        } else {
                            $error_message .= $result['barcode'] . ' Parcel is not scan by DPD<br />';
                        }
                    }
                }

                // print_r($success_message);
                // die();

                // if (isset($requestResult[0]['status']) && strtolower($requestResult[0]['status']) == 'ok') {
                // 	if ($requestResult[0]['parcel_status'] != '') {
                // 		if ($requestResult[0]['parcel_status'] == 'Pickup scan') {
                // 			$status = 'Parcel pickup made';
                // 		} else if ($requestResult[0]['parcel_status'] == 'HUB-scan') {
                // 			$status = 'Parcel is in DPD warehouse';
                // 		} else if ($requestResult[0]['parcel_status'] == 'Out for delivery') {
                // 			$status = 'Parcel is out for delivery';
                // 		} else if ($requestResult[0]['parcel_status'] == 'Infoscan') {
                // 			$status = 'Additional information added';
                // 		} else if ($requestResult[0]['parcel_status'] == 'Delivered') {
                // 			$status = 'Parcel is delivered successfully';
                // 		} else {
                // 			$status = 'Parcel is not scan by DPD';
                // 		}

                // 		$success_message .=  $status . '<br />';
                // 	} else {
                // 		$error_message .= 'Parcel is not scan by DPD <br />';
                // 	}
                // } else {
                // 	// Sis ifas netikrintas
                // 	if (is_array($requestResult)) {
                // 		$error_message .= $requestResult[0]['errlog'] . '<br />';
                // 	} else {
                // 		$error_message .= $requestResult['errlog'] . '<br />';
                // 	}
                // }
            } else {
                $error_message .= isset($requestResult['errlog']) ? $requestResult['errlog'] . '<br />' : '';
                $this->model_extension_shipping_dpdlivehandler->setOrderHistory($order_id, $error_message);
            }

            // if (isset($status) && $status != '') {
            // 	$this->model_shipping_dpdlivehandler->setOrderHistory($order_id, $status);
            // } else {
            // 	$this->model_shipping_dpdlivehandler->setOrderHistory($order_id, $error_message);
            // }

            // Return messages
            if ($success_message) {
                $json['success'] = $success_message;
            }

            if ($error_message) {
                $json['error'] = $error_message;
            }
        } else {
            $json['error'] = 'Unknown error';
        }

        $this->response->setOutput(json_encode($json));
    }

    public function submit_orders(){
        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');

        $action = $this->request->get['action'];

        // Variables
        $success_message = '';
        $error_message = '';
        $tracking_numbers = '';

        // What to do print labels or manifsts
        if(isset($action) != '') {
            if ($action == 'print_dpd_manifests') {
                $result = $this->model_extension_shipping_dpdlivehandler->close_manifest();

                $module_url = $this->url->link('extension/module/dpd_settings', 'user_token=' . $this->session->data['user_token'], true);

                $this->session->data['error_dpd'] = sprintf($this->language->get('entry_help_manifest'), $module_url);
                $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
            } else if ($action == 'print_dpd_labels') {
                // If no orders selected redirect to ordr list
                if (!isset($this->request->post['selected'])) {
                    $this->session->data['error_dpd'] = $this->language->get('error_non_dpd_orders_selected');

                    $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
                }

                // Get tracking numbers
                $result = $this->model_extension_shipping_dpdlivehandler->shipment_creation($this->request->post['selected']);
                foreach ($result as $order_id => $order_data) {
                    if ($order_data['status'] == 'err') {
                        $error_message .= $this->language->get('text_order_error') . $order_id . '. <strong>' . $order_data['errlog'] . '</strong><br />';
                    } else if ($order_data['status'] == 'ok') {
                        foreach ($order_data['barcodes'] as $barcode) {
                            $tracking_numbers .= $barcode['dpd_barcode'] . '|';
                        }
                    }
                }

                //If got error, redirect to order list and print message
                if ($error_message != '') {
                    // Show error message
                    $this->session->data['error_dpd'] = $error_message;

                    $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
                }

                if ($tracking_numbers) {
                    $this->model_extension_shipping_dpdlivehandler->print_parcel_label($tracking_numbers);
                }
            } else if ($action == 'cancel_shipments') {
                if (!isset($this->request->post['selected'])) {
                    $this->session->data['error_dpd'] = $this->language->get('error_non_dpd_orders_canceled');

                    $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
                }

                // Get selected orders barcodes
                foreach ($this->request->post['selected'] as $order_id) {
                    $result = $this->model_extension_shipping_dpdlivehandler->getOrderBarcode($order_id);

                    foreach ($result as $barcode) {
                        $tracking_numbers .= $barcode['dpd_barcode'] . '|';
                    }
                }

                // If tracking numbers exist, cancel thems
                if ($tracking_numbers) {
                    $result = $this->model_extension_shipping_dpdlivehandler->cancel_Shipment($tracking_numbers);

                    if ($result['status'] == 'err') {
                        $error_message .= $this->language->get('text_error_response') .'<strong>' . $result['errlog'] . '</strong>';

                        foreach ($this->request->post['selected'] as $order_id) {
                            $this->model_extension_shipping_dpdlivehandler->setOrderHistory($order_id, $this->language->get('text_error_response') . ' ' . $result['errlog']);

                            // $this->model_shipping_dpdlivehandler->deleteBarcode($order_id);
                        }
                    } else if ($result['status'] == 'ok') {
                        $success_message .= $this->language->get('text_success_dpd_canceled');

                        foreach ($this->request->post['selected'] as $order_id) {
                            $this->model_extension_shipping_dpdlivehandler->setOrderHistory($order_id, $this->language->get('text_dpd_order_successfully_canceled'));

                            $this->model_extension_shipping_dpdlivehandler->deleteBarcode($order_id);
                        }
                    }

                    if ($error_message != '' ) {
                        // Show error message
                        $this->session->data['error_dpd'] = $error_message;

                        $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
                    }

                    if ($success_message != '') {
                        // Show error message
                        $this->session->data['success_dpd'] = $success_message;

                        $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
                    }
                } else {
                    $this->session->data['error_dpd'] = $this->language->get('error_non_tracking_numbers');
                    $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
                }
            } else {
                $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
            }
        } else {
            $this->response->redirect($this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true));
        }
    }

    public function requestCourier (){
        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');

        $json = array();

        $data['action'] = $this->url->link('extension/module/dpd_settings/requestCourier', 'user_token=' . $this->session->data['user_token'], true);
        $data['warehouses'] = $this->config->get('dpd_setting_warehouse');

        $data['success'] = false;
        $data['error_warning'] = false;

        $data['modal'] = 0;

        // set default timezone
        //Europe/Riga, Europe/Tallinn
        date_default_timezone_set('Europe/Vilnius');
        $hours = getdate();
        $current_time = strtotime($hours['hours'] . ':' . $hours['minutes'] . ':' . $hours['seconds']);

        // Pick up from
        $customer_time = date('H', strtotime(date('H:i:s', strtotime("+20 minutes", $current_time))));

        if ($customer_time >= 7 && $customer_time < 15) {
            // Pick up times
            $data['pickup_from'] = date('H:i', strtotime("+20 minutes", $current_time));
            $pickup_from = strtotime($data['pickup_from']);
            $data['pickup_until'] = date('H:i', strtotime("+160 minutes", $pickup_from));
            $data['status'] = 1;


        } else {
            $data['pickup_from'] = '10:00:00';
            $data['pickup_until'] = '17:00:00';
            $data['status'] = 0;
        }

        // Load language data
        $language_strings = [
            'entry_select_warehouse',
            'entry_pallets_no',
            'entry_parcels_no',
            'entry_comment_for_courier',
            'button_request_dpd_courier',
            'entry_pickuptime',
            'text_from',
            'text_until'
        ];

        foreach ($language_strings as $key) {
            $data[$key] = $this->language->get($key);
        }

        $params = array();

        if (isset($this->request->post['warehouse_id'])) {
            $params['warehouse_id'] = $this->request->post['warehouse_id'];
        }

        if (isset($this->request->post['nonStandard'])) {
            $params['nonStandard'] = $this->request->post['nonStandard']; // comment for courier
        }

        if (isset($this->request->post['selected'])) {
            $params['selected'] = $this->request->post['selected']; // selected order
        }

        if (isset($this->request->post['parcelsCount'])) {
            $params['parcelsCount'] = $this->request->post['parcelsCount'];
        }

        if (isset($this->request->post['palletsCount'])) {
            $params['palletsCount'] = $this->request->post['palletsCount'];
        }

        if (isset($this->request->post['pickup_from']) && $this->request->post['pickup_from']) {
            $params['pickup_from'] = $this->request->post['pickup_from'];
        }

        if (isset($this->request->post['pickup_until']) && $this->request->post['pickup_until']) {
            $params['pickup_until'] = $this->request->post['pickup_until'];
        }

        if ($params) {
            if (isset($this->request->server['HTTP_ORIGIN'])) {
                $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            }

            if (!isset($this->request->post['pickup_until'])) {
                $params['pickup_until'] = '10:00:00';
            }

            if (!isset($this->request->post['pickup_from'])) {
                $params['pickup_from'] = '17:00:00';
            }

            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

            $this->response->addHeader('Content-Type: application/json');
            // Callcourier
            $result = $this->model_extension_shipping_dpdlivehandler->requestCourier($params);

            $result = explode('|', $result);

            if ($result[0] == 'DONE') {
                $pick_up_range = explode('#', $result[1]);

                $json['success'] = sprintf($this->language->get('text_courier_success'), $pick_up_range[2], $pick_up_range[4]);
            } else {
                $json['error'] = $this->language->get('error_courier_shipment') . ' ' . $this->language->get('text_error_response') . ' ' . $result[0];
            }

            $this->response->setOutput(json_encode($json));
        } else {
            $data['modal'] = isset($this->request->get['modal']);

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('extension/module/dpd_courier_request', $data));
        }
    }

    public function collectionReqesut() {
        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');

        $postParams = $this->request->post['collectionRequest'];

        $json = array();

        if (isset($postParams) && $postParams) {
            if ((utf8_strlen($postParams['pickup_name']) < 1) || (utf8_strlen($postParams['pickup_name']) > 141)) {
                $json['error']['pickup_name'] = $this->language->get('error_pickup_name');
            }

            if ((utf8_strlen($postParams['pickup_address']) < 1) || (utf8_strlen($postParams['pickup_address']) > 36)) {
                $json['error']['pickup_address'] = $this->language->get('error_pickup_address');
            }

            if ((utf8_strlen($postParams['pickup_postcode']) < 1) || (utf8_strlen($postParams['pickup_postcode']) > 9)) {
                $json['error']['pickup_postcode'] = $this->language->get('error_pickup_postcode');
            }

            if ((utf8_strlen($postParams['pickup_city']) < 1) || (utf8_strlen($postParams['pickup_city']) > 26)) {
                $json['error']['pickup_city'] = $this->language->get('error_pickup_city');
            }

            if ((int)$postParams['weight'] == 0) {
                $json['error']['weight'] = $this->language->get('error_weight');
            }

            if ((int)$postParams['parcels'] == 0) {
                $json['error']['parcels'] = $this->language->get('error_parcels');
            }

            if ((utf8_strlen($postParams['recipient_name']) < 1) || (utf8_strlen($postParams['recipient_name']) > 71)) {
                $json['error']['recipient_name'] = $this->language->get('error_recipient_name');
            }

            if ((utf8_strlen($postParams['recipient_pickup_address']) < 1) || (utf8_strlen($postParams['recipient_pickup_address']) > 71)) {
                $json['error']['recipient_pickup_address'] = $this->language->get('error_recipient_pickup_address');
            }

            if ((utf8_strlen($postParams['recipient_pickup_postcode']) < 1) || (utf8_strlen($postParams['recipient_pickup_postcode']) > 9)) {
                $json['error']['recipient_pickup_postcode'] = $this->language->get('error_recipient_pickup_postcode');
            }

            if ((utf8_strlen($postParams['recipient_pickup_city']) < 1) || (utf8_strlen($postParams['recipient_pickup_city']) > 26)) {
                $json['error']['recipient_pickup_city'] = $this->language->get('error_recipient_pickup_city');
            }

            // if ((int)$postParams['recipient_pickup_country'] == 0) {
            // 	$json['error']['recipient_pickup_country'] = $this->language->get('error_recipient_pickup_country');
            // }


            if (empty($json['error'])) {
                $requestCollection = $this->model_extension_shipping_dpdlivehandler->requestCollection($postParams);

                if (strpos($requestCollection, '201') !== false) {
                    $json['response']['success'] = $this->language->get('text_success_collection_requested');
                } else {
                    $json['response']['error'] = $requestCollection;
                }
            }

            $this->response->setOutput(json_encode($json));
        }
    }

    public function reverseCollectionRequest() {
        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');

        $data['action'] = $this->url->link('extension/module/dpd_settings/reverseCollectionRequest', 'user_token=' . $this->session->data['user_token'], true);

        $data['warehouses'] = $this->config->get('dpd_setting_warehouse');
        $data['entry_select_warehouse'] = $this->language->get('entry_select_warehouse_return');
        $data['button_request_dpd'] = $this->language->get('button_request_dpd');

        $data['order_id'] = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : $this->request->post['order_id'];

        $json = array();

        // requestCollection
        if (isset($this->request->get['modal_ajax']) && $this->request->get['modal_ajax'] == 1) {
            if (isset($this->request->server['HTTP_ORIGIN'])) {
                $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            }

            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

            $this->response->addHeader('Content-Type: application/json');

            $warehouse_id = $this->request->post['warehouse_id'];
            $order_id = $this->request->post['order_id'];

            $response = $this->model_extension_shipping_dpdlivehandler->reverseCollectionRequest($order_id, $warehouse_id);

            if (strpos($response, '201') !== false) {
                $json['success'] = $this->language->get('text_success_collection_requested');
            } else {
                $json['error'] = $response;
            }

            $this->response->setOutput(json_encode($json));
        } else {
            $data['modal'] = isset($this->request->get['modal']);

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('extension/module/dpd_collection_request', $data));
        }
    }

    public function getParcelsTerminals() {
        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');

        $this->model_extension_shipping_dpdlivehandler->updateTerminalList();
    }

    public function updatePrintingLabelsNo() {
        $json = array();

        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');

        $labels_number = $this->request->post['labels_number'];
        $order_id = $this->request->post['order_id'];

        if ($order_id && $labels_number > 0) {
            $this->model_extension_shipping_dpdlivehandler->setLabelsNumber($order_id, $labels_number);

            $json['success'] = $this->language->get('text_success_labels_no_changed');
        } else {
            $json['error'] = $this->language->get('error_labels_number_empty');
        }

        $this->response->setOutput(json_encode($json));
    }

    public function updateRodServices() {
        $json = array();

        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');


        $return_documents = $this->request->post['return_documents'];
        $order_id = $this->request->post['order_id'];

        $this->model_extension_shipping_dpdlivehandler->updateRodServices($order_id, $return_documents);
    }

    public function updateRodNote() {
        $json = array();

        $this->load->language('extension/module/dpd_settings');
        $this->load->model('extension/shipping/dpdlivehandler');

        $note = $this->request->post['note'];
        $order_id = $this->request->post['order_id'];

        if ($order_id) {
            $this->model_extension_shipping_dpdlivehandler->updateRodNote($order_id, $note);

            $json['success'] = $this->language->get('text_success_note_added');
        }

        $this->response->setOutput(json_encode($json));
    }

    protected function validate() {
        // if (!$this->user->hasPermission('modify', 'module/dpd_extension')) {
        // 	$this->error['warning'] = $this->language->get('error_permission');
        // }

        $variables = $this->required_field;

        foreach ($variables as $key) {
            if (!$this->request->post[$key]) {
                $this->error[$key] = $this->language->get('error_' . $key);
            }
        }

        return !$this->error;
    }

    private function uninstall() {
        $this->db->query("DROP TABLE `" . DB_PREFIX . "dpd_barcodes`");
    }
}