<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends APP_Controller {
    public function __construct() {
        parent::__construct();
        $this->_check_login_status_default_page();
    }

    public function index() {
        $this->daftar_produk();
    }

    public function daftar_produk() {

        $get_input = $this->input->get();
        $per_page = 5;
        $page_number = 1;
        $search = "";
        $id_subcategory = "";
        if(array_key_exists('per_page', $get_input)) {
            $per_page = $this->Produk_model->pagination_getPerPage($get_input['per_page']);
        }

        if(array_key_exists('page', $get_input)) {
            $page_number = $get_input['page'];
        }

        if(array_key_exists('id_subkategori_input', $get_input)) {
            $id_subcategory = $get_input['id_subkategori_input'];
        }

        if(array_key_exists('search_product_name', $get_input)) {
            $search = $get_input['search_product_name'];
        }
        $total_rows = $this->Produk_model->getTotalProduct($search); // Ambil total baris produk dari database
        $this->pagepagination->setDefaultConfig(base_url("produk/daftar_produk"), $total_rows, $per_page);
        $this->_site_data['pagination'] = $this->pagepagination->create_links(); // Membuat pagination
        $this->_site_data['site_title'] = "Daftar Produk";
        $this->_site_data['search_product'] = $search;
        $this->_site_data['pagination_per_page'] = $this->Produk_model->pagination_perPageSelect($per_page);
        $this->_site_data['category_list'] = $this->Produk_model->getCategoryList($id_subcategory);
        $this->_site_data['product_list'] = $this->Produk_model->pagination_getProduct($id_subcategory, $search, $page_number, $total_rows, $per_page, $per_page);
        $this->load->view('main-template/header', $this->_site_data);
        $this->load->view('main-template/navbar', $this->_site_data);
        $this->load->view('main-template/sidebar', $this->_site_data);
        $this->load->view('produk/daftar_produk', $this->_site_data);
        $this->load->view('main-template/footer', $this->_site_data);
    }

    public function tambah_produk() {
        $this->_site_data['site_title'] = "Tambah Produk";
        $this->_site_data['category_list'] = $this->Produk_model->getCategoryList();
        $this->load->view('main-template/header', $this->_site_data);
        $this->load->view('main-template/navbar', $this->_site_data);
        $this->load->view('main-template/sidebar', $this->_site_data);
        $this->load->view('produk/tambah_produk', $this->_site_data);
        $this->load->view('main-template/footer', $this->_site_data);
    }

    public function input_tambah_produk() {
        if(!$this->input->is_ajax_request()) {
            $this->page403();
            return;
        }
        $post_input = $this->input->post();
        $files = $_FILES;
        $img_validation = validateMultipleImage($files);

        $validation_config = [
            [
                'field' => "namaProdukInput",
                'label' => "Nama Produk",
                'rules' => "required|max_length[200]|validation_no_special_character"
            ],
            [
                'field' => "hargaProdukInput",
                'label' => "Harga Produk",
                'rules' => "required|max_length[20]|validation_money_rupiah_format",
            ],
            [
                'field' => "kategoriProdukInput",
                'label' => "Kategori Produk",
                'rules' => "required|max_length[20]|alpha_numeric",
            ],
            [
                'field' => "stokProdukInput",
                'label' => "Stok Produk",
                'rules' => "required|max_length[20]|numeric",
            ],
            [
                'field' => "beratProdukInput",
                'label' => "Berat Produk",
                'rules' => "required|max_length[20]|validation_no_special_character",
            ],
            [
                'field' => "diskonProdukInput",
                'label' => "Diskon Produk",
                'rules' => "required|max_length[20]|numeric",
            ],
            [
                'field' => "deskripsiProdukInput",
                'label' => "Deskripsi Produk",
                'rules' => "required",
            ]
        ];
        $this->form_validation->set_rules($validation_config);
        if($this->form_validation->run()) {
            if(!is_array($img_validation)) {
                
                // $files = $_FILES; Sesudah pulang baru dikerjakan :)
                $harga = join('', explode(".", $post_input['hargaProdukInput'])); // Hilangkan titik sebelum di input ke database
                $data = [
                    'idProduk' => $this->Produk_model->generateID(),
                    'inputBy' => $this->_site_data['auth_userid'],
                    'idSubKategori' => $post_input['kategoriProdukInput'],
                    'hargaProduk' => $harga,
                    'namaProduk' => $post_input['namaProdukInput'],
                    'deskripsiProduk' => $post_input['deskripsiProdukInput'],
                    'stok' => $post_input['stokProdukInput'],
                    'berat' => $post_input['beratProdukInput'],
                    'diskon' => $post_input['diskonProdukInput'],
                    'hargaDiskon' => $post_input['diskonProdukInput'] > 0 ? $harga - (($harga * $post_input['diskonProdukInput']) / 100) : $harga,
                    'createdAt' => $this->_current_date,
                    'modifiedAt' => $this->_current_date
                ];
                $query = $this->Produk_model->insertProduct($data, $files);
                if(!is_array($query)) {
                    $this->notify->setNotify('notifyStatusError', false);
                    $this->notify->setNotify('notifySuccess', "Berhasil menginput data!");
                }
                else if(array_key_exists('upload_error_status', $query)) {
                    if($query['upload_error_status']) {
                        $this->notify->setNotify('notifyStatusError', false);
                        $this->notify->setNotify('notifySuccess', $query['upload_error_msg']);
                    }
                }
                else {
                    $this->notify->setNotify('notifyStatusError', true);
                    $this->notify->setNotify('notifyFailed', "Gagal menginput data!");
                }

            }
            else {
                $this->notify->setNotify("notifyStatusError", true);
                $this->notify->setNotify("notifyFileErrorStatus", true);
                $list_error = [];
                foreach($img_validation as $img_name => $img) {
                    $img_error = "
                        <div class=\"card-body rounded-0 alert alert-danger pb-0 mb-0\">
                            <span>Error Terjadi</span>
                    ";
                    foreach($img as $error) {
                        $img_error .= "
                            <div><span>{$error}</span></div>
                        ";
                    }
                    $img_error .= "
                        </div>
                    ";
                    $list_error["{$img_name}"] = $img_error;
                }
                $this->notify->setNotify('notifyFileListError', $list_error);
            }
        }
        else {
            $this->notify->setNotify('notifyStatusError', true);
            $this->notify->setNotify('notifyInputError|namaProdukInput', form_error("namaProdukInput"));
            $this->notify->setNotify('notifyInputError|hargaProdukInput', form_error("hargaProdukInput"));
            $this->notify->setNotify('notifyInputError|kategoriProdukInput', form_error("kategoriProdukInput"));
            $this->notify->setNotify('notifyInputError|stokProdukInput', form_error("stokProdukInput"));
            $this->notify->setNotify('notifyInputError|beratProdukInput', form_error("beratProdukInput"));
            $this->notify->setNotify('notifyInputError|diskonProdukInput', form_error("diskonProdukInput"));
            $this->notify->setNotify('notifyInputError|deskripsiProdukInput', form_error("deskripsiProdukInput"));
        }
        echo json_encode($this->notify->getNotifyList());
        exit();

    }
}
