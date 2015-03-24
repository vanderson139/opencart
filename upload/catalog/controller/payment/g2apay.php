<?php
class ControllerPaymentG2APay extends Controller {
	public function index() {
		$this->load->language('payment/g2apay');

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['action'] = $this->url->link('payment/g2apay/checkout', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/g2apay.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/g2apay.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/g2apay.tpl', $data);
		}
	}

	public function checkout() {
		$this->load->model('checkout/order');
		$this->load->model('account/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$this->load->model('extension/extension');
		$results = $this->model_extension_extension->getExtensions('total');
		$order_data = array();
		$total = 0;
		$items = array();
		$taxes = $this->cart->getTaxes();

		$i = 0;
		foreach ($results as $result) {
		    if ($this->config->get($result['code'] . '_status')) {
			    $this->load->model('total/' . $result['code']);

			    $this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes);

			    if (isset($order_data['totals'][$i])) {
					if (strstr(strtolower($order_data['totals'][$i]['code']), 'total') === false) {
						$item = new stdClass();
						$item->sku = $order_data['totals'][$i]['code'];
						$item->name = $order_data['totals'][$i]['title'];
						$item->amount = $order_data['totals'][$i]['value'];
						$item->qty = 1;
						$items[] = $item;
					}
					$i++;
			    }
		    }
		}

		$ordered_products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);

		foreach ($ordered_products as $product) {
		    $item = new stdClass();
		    $item->sku = $product['product_id'];
		    $item->name = $product['name'];
		    $item->amount = $product['price'];
		    $item->qty = $product['quantity'];
		    $items[] = $item;
		}

		if ($this->config->get('g2apay_environment') == 1) {
		    $url = 'https://checkout.pay.g2a.com/index/createQuote';
		} else {
		    $url = 'https://checkout.test.pay.g2a.com/index/createQuote';
		}

		$string = $this->session->data['order_id'] . $order_info['total'] . $order_info['currency_code'] . $this->config->get('g2apay_secret');
		$ch = curl_init();
		$fields = array(
		    'api_hash' => $this->config->get('g2apay_api_hash'),
		    'hash' => hash('sha256', $string),
		    'order_id' => $this->session->data['order_id'],
		    'amount' => $order_info['total'], //$items[0]->amount,
		    'currency' => $order_info['currency_code'],
		    'email' => $order_info['email'],
		    'url_failure' => $this->url->link('checkout/failure'),
		    'url_ok' => $this->url->link('payment/g2apay/success'),
		    'items' => json_encode($items)
		);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$result = curl_exec($ch);
		curl_close($ch);

		if ($result === false) {
		    $this->response->redirect($this->url->link('payment/failure', '', 'SSL'));
		}

		$result = json_decode($result);

		if (strtolower($result->status) != 'ok') {
		    $this->response->redirect($this->url->link('payment/failure', '', 'SSL'));
		}

		if ($this->config->get('g2apay_environment') == 1) {
		    $this->response->redirect('https://checkout.pay.g2a.com/index/gateway?token=' . $result->token);
		} else {
		    $this->response->redirect('https://checkout.test.pay.g2a.com/index/gateway?token=' . $result->token);
		}
	}

	public function success() {
		$order_id = $this->session->data['order_id'];

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('g2apay_order_status_id'), $this->request->post['transaction_id'], true);
		}

		$this->response->redirect($this->url->link('checkout/success'));
	}
}