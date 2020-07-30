<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
    class Token {
        private $_token;
        private $_token_generated;

        public function __construct() {
            $this->_token = "";
            $this->_token_generated = false;
        }

        public function generateToken($prefix = "", $strlen = 5) {
            $str = randomString($prefix, $strlen);
            $this->_token = md5("C4t1sSoL4zy_{$str}");
            $this->_token_generated = true;
        }

        public function getGeneratedToken() {
            if($this->_token_generated) {
                $this->_token_generated = false;
                return $this->_token;
            }
        }

    }
?>