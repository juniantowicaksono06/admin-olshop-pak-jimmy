<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends APP_Controller {
    public function __construct() {
        parent::__construct();
        $this->notify->setNotify("notifyStatusError", false);
        $this->_site_data['remember_me_value'] = "remember-me";
    }

    public function index() {
        $this->login();
    }

    public function login() {
        $this->_check_login_status_login_page();
        $this->_site_data['site_title'] = "Halaman login!";
        $this->load->view('auth/main_login', $this->_site_data);
    }

    public function check_login() {
        if($this->input->is_ajax_request()) {
            $post_input = $this->input->post(); // Ambil data user dari input post
            // Atur validasi
            $validation_config = [
                [
                    // Username input rule validation
                    'field' => 'usernameInput',
                    'label' => 'Username',
                    'rules' => 'required|max_length[30]'
                ],
                [
                    // Password input rule validation
                    'field' => 'passwordInput',
                    'label' => 'Password',
                    'rules' => 'required|max_length[30]'
                ],
                [
                    // Remember Me Input rule validation
                    'field' => "rememberMeInput",
                    'label' => "Ingat Saya",
                    'rules' => 'alpha_dash|max_length[30]'
                    ]
                ];
                $this->form_validation->set_rules($validation_config);
                if($this->form_validation->run()) {
                // echo json_encode(["tes" => $post_input]);
                // die();
                $input_username = $post_input['usernameInput']; // Set variable username
                $input_password = $post_input['passwordInput']; // Set variable password
                $db_data = $this->Auth_model->getUser("username|{$input_username}"); // Ambil data username dari database
                if(is_object($db_data)) {
                    if(password_verify($input_password, $db_data->password)) {
                        $this->token->generateToken("H0lYSw0rd3xc4l1buR", 20); // Buat token
                        $username_token = $this->token->getGeneratedToken(); // Ambil token
                        // Set session
                        $this->session->set_userdata("user_auth_token", $username_token);
                        $this->session->set_userdata("auth_userid", $db_data->userid);
                        $this->session->set_userdata("auth_username", $input_username);
                        
                        $cookie_expire_time = 0; // Atur waktu expire jadi 3 hari
                        $auth_ID = $this->Auth_model->generateID("user_admin_auth_tokens"); // Auth ID

                        $auth_data = [
                            'authID' => $auth_ID,
                            'token' => $username_token,
                            'userID' => $db_data->userid
                        ];
                        
                        // Atur cookie jika checkbox remember me dicentang
                        if(array_key_exists("rememberMeInput", $post_input)) {
                            if($post_input["rememberMeInput"] == $this->_site_data['remember_me_value']) { // Cek remember me di centang?
                                $mod = 259200;
                                $cookie_expire_time = time() + $mod;
                                $user_cookie = [
                                    'name' => "user_auth_token", // Atur nama cookie
                                    'value' => $username_token,
                                    'expire' => $mod// Atur waktu kedaluarsa
                                ];
                                $this->input->set_cookie($user_cookie);
                            }
                        }
                        $auth_data['expires'] = $cookie_expire_time;
                        $this->Auth_model->insertAuthToken($auth_data); // Input data token ke database!
                        
                        $this->notify->setNotify("notifySuccess", "Berhasil login!"); // Atur pesan sukses
                        $this->notify->setNotify("notifyRedirect", base_url("dashboard"));
                        $this->notify->setNotify("notifyRedirectTimeout", 4000);
                        echo json_encode($this->notify->getnotifyList()); // Kembalikan pesan sukses ke client berupa JSON
                        exit(); // Hentikan script
                    }
                }
                $this->notify->setNotify("notifyStatusError", true); // Atur status error jadi True
                $this->notify->setNotify("notifyFailed", "Username atau password salah!"); // Atur pesan gagal
            }
            else {
                $this->notify->setNotify('notifyStatusError', true); // Atur status error jadi True
                $this->notify->setNotify('notifyInputError|usernameInput', form_error('usernameInput')); // Atur pesan input gagal username input
                $this->notify->setNotify('notifyInputError|passwordInput', form_error('passwordInput')); // Atur pesan input gagal password input
                $this->notify->setNotify('notifyInputError|rememberMeInput', form_error('rememberMeInput'));
            }
            echo json_encode($this->notify->getNotifyList()); // Kembalikan pesan gagal ke client berupa JSON
            exit();
        }
        else {
            $this->page403();  // Redirect ke Halaman 403 / Forbidden Page
        }
    }


    public function logout() {
        $token = "";
        if(!empty($this->session->userdata('user_auth_token'))) {
            $token = $this->session->userdata('user_auth_token');
        }
        else if(!empty($this->input->cookie('user_auth_token'))) {
            $token = $this->input->cookie('user_auth_token');
        }
        $this->Auth_model->deleteAuthToken($token);
        delete_cookie("user_auth_token");
        $this->session->sess_destroy();
        redirect(base_url("auth"));
    }


    public function dev_tes() {
        $this->token->generateToken("shitJustGotReal", 20);
        echo $this->token->getGeneratedToken();
    }
}

