<?php
class ControllerExtensionPaymentRoskassa extends Controller {
	private $error = array();

	public function index() 
	{
		$this->load->language('extension/payment/roskassa');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) 
		{
			$this->model_setting_setting->editSetting('roskassa', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_payment'] = $this->language->get('text_payment');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_pay'] = $this->language->get('text_pay');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_roskassa'] = $this->language->get('text_roskassa');
		$data['text_email_subject'] = $this->language->get('text_email_subject');
		$data['text_email_message1'] = $this->language->get('text_email_message1');
		$data['text_email_message2'] = $this->language->get('text_email_message2');
		$data['text_email_message3'] = $this->language->get('text_email_message3');
		$data['text_email_message4'] = $this->language->get('text_email_message4');
		$data['text_email_message5'] = $this->language->get('text_email_message5');
		$data['text_email_message6'] = $this->language->get('text_email_message6');
		
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_merchant'] = $this->language->get('entry_merchant');
		$data['entry_security'] = $this->language->get('entry_security');
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_order_wait'] = $this->language->get('entry_order_wait');
		$data['entry_order_success'] = $this->language->get('entry_order_success');
		$data['entry_order_fail'] = $this->language->get('entry_order_fail');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_log'] = $this->language->get('entry_log');
		$data['entry_admin_email'] = $this->language->get('entry_admin_email');
		$data['entry_result_url'] = $this->language->get('entry_result_url');
		$data['entry_success_url'] = $this->language->get('entry_success_url');
		$data['entry_fail_url'] = $this->language->get('entry_fail_url');
		
		$data['error_url'] = $this->language->get('error_url');
		$data['error_permission'] = $this->language->get('error_permission');
		$data['error_merchant'] = $this->language->get('error_merchant');
		$data['error_security'] = $this->language->get('error_security');

		$data['help_url'] = $this->language->get('help_url');
		$data['help_merchant'] = $this->language->get('help_merchant');
		$data['help_security'] = $this->language->get('help_security');
		$data['help_log'] = $this->language->get('help_log');
		$data['help_admin_email'] = $this->language->get('help_admin_email');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) 
		{
			$data['error_warning'] = $this->error['warning'];
		} 
		else 
		{
			$data['error_warning'] = '';
		}

		if (isset($this->error['url'])) 
		{
			$data['error_url'] = $this->error['url'];
		} 
		else 
		{
			$data['error_url'] = '';
		}
		
		if (isset($this->error['merchant'])) 
		{
			$data['error_merchant'] = $this->error['merchant'];
		} 
		else 
		{
			$data['error_merchant'] = '';
		}

		if (isset($this->error['security'])) 
		{
			$data['error_security'] = $this->error['security'];
		} 
		else 
		{
			$data['error_security'] = '';
		}

		if (isset($this->error['type'])) 
		{
			$data['error_type'] = $this->error['type'];
		} 
		else 
		{
			$data['error_type'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/roskassa', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/roskassa', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);
		
		if (isset($this->request->post['roskassa_url']))
		{
			$data['roskassa_url'] = $this->request->post['roskassa_url'];
		} 
		else
		{
			if (!$this->config->get('roskassa_url'))
			{
				$data['roskassa_url'] = '//pay.roskassa.net/';
			}
			else
			{
				$data['roskassa_url'] = $this->config->get('roskassa_url');
			}
		}

		if (isset($this->request->post['roskassa_merchant']))
		{
			$data['roskassa_merchant'] = $this->request->post['roskassa_merchant'];
		} 
		else 
		{
			$data['roskassa_merchant'] = $this->config->get('roskassa_merchant');
		}

		if (isset($this->request->post['roskassa_security']))
		{
			$data['roskassa_security'] = $this->request->post['roskassa_security'];
		} 
		else 
		{
			$data['roskassa_security'] = $this->config->get('roskassa_security');
		}

		if (isset($this->request->post['roskassa_test'])) 
		{
			$data['roskassa_test'] = $this->request->post['roskassa_test'];
		} 
		else 
		{
			$data['roskassa_test'] = $this->config->get('roskassa_test');
		}
        
		if (isset($this->request->post['roskassa_order_wait_id']))
		{
			$data['roskassa_order_wait_id'] = $this->request->post['roskassa_order_wait_id'];
		}
		else 
		{
			if (!$this->config->get('roskassa_order_wait_id'))
			{
				$data['roskassa_order_wait_id'] = 1;
			}
			else
			{
				$data['roskassa_order_wait_id'] = $this->config->get('roskassa_order_wait_id');
			}
		}
		
		if (isset($this->request->post['roskassa_order_success_id']))
		{
			$data['roskassa_order_success_id'] = $this->request->post['roskassa_order_success_id'];
		}
		else 
		{
			if (!$this->config->get('roskassa_order_success_id'))
			{
				$data['roskassa_order_success_id'] = 5;
			}
			else
			{
				$data['roskassa_order_success_id'] = $this->config->get('roskassa_order_success_id');
			}
		}
		
		if (isset($this->request->post['roskassa_order_fail_id']))
		{
			$data['roskassa_order_fail_id'] = $this->request->post['roskassa_order_fail_id'];
		}
		else 
		{
			if (!$this->config->get('roskassa_order_fail_id'))
			{
				$data['roskassa_order_fail_id'] = 10;
			}
			else
			{
				$data['roskassa_order_fail_id'] = $this->config->get('roskassa_order_fail_id');
			}
		}

		if(!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) 
		{
			$data['roskassa_result_url'] 		= 'https://' . $_SERVER['SERVER_NAME'] . '/index.php?route=extension/payment/roskassa/result';
			$data['roskassa_success_url'] 	= 'https://' . $_SERVER['SERVER_NAME'] . '/index.php?route=extension/payment/roskassa/success';
			$data['roskassa_fail_url'] 		= 'https://' . $_SERVER['SERVER_NAME'] . '/index.php?route=extension/payment/roskassa/fail';
		}
		else
		{
			$data['roskassa_result_url'] 		= HTTP_CATALOG . 'index.php?route=extension/payment/roskassa/result';
			$data['roskassa_success_url'] 	= HTTP_CATALOG . 'index.php?route=extension/payment/roskassa/success';
			$data['roskassa_fail_url'] 		= HTTP_CATALOG . 'index.php?route=extension/payment/roskassa/fail';
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['roskassa_geo_zone_id']))
		{
			$data['roskassa_geo_zone_id'] = $this->request->post['roskassa_geo_zone_id'];
		} 
		else 
		{
			$data['roskassa_geo_zone_id'] = $this->config->get('roskassa_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['roskassa_status']))
		{
			$data['roskassa_status'] = $this->request->post['roskassa_status'];
		} 
		else 
		{
			$data['roskassa_status'] = $this->config->get('roskassa_status');
		}

		if (isset($this->request->post['roskassa_sort_order']))
		{
			$data['roskassa_sort_order'] = $this->request->post['roskassa_sort_order'];
		} 
		else 
		{
			$data['roskassa_sort_order'] = $this->config->get('roskassa_sort_order');
		}
		
		if (isset($this->request->post['roskassa_log_value']))
		{
			$data['roskassa_log_value'] = $this->request->post['roskassa_log_value'];
		} 
		else 
		{
			$data['roskassa_log_value'] = $this->config->get('roskassa_log_value');
		}

		if (isset($this->request->post['roskassa_admin_email']))
		{
			$data['roskassa_admin_email'] = $this->request->post['roskassa_admin_email'];
		} 
		else 
		{
			$data['roskassa_admin_email'] = $this->config->get('roskassa_admin_email');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/roskassa', $data));
	}

	protected function validate(){
		
		if (!$this->user->hasPermission('modify', 'extension/payment/roskassa'))
		{
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['roskassa_url'])
		{
			$this->error['url'] = $this->language->get('error_url');
		}

		if (!$this->request->post['roskassa_merchant'])
		{
			$this->error['merchant'] = $this->language->get('error_merchant');
		}
		
		if (!$this->request->post['roskassa_security'])
		{
			$this->error['security'] = $this->language->get('error_security');
		}

		return !$this->error;
	}
}