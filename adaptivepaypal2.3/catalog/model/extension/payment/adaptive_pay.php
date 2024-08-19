<?php 
class ModelExtensionPaymentAdaptivePay extends Model {
  	public function getMethod($address, $total) {
		$this->language->load('extension/payment/adaptive_pay');
		
		
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('adaptive_pay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		

		if ($this->config->get('adaptive_pay_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('adaptive_pay_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		
		
		

		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY'
		);
		
		if (!in_array(strtoupper($this->session->data['currency']), $currencies)) {
			$status = false;
		}			
					
		$method_data = array();

		//echo $status.'-status';
	
		if ($status) {  
      		$method_data = array( 
        		'code'       => 'adaptive_pay',
        		'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('adaptive_pay_sort_order')
      		);
    	}
   	
   		//print_r($this->config);

    	return $method_data;
  	}
}
?>