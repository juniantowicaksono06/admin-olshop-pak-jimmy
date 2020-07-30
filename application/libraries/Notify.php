<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Notify {
    private $_msg;
    public function __construct() {
        $this->_msg = [];
    }

    public function setNotify($path, $value) {
        $parts = explode("|", $path);
        $curr =& $this->_msg;
        foreach($parts as $index => $key) {
            $curr =& $curr[$key];
            if($index != count($parts) - 1) {
                continue;
            }
            $curr = $value;
        }
    }

    public function getNotify($path) {
        $parts = explode("|", $path);
        $curr = $this->_msg;
        for ($i = 0, $l = count($parts); $i < $l; ++$i) {
            if (!isset($curr[$parts[$i]])) {
                // path tidak ditemukan
                return null;
            } else if (($i < $l - 1) && !is_array($curr[$parts[$i]])) {
                // path tidak ditemukan
                return null;
            }
            $curr = $curr[$parts[$i]];
        }
        return $curr;
    }
    
    public function getNotifyList() {
        return $this->_msg;
    }
}