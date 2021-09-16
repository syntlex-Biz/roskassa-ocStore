<?php
class ControllerExtensionPaymentRoskassa extends Controller
{	
	public function index() 
	{
		$data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['action'] = $this->config->get('roskassa_url');
		$data['roskassa_login'] = $this->config->get('roskassa_merchant');
		$data['order_id'] = $this->session->data['order_id'];
		$data['out_summ'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$curr = strtoupper($order_info['currency_code']);
		$data['out_summ_currency'] = $curr;

		$data['products'] = array();

		$i = 0;
		foreach ($this->cart->getProducts() as $product) {

			$data['products']['receipt[items]['.$i.'][name]'] = $product['name'];
			$data['products']['receipt[items]['.$i.'][count]'] = $product['quantity'];
			$data['products']['receipt[items]['.$i.'][price]']  = $product['price'];

			$i++;

		}
		
		if (!empty($this->session->data['shipping_method']['cost'])) {
			$data['products']['receipt[items]['.$i.'][name]'] = $this->session->data['shipping_method']['title'];
			$data['products']['receipt[items]['.$i.'][count]'] = 1;
			$data['products']['receipt[items]['.$i.'][price]'] = $this->session->data['shipping_method']['cost'];
		}
		
		if ($this->config->get('roskassa_test')) {
			$data['roskassa_test'] = '1';
		} else {
			$data['roskassa_test'] = '0';
		}

		$arrSign = array(
			'shop_id' => $data['roskassa_login'],
			'order_id' => $data['order_id'],
			'amount' => $data['out_summ'],
			'currency' => $data['out_summ_currency'],
			'test' => $data['roskassa_test'],
		);
		ksort($arrSign);
		$str = http_build_query($arrSign);
		$data['sign'] = md5($str . $this->config->get('roskassa_security'));
		
		$this->model_checkout_order->addOrderHistory($data['order_id'], $this->config->get('roskassa_order_wait_id'));
		
		return $this->load->view('extension/payment/roskassa', $data);
	}

	public function status()
	{
		$request = $this->request->request;
		
		if (isset($request["id"]) && isset($request["sign"]))
		{
			$err = false;
			$message = '';
			$this->load->language('extension/payment/roskassa');

			// запись логов

			$log_text =
			"--------------------------------------------------------\n" .
			"shop               " . $request['shop_id'] . "\n" .
			"amount             " . $request['amount'] . "\n" .
			"operation id       " . $request['id'] . "\n" .
			"order id           " . $request['order_id'] . "\n" .
			"currency           " . $request['currency'] . "\n" .
			"sign               " . $request['sign'] . "\n\n";
			
			$log_file = $this->config->get('roskassa_log_value');
			
			if (!empty($log_file))
			{
				file_put_contents($_SERVER['DOCUMENT_ROOT'] . $log_file, $log_text, FILE_APPEND);
			}

			$data = $_POST;

			unset($data['sign']);
			ksort($data);
			$str = http_build_query($data);
			$sign_hash = md5($str . $this->config->get('roskassa_security'));

			if (!$err)
			{
				// загрузка заказа
				
				$this->load->model('checkout/order');
				$order = $this->model_checkout_order->getOrder($request['order_id']);
				
				if (!$order)
				{
					$message .= $this->language->get('text_email_message9') . "\n";
					$err = true;
				}
				else
				{
					$order_curr = ($order['currency_code'] == 'RUR') ? 'RUB' : $order['currency_code'];
					$order_amount = number_format($order['total'], 2, '.', '');
					
					// проверка суммы

					if ($request['amount'] != $order_amount)
					{
						$message .= $this->language->get('text_email_message7') . "\n";
						$err = true;
					}

					// проверка статуса

					if (!$err)
					{
						if ($request['sign'] == $sign_hash) {

							if ($order['order_status_id'] !== $this->config->get('roskassa_order_success_id')) {
								$this->model_checkout_order->addOrderHistory($request['order_id'], $this->config->get('roskassa_order_success_id'));
								echo 'YES';
							}
						}else{
							
							if ($order['order_status_id'] !== $this->config->get('roskassa_order_fail_id')) {

								$message .= $this->language->get('text_email_message2') . "\n";
								$this->model_checkout_order->addOrderHistory($request['order_id'], $this->config->get('roskassa_order_fail_id'));
								$err = true;
							}
						}
					}
				}
			}
			
			if ($err)
			{
				$to = $this->config->get('roskassa_admin_email');

				if (!empty($to))
				{
					$message = $this->language->get('text_email_message1') . "\n\n" . $message . "\n" . $log_text;
					$headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" . 
					"Content-type: text/plain; charset=utf-8 \r\n";
					mail($to, $this->language->get('text_email_subject'), $message, $headers);
				}
				
				echo $request['order_id'] . '| error |' . $message;
			}
		}
	}

	public function fail() 
	{
		$this->response->redirect($this->url->link('checkout/checkout'));	
		return true;
	}

	public function success() 
	{
		$this->response->redirect($this->url->link('checkout/success'));
		return true;
	}
}