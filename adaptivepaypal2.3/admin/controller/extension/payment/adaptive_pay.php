<?php 
class ControllerExtensionPaymentAdaptivePay extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('extension/payment/adaptive_pay');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('adaptive_pay', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_sale'] = $this->language->get('text_sale');
		
		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_signature'] = $this->language->get('entry_signature');
		$data['entry_test'] = $this->language->get('entry_test');
		$data['entry_transaction'] = $this->language->get('entry_transaction');
		$data['entry_total'] = $this->language->get('entry_total');	
		$data['entry_order_status'] = $this->language->get('entry_order_status');		
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['entry_title'] = $this->language->get('entry_title');		
		$data['entry_gateway_url'] = $this->language->get('entry_gateway_url');
		$data['entry_merchant_payid'] = $this->language->get('entry_merchant_payid');
		$data['entry_application_payid'] = $this->language->get('entry_application_payid');
		//$data['error_gateway_url'] = $this->language->get('error_gateway_url');
		//$data['error_merchant_payid'] = $this->language->get('error_merchant_payid');
		//$data['error_application_payid'] = $this->language->get('error_application_payid');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}
		
 		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}
		
 		if (isset($this->error['signature'])) {
			$data['error_signature'] = $this->error['signature'];
		} else {
			$data['error_signature'] = '';
		}

		

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);



   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/adaptive_pay', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
	$data['action'] = $this->url->link('extension/payment/adaptive_pay', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);


		if (isset($this->request->post['adaptive_pay_title'])) {
			$data['adaptive_pay_title'] = $this->request->post['adaptive_pay_title'];
		} else {
			$data['adaptive_pay_title'] = $this->config->get('adaptive_pay_title');
		}
		
		if (isset($this->request->post['adaptive_pay_adminemail'])) {
			$data['adaptive_pay_adminemail'] = $this->request->post['adaptive_pay_adminemail'];
		} else {
			$data['adaptive_pay_adminemail'] = $this->config->get('adaptive_pay_adminemail');
		}

		if (isset($this->request->post['adaptive_pay_gatway'])) {
			$data['adaptive_pay_gatway'] = $this->request->post['adaptive_pay_gatway'];
		} else {
			$data['adaptive_pay_gatway'] = $this->config->get('adaptive_pay_gatway');
		}

		if (isset($this->request->post['adaptive_pay_merchant_payid'])) {
			$data['adaptive_pay_merchant_payid'] = $this->request->post['adaptive_pay_merchant_payid'];
		} else {
			$data['adaptive_pay_merchant_payid'] = $this->config->get('adaptive_pay_merchant_payid');
		}

		if (isset($this->request->post['adaptive_pay_application_payid'])) {
			$data['adaptive_pay_application_payid'] = $this->request->post['adaptive_pay_application_payid'];
		} else {
			$data['adaptive_pay_application_payid'] = $this->config->get('adaptive_pay_application_payid');
		}




		if (isset($this->request->post['adaptive_pay_username'])) {
			$data['adaptive_pay_username'] = $this->request->post['adaptive_pay_username'];
		} else {
			$data['adaptive_pay_username'] = $this->config->get('adaptive_pay_username');
		}
		
		if (isset($this->request->post['adaptive_pay_password'])) {
			$data['adaptive_pay_password'] = $this->request->post['adaptive_pay_password'];
		} else {
			$data['adaptive_pay_password'] = $this->config->get('adaptive_pay_password');
		}
				
		if (isset($this->request->post['adaptive_pay_signature'])) {
			$data['adaptive_pay_signature'] = $this->request->post['adaptive_pay_signature'];
		} else {
			$data['adaptive_pay_signature'] = $this->config->get('adaptive_pay_signature');
		}
		
		if (isset($this->request->post['adaptive_pay_test'])) {
			$data['adaptive_pay_test'] = $this->request->post['adaptive_pay_test'];
		} else {
			$data['adaptive_pay_test'] = $this->config->get('adaptive_pay_test');
		}
		
		
		if (isset($this->request->post['adaptive_pay_method'])) {
			$data['adaptive_pay_method'] = $this->request->post['adaptive_pay_method'];
		} else {
			$data['adaptive_pay_method'] = $this->config->get('adaptive_pay_method');
		}
		
		if (isset($this->request->post['adaptive_pay_total'])) {
			$data['adaptive_pay_total'] = $this->request->post['adaptive_pay_total'];
		} else {
			$data['adaptive_pay_total'] = $this->config->get('adaptive_pay_total'); 
		} 

		$data['entry_canceled_reversal_status'] = $this->language->get('entry_canceled_reversal_status');
		$data['entry_completed_status'] = $this->language->get('entry_completed_status');
		$data['entry_denied_status'] = $this->language->get('entry_denied_status');
		$data['entry_expired_status'] = $this->language->get('entry_expired_status');
		$data['entry_failed_status'] = $this->language->get('entry_failed_status');
		$data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$data['entry_processed_status'] = $this->language->get('entry_processed_status');
		$data['entry_refunded_status'] = $this->language->get('entry_refunded_status');
		$data['entry_reversed_status'] = $this->language->get('entry_reversed_status');
		$data['entry_voided_status'] = $this->language->get('entry_voided_status');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['tab_order_status'] = $this->language->get('tab_order_status');
				
		if (isset($this->request->post['adaptive_pay_order_status_id'])) {
			$data['adaptive_pay_order_status_id'] = $this->request->post['adaptive_pay_order_status_id'];
		} else {
			$data['adaptive_pay_order_status_id'] = $this->config->get('adaptive_pay_order_status_id'); 
		} 

		if (isset($this->request->post['adaptive_pay_expired_status_id'])) {
			$data['adaptive_pay_expired_status_id'] = $this->request->post['adaptive_pay_expired_status_id'];
		} else {
			$data['adaptive_pay_expired_status_id'] = $this->config->get('adaptive_pay_expired_status_id');
		}


		if (isset($this->request->post['adaptive_pay_denied_status_id'])) {
			$data['adaptive_pay_denied_status_id'] = $this->request->post['adaptive_pay_denied_status_id'];
		} else {
			$data['adaptive_pay_denied_status_id'] = $this->config->get('adaptive_pay_denied_status_id'); 
		}
		
		if (isset($this->request->post['adaptive_pay_completed_status_id'])) {
			$data['adaptive_pay_completed_status_id'] = $this->request->post['adaptive_pay_completed_status_id'];
		} else {
			$data['adaptive_pay_completed_status_id'] = $this->config->get('adaptive_pay_completed_status_id'); 
		}

		if (isset($this->request->post['adaptive_pay_canceled_reversal_status_id'])) {
			$data['adaptive_pay_canceled_reversal_status_id'] = $this->request->post['adaptive_pay_canceled_reversal_status_id'];
		} else {
			$data['adaptive_pay_canceled_reversal_status_id'] = $this->config->get('adaptive_pay_canceled_reversal_status_id'); 
		}

		if (isset($this->request->post['adaptive_pay_canceled_reversal_status_id'])) {
			$data['adaptive_pay_canceled_reversal_status_id'] = $this->request->post['adaptive_pay_canceled_reversal_status_id'];
		} else {
			$data['adaptive_pay_canceled_reversal_status_id'] = $this->config->get('adaptive_pay_canceled_reversal_status_id'); 
		}

		if (isset($this->request->post['adaptive_pay_failed_status_id'])) {
			$data['adaptive_pay_failed_status_id'] = $this->request->post['adaptive_pay_failed_status_id'];
		} else {
			$data['adaptive_pay_failed_status_id'] = $this->config->get('adaptive_pay_failed_status_id');
		}

		if (isset($this->request->post['adaptive_pay_processed_status_id'])) {
			$data['adaptive_pay_processed_status_id'] = $this->request->post['adaptive_pay_processed_status_id'];
		} else {
			$data['adaptive_pay_processed_status_id'] = $this->config->get('adaptive_pay_processed_status_id');
		}

		if (isset($this->request->post['adaptive_pay_refunded_status_id'])) {
			$data['adaptive_pay_refunded_status_id'] = $this->request->post['adaptive_pay_refunded_status_id'];
		} else {
			$data['adaptive_pay_refunded_status_id'] = $this->config->get('adaptive_pay_refunded_status_id');
		}

		if (isset($this->request->post['adaptive_pay_reversed_status_id'])) {
			$data['adaptive_pay_reversed_status_id'] = $this->request->post['adaptive_pay_reversed_status_id'];
		} else {
			$data['adaptive_pay_reversed_status_id'] = $this->config->get('adaptive_pay_reversed_status_id');
		}

		if (isset($this->request->post['adaptive_pay_voided_status_id'])) {
			$data['adaptive_pay_voided_status_id'] = $this->request->post['adaptive_pay_voided_status_id'];
		} else {
			$data['adaptive_pay_voided_status_id'] = $this->config->get('adaptive_pay_voided_status_id');
		}

		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['adaptive_pay_geo_zone_id'])) {
			$data['adaptive_pay_geo_zone_id'] = $this->request->post['adaptive_pay_geo_zone_id'];
		} else {
			$data['adaptive_pay_geo_zone_id'] = $this->config->get('adaptive_pay_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['adaptive_pay_status'])) {
			$data['adaptive_pay_status'] = $this->request->post['adaptive_pay_status'];
		} else {
			$data['adaptive_pay_status'] = $this->config->get('adaptive_pay_status');
		}
		
		if (isset($this->request->post['adaptive_pay_sort_order'])) {
			$data['adaptive_pay_sort_order'] = $this->request->post['adaptive_pay_sort_order'];
		} else {
			$data['adaptive_pay_sort_order'] = $this->config->get('adaptive_pay_sort_order');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/adaptive_pay', $data));


	

		
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/adaptive_pay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['adaptive_pay_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['adaptive_pay_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if (!$this->request->post['adaptive_pay_signature']) {
			$this->error['signature'] = $this->language->get('error_signature');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>