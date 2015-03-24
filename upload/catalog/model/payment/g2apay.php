<?php
class ModelPaymentG2APay extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/g2apay');

		$method_data = array(
			'code'       => 'g2apay',
			'title'      => $this->language->get('text_title'),
			'terms'      => '',
			'sort_order' => ''
		);

		return $method_data;
	}
}