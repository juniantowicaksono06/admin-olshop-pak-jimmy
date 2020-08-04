<?php 

class APP_Form_validation extends CI_Form_validation {

    protected $CI;
    public function __construct() {
        parent::__construct();
        $this->CI =& get_instance();
    }

    public function validation_no_special_character($str) {
        $this->CI->form_validation->set_message('validation_no_special_character', "Input tidak boleh mengandung #$%^&*[]{}\\|;:\"',.<>/?");
        if(preg_match('/^[a-zA-Z0-9\&\s\t\+\_\-\!\(\)\@]+$/', $str)) {
            return true;
        }
        return false;
    }
    
    // Fungsinya sama seperti validation_no_special_character tetapi titik diperbolehkan
    public function validation_no_special_character_only_colon($str){
        $this->CI->form_validation->set_message('validation_no_special_character_only_colon', "Input tidak boleh mengandung #$%^&*[]{}\\|;:\"',<>/?");
        if(preg_match('/^[a-zA-Z0-9\&\s\t\+\_\-\!\(\)\@\.]+$/', $str)) {
            return true;
        }
        return false;
    }

    public function validation_alpha_dash_num($str) {
        $this->CI->form_validation->set_message('validation_alpha_dash_num', "Input hanya boleh berupa huruf, angka, underscore, dan minus");
        if(preg_match('/^[a-zA-Z0-9\_\-]+$/', $str)) {
            return true;
        }
        return false;
    }

    public function validation_money_rupiah_format($str) {
        $this->CI->form_validation->set_message('validation_money_rupiah_format', "Input harus berupa uang");
        if(preg_match('/^[1-9](\d{1,2}|\.\d{1,3})(\.\d{3}|)+$/', $str)) {
            return true;
        }
        return false;
    }
}

?>