<?php 
class ModelExtensionPaymentRoskassa extends Model
{
  	public function getMethod($address, $total) 
	{
		$this->load->language('extension/payment/roskassa');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('roskassa_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
		if ($this->config->get('roskassa_total') > 0 && $this->config->get('roskassa_total') > $total)
		{
			$status = false;
		} 
		elseif (!$this->config->get('roskassa_geo_zone_id'))
		{
			$status = true;
		}
		elseif ($query->num_rows) 
		{
			$status = true;
		}
		else 
		{
			$status = false;
		}
		
		$currencies = array(
			'RUB',
			'USD',
			'EUR'
		);

		if (!in_array(strtoupper($this->session->data['currency']), $currencies))
		{
			$status = false;
		}
		
		$method_data = array();
	
		if ($status)
		{
      		$method_data = array( 
        		'code'       => 'roskassa',
        		'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('roskassa_sort_order')
      		);
    	}

    	return $method_data;
  	}
}
?>