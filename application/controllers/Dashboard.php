<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends APP_Controller {
    public function __construct() {
        parent::__construct();
        $this->_check_login_status_default_page();
    }

    public function index() {
        $this->_site_data['site_title'] = "Selamat datang {$this->_site_data['auth_username']}";
        $this->_site_data['total_category'] = $this->Kategori_model->getTotalCategory();
        $this->_site_data['total_sub_category'] = $this->Kategori_model->getTotalSubCategory();
        $this->_site_data['total_product'] = $this->Produk_model->getTotalProduct();
        $this->load->view('main-template/header', $this->_site_data);
        $this->load->view('main-template/navbar', $this->_site_data);
        $this->load->view('main-template/sidebar', $this->_site_data);
        $this->load->view('dashboard/main', $this->_site_data);
        $this->load->view('main-template/footer', $this->_site_data);
    }
}