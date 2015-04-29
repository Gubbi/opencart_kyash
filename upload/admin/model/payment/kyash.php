<?php
class ModelPaymentKyash extends Model 
{
	public function install() 
	{
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD kyash_code VARCHAR(50)");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD kyash_status VARCHAR(50)");
	}

	public function uninstall() 
	{
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` DROP  kyash_code");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` DROP  kyash_status");
	}
	
	public function getOrderInfo($order_id,$column)
	{
		$result = $this->db->query('SELECT '.$column.' FROM '.DB_PREFIX.'order  WHERE order_id = '.(int)$order_id);
		if(isset($result->row[$column]))
		{
			return $result->row[$column];
		}
	}
	
	public function updateKyashStatus($order_id,$status)
    {
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET kyash_status = '".$status."' WHERE order_id = '" . (int)$order_id . "'");
	}
	
	public function update($order_id)
	{
		$this->load->model('setting/setting');
		$config = $this->model_setting_setting->getSetting('kyash');
		
		$this->load->library('log');
		$this->logger = new Log('kyash.log');
		
		require_once(DIR_SYSTEM.'lib/KyashPay.php');
		$api = new KyashPay($config['kyash_public_api_id'],$config['kyash_api_secrets']);
		$api->setLogger($this->logger);
		
		$this->load->model('sale/order');
		if($order_id > 0)
		{
			$order_info = $this->model_sale_order->getOrder($order_id);
			$kyash_code = $this->getOrderInfo($order_id,'kyash_code');
			$kyash_status = $this->getOrderInfo($order_id,'kyash_status');
			
			if ($order_info && !empty($kyash_code)) 
			{
				if($order_info['order_status_id'] == 7 )
				{
					if($kyash_status == 'pending' || $kyash_status == 'paid')
					{
						$response = $api->cancel($kyash_code);
						if(isset($response['status']) && $response['status'] == 'error')
						{
							return '<span class="error">'.$response['message'].'</span>';
						}
						else
						{
							$this->updateKyashStatus($order_id,'cancelled');
							$message = '<br/>Kyash payment collection has been cancelled for this order.';
							return $message;
						}
					}
					else if($kyash_status == 'captured')
					{
						$message = 'Customer payment has already been transferred to you. Refunds if any, are to be handled by you.';
						return $message;
					}
				}
				else if($order_info['order_status_id'] == 3 )
				{
					if($kyash_status == 'pending')
					{
						$response = $api->cancel($kyash_code);
						if(isset($response['status']) && $response['status'] == 'error')
						{
							return '<span class="error">'.$response['message'].'</span>';
						}
						else
						{
							$this->updateKyashStatus($order_id,'cancelled');
							$message = '<br/>You have shipped before Kyash payment was done. Kyash payment collection has been cancelled for this order.';
							return $message;
						}
					}
					else if($kyash_status == 'paid')
					{
						$response = $api->capture($kyash_code);
						if(isset($response['status']) && $response['status'] == 'error')
						{
							return '<span class="error">'.$response['message'].'</span>';
						}
						else
						{
							$this->updateKyashStatus($order_id,'captured');
							$message = '<br/>Kyash payment has been successfully captured.';
							return $message;
						}
					}
				}
			}
		}
	}
}
?>