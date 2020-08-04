<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Produk_model extends APP_Model
{
    private $_product_table;
    private $_product_img_table;
    public function __construct()
    {
        parent::__construct();
        $this->_product_table = 'produk';
        $this->_product_img_table = 'produk_gambar';
        $this->_category_product_table = "kategori_produk";
        $this->_sub_category_product_table = "subkategori_produk";
    }
    //  Generate ID Produk
    public function generateID()
    {
        $x = rand() * 16;
        $res = base_convert($x, 10, 36);
        return $res;
    }

    public function queryProduct($data, $files = [], $type)
    {
        $query = "";
        if ($type == "edit") {
            unset($data['createdAt']); // Hapus jam dibuatnya dari array / jangan diupdate
            $query = $this->db->where("idProduk", $data['idProduk'])->update($this->_product_table, $data); // Update / edit produk
        } else {
            $query = $this->global_general_InsertData($this->_product_table, $data);
        }

        if ($query || $this->db->affected_rows() > 0) {
            if (array_key_exists('files', $files)) {
                $upload_status_error = false;
                $id_product = $data['idProduk'];
                for ($x = 0; $x < count($files['files']['error']); $x++) {
                    $ext = explode('/', getMimeType($files['files']['tmp_name'][$x]));
                    $ext = end($ext);
                    if ($ext == 'jpeg') {
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
                    if (!$upload) {
                        $upload_status_error = true;
                    } else {
                        $img_data = [
                            'id' => $this->generateID(),
                            'idProduk' => $data['idProduk'],
                            'gambar' => $file_name . ".{$ext}"
                        ];
                        $upload_db = $this->global_general_InsertData($this->_product_img_table, $img_data);
                        if (!$upload_db) {
                            $upload_status_error = true;
                        }
                    }
                }
                if ($upload_status_error) {
                    return [
                        'upload_error_status' => $upload_status_error,
                        'upload_error_msg' => "Input data berhasil dengan 1 atau lebih gambar gagal di unggah!"
                    ];
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }


    public function getTotalProduct($id_subcategory = "", $search = "")
    {
        $data = $this->db->from($this->_product_table)->like('namaProduk', $search);
        if (!empty($id_subcategory)) {
            $data = $data->where('idSubKategori', $id_subcategory);
        }
        $data = $data->get()->result();
        return count($data);
    }
    // Render nomor halaman ke bentuk DOM HTML
    public function pagination_perPageSelect($selected = 5)
    {
        $pagination_number = $this->pagination_numberPerPage();
        $element = "";
        for ($x = 0; $x < count($pagination_number); $x++) {
            if ($pagination_number[$x] == $selected) {
                $element .= "<option value=\"{$pagination_number[$x]}\" selected>{$pagination_number[$x]}</option>";
            } else {
                $element .= "<option value=\"{$pagination_number[$x]}\">{$pagination_number[$x]}</option>";
            }
        }
        return $element;
    }


    public function html_getCategorySelect($id_subcategory = "")
    {
        $category = $this->global_general_GetData($this->_category_product_table); // Ambil data dari database
        if (count($category) > 0) {
            $element = "
            <optgroup label=\"--SEMUA KATEGORI\">
                <option value=\"\">--Semua Kategori--</option>
            </optgroup>";
            foreach ($category as $category_key => $category_value) {
                $sub_category = $this->global_general_GetData($this->_sub_category_product_table, '', "idKategori|{$category_value->idKategori}");
                if (count((array)$sub_category) > 0) {
                    $sub_category_option = "";
                    foreach ($sub_category as $subcategory_key => $sub_category_value) {
                        if ($id_subcategory == $sub_category_value->idSubKategori) {
                            $sub_category_option .= "
                                <option value=\"{$sub_category_value->idSubKategori}\" selected>{$sub_category_value->namaSubKategori}</option>
                            ";
                        } else {
                            $sub_category_option .= "
                                <option value=\"{$sub_category_value->idSubKategori}\">{$sub_category_value->namaSubKategori}</option>
                            ";
                        }
                    }
                    $element .= "
                        <optgroup label=\"{$category_value->namaKategori}\">
                            {$sub_category_option}
                        </optgroup>
                    ";
                }
            }
            if (!empty($element)) {
                return $element;
            }
        }
        return false;
    }

    // Produk pagination

    public function pagination_getProduct($id_subcategory, $search, $page_number, $total_rows, $per_page, $actual_per_page, $pagination_delete = false)
    {
        $offset = $page_number > 0 ? ($page_number - 1) * $actual_per_page : 0;
        if ($offset > $total_rows) {
            $offset = $total_rows > $actual_per_page ? (ceil($total_rows / $actual_per_page)  - 1) * $actual_per_page : 0; // Cek jika total baris kategori lebih besar dari per page ?
        }

        if ($pagination_delete) {
            $offset = ($offset + $actual_per_page) - 1;
        }
        $product_data = $this->getProductBySubCategory($id_subcategory, $search, $offset, $per_page);
        $element_data = "";
        if (count((array)$product_data) > 0) {
            foreach ($product_data as $product) {
                $img_data = $this->getProductImage($product->idProduk);
                $text_money = number_format($product->hargaDiskon, 0, ".", ".");
                $element_data .= "
                    <div class=\"col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-2 product-list\">
                        <div class=\"card w-100\">
                            <div class=\"card-body\">
                                <img src=\"". base_url('uploads/gambar_produk/') ."{$img_data->gambar}\" class=\"w-100\">
                                <div class=\"mt-3\">
                                    <div class=\"product-title card-title\">
                                        <h5>{$product->namaProduk}</h5>
                                    </div>
                                    <div class=\"product-price mb-2\">
                                        <span>Rp. <span>{$text_money}</span></span>
                                    </div>
                                    <div class=\"product-action\">
                                        <abbr title=\"Detail Produk\">
                                            <a href=\"". base_url("produk/detail_produk?idProduk={$product->idProduk}") ."\" class=\"btn btn-success rounded-0 btn-sm\"><i class=\"fa fa-fw fa-search\"></i></a>
                                        </abbr>
                                        <abbr title=\"Edit Produk\">
                                            <a href=\"". base_url("produk/edit_produk?idProduk={$product->idProduk}") ."\" class=\"btn btn-primary btn-sm rounded-0\"><i class=\"fa fa-fw fa-edit\"></i></a>
                                        </abbr>
                                        <abbr title=\"Hapus Produk\">
                                            <a href=\"". base_url("produk/input_hapus_produk") ."\" class=\"btn btn-danger btn-sm rounded-0 btn-hapus-produk\" data-id-produk=\"{$product->idProduk}\"><i class=\"fa fa-fw fa-trash\"></i></a>
                                        </abbr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ";
            }
        } else {
            if (!$pagination_delete) {
                $element_data = "
                    <div class=\"text-center w-100\">
                        <svg xmlns=\"http://www.w3.org/2000/svg\" x=\"0px\" y=\"0px\"
                        width=\"120\" height=\"120\"
                        viewBox=\"0 0 225 225\"
                        style=\" fill:#000000;\"><defs><radialGradient cx=\"853.63945\" cy=\"1010.15508\" r=\"726.69375\" gradientUnits=\"userSpaceOnUse\" id=\"color-1_119094_gr1\"><stop offset=\"0\" stop-color=\"#f4e9c3\"></stop><stop offset=\"0.219\" stop-color=\"#f8eecd\"></stop><stop offset=\"0.644\" stop-color=\"#fdf4dc\"></stop><stop offset=\"1\" stop-color=\"#fff6e1\"></stop></radialGradient><linearGradient x1=\"110.74219\" y1=\"21.09375\" x2=\"110.74219\" y2=\"200.40469\" gradientUnits=\"userSpaceOnUse\" id=\"color-2_119094_gr2\"><stop offset=\"0\" stop-color=\"#a4a4a4\"></stop><stop offset=\"0.63\" stop-color=\"#7f7f7f\"></stop><stop offset=\"1\" stop-color=\"#6f6f6f\"></stop><stop offset=\"1\" stop-color=\"#6f6f6f\"></stop></linearGradient><linearGradient x1=\"93.16406\" y1=\"144.14063\" x2=\"93.16406\" y2=\"42.1875\" gradientUnits=\"userSpaceOnUse\" id=\"color-3_119094_gr3\"><stop offset=\"0\" stop-color=\"#739eeb\"></stop><stop offset=\"0.405\" stop-color=\"#7ab5f0\"></stop><stop offset=\"1\" stop-color=\"#82d2f6\"></stop></linearGradient></defs><g fill=\"none\" fill-rule=\"nonzero\" stroke=\"none\" stroke-width=\"1\" stroke-linecap=\"butt\" stroke-linejoin=\"miter\" stroke-miterlimit=\"10\" stroke-dasharray=\"\" stroke-dashoffset=\"0\" font-family=\"none\" font-weight=\"none\" font-size=\"none\" text-anchor=\"none\" style=\"mix-blend-mode: normal\"><path d=\"M0,225.99298v-225.99298h225.99298v225.99298z\" fill=\"none\"></path><g><path d=\"M21.09375,14.0625v0c0,-7.76602 6.29648,-14.0625 14.0625,-14.0625v0c7.76602,0 14.0625,6.29648 14.0625,14.0625v0c0,7.76602 -6.29648,14.0625 -14.0625,14.0625v0c-7.76602,0 -14.0625,-6.29648 -14.0625,-14.0625zM26.36719,225v0c6.7957,0 12.30469,-5.50898 12.30469,-12.30469v0c0,-6.7957 -5.50898,-12.30469 -12.30469,-12.30469v0c-6.7957,0 -12.30469,5.50898 -12.30469,12.30469v0c0,6.7957 5.50898,12.30469 12.30469,12.30469zM202.14844,87.89063h-35.15625c-6.7957,0 -12.30469,5.50898 -12.30469,12.30469v0c0,6.7957 5.50898,12.30469 12.30469,12.30469h5.27344c7.76602,0 14.0625,6.29648 14.0625,14.0625v0c0,7.76602 -6.29648,14.0625 -14.0625,14.0625h-1.75781c-4.85508,0 -8.78906,3.93398 -8.78906,8.78906v0c0,4.85508 3.93398,8.78906 8.78906,8.78906h19.33594c7.76602,0 14.0625,6.29648 14.0625,14.0625v0c0,7.76602 -6.29648,14.0625 -14.0625,14.0625h-28.125c-3.88477,0 -7.03125,3.14648 -7.03125,7.03125v0c0,3.88477 3.14648,7.03125 7.03125,7.03125h1.75781c6.7957,0 12.30469,5.50898 12.30469,12.30469v0c0,6.7957 -5.50898,12.30469 -12.30469,12.30469h-101.95312c-6.7957,0 -12.30469,-5.50898 -12.30469,-12.30469v0c0,-6.7957 5.50898,-12.30469 12.30469,-12.30469v0c4.85508,0 8.78906,-3.93398 8.78906,-8.78906v0c0,-4.85508 -3.93398,-8.78906 -8.78906,-8.78906h-29.88281c-7.76602,0 -14.0625,-6.29648 -14.0625,-14.0625v0c0,-7.76602 6.29648,-14.0625 14.0625,-14.0625h15.82031c6.7957,0 12.30469,-5.50898 12.30469,-12.30469v0c0,-6.7957 -5.50898,-12.30469 -12.30469,-12.30469h-29.88281c-9.70664,0 -17.57812,-7.87148 -17.57812,-17.57812v0c0,-9.70664 7.87148,-17.57812 17.57813,-17.57812h10.54688c5.82539,0 10.54688,-4.72148 10.54688,-10.54687v0c0,-5.82539 -4.72148,-10.54687 -10.54687,-10.54687h-8.78906c-6.7957,0 -12.30469,-5.50898 -12.30469,-12.30469v0c0,-6.7957 5.50898,-12.30469 12.30469,-12.30469h65.03906c5.82539,0 10.54688,-4.72148 10.54688,-10.54687v0c0,-5.82539 -4.72148,-10.54687 -10.54687,-10.54687h-7.03125c-7.76602,0 -14.0625,-6.29648 -14.0625,-14.0625v0c0,-7.76602 6.29648,-14.0625 14.0625,-14.0625h84.375c7.76602,0 14.0625,6.29648 14.0625,14.0625v0c0,7.76602 -6.29648,14.0625 -14.0625,14.0625h-7.03125c-7.76602,0 -14.0625,6.29648 -14.0625,14.0625v0c0,7.76602 6.29648,14.0625 14.0625,14.0625h47.46094c8.73633,0 15.82031,7.08398 15.82031,15.82031v0c0,8.73633 -7.08398,15.82031 -15.82031,15.82031zM221.48438,126.5625v0c0,-7.76602 -6.29648,-14.0625 -14.0625,-14.0625v0c-7.76602,0 -14.0625,6.29648 -14.0625,14.0625v0c0,7.76602 6.29648,14.0625 14.0625,14.0625v0c7.76602,0 14.0625,-6.29648 14.0625,-14.0625z\" fill=\"url(#color-1_119094_gr1)\"></path><path d=\"M196.33359,175.77422l0.02109,-0.02109l-42.99961,-42.99961c7.4918,-11.36602 11.8793,-24.95742 11.8793,-39.58945c0,-39.80391 -32.26641,-72.07031 -72.07031,-72.07031c-39.80391,0 -72.07031,32.26641 -72.07031,72.07031c0,39.80391 32.26641,72.07031 72.07031,72.07031c14.63555,0 28.23398,-4.39102 39.60352,-11.88633l43.00313,42.99258c5.42109,5.42109 14.20312,5.42109 19.6207,0l0.94219,-0.94219c5.41055,-5.42109 5.41055,-14.20312 0,-19.62422z\" fill=\"url(#color-2_119094_gr2)\"></path><path d=\"M93.16406,42.1875c-28.15358,0 -50.97656,22.82298 -50.97656,50.97656c0,28.15358 22.82298,50.97656 50.97656,50.97656c28.15358,0 50.97656,-22.82298 50.97656,-50.97656c0,-28.15358 -22.82298,-50.97656 -50.97656,-50.97656z\" fill=\"url(#color-3_119094_gr3)\"></path><path d=\"M105.46875,77.34375v0c0,-3.86719 3.16406,-7.03125 7.03125,-7.03125v0c3.86719,0 7.03125,3.16406 7.03125,7.03125v0c0,3.86719 -3.16406,7.03125 -7.03125,7.03125v0c-3.86719,0 -7.03125,-3.16406 -7.03125,-7.03125zM66.79688,77.34375v0c0,-3.86719 3.16406,-7.03125 7.03125,-7.03125v0c3.86719,0 7.03125,3.16406 7.03125,7.03125v0c0,3.86719 -3.16406,7.03125 -7.03125,7.03125v0c-3.86719,0 -7.03125,-3.16406 -7.03125,-7.03125z\" fill=\"#ffffff\"></path><g fill=\"#ffffff\"><path d=\"M110.74219,119.53125c-1.36758,0 -2.73164,-0.52734 -3.76523,-1.58203c-0.89297,-0.91055 -5.75508,-5.44922 -13.81289,-5.44922c-0.00703,0 -0.01406,0 -0.02109,0c-8.05078,0.00703 -12.89883,4.54219 -13.7918,5.44922c-2.03555,2.08125 -5.37891,2.11641 -7.45664,0.07383c-2.07773,-2.04258 -2.11289,-5.37891 -0.07383,-7.45664c1.9793,-2.01797 9.36211,-8.60273 21.31172,-8.61328c0.01055,0 0.02109,0 0.03164,0c11.96719,0 19.36055,6.5918 21.34336,8.61328c2.03906,2.07773 2.00391,5.41758 -0.07383,7.45664c-1.02656,1.00547 -2.35898,1.5082 -3.69141,1.5082z\"></path></g></g></g></svg>
                        <div>
                            <h3>Tidak Ada Data Produk Ditemukan!</h3>
                            <h4><a href=\"". base_url("produk/tambah_produk") ."\">Silahkan Tambahkan Produk</a></h4>
                        </div>
                    </div>
                ";
            }
        }
        return $element_data;
    }

    public function getProductImage($idProduct, $limit = 1)
    {
        $img_data = $this->global_general_GetData($this->_product_img_table, '', "idProduk|{$idProduct}", $limit);
        return $img_data;
    }

    public function getProduct($offset, $search_name, $limit)
    {
        $product_data = $this->db->from($this->_product_table)->like('namaProduk', $search_name)->limit($limit, $offset)->get()->result();
        return $product_data;
    }
    // Ambil produk dengan id produk
    public function getProductByIdProduct($id_product)
    {
        $product_data = $this->db->from($this->_product_table)->limit(1)->where('idProduk', $id_product)->get()->row();
        return $product_data;
    }


    // Render elemen produk detail
    public function productDetail($id_product)
    {
        $element_data = "";
        $product_data = $this->getProductByIdProduct($id_product);
        $carousel_img_element = "";
        $img_active_element = "";
        if (count((array)$product_data)) {
            $product_img_data = $this->getProductImage($product_data->idProduk, 0);
            if (count((array)$product_img_data) > 0) {
                $index = 0;
                $tmp_carousel = "
                    <div class=\"carousel-item active\">
                        <div class=\"row justify-content-center\" style=\"border: 1px\">
                ";
                foreach ($product_img_data as $img) {
                    $img_list_box_active = "";
                    if ($index == 0) {
                        $img_list_box_active = "img-list-box-active";
                        $img_active_element = "
                            <img src=\"". base_url("uploads/gambar_produk/{$img->gambar}") ."\" class=\"w-100\" id=\"imgPreviewMain\">
                        ";
                    }
                    $tmp_carousel .= "
                        <div class=\"col-3 img-list-box py-1 px-1 mx-1 cursor-pointer {$img_list_box_active}\"  data-image-preview-target=\"imgPreviewMain\">
                            <img class=\"d-block w-100\" src=\"". base_url("uploads/gambar_produk/{$img->gambar}") ."\">
                        </div>
                    ";

                    if ((($index + 1) % 3 == 0 && $index != 0) || (($index + 1) == count($product_img_data))) {
                        $tmp_carousel .= "
                                </div>
                            </div>
                        ";
                        $carousel_img_element .= $tmp_carousel;
                        $tmp_carousel = "
                            <div class=\"carousel-item\">
                                <div class=\"row justify-content-center\" style=\"border: 1px\">
                        ";
                    }
                    $index++;
                }
            }


            $element_data = "
                <div class=\"container-fluid\">
                    <div class=\"row justify-content-center\">
                        <div class=\"col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12 mb-3\">
                            <div class=\"py-1 px-1 mb-2\">
                                {$img_active_element}
                            </div>
                            <div id=\"carouselImgList\" class=\"carousel slide d-flex\" data-interval=\"false\">
                                <a href=\"#carouselImgList\" role=\"button\" data-slide=\"prev\" class=\"align-self-center\">
                                    <i class=\"fa fa-fw fa-chevron-left text-primary\"></i>
                                </a>
                                <div class=\"carousel-inner\">
                                    {$carousel_img_element}
                                </div>
                                <a href=\"#carouselImgList\" role=\"button\" data-slide=\"next\" class=\"align-self-center\">
                                    <i class=\"fa fa-fw fa-chevron-right text-primary\"></i>
                                </a>
                            </div>
                        </div>
                        <div class=\"col-xl-7 col-lg-7 col-md-5 col-sm-12 col-12 px-2\">
                            <div>
                                <h2><strong>Detail Produk</strong></h2>
                            </div>
                            <hr>
                            <div class=\"product-title\">
                                <h2><strong>{$product_data->namaProduk}</strong></h2>
                            </div>
                            <div class=\"product-price\">
                                <h5>Rp. <span class=\"text-rupiah\">{$product_data->hargaDiskon}</span></h5>
                            </div>
                            <div class=\"product-description\">
                                {$product_data->deskripsiProduk}
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
        return $element_data;
    }

    // Ambil produk dengan subcategory
    public function getProductBySubCategory($id_subcategory, $search_name, $offset, $limit)
    {
        if ($id_subcategory != "") {
            $product_data = $this->db->from($this->_product_table)->where('idSubKategori', $id_subcategory)->like('namaProduk', $search_name)->limit($limit, $offset)->get()->result();
            return $product_data;
        } else {
            return $this->getProduct($offset, $search_name, $limit);
        }
    }

    public function deleteProduct($id_product)
    {
        $img_path = FCPATH . "/uploads/gambar_produk";
        $img_file_data = $this->global_general_GetData($this->_product_img_table, "gambar", "idProduk|{$id_product}");
        $this->db->delete($this->_product_table, [
            'idProduk' => $id_product
        ]);
        if ($this->db->affected_rows() > 0) {
            if (count((array)$img_file_data) > 0) {
                foreach ($img_file_data as $img) {
                    if (file_exists("{$img_path}/{$img->gambar}")) {
                        unlink("{$img_path}/{$img->gambar}");
                    }
                }
            }
            return true;
        }
        return false;
    }

    // Hapus gambar

    public function deleteProductImage($image_name)
    {
        $img_product_path = FCPATH . "/uploads/gambar_produk";
        $this->db->delete($this->_product_img_table, [
        'gambar' => $image_name
      ]);
        if ($this->db->affected_rows() > 0) {
            unlink("{$img_product_path}/{$image_name}");
            return true;
        }
        return false;
    }

    public function html_getProductImageList($id_product)
    {
        $img_data = $this->getProductImage($id_product, 0);
        $element_data = "";
        if (count((array)$img_data) > 0) {
            $img_tmp = "";
            $index = 1;
            foreach ($img_data as $img) {
                $img_tmp .= "
                <div class=\"col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 mb-4 product-image\">
                  <div class=\"card\" style=\"min-height: 200px\">
                    <div class=\"card-body\">
                      <div class=\"mb-4 px-2\">
                        <img src=\"".base_url("uploads/gambar_produk/{$img->gambar}")."\" class=\"w-100\">
                      </div>
                      <div class=\"mb-2\">
                        <button type=\"button\" class=\"btn btn-danger btn-sm btn-block rounded-0 btn-hapus-gambar-produk\" data-image=\"{$img->gambar}\" data-target-remove=\"product-image\"><i class=\"fa fa-fw fa-trash\"></i> Hapus Gambar</button>
                      </div>
                    </div>
                  </div>

                </div>
              ";
                $index++;
            }
            $element_data = "
            <div class=\"col-lg-12 mt-2 mb-2\">
              <div class=\"mb-2\">
                <h4>Daftar Gambar Terunggah</h4>
              </div>
              <div class=\"row\">
                {$img_tmp}
              </div>
            </div>
          ";
        }
        return $element_data;
    }
}
