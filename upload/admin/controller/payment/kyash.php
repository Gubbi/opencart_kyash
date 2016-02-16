<?php

class ControllerPaymentKyash extends Controller {
    private $error = array();

    public function install() {
        $this->load->model('payment/kyash');
        $this->model_payment_kyash->install();
    }

    public function uninstall() {
        $this->load->model('payment/kyash');
        $this->model_payment_kyash->uninstall();
    }

    public function index() {
        $this->language->load('payment/kyash');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('kyash', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_payment'] = $this->language->get('text_payment');

        $data['entry_public_api_id'] = $this->language->get('entry_public_api_id');
        $data['entry_api_secrets'] = $this->language->get('entry_api_secrets');
        $data['entry_callback_secret'] = $this->language->get('entry_callback_secret');
        $data['entry_hmac_secret'] = $this->language->get('entry_hmac_secret');
        $data['entry_callback_url'] = $this->language->get('entry_callback_url');
        $data['entry_pg_text'] = $this->language->get('entry_pg_text');

        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['help_total'] = $this->language->get('help_total');
        $data['help_pg_text'] = $this->language->get('help_pg_text');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['public_api_id'])) {
            $data['error_public_api_id'] = $this->error['public_api_id'];
        } else {
            $data['error_public_api_id'] = '';
        }

        if (isset($this->error['api_secrets'])) {
            $data['error_api_secrets'] = $this->error['api_secrets'];
        } else {
            $data['error_api_secrets'] = '';
        }

        if (isset($this->error['callback_secret'])) {
            $data['error_callback_secret'] = $this->error['callback_secret'];
        } else {
            $data['error_callback_secret'] = '';
        }

        if (isset($this->error['hmac_secret'])) {
            $data['error_hmac_secret'] = $this->error['hmac_secret'];
        } else {
            $data['error_hmac_secret'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/kyash', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('payment/kyash', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['kyash_public_api_id'])) {
            $data['public_api_id'] = $this->request->post['kyash_public_api_id'];
        } else {
            $data['public_api_id'] = $this->config->get('kyash_public_api_id');
        }

        if (isset($this->request->post['kyash_api_secrets'])) {
            $data['api_secrets'] = $this->request->post['kyash_api_secrets'];
        } else {
            $data['api_secrets'] = $this->config->get('kyash_api_secrets');
        }

        if (isset($this->request->post['kyash_callback_secret'])) {
            $data['callback_secret'] = $this->request->post['kyash_callback_secret'];
        } else {
            $data['callback_secret'] = $this->config->get('kyash_callback_secret');
        }

        if (isset($this->request->post['kyash_hmac_secret'])) {
            $data['hmac_secret'] = $this->request->post['kyash_hmac_secret'];
        } else {
            $data['hmac_secret'] = $this->config->get('kyash_hmac_secret');
        }

        if (isset($this->request->post['kyash_pg_text'])) {
            $data['pg_text'] = $this->request->post['kyash_pg_text'];
        } else {
            $data['pg_text'] = $this->config->get('kyash_pg_text');
            if (empty($data['pg_text'])) {
                $data['pg_text'] = 'Kyash - Pay at a nearby Shop';
            }
        }

        if (isset($this->request->post['kyash_total'])) {
            $data['kyash_total'] = $this->request->post['kyash_total'];
        } else {
            $data['kyash_total'] = $this->config->get('kyash_total');
        }

        if (isset($this->request->post['kyash_geo_zone_id'])) {
            $data['kyash_geo_zone_id'] = $this->request->post['kyash_geo_zone_id'];
        } else {
            $data['kyash_geo_zone_id'] = $this->config->get('kyash_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['kyash_status'])) {
            $data['kyash_status'] = $this->request->post['kyash_status'];
        } else {
            $data['kyash_status'] = $this->config->get('kyash_status');
        }

        if (isset($this->request->post['kyash_sort_order'])) {
            $data['kyash_sort_order'] = $this->request->post['kyash_sort_order'];
        } else {
            $data['kyash_sort_order'] = $this->config->get('kyash_sort_order');
        }

        $data['callback_url'] = HTTPS_CATALOG . 'index.php?route=payment/kyash/handler';

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/kyash.tpl', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'payment/kyash')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['kyash_api_secrets']) {
            $this->error['api_secrets'] = $this->language->get('error_api_secrets');
        }

        if (!$this->request->post['kyash_public_api_id']) {
            $this->error['public_api_id'] = $this->language->get('error_public_api_id');
        }

        if (!$this->request->post['kyash_callback_secret']) {
            $this->error['callback_secret'] = $this->language->get('error_callback_secret');
        }

        if (!$this->request->post['kyash_hmac_secret']) {
            $this->error['hmac_secret'] = $this->language->get('error_hmac_secret');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}

?>