<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth_model extends APP_Model {
    private $_useradmin_table;
    private $_auth_token_user_table;
    public function __construct() {
        $this->_useradmin_table = "useradmin_page";
        $this->_auth_token_user_table = "user_admin_auth_tokens";
    }

    public function checkDataExists($table_name, $input) {
        return $this->global_general_CheckDataExists($table_name, $input);
    }

    public function getUser($input, $select = "userid, username, password", $return_type = "object") {
        return $this->global_general_GetData($this->_useradmin_table, $select, $input, 1, $return_type);
    }

    public function deleteAuthToken($token) {
        $this->db->delete($this->_auth_token_user_table, [
            'token' => $token
        ]);
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    
    public function getAuthToken($input, $return_type = "object") {
        
        $db_token = $this->global_general_GetData($this->_auth_token_user_table, "userID", $input, 1, $return_type);
        if(count((array)$db_token) > 0) {
            return $this->getUser("userid|{$db_token->userID}", "userid, username, fotoProfil");
        }
        else {
            if(array_key_exists("token", $input)) {
                $this->db->delete($this->_auth_token_user_table, ['token', $input['token']]);
            }
            return false;
        }
    }

    public function insertAuthToken($data) {
        return $this->global_general_InsertData($this->_auth_token_user_table, $data);
    }

    public function generateID($table_name) {
        // $id = randomString("", 13, "all");
        $id = md5(time());
        $column_search = "";
        if($table_name == $this->_useradmin_table) {
            $column_search = "userid";
        }
        else if($table_name == $this->_auth_token_user_table) {
            $column_search = "authID";
        }
        $column_search .= "|{$id}";
        if($this->checkDataExists($table_name, $column_search) > 0) {
            $this->generateID($table_name);
        }
        return $id;
    }
}
?>