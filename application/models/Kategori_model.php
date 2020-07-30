<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
class Kategori_model extends APP_Model {
    private $_category_table;
    private $_sub_category_table;
    public function __construct() {
        parent::__construct();
        $this->_category_table = 'kategori_produk';
        $this->_sub_category_table = 'subkategori_produk';
    }

    public function getTotalCategory() {
        $data = $this->global_general_GetData($this->_category_table, 'idKategori');
        if($data != null) {
            if(is_array($data)) {
                return count($data);
            }
        }
        return 0;
    }

    public function checkDataExists($table_name, $input) {
        return $this->global_general_CheckDataExists($table_name, $input);
    }

    public function getTotalSubCategory($id_category = "") {
        $data = $id_category != "" ? $this->global_general_GetData($this->_sub_category_table, 'idSubKategori', "idKategori|{$id_category}") : $this->global_general_GetData($this->_sub_category_table, 'idSubKategori');
        if($data != null) {
            if(is_array($data)) {
                return count($data);
            }
        }
        return 0;
    }

    public function generateID($table_name) {
        $id = randomString("", 13, "char_low_and_num");
        $column_search = "";
        if($table_name == $this->_category_table) {
            $column_search = "idKategori";
        }
        else if($table_name == $this->_sub_category_table) {
            $column_search = "idSubKategori";
        }
        else {
            return "";
        }
        $column_search .= "|{$id}";
        if($this->checkDataExists($table_name, $column_search) > 0) {
            $this->generateID($table_name);
        }
        return $id;
    }
    // Input data kategori dan subkategori
    public function inputCategoryAndSubCategory($data) {
        $msg = [
            'error' => true,
            'msg' => ''
        ];
        if(is_array($data)) {
            $category = [];
            $sub_category = [];
            $input_type = '';
            if(array_key_exists('kategori', $data)) {
                $category = $data['kategori'];
            }
            if(array_key_exists('subkategori', $data)) {
                $sub_category = $data['subkategori'];
            }

            if(array_key_exists('inputType', $data)) {
                $input_type = $data['inputType'];
                unset($data['inputType']);
            }

            $category_insert = false;
            // Cek apakah tipe input kategori adalah menambahkan?
            if($input_type == 'add_category_input') {
                $category_insert = $this->global_general_InsertData($this->_category_table, $category);
            }
            // Cek apakah tipe input kategori adalah mengedit
            else if($input_type == 'edit_category_input') {
                $this->db->where('idKategori', $data['idKategori'])->update($this->_category_table, $data);
                if($this->db->affected_rows() > 0) {
                    $msg['error'] = false;
                    $msg['msg'] = 'Berhasil mengubah data!';
                }
            }

            if($category_insert || ($input_type != 'add_category_input' && preg_match('/^add/', $input_type))) {
                foreach($sub_category as $sub) {
                    $this->global_general_InsertData($this->_sub_category_table, $sub);
                }
                $msg['error'] = false;
                $msg['msg'] = "Berhasil menginput data!";
            }

            if($msg['error']) {
                $msg['msg'] = "Gagal menginput data.<br>Data kategori tidak dapat ditemukan!";
            }
        }
        return $msg;
    }

    // Input data subkategori

    public function editSubCategory($id_subcategory, $data) {
        $msg = [
            'error' => true
        ];
        $this->db->where('idSubKategori', $id_subcategory)->update($this->_sub_category_table, $data);

        if($this->db->affected_rows() > 0) {
            $msg['error'] = false;
            $msg['msg'] = "Berhasil mengubah data!";
        }
        else {
            $msg['msg'] = "Gagal mengubah data!";
        }
        return $msg;
    }

    public function pagination_categoryAndSubCategory($page_number, $total_rows, $per_page, $actual_per_page, $pagination_delete = false) {
        $offset = $page_number > 0 ? ($page_number - 1) * $actual_per_page : 0;
        if($offset > $total_rows) {
            $offset = $total_rows > $actual_per_page ? (ceil($total_rows / $actual_per_page)  - 1) * $actual_per_page : 0; // Cek jika total baris kategori lebih besar dari per page ?
        }

        if($pagination_delete) {
            $offset = ($offset + $actual_per_page) - 1;
        } 

        $data = $this->getCategoryAndSubCategory($offset, $per_page);
        return $data;
    }

    public function getCategoryAndSubCategory($offset = 0, $limit = 6) {
        $element = "";
        $category = $this->db->from($this->_category_table)->limit($limit, $offset)->get()->result();
        if(count($category) > 0) {
            foreach($category as $key => $cat) {
                $element .= "
                <div class=\"col-lg-12 my-2 list-container\" data-idkategori=\"{$cat->idKategori}\">
                    <div class=\"card rounded-0\">
                        <div class=\"list-header font-md py-3 px-3\">
                            <div class=\"position-relative w-100\">
                                <span class=\"link-title\">{$cat->namaKategori}</span>
                            </div>
                        </div>
                        <div class=\"list-content d-inline-block w-100 py-3 px-3\">
                            <div class=\"w-100 mb-2\">
                                <abbr title=\"Tambah Sub Kategori\">
                                    <a href=\"" . base_url("kategori_produk/tambah_subkategori_produk?idKategori={$cat->idKategori}") . "\" class=\"btn btn-success rounded-0\"><i class=\"fa fa-fw fa-plus\"></i></a>
                                </abbr>
                                <abbr title=\"Edit Kategori\">
                                    <a href=\"" . base_url("kategori_produk/edit_kategori_produk?idKategori={$cat->idKategori}") . "\" class=\"btn btn-primary rounded-0\"><i class=\"fa fa-fw fa-edit\"></i></a>
                                </abbr>
                                <abbr title=\"Hapus Kategori\">
                                    <button class=\"btn btn-hapus-kategori btn-danger rounded-0\" data-target=\"list-container\"><i class=\"fa fa-fw fa-trash\"></i></button>
                                </abbr>
                            </div>
                ";
                $sub_category = $this->global_general_GetData($this->_sub_category_table, '', "idKategori|{$cat->idKategori}");
                if(count($sub_category) > 0) {
                    foreach($sub_category as $key => $sub) {
                        $element .= "
                        <div class=\"list-item py-2 position-relative\" data-idsubkategori=\"{$sub->idSubKategori}\">
                            <span class=\"align-self-center w-75 d-inline-block w-75\">{$sub->namaSubKategori}</span>
                            <div class=\"position-absolute d-inline-block\" style=\"right: 5px;\">
                                <abbr title=\"Edit Sub Kategori\">
                                    <a href=\"". base_url("kategori_produk/edit_subkategori_produk?idSubKategori={$sub->idSubKategori}") ."\" class=\"btn btn-primary btn-sm rounded-0\"><i class=\"fa fa-fw fa-edit\"></i></a>
                                </abbr>
                                <abbr title=\"Hapus Sub Kategori\">
                                    <button class=\"btn btn-danger btn-sm rounded-0 btn-hapus-subkategori\" data-id-subkategori=\"{$sub->idSubKategori}\" data-target=\"list-item\" data-target2=\"list-container\"><i class=\"fa fa-fw fa-trash\"></i></button>
                                </abbr>
                            </div>
                        </div>
                        ";
                    }
                }
                $element .= "
                        </div>
                    </div>
                </div>
                ";
            }
        }
        return $element;
    }


    // Ambil kategori dengan nama kategori 

    public function getCategory($search, $column = "nama") {
        $data_tmp = false;
        if(!is_array($search)) {
            $column_name = "linkKategori";
            if($column == "nama") {
                $column_name = "namaKategori";
            }
            $data_tmp = $this->global_general_GetData($this->_category_table, '', "{$column_name}|{$search}", 1);
            if(count((array)$data_tmp) == 0) {
                $data_tmp = false;
            }
        }
        else {
            $data_tmp = $this->$this->global_general_GetData($this->_category_table, '', "{$search}", 1);
            if(count((array)$data_tmp) == 0) {
                $data_tmp = false; 
            }
        }
        return $data_tmp;
    }

    public function getSubCategory($search, $column = "nama") {
        $column_name = "namaSubKategori";
        $data_tmp = 0;
        if(!is_array($search)) {
            if($column == "link") {
                $column_name = "linkSubKategori";
            }
            else if($column == "id") {
                $column_name = "idSubKategori";
            }
            $data_tmp = $this->global_general_GetData($this->_sub_category_table, '', "{$column_name}|{$search}", 1);
        }
        else {
            $data_tmp = $this->global_general_GetData($this->_sub_category_table, '', $search, 1);
        }
        if(count((array)$data_tmp) > 0)  {
            return $data_tmp;
        }
        return false;
    }


    // Ambil kategori dengan id kategori
    public function getCategoryByCategoryID($id_category) {
        $data_tmp = $this->global_general_GetData($this->_category_table, 'idKategori, namaKategori, linkKategori', "idKategori|{$id_category}", 1);
        if(count((array)$data_tmp) > 0)  {
            return $data_tmp;
        }
        return false;
    }
    // Ambil subkategori dengan id subkategori
    public function getSubCategoryBySubCategoryID($id_subcategory) {
        $data_tmp = $this->global_general_GetData($this->_sub_category_table, 'idKategori, idSubKategori, namaSubKategori, linkSubKategori', "idSubKategori|{$id_subcategory}", 1);
        if(count((array)$data_tmp) > 0) {
            return $data_tmp;
        }
        return false;
    }
    // Ambil id kategori dengan id subkategori dari table subkategori
    public function getIDCategoryByIDSubCategory($id_subcategory) {
        $data_tmp = $this->global_general_GetData($this->_sub_category_table, 'idKategori', "idSubKategori|{$id_subcategory}", 1);
        if($data_tmp != null) {
            if(property_exists($data_tmp, 'idKategori')) {
                return $data_tmp->idKategori;
            }
        }
        return false;
    }


    // Hapus kategori model

    public function deleteCategory($id_category = "") {
        if($id_category != "") {
            $this->db->delete($this->_category_table, [
                'idKategori' => $id_category
            ]);
            if($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }
    }

    public function deleteSubCategory($id_subcategory = "") {
        if($id_subcategory != "") {
            $this->db->delete($this->_sub_category_table, [
                'idSubKategori' => $id_subcategory
            ]);
            if($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }
    }

}