<?php
class ControllerExtensionTotalCodfee extends Controller {
    private $error = array();
    public function index() {
        $this->load->language('extension/total/cod_fee');
        
        $this->document->setTitle($this->language->get('heading_title'));
       
        $this->load->model('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_setting_setting->editSetting('total_cod_fee', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['entry_cod_fee'] = $this->language->get('entry_cod_fee');
        $data['entry_tax_class'] = $this->language->get('entry_tax_class');

        $data['text_none'] = $this->language->get('text_none');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/total/cod_fee', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/total/cod_fee', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

        if (isset($this->request->post['total_cod_fee_status'])) {
            $data['total_cod_fee_status'] = $this->request->post['total_cod_fee_status'];
        } else {
            $data['total_cod_fee_status'] = $this->config->get('total_cod_fee_status');
        }

        if (isset($this->request->post['total_cod_fee_fee'])) {
            $data['total_cod_fee_fee'] = $this->request->post['total_cod_fee_fee'];
        } else {
            $data['total_cod_fee_fee'] = $this->config->get('total_cod_fee_fee');
        }

        $this->load->model('localisation/tax_class');

        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        if (isset($this->request->post['total_cod_fee_tax_class_id'])) {
            $data['total_cod_fee_tax_class_id'] = $this->request->post['total_cod_fee_tax_class_id'];
        } else {
            $data['total_cod_fee_tax_class_id'] = $this->config->get('total_cod_fee_tax_class_id');
        }

        if (isset($this->request->post['total_cod_fee_sort_order'])) {
            $data['total_cod_fee_sort_order'] = $this->request->post['total_cod_fee_sort_order'];
        } else {
            $data['total_cod_fee_sort_order'] = $this->config->get('total_cod_fee_sort_order');
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/total/cod_fee', $data));
    }
}