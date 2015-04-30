<?php

class ModelPaymentKyash extends Model {
    public function getMethod($address, $total) {
        $this->language->load('payment/kyash');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('kyash_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('kyash_total') > 0 && $this->config->get('kyash_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('kyash_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $additional = '';
            if ($_REQUEST['route'] == 'checkout/payment_method') {
                $additional = $this->getShopsLink($address['postcode']);
            }

            $method_data = array(
                'code' => 'kyash',
                'title' => $this->language->get('text_title') . $additional,
                'terms' => '',
                'sort_order' => $this->config->get('kyash_sort_order')
            );
        }

        return $method_data;
    }

    public function getShopsLink($postcode) {
        $this->language->load('payment/kyash');
        if (empty($postcode)) {
            $postcode = 'Enter Pincode';
        }
        $url = $this->url->link('payment/kyash/getPaymentPoints');
        $css = '<link href="catalog/view/theme/default/stylesheet/kyash.css" rel="stylesheet">';
        $html = '
		<span id="kyash_postcode_payment">
			<a href="javascript:void(0);" onclick=\'openShops("' . $url . '","")\' id="kyash_open">
			See nearby shops
			</a>
		   
			<span id="kyash_postcode_payment_sub">
				<input type="text" class="input-text" id="kyash_postcode" value="' . $postcode . '" maxlength="12" />
				<input type="button" class="button" id="kyash_postcode_button" value="See nearby shops" onclick=\'pullNearByShops("' . $url . '","")\'>
				<a href="javascript:void(0);" onclick="closeShops()" id="kyash_close" style="float:right">X</a>
			</span>
		</span>
		<div style="display: none" id="see_nearby_shops_container" class="content">
		</div>';
        $js = '<script src="catalog/view/javascript/kyash.js" type="text/javascript"></script>';
        return $css . $html . $js;
    }

    public function getOrderParams($order_info) {
        $address1 = $order_info['payment_address_1'];
        if ($order_info['payment_address_2']) {
            $address1 .= ',' . $order_info['payment_address_2'];
        }

        $address2 = $order_info['shipping_address_1'];
        if ($order_info['shipping_address_2']) {
            $address2 .= ',' . $order_info['shipping_address_2'];
        }

        $params = array(
            'order_id' => $order_info['order_id'],
            'amount' => $order_info['total'],
            'billing_contact.first_name' => $order_info['payment_firstname'],
            'billing_contact.last_name' => $order_info['payment_lastname'],
            'billing_contact.email' => $order_info['email'],
            'billing_contact.address' => $address1,
            'billing_contact.city' => $order_info['payment_city'],
            'billing_contact.state' => $order_info['payment_zone'],
            'billing_contact.pincode' => $order_info['payment_postcode'],
            'billing_contact.phone' => $order_info['telephone'],
            'shipping_contact.first_name' => $order_info['shipping_firstname'],
            'shipping_contact.last_name' => $order_info['shipping_lastname'],
            'shipping_contact.address' => $address2,
            'shipping_contact.city' => $order_info['shipping_city'],
            'shipping_contact.state' => $order_info['shipping_zone'],
            'shipping_contact.pincode' => $order_info['shipping_postcode'],
            'shipping_contact.phone' => $order_info['telephone']
        );

        return http_build_query($params);
    }

    public function updateKyashCode($order_id, $code) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET kyash_code = '" . $code . "' WHERE order_id = '" . (int)$order_id . "'");
    }

    public function updateKyashStatus($order_id, $status) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET kyash_status = '" . $status . "' WHERE order_id = '" . (int)$order_id . "'");
    }

    public function updatePaymentMethod($order_id, $additional) {
        $this->language->load('payment/kyash');
        $method = $this->language->get('text_title') . $additional;
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = '" . $method . "' WHERE order_id = '" . (int)$order_id . "'");
    }

    public function getOrderInfo($order_id, $column) {
        $result = $this->db->query('SELECT ' . $column . ' FROM ' . DB_PREFIX . 'order  WHERE order_id = ' . (int)$order_id);
        if (isset($result->row[$column])) {
            return $result->row[$column];
        }
    }

    public function getSuccessContent($order_id) {
        if ($order_id > 0) {
            $kyash_code = $this->getOrderInfo($order_id, 'kyash_code');
            if (empty($kyash_code))
                return '';

            $this->load->model('setting/setting');
            $config = $this->model_setting_setting->getSetting('kyash');

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $postcode = $order_info['payment_postcode'];
            if (empty($postcode)) {
                $postcode = 'Enter Pincode';
            }

            $url = $this->url->link('payment/kyash/getPaymentPoints2');
            $css = '<link href="catalog/view/theme/default/stylesheet/kyash.css" rel="stylesheet">';
            $html = '
			<div class="kyash_succcess_instructions" style="border-top:1px solid #ededed; margin-top:60px">
				<h4>KyashCode: ' . $kyash_code . '</h4>
				<p>' . nl2br(html_entity_decode($config['kyash_instructions'])) . '</p>
			</div>
			<div class="kyash_succcess_instructions2">
				<input type="text" class="input-text" id="postcode" value="' . $postcode . '" maxlength="12" style="width:120px; text-align:center"
				onblur="if(this.value ==\'\'){this.value=\'Enter Pincode\';}" 
				onclick="if(this.value == \'Enter Pincode\'){this.value=\'\';}" />
				<input type="button" class="button" id="kyash_postcode_button" value="See nearby shops" onclick="preparePullShops(\'' . $url . '\')">
			</div>
			<div style="display: none" id="see_nearby_shops_container" class="content">
			</div>
			';

            $js = '
			<script src="catalog/view/javascript/kyash_success.js" type="text/javascript"></script>
			<script>preparePullShops("' . $url . '");</script>
			';
            return $css . $html . $js;
        }
    }

    public function update($order_id) {
        $this->load->model('setting/setting');
        $config = $this->model_setting_setting->getSetting('kyash');

        $this->load->library('log');
        $this->logger = new Log('kyash.log');

        require_once(DIR_SYSTEM . 'lib/KyashPay.php');
        $api = new KyashPay($config['kyash_public_api_id'], $config['kyash_api_secrets']);
        $api->setLogger($this->logger);

        $this->load->model('checkout/order');

        if ($order_id > 0) {
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $kyash_code = $this->getOrderInfo($order_id, 'kyash_code');
            $kyash_status = $this->getOrderInfo($order_id, 'kyash_status');

            if ($order_info && !empty($kyash_code)) {
                if ($order_info['order_status_id'] == 7) {
                    if ($kyash_status == 'pending' || $kyash_status == 'paid') {
                        $response = $api->cancel($kyash_code);
                        if (isset($response['status']) && $response['status'] == 'error') {
                            return '<span class="error">' . $response['message'] . '</span>';
                        } else {
                            $this->updateKyashStatus($order_id, 'cancelled');
                            $message = '<br/>Kyash payment collection has been cancelled for this order.';
                            return $message;
                        }
                    } else if ($kyash_status == 'captured') {
                        $message = 'Customer payment has already been transferred to you. Refunds if any, are to be handled by you.';
                        return $message;
                    }
                } else if ($order_info['order_status_id'] == 3) {
                    if ($kyash_status == 'pending') {
                        $response = $api->cancel($kyash_code);
                        if (isset($response['status']) && $response['status'] == 'error') {
                            return '<span class="error">' . $response['message'] . '</span>';
                        } else {
                            $this->updateKyashStatus($order_id, 'cancelled');
                            $message = '<br/>You have shipped before Kyash payment was done. Kyash payment collection has been cancelled for this order.';
                            return $message;
                        }
                    } else if ($kyash_status == 'paid') {
                        $response = $api->capture($kyash_code);
                        if (isset($response['status']) && $response['status'] == 'error') {
                            return '<span class="error">' . $response['message'] . '</span>';
                        } else {
                            $this->updateKyashStatus($order_id, 'captured');
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