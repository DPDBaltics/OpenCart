<?php
class ControllerExtensionShippingDpdCourier extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/dpd_courier');

		$this->document->setTitle(preg_replace("/<img[^>]+\>/i", "", $this->language->get('heading_title')));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_dpd_courier', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

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
			'href' => $this->url->link('extension/shipping/dpd_courier', 'user_token=' . $this->session->data['user_token'], true)
		);

		// Languages variables
		$languages = [
			'heading_title',
			'text_edit',
			'text_none',
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
			'tab_general'	

		];

		foreach ($languages as $value) {
			$data[$value] = preg_replace("/<img[^>]+\>/i", "", $this->language->get($value));
		};

		// Warning messages
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		// Actions
		$data['action'] = $this->url->link('extension/shipping/dpd_courier', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		$data['price_calculation_method'] = $this->config->get('dpd_setting_price_calculation');

		// Fields by geo zones
		$this->load->model('localisation/geo_zone');

		$geo_zones = $this->model_localisation_geo_zone->getGeoZones();
		$data['geo_zones'] = $geo_zones;

		foreach ($geo_zones as $geo_zone) {
			if (isset($this->request->post['shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_rate'])) {
				$data['shipping_dpd_courier_geo_zone_rate'][$geo_zone['geo_zone_id']] = $this->request->post['shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_rate'];
			} else {
				$data['shipping_dpd_courier_geo_zone_rate'][$geo_zone['geo_zone_id']] = $this->config->get('shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_rate');
			}

			if (isset($this->request->post['shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_price'])) {
				$data['shipping_dpd_courier_geo_zone_price'][$geo_zone['geo_zone_id']] = $this->request->post['shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_price'];
			} else {
				$data['shipping_dpd_courier_geo_zone_price'][$geo_zone['geo_zone_id']] = $this->config->get('shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_price');
			}

			if (isset($this->request->post['shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_free_shipping_from'])) {
				$data['shipping_dpd_courier_geo_zone_free_shipping_from'][$geo_zone['geo_zone_id']] = $this->request->post['shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_free_shipping_from'];
			} else {
				$data['shipping_dpd_courier_geo_zone_free_shipping_from'][$geo_zone['geo_zone_id']] = $this->config->get('shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_free_shipping_from');
			}

			if (isset($this->request->post['shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_status'])) {
				$data['shipping_dpd_courier_geo_zone_status'][$geo_zone['geo_zone_id']] = $this->request->post['shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_status'];
			} else {
				$data['shipping_dpd_courier_geo_zone_status'][$geo_zone['geo_zone_id']] = $this->config->get('shipping_dpd_courier_' . $geo_zone['geo_zone_id'] . '_status');
			}
		}

		$fields = [
			'shipping_dpd_courier_tax_class_id',
			'shipping_dpd_courier_cod_status',
			'shipping_dpd_courier_status',
			'shipping_dpd_courier_sort_order'
		];

		foreach ($fields as $field) {
			if (isset($this->request->post[$field])) {
				$data[$field] = $this->request->post[$field];
			} else {
				$data[$field] = $this->config->get($field);
			}
		}

		// Load tax Class
		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		// Lod the rest template data
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/dpd_courier', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/dpd_courier')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}