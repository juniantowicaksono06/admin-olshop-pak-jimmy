<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
class Produk_model extends APP_Model {
    private $_product_table;
    private $_product_img_table;
    public function __construct() {
        parent::__construct();
        $this->_product_table = 'produk';
        $this->_product_img_table = 'produk_gambar';
        $this->_category_product_table = "kategori_produk";
        $this->_sub_category_product_table = "subkategori_produk";
    }
    //  Generate ID Produk
    public function generateID() {
        $x = rand() * 16;
        $res = base_convert($x, 10, 36);
        return $res;
    }

    public function insertProduct($data, $files = []) {
        $query = $this->global_general_InsertData($this->_product_table, $data);
        if($query) {
            if(array_key_exists('files', $files)) {
                $upload_status_error = false;
                $id_product = $data['idProduk'];
                for($x = 0; $x < count($files['files']['error']); $x++) {
                    $ext = explode('/', getMimeType($files['files']['tmp_name'][$x]));
                    $ext = end($ext);
                    if($ext == 'jpeg') {
                        $ext = 'jpg'; // Kalo mime type nya sama dengan jpeg ganti jadi jpg
                    }

                    $file_name = randomString("", 40, "char_low_and_num");

                    $tmp_file = [
                        'name' => $file_name . ".{$ext}",
                        'type' => $files['files']['type'][$x],
                        'error' => $files['files']['error'][$x],
                        'tmp_name' => $files['files']['tmp_name'][$x],
                        'size' => $files['files']['size'][$x]
                    ];
                    $upload = $this->_uploadImg($tmp_file, 'gif|jpg|jpeg|png');
                    if(!$upload) {
                        $upload_status_error = true;
                    }
                    else {
                        $img_data = [
                            'id' => $this->generateID(),
                            'idProduk' => $data['idProduk'],
                            'gambar' => $file_name . ".{$ext}"
                        ];
                        $upload_db = $this->global_general_InsertData($this->_product_img_table, $img_data);
                        if(!$upload_db) {
                            $upload_status_error = true;
                        }
                    }
                }
                if($upload_status_error) {
                    return [
                        'upload_error_status' => $upload_status_error,
                        'upload_error_msg' => "Input data berhasil dengan 1 atau lebih gambar gagal di unggah!"
                    ];
                }
                else {
                    return true;
                }
            }
            else {
                return true;
            }
        }
        return false;
    }


    public function getTotalProduct($search = "") {

        $data = $this->db->from($this->_product_table)->like('namaProduk', $search)->get()->result();

        return count($data);

        // $data = $this->global_general_GetData($this->_product_table, 'idProduk');
        // if(is_array($data)) {
        //     return count($data);
        // }
        // return 0;
    }
    // Render nomor halaman ke bentuk DOM HTML
    public function pagination_perPageSelect($selected = 5) {
        $pagination_number = $this->pagination_numberPerPage();
        $element = "";
        for($x = 0; $x < count($pagination_number); $x++) {
            if($pagination_number[$x] == $selected) {
                $element .= "<option value=\"{$pagination_number[$x]}\" selected>{$pagination_number[$x]}</option>";
            }
            else {
                $element .= "<option value=\"{$pagination_number[$x]}\">{$pagination_number[$x]}</option>";
            }
        }
        return $element;
    }


    public function getCategoryList($id_subcategory = "") {
        $category = $this->global_general_GetData($this->_category_product_table);
        if(count($category) > 0) {
            $element = "";
            foreach($category as $category_key => $category_value) {
                $sub_category = $this->global_general_GetData($this->_sub_category_product_table, '', "idKategori|{$category_value->idKategori}");
                if(count((array)$sub_category) > 0) {
                    $sub_category_option = "";
                    foreach($sub_category as $subcategory_key => $sub_category_value) {
                        if($id_subcategory == $sub_category_value->idSubKategori) {
                            $sub_category_option .= "
                                <option value=\"{$sub_category_value->idSubKategori}\" selected>{$sub_category_value->namaSubKategori}</option>
                            ";
                        }
                        else {
                            $sub_category_option .= "
                                <option value=\"{$sub_category_value->idSubKategori}\">{$sub_category_value->namaSubKategori}</option>
                            ";
                        }
                    }
                    $element .= "
                        <optgroup label=\"{$category_value->namaKategori}\">
                            <option value=\"\">--Semua Kategori--</option>
                            {$sub_category_option}
                        </optgroup>
                    ";
                }
            }
            if(!empty($element)) {
                return $element;
            }
        }
        return false;
    }

    // Produk pagination

    public function pagination_getProduct($id_subcategory, $search, $page_number, $total_rows, $per_page, $actual_per_page, $pagination_delete = false) {
        $offset = $page_number > 0 ? ($page_number - 1) * $actual_per_page : 0;
        if($offset > $total_rows) {
            $offset = $total_rows > $actual_per_page ? (ceil($total_rows / $actual_per_page)  - 1) * $actual_per_page : 0; // Cek jika total baris kategori lebih besar dari per page ?
        }

        if($pagination_delete) {
            $offset = ($offset + $actual_per_page) - 1;
        } 
        $product_data = $this->getProductBySubCategory($id_subcategory, $search, $offset, $per_page);
        $element_data = "";
        if(count((array)$product_data) > 0) {
            foreach($product_data as $product) {
                $img_data = $this->getProductImage($product->idProduk);
                $element_data .= "
                    <div class=\"col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-2\">
                        <div class=\"card w-100\">
                            <div class=\"card-body\">
                                <img src=\"". base_url('uploads/gambar_produk/') ."{$img_data->gambar}\" class=\"w-100\">
                                <div class=\"mt-3\">
                                    <div class=\"product-title card-title\">
                                        <h5>{$product->namaProduk}</h5>
                                    </div>
                                    <div class=\"product-price mb-2\">
                                        <span>Rp. <span class=\"text-rupiah\">{$product->hargaDiskon}</span></span>
                                    </div>
                                    <div class=\"product-action\">
                                        <abbr title=\"Edit Produk\">
                                            <a href=\"#\" class=\"btn btn-primary btn-sm rounded-0\"><i class=\"fa fa-fw fa-edit\"></i></a>
                                        </abbr>
                                        <abbr title=\"Hapus Produk\">
                                            <button class=\"btn btn-danger btn-sm rounded-0\" data-id-produk=\"{$product->idProduk}\"><i class=\"fa fa-fw fa-trash\"></i></button>
                                        </abbr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ";
            }
        }
        return $element_data;
    }

    public function getProductImage($idProduct) {
        $img_data = $this->global_general_GetData($this->_product_img_table, '', "idProduk|{$idProduct}", 1);
        return $img_data;
    }

    public function getProduct($offset, $search_name, $limit) {
        $product_data = $this->db->from($this->_product_table)->like('namaProduk', $search_name)->limit($limit, $offset)->get()->result();
        return $product_data;
    }
    // Ambil produk dengan subcategory
    public function getProductBySubCategory($id_subcategory, $search_name, $offset, $limit) {
        if($id_subcategory != "") {
            $product_data = $this->db->from($this->_product_table)->where('idSubKategori', $id_subcategory)->like('namaProduk', $search_name)->limit($limit, $offset)->get()->result();
            return $product_data;
        }
        else {
            return $this->getProduct($offset, $search_name, $limit);
        }
    }
}