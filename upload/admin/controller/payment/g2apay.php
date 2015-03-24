<?php
class ControllerPaymentG2APay extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/g2apay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('g2apay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['entry_environment'] = $this->language->get('entry_environment');
		$data['entry_secret'] = $this->language->get('entry_secret');
		$data['entry_api_hash'] = $this->language->get('entry_api_hash');
		$data['entry_ipn_uri'] = $this->language->get('entry_ipn_uri');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['g2apay_environment_live'] = $this->language->get('g2apay_environment_live');
		$data['g2apay_environment_test'] = $this->language->get('g2apay_environment_test');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['secret'])) {
			$data['error_secret'] = $this->error['secret'];
		} else {
			$data['error_secret'] = '';
		}

		if (isset($this->error['api_hash'])) {
			$data['error_api_hash'] = $this->error['api_hash'];
		} else {
			$data['error_api_hash'] = '';
		}

		if (isset($this->request->post['g2apay_order_status_id'])) {
			$data['g2apay_order_status_id'] = $this->request->post['g2apay_order_status_id'];
		} else {
			$data['g2apay_order_status_id'] = $this->config->get('g2apay_order_status_id');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/g2apay', 'token=' . $this->session->data['token'], 'SSL')
		);

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['action'] = $this->url->link('payment/g2apay', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['g2apay_environment'])) {
			$data['g2apay_environment'] = $this->request->post['g2apay_environment'];
		} else {
			$data['g2apay_environment'] = $this->config->get('g2apay_environment');
		}

		if (isset($this->request->post['g2apay_secret'])) {
			$data['g2apay_secret'] = $this->request->post['g2apay_secret'];
		} else {
			$data['g2apay_secret'] = $this->config->get('g2apay_secret');
		}

		if (isset($this->request->post['g2apay_api_hash'])) {
			$data['g2apay_api_hash'] = $this->request->post['g2apay_api_hash'];
		} else {
			$data['g2apay_api_hash'] = $this->config->get('g2apay_api_hash');
		}

		if (isset($this->request->post['g2apay_ipn_uri'])) {
			$data['g2apay_ipn_uri'] = $this->request->post['g2apay_ipn_uri'];
		} else {
			$data['g2apay_ipn_uri'] = $this->config->get('g2apay_ipn_uri');
		}

		if (isset($this->request->post['g2apay_status'])) {
			$data['g2apay_status'] = $this->request->post['g2apay_status'];
		} else {
			$data['g2apay_status'] = $this->config->get('g2apay_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/g2apay.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/g2apay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['g2apay_secret']) {
			$this->error['secret'] = $this->language->get('error_secret');
		}

		if (!$this->request->post['g2apay_api_hash']) {
			$this->error['api_hash'] = $this->language->get('error_api_hash');
		}

		return !$this->error;
	}
}