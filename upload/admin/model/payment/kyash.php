<?php
require_once(DIR_SYSTEM . 'lib/common.php');

class ModelPaymentKyash extends KyashModel {
    function __construct() {
        $this->load->model('sale/order');
        $this->model_order = $this->model_sale_order;
        parent::__construct();
    }

    public function install() {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD kyash_code VARCHAR(50)");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD kyash_status VARCHAR(50)");
    }

    public function uninstall() {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` DROP  kyash_code");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` DROP  kyash_status");
    }
}
?>