<?php
class ControllerPaymentKyash extends Controller {
	public function index() {
		$data['button_confirm'] = 'Confirm Order';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/cod.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/kyash/kyash.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/kyash/kyash.tpl', $data);
		}
	}

	public function placeorder() 
	{
		$this->load->model('setting/setting');
		$config = $this->model_setting_setting->getSetting('kyash');
		
		$this->load->library('log');
		$this->logger = new Log('kyash.log');
		
		require_once(DIR_SYSTEM.'lib/KyashPay.php');
		$api = new KyashPay($config['kyash_public_api_id'],$config['kyash_api_secrets']);
		$api->setLogger($this->logger);
		
		$this->load->model('checkout/order');
		$this->load->model('payment/kyash');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$params = $this->model_payment_kyash->getOrderParams($order_info);
		$response = $api->createKyashCode($params);
		
		$json = array();
		if(isset($response['status']) && $response['status'] == 'error')
		{
			$json['error'] = 'Payment error. '.$response['message'];
		}
		else
		{
			$message = '';
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1,$message, false);
			$this->model_payment_kyash->updateKyashCode($order_info['order_id'],$response['id']);
			$this->model_payment_kyash->updateKyashStatus($order_info['order_id'],'pending');
			$this->model_payment_kyash->updatePaymentMethod($order_info['order_id'],', Kyash code - '.$response['id']);
			$json['success'] = $this->url->link('checkout/success').'&order_id='.$order_info['order_id'];
		}
		$this->response->setOutput(json_encode($json));
	}	 

	public function getPaymentPoints() 
	{
		$this->load->model('setting/setting');
		$config = $this->model_setting_setting->getSetting('kyash');
		
		$this->load->library('log');
		$this->logger = new Log('kyash.log');
		
		require_once(DIR_SYSTEM.'lib/KyashPay.php');
		$api = new KyashPay($config['kyash_public_api_id'],$config['kyash_api_secrets']);
		$api->setLogger($this->logger);

		$pincode = $this->request->get['postcode'];
		$response = $api->getPaymentPoints($pincode);
		if(isset($response['status']) && $response['status'] == 'error')
		{
			$data['error'] = $response['message'];
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/kyash/error.tpl')) {
				$template = $this->config->get('config_template') . '/template/payment/kyash/error.tpl';
			} else {
				$template = 'default/template/payment/kyash/error.tpl';
			}	
			echo $this->response->setOutput($this->load->view($template, $data));
		}
		else
		{
			$data['payments'] = $response;
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/kyash/payment_points.tpl')) {
				$template = $this->config->get('config_template') . '/template/payment/kyash/payment_points.tpl';
			} else {
				$template = 'default/template/payment/kyash/payment_points.tpl';
			}	
	
			echo $this->response->setOutput($this->load->view($template, $data));
		}
	}
	
	public function getPaymentPoints2() 
	{
		$this->load->model('setting/setting');
		$config = $this->model_setting_setting->getSetting('kyash');
		
		$this->load->library('log');
		$this->logger = new Log('kyash.log');
		
		require_once(DIR_SYSTEM.'lib/KyashPay.php');
		$api = new KyashPay($config['kyash_public_api_id'],$config['kyash_api_secrets']);
		$api->setLogger($this->logger);
		
		$pincode = $this->request->get['postcode'];
		$response = $api->getPaymentPoints($pincode);
		if(isset($response['status']) && $response['status'] == 'error')
		{
			$data['error'] = $response['message'];
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/kyash/error.tpl')) {
				$template = $this->config->get('config_template') . '/template/payment/kyash/error.tpl';
			} else {
				$template = 'default/template/payment/kyash/error.tpl';
			}	
			echo $this->response->setOutput($this->load->view($template, $data));
		}
		else
		{
			$data['payments'] = $response;
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/kyash/payment_points2.tpl')) {
				$template = $this->config->get('config_template') . '/template/payment/kyash/payment_points2.tpl';
			} else {
				$template = 'default/template/payment/kyash/payment_points.tpl';
			}	
	
			echo $this->response->setOutput($this->load->view($template, $data));
		}
	}
	
	public function handler()
    {
		$this->load->model('setting/setting');
		$config = $this->model_setting_setting->getSetting('kyash');
		
		$this->load->library('log');
		$this->logger = new Log('kyash.log');
		
		require_once(DIR_SYSTEM.'lib/KyashPay.php');
		$api = new KyashPay($config['kyash_public_api_id'],$config['kyash_api_secrets']);
		$api->setLogger($this->logger);
		
		$this->load->model('checkout/order');
		$this->load->model('payment/kyash');
		
		$params = array();
		$params['order_id'] = trim($this->request->post['order_id']);
		$params['kyash_code'] = trim($this->request->post['kyash_code']);
		$params['status'] = trim($this->request->post['status']);
		$params['paid_by'] = trim($this->request->post['paid_by']);
		$params['amount'] = trim($this->request->post['amount']);
															   
		$order_id = trim($params['order_id']);
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if(!$order_info)
		{
			$this->logger->write("HTTP/1.1 500 Order is not found");
			header("HTTP/1.1 500 Order is not found");
			exit;								 
		}
		else
		{
			$url = $this->url->link('payment/kyash/handler');
			$updater = new KyashUpdater($this->model_checkout_order,$this->model_payment_kyash,$order_id);
			$api->handler($params,$this->model_payment_kyash->getOrderInfo($order_id,'kyash_code'),$this->model_payment_kyash->getOrderInfo($order_id,'kyash_status'),$url,$updater);
		}
	}
}

class KyashUpdater
{
	public $order = NULL;
	public $kyash = NULL;
	public $order_id = NULL;
	
	public function __construct($order,$kyash,$order_id)
	{
		$this->order = $order;
		$this->kyash = $kyash;
		$this->order_id = $order_id;
	}
	
	public function update($status,$comment)
	{
		if($status == 'paid')
		{
			$this->order->addOrderHistory($this->order_id,2,$comment);
			$this->kyash->updateKyashStatus($this->order_id,'paid');
		}
		else if($status == 'expired')
		{
			$this->order->addOrderHistory($this->order_id,7,$comment);
			$this->kyash->updateKyashStatus($this->order_id,'expired');
		}
	}
}
?>