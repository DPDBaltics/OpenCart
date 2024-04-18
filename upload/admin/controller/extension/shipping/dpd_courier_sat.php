<?php
class ControllerExtensionShippingDpdCourierSat extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/dpd_courier');
		$this->load->language('extension/shipping/dpd_courier_sat');

		$this->document->setTitle(preg_replace("/<img[^>]+\>/i", "", $this->language->get('heading_title')));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_dpd_courier_sat', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		// Breadcrumbs
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_shipping'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => preg_replace("/<img[^>]+\>/i", "", $this->language->get('heading_title')),
			'href' => $this->url->link('extension/shipping/dpd_courier_sat', 'user_token=' . $this->session->data['user_token'], true)
		);

		// Languages variables
		$languages = [
			'heading_title',
			'text_edit',
			'text_none',
			'tab_general',
			'text_enabled',
			'text_disabled',
			'entry_rate',
			'entry_price',
			'entry_free_shipping',
			'entry_tax_class',
			'entry_cod_status',
			'entry_status',
			'entry_sort_order',
			'help_rate',
			'help_free_shipping',
			'button_save',
			'button_cancel',
			'help_day',
			'entry_monday',
			'entry_tuesday',
			'entry_wednesday',
			'entry_thursday',
			'entry_friday',
			'text_from',
			'text_until',
			'entry_status_per_day',
			'entry_title'
		];

		foreach ($languages as $value) {
			$data[$value] = preg_replace("/<img[^>]+\>/i", "", $this->language->get($value));
		};

		// Geo zones
		$this->load->model('localisation/geo_zone');

		$geo_zones = $this->model_localisation_geo_zone->getGeoZones();
		$data['geo_zones'] = $geo_zones;

		foreach ($geo_zones as $geo_zone) {
			if (isset($this->request->post['dpd_parcel_' . $geo_zone['geo_zone_id'] . '_rate'])) {
				$data['shipping_dpd_courier_sat_geo_zone_rate'][$geo_zone['geo_zone_id']] = $this->request->post['shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_rate'];
			} else {
				$data['shipping_dpd_courier_sat_geo_zone_rate'][$geo_zone['geo_zone_id']] = $this->config->get('shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_rate');
			}

			if (isset($this->request->post['shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_price'])) {
				$data['shipping_dpd_courier_sat_geo_zone_price'][$geo_zone['geo_zone_id']] = $this->request->post['shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_price'];
			} else {
				$data['shipping_dpd_courier_sat_geo_zone_price'][$geo_zone['geo_zone_id']] = $this->config->get('shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_price');
			}

			if (isset($this->request->post['shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_free_shipping_from'])) {
				$data['shipping_dpd_courier_sat_geo_zone_free_shipping_from'][$geo_zone['geo_zone_id']] = $this->request->post['shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_free_shipping_from'];
			} else {
				$data['shipping_dpd_courier_sat_geo_zone_free_shipping_from'][$geo_zone['geo_zone_id']] = $this->config->get('shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_free_shipping_from');
			}

			if (isset($this->request->post['shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_status'])) {
				$data['shipping_dpd_courier_sat_geo_zone_status'][$geo_zone['geo_zone_id']] = $this->request->post['shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_status'];
			} else {
				$data['shipping_dpd_courier_sat_geo_zone_status'][$geo_zone['geo_zone_id']] = $this->config->get('shipping_dpd_courier_sat_' . $geo_zone['geo_zone_id'] . '_status');
			}
		}
		// 

		// Warning messages
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		// Actions
		$data['action'] = $this->url->link('extension/shipping/dpd_courier_sat', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		$data['price_calculation_method'] = $this->config->get('dpd_setting_price_calculation');

		$fields = [
			'shipping_dpd_courier_sat_tax_class_id',
			'shipping_dpd_courier_sat_cod_status',
			'shipping_dpd_courier_sat_status',
			'shipping_dpd_courier_sat_sort_order',
			// 'shipping_dpd_courier_sat_price',
			// 'shipping_dpd_courier_sat_rate',
			'shipping_dpd_courier_sat_mon_pickup',
			'shipping_dpd_courier_sat_tue_pickup',
			'shipping_dpd_courier_sat_wed_pickup',
			'shipping_dpd_courier_sat_thu_pickup',
			'shipping_dpd_courier_sat_fri_pickup',
			'shipping_dpd_courier_sat_weekdays',
			'shipping_dpd_courier_sat_title'
		];

		foreach ($fields as $field) {
			if (isset($this->request->post[$field])) {
				$data[$field] = $this->request->post[$field];
			} else {
				$data[$field] = $this->config->get($field);
			}
		}

		// Monday
		$default_monday_times = $this->config->get('shipping_dpd_courier_sat_mon_pickup');

		if ($default_monday_times == '') {
			$data['shipping_dpd_courier_sat_mon_pickup'][1]['from'] = '00:00';
			$data['shipping_dpd_courier_sat_mon_pickup'][1]['until'] = '09:30';

			$data['shipping_dpd_courier_sat_mon_pickup'][2]['from'] = '15:00';
			$data['shipping_dpd_courier_sat_mon_pickup'][2]['until'] = '23:59';
		}

		// Thuesday
		$default_thuesday_times = $this->config->get('shipping_dpd_courier_sat_tue_pickup');

		if ($default_thuesday_times == '') {
			$data['shipping_dpd_courier_sat_tue_pickup'][1]['from'] = '00:00';
			$data['shipping_dpd_courier_sat_tue_pickup'][1]['until'] = '09:30';

			$data['shipping_dpd_courier_sat_tue_pickup'][2]['from'] = '15:00';
			$data['shipping_dpd_courier_sat_tue_pickup'][2]['until'] = '23:59';
		}

		// Wednesday
		$default_wednesday_times = $this->config->get('shipping_dpd_courier_sat_wed_pickup');

		if ($default_wednesday_times == '') {
			$data['shipping_dpd_courier_sat_wed_pickup'][1]['from'] = '00:00';
			$data['shipping_dpd_courier_sat_wed_pickup'][1]['until'] = '09:30';

			$data['shipping_dpd_courier_sat_wed_pickup'][2]['from'] = '15:00';
			$data['shipping_dpd_courier_sat_wed_pickup'][2]['until'] = '23:59';
		}

		// Thursday
		$default_thursday_times = $this->config->get('shipping_dpd_courier_sat_thu_pickup');

		if ($default_thursday_times == '') {
			$data['shipping_dpd_courier_sat_thu_pickup'][1]['from'] = '00:00';
			$data['shipping_dpd_courier_sat_thu_pickup'][1]['until'] = '09:30';

			$data['shipping_dpd_courier_sat_thu_pickup'][2]['from'] = '15:00';
			$data['shipping_dpd_courier_sat_thu_pickup'][2]['until'] = '23:59';
		}

		// Friday
		$default_friday_times = $this->config->get('shipping_dpd_courier_sat_fri_pickup');

		if ($default_friday_times == '') {
			$data['shipping_dpd_courier_sat_fri_pickup'][1]['from'] = '00:00';
			$data['shipping_dpd_courier_sat_fri_pickup'][1]['until'] = '09:30';

			$data['shipping_dpd_courier_sat_fri_pickup'][2]['from'] = '15:00';
			$data['shipping_dpd_courier_sat_fri_pickup'][2]['until'] = '23:59';
		}

		// Weekdays
		$data['weekdays'] = [
			'mon' => $this->language->get('entry_monday'),
			'tue' => $this->language->get('entry_tuesday'),
			'wed' => $this->language->get('entry_wednesday'),
			'thu' => $this->language->get('entry_thursday'),
			'fri' => $this->language->get('entry_friday'),
		];

		// Get check weekdays
		$data['status_per_day'] = $this->config->get('shipping_dpd_courier_sat_weekdays');

		// Load tax Class
		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		// Lod the rest template data
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/dpd_courier_sat', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/dpd_courier_sat')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}