<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class APP_Controller extends CI_Controller
{
    protected $_site_data;
    protected $_login_status;
    protected $_current_date;
    public function __construct() {
        parent::__construct();
        $this->_site_data = [
            'css_path' => $this->config->item('assets_css_path'),
            'img_path' => $this->config->item('assets_img_path'),
            'js_path' => $this->config->item('assets_js_path'),
            'auth_username' => "",
            'auth_userid' => "",
            'auth_user_photo_profile' => ""
        ];
        $this->_login_status = false;
        $this->_current_date = date('Y-m-d H:i:s');
    }


    public function _check_token() {
        // echo $this->input->cookie("user_auth_token");
        $user_auth_token_cookie = $this->input->cookie("user_auth_token");
        $user_auth_token_session = $this->session->userdata("user_auth_token");
        $data = [];
        $current_time = time();
        if($user_auth_token_cookie != "") {
            $data = [
                'token' => $user_auth_token_cookie,
                'and_expires >=' => $current_time
            ];
        }
        else if($user_auth_token_session != "") {
            $data = [
                'token' => $user_auth_token_session,
                'expires' => 0
            ];
        }
        $db_user = $this->Auth_model->getAuthToken($data);
        if($db_user !== false) {
            $this->session->set_userdata("user_auth_token", $data['token']);
            $this->session->set_userdata("auth_id", $db_user->userid);
            $this->session->set_userdata("auth_username", $db_user->username);
            $this->_login_status = true;
            $this->_site_data['auth_username'] = $db_user->username;
            $this->_site_data['auth_userid'] = $db_user->userid;
        }
        else {
            $token = !empty($this->session->userdata('auth_user_token')) ? $this->session->userdata('auth_user_token') : !empty($this->input->cookie("user_auth_token")) ? $this->input->cookie("user_auth_token") : ''; // Ambil token
            $this->Auth_model->deleteAuthToken($token);
            delete_cookie("user_auth_token");
            $this->session->sess_destroy();
        }
    }

    public function _check_login_status_login_page() {
        $this->_check_token();
        if($this->_login_status) {
            redirect(base_url("dashboard")); // Redirect jika Sudah login
        }
    }

    public function _check_login_status_default_page() {
        $this->_check_token();
        if(!$this->_login_status) {
            redirect(base_url("auth"));
        }
    }


    // error 404
    public function page404() {
        $this->_site_data['site_title'] = "Error 404 - Halaman tidak ditemukan!";
        $this->_site_data['detail_err400'] = "Halaman yang anda tuju tidak dapat ditemukan!";
        $this->load->view('web-error/err_400', $this->_site_data);
    }
    // error 403
    public function page403() {
        $this->_site_data['site_title'] = "Error 403 - Forbidden page";
        $this->_site_data['detail_err400'] = "Anda dilarang mengakses halaman ini!";
        $this->load->view('web-error/err_400', $this->_site_data);
    }
}