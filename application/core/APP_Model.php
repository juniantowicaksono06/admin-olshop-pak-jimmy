<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class APP_Model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function global_general_GetData($table, $select = '', $where = '', $limit = 0, $return_type = "object") {
        $this->db->from($table);
        if($select != "") {
            $this->db->select($select); // Ambil data dan pilih kolom pisahkan dengan koma
        }

        // Cek $limit lebih besar dari 0 jika $limit == 0 
        // kembalikan semua baris / row yang cocok dan jangan panggil method limit() 
        // codeigniter Jika lebih besar dari 0 kembalikan
        // baris / row yang cocok sesuai jumlah 
        // variable $limit

        if($limit != 0) {
            $this->db->limit($limit);
        }

        // Cek parameter $where apakah array atau bukan?
        if(is_array($where) && count($where) > 0) {
            foreach($where as $key => $value) {
                if(preg_match("/^or_/", $key)) {
                    $column = explode("or_", $key); // Pecah key menjadi array dengan delimiter or_
                    $column = end($column); // Ambil element terakhir jadi nilai untuk kolom
                    $this->db->or_where($column, $value); // OR Where
                }
                else if(preg_match("/^and_/", $key)) {
                    $column = explode("and_", $key); // Pecah key menjadi array dengan delimiter and_
                    $column = end($column); // Ambil element terakhir jadi nilai untuk kolom
                    $this->db->where($column, $value); // AND Where atau where biasa
                }
                else {
                    $this->db->where($key, $value);
                }
            }
        }
        else if(is_string($where)) {
            if(!empty($where)) {
                $column = explode("|", $where);
                // !!! Perubahan !!!
                if(count($column) == 2) { 
                    $column_name = $column[0];
                    $column_value = end($column);
                    $this->db->where($column_name, $column_value);
                }
            }
        }
        else {
            return array(); // Kembalikan array kosong
        }

        if(strtolower($return_type) == "object") {
            return $limit == 1 ? $this->db->get()->row() : $this->db->get()->result();
        }
        else {
            return $limit == 1 ? $this->db->get()->row_array() : $this->db->get()->result_array();
        }
    }


    // Ambil actual per halaman
    public function pagination_getPerPage($number) {
        $per_page = 5;
        if(is_int(intval($number))) {
            if($number > $per_page) {
                $per_page = $number - ($number % $per_page);
                $per_page = getClosestNumberInArray($per_page, $this->pagination_numberPerPage());
            }
        }
        return $per_page;
    }

    public function global_general_InsertData($table, $data) {
        $this->db->insert($table, $data);
        if($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function pagination_numberPerPage() {
        $number = [5, 10, 25, 50, 100];
        return $number;
    }

    public function global_general_CheckDataExists($table, $where) {
        $this->db->from($table);
        if(is_array($where)) {
            foreach($where as $key => $value) {
                if(preg_match("/^or_", $key)) {
                    $column = explode("or_", $key);
                    $column = end($column);
                    $this->db->or_where($column, $value);
                }
                else if(preg_match("/^and_", $key)) {
                    $column = explode("and_", $key);
                    $column = end($column);
                    $this->db->where($column, $value);
                }
            }
        }
        else if(is_string($where)) {
            $where_split = explode("|", $where);
            $this->db->where($where_split[0], end($where_split));
        }
        return $this->db->get()->num_rows();
    }

    protected function _uploadImg($img, $extension = 'gif|jpg|jpeg|png', $path = "./uploads/gambar_produk/", $max_size = 2048, $max_width = 2048, $max_height = 1444)
      {
          $this->load->library('upload');
          $_FILES = [
                    'tmp_file' => $img
                ]; // Buat index sementara pada superglobal variable $_FILES
          $config = [
                    'upload_path' => $path,
                    'allowed_types' => $extension,
                    'max_size' => $max_size,
                    'max_height' => $max_height,
                    'max_width' => $max_width,
                    'file_name' => $_FILES['tmp_file']['name'],
                    'overwrite' => true
                ];
          $this->upload->initialize($config);
        if ($this->upload->do_upload('tmp_file')) {
            return true;
        } else {
            return false;
        }
      }
}