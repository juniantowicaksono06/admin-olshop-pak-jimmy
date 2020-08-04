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

        $data = $this->getCategoryAndSubCategory($offset, $per_page, $pagination_delete);
        return $data;
    }

    public function getCategoryAndSubCategory($offset = 0, $limit = 6, $pagination_delete = false) {
        $element_data = "";
        $category = $this->db->from($this->_category_table)->limit($limit, $offset)->get()->result();
        if(count($category) > 0) {
            foreach($category as $key => $cat) {
                $element_data .= "
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
                        $element_data .= "
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
                $element_data .= "
                        </div>
                    </div>
                </div>
                ";
            }
        }
        else {
            if(!$pagination_delete) {
                $element_data = "
                    <div class=\"text-center w-100\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" x=\"0px\" y=\"0px\"
                        width=\"120\" height=\"120\"
                        viewBox=\"0 0 225 225\"
                        style=\" fill:#000000;\"><defs><radialGradient cx=\"853.63945\" cy=\"1010.15508\" r=\"726.69375\" gradientUnits=\"userSpaceOnUse\" id=\"color-1_119094_gr1\"><stop offset=\"0\" stop-color=\"#f4e9c3\"></stop><stop offset=\"0.219\" stop-color=\"#f8eecd\"></stop><stop offset=\"0.644\" stop-color=\"#fdf4dc\"></stop><stop offset=\"1\" stop-color=\"#fff6e1\"></stop></radialGradient><linearGradient x1=\"110.74219\" y1=\"21.09375\" x2=\"110.74219\" y2=\"200.40469\" gradientUnits=\"userSpaceOnUse\" id=\"color-2_119094_gr2\"><stop offset=\"0\" stop-color=\"#a4a4a4\"></stop><stop offset=\"0.63\" stop-color=\"#7f7f7f\"></stop><stop offset=\"1\" stop-color=\"#6f6f6f\"></stop><stop offset=\"1\" stop-color=\"#6f6f6f\"></stop></linearGradient><linearGradient x1=\"93.16406\" y1=\"144.14063\" x2=\"93.16406\" y2=\"42.1875\" gradientUnits=\"userSpaceOnUse\" id=\"color-3_119094_gr3\"><stop offset=\"0\" stop-color=\"#739eeb\"></stop><stop offset=\"0.405\" stop-color=\"#7ab5f0\"></stop><stop offset=\"1\" stop-color=\"#82d2f6\"></stop></linearGradient></defs><g fill=\"none\" fill-rule=\"nonzero\" stroke=\"none\" stroke-width=\"1\" stroke-linecap=\"butt\" stroke-linejoin=\"miter\" stroke-miterlimit=\"10\" stroke-dasharray=\"\" stroke-dashoffset=\"0\" font-family=\"none\" font-weight=\"none\" font-size=\"none\" text-anchor=\"none\" style=\"mix-blend-mode: normal\"><path d=\"M0,225.99298v-225.99298h225.99298v225.99298z\" fill=\"none\"></path><g><path d=\"M21.09375,14.0625v0c0,-7.76602 6.29648,-14.0625 14.0625,-14.0625v0c7.76602,0 14.0625,6.29648 14.0625,14.0625v0c0,7.76602 -6.29648,14.0625 -14.0625,14.0625v0c-7.76602,0 -14.0625,-6.29648 -14.0625,-14.0625zM26.36719,225v0c6.7957,0 12.30469,-5.50898 12.30469,-12.30469v0c0,-6.7957 -5.50898,-12.30469 -12.30469,-12.30469v0c-6.7957,0 -12.30469,5.50898 -12.30469,12.30469v0c0,6.7957 5.50898,12.30469 12.30469,12.30469zM202.14844,87.89063h-35.15625c-6.7957,0 -12.30469,5.50898 -12.30469,12.30469v0c0,6.7957 5.50898,12.30469 12.30469,12.30469h5.27344c7.76602,0 14.0625,6.29648 14.0625,14.0625v0c0,7.76602 -6.29648,14.0625 -14.0625,14.0625h-1.75781c-4.85508,0 -8.78906,3.93398 -8.78906,8.78906v0c0,4.85508 3.93398,8.78906 8.78906,8.78906h19.33594c7.76602,0 14.0625,6.29648 14.0625,14.0625v0c0,7.76602 -6.29648,14.0625 -14.0625,14.0625h-28.125c-3.88477,0 -7.03125,3.14648 -7.03125,7.03125v0c0,3.88477 3.14648,7.03125 7.03125,7.03125h1.75781c6.7957,0 12.30469,5.50898 12.30469,12.30469v0c0,6.7957 -5.50898,12.30469 -12.30469,12.30469h-101.95312c-6.7957,0 -12.30469,-5.50898 -12.30469,-12.30469v0c0,-6.7957 5.50898,-12.30469 12.30469,-12.30469v0c4.85508,0 8.78906,-3.93398 8.78906,-8.78906v0c0,-4.85508 -3.93398,-8.78906 -8.78906,-8.78906h-29.88281c-7.76602,0 -14.0625,-6.29648 -14.0625,-14.0625v0c0,-7.76602 6.29648,-14.0625 14.0625,-14.0625h15.82031c6.7957,0 12.30469,-5.50898 12.30469,-12.30469v0c0,-6.7957 -5.50898,-12.30469 -12.30469,-12.30469h-29.88281c-9.70664,0 -17.57812,-7.87148 -17.57812,-17.57812v0c0,-9.70664 7.87148,-17.57812 17.57813,-17.57812h10.54688c5.82539,0 10.54688,-4.72148 10.54688,-10.54687v0c0,-5.82539 -4.72148,-10.54687 -10.54687,-10.54687h-8.78906c-6.7957,0 -12.30469,-5.50898 -12.30469,-12.30469v0c0,-6.7957 5.50898,-12.30469 12.30469,-12.30469h65.03906c5.82539,0 10.54688,-4.72148 10.54688,-10.54687v0c0,-5.82539 -4.72148,-10.54687 -10.54687,-10.54687h-7.03125c-7.76602,0 -14.0625,-6.29648 -14.0625,-14.0625v0c0,-7.76602 6.29648,-14.0625 14.0625,-14.0625h84.375c7.76602,0 14.0625,6.29648 14.0625,14.0625v0c0,7.76602 -6.29648,14.0625 -14.0625,14.0625h-7.03125c-7.76602,0 -14.0625,6.29648 -14.0625,14.0625v0c0,7.76602 6.29648,14.0625 14.0625,14.0625h47.46094c8.73633,0 15.82031,7.08398 15.82031,15.82031v0c0,8.73633 -7.08398,15.82031 -15.82031,15.82031zM221.48438,126.5625v0c0,-7.76602 -6.29648,-14.0625 -14.0625,-14.0625v0c-7.76602,0 -14.0625,6.29648 -14.0625,14.0625v0c0,7.76602 6.29648,14.0625 14.0625,14.0625v0c7.76602,0 14.0625,-6.29648 14.0625,-14.0625z\" fill=\"url(#color-1_119094_gr1)\"></path><path d=\"M196.33359,175.77422l0.02109,-0.02109l-42.99961,-42.99961c7.4918,-11.36602 11.8793,-24.95742 11.8793,-39.58945c0,-39.80391 -32.26641,-72.07031 -72.07031,-72.07031c-39.80391,0 -72.07031,32.26641 -72.07031,72.07031c0,39.80391 32.26641,72.07031 72.07031,72.07031c14.63555,0 28.23398,-4.39102 39.60352,-11.88633l43.00313,42.99258c5.42109,5.42109 14.20312,5.42109 19.6207,0l0.94219,-0.94219c5.41055,-5.42109 5.41055,-14.20312 0,-19.62422z\" fill=\"url(#color-2_119094_gr2)\"></path><path d=\"M93.16406,42.1875c-28.15358,0 -50.97656,22.82298 -50.97656,50.97656c0,28.15358 22.82298,50.97656 50.97656,50.97656c28.15358,0 50.97656,-22.82298 50.97656,-50.97656c0,-28.15358 -22.82298,-50.97656 -50.97656,-50.97656z\" fill=\"url(#color-3_119094_gr3)\"></path><path d=\"M105.46875,77.34375v0c0,-3.86719 3.16406,-7.03125 7.03125,-7.03125v0c3.86719,0 7.03125,3.16406 7.03125,7.03125v0c0,3.86719 -3.16406,7.03125 -7.03125,7.03125v0c-3.86719,0 -7.03125,-3.16406 -7.03125,-7.03125zM66.79688,77.34375v0c0,-3.86719 3.16406,-7.03125 7.03125,-7.03125v0c3.86719,0 7.03125,3.16406 7.03125,7.03125v0c0,3.86719 -3.16406,7.03125 -7.03125,7.03125v0c-3.86719,0 -7.03125,-3.16406 -7.03125,-7.03125z\" fill=\"#ffffff\"></path><g fill=\"#ffffff\"><path d=\"M110.74219,119.53125c-1.36758,0 -2.73164,-0.52734 -3.76523,-1.58203c-0.89297,-0.91055 -5.75508,-5.44922 -13.81289,-5.44922c-0.00703,0 -0.01406,0 -0.02109,0c-8.05078,0.00703 -12.89883,4.54219 -13.7918,5.44922c-2.03555,2.08125 -5.37891,2.11641 -7.45664,0.07383c-2.07773,-2.04258 -2.11289,-5.37891 -0.07383,-7.45664c1.9793,-2.01797 9.36211,-8.60273 21.31172,-8.61328c0.01055,0 0.02109,0 0.03164,0c11.96719,0 19.36055,6.5918 21.34336,8.61328c2.03906,2.07773 2.00391,5.41758 -0.07383,7.45664c-1.02656,1.00547 -2.35898,1.5082 -3.69141,1.5082z\"></path></g></g></g></svg>
                        <div>
                            <h3>Tidak Ada Data Kategori Ditemukan!</h3>
                            <h4><a href=\"". base_url("kategori_produk/tambah_kategori_produk") ."\">Silahkan Tambahkan Kategori</a></h4>
                        </div>
                    </div>
                ";
            }
        }
        return $element_data;
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