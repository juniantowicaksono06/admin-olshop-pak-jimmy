<?php 

defined('BASEPATH') OR exit('No direct script access allowed');


class Kategori_produk extends APP_Controller {
    public function __construct() {
        parent::__construct();
        $this->_check_login_status_default_page();
        $this->notify->setNotify("notifyStatusError", false);
    }

    public function index() {
        $this->daftar_kategori_produk();
    }

    public function daftar_kategori_produk() {
        $page_number = $this->input->get('page'); // Ambil offset untuk halaman
        // cek nomor halaman dari get input adalah nomor?
        if(is_numeric($page_number) == 1) {
            $page_number = floor($page_number);
        }
        else {
            $page_number = 1; // Kalo bukan tipe data integer, float atur $page_number jadi 1
        }

        $url = base_url("kategori_produk/daftar_kategori_produk");
        $total_rows = $this->Kategori_model->getTotalCategory(); // Ambil total baris kategori
        $per_page = 5; // Atur jumlah konten per halaman

        
        $this->pagepagination->setDefaultConfig($url, $total_rows, $per_page);
        $element = $this->Kategori_model->pagination_categoryAndSubCategory($page_number, $total_rows, $per_page, $per_page);
        $this->_site_data['pagination'] = $this->pagepagination->create_links(); // Membuat pagination
        $this->_site_data['site_title'] = "Daftar Kategori Produk";
        $this->_site_data['pagination_content'] = $element;
        $this->load->view('main-template/header', $this->_site_data);
        $this->load->view('main-template/navbar', $this->_site_data);
        $this->load->view('main-template/sidebar', $this->_site_data);
        $this->load->view('kategori/daftar_kategori', $this->_site_data);
        $this->load->view('main-template/footer', $this->_site_data);
    }

    public function tambah_kategori_produk() {
        $this->_site_data['site_title'] = "Tambah Kategori Produk";
        $this->load->view('main-template/header', $this->_site_data);
        $this->load->view('main-template/navbar', $this->_site_data);
        $this->load->view('main-template/sidebar', $this->_site_data);
        $this->load->view('kategori/tambah_kategori', $this->_site_data);
        $this->load->view('main-template/footer', $this->_site_data);
    }
    // Input method kategori produk
    public function input_kategori_produk($input_type = 'add_category_input') {
        if($this->input->is_ajax_request()) {
            $post_input = $this->input->post();
            $sub_category_name_input_value_list = [];
            $sub_category_link_input_value_list = [];
            $validation_config = [
                [
                    // Kategori input rule validation
                    'field' => 'kategoriInput',
                    'label' => 'Kategori',
                    'rules' => 'required|max_length[30]|validation_no_special_character'
                ], 
                [
                    // Kategori link input rule validation
                    'field' => 'kategoriLinkInput',
                    'label' => 'Kategori Link',
                    'rules' => 'max_length[40]|validation_alpha_dash_num'
                ],
                [
                    // Sub Kategori Input pertama rule validation
                    'field' => 'subKategoriInput1',
                    'label' => 'Sub Kategori',
                    'rules' => 'required|max_length[30]|validation_no_special_character'
                ],
                [
                    // Sub Kategori Link Input pertama rule validation
                    'field' => 'subKategoriLinkInput1',
                    'label' => 'Sub Kategori Link',
                    'rules' => 'max_length[30]|validation_alpha_dash_num'
                ],
            ];

            // Cek tipe input?
            if($input_type == 'add_subcategory_input') {
                array_push($validation_config, [
                    'field' => 'idKategoriInput',
                    'label' => "ID Kategori",
                    'rules' => 'required|max_length[13]|validation_alpha_dash_num'
                ]);
            }

            if(array_key_exists('dynamic_subKategoriInput', $post_input) || array_key_exists('dynamic_subKategoriLinkInput', $post_input)) {
              
                for($x = 0; $x < count($post_input['dynamic_subKategoriInput']); $x++) {
                    $_POST["subKategoriInput" . ((int)$x + 2)] = $post_input["dynamic_subKategoriInput"][$x];
                    array_push($validation_config, [
                        'field' => "subKategoriInput" . ((int)$x + 2),
                        'label' => 'Sub Kategori',
                        'rules' => 'required|max_length[40]|validation_no_special_character'
                    ]);
                    $_POST["subKategoriLinkInput" . ((int)$x + 2)] = $post_input["dynamic_subKategoriLinkInput"][$x];
                    array_push($validation_config, [
                        'field' => "subKategoriLinkInput" . ((int)$x + 2),
                        'label' => 'Sub Kategori',
                        'rules' => 'max_length[40]|validation_alpha_dash_num'
                    ]);
                }
    
            }
            $this->form_validation->set_rules($validation_config);
            if($this->form_validation->run()) {
                $id_category = array_key_exists('idKategoriInput', $post_input) ? $post_input['idKategoriInput'] : $this->Kategori_model->generateID('kategori_produk');
                $data = [
                    'kategori' => [
                        'idKategori' => $id_category,
                        'inputBy' => $this->_site_data['auth_userid'],
                        'namaKategori' => $post_input['kategoriInput'],
                        'linkKategori' => empty($post_input['kategoriLinkInput']) ? strToLink($post_input['kategoriInput']) : strtolower($post_input['kategoriLinkInput']),
                        'modifiedAt' => $this->_current_date
                    ],
                    'subkategori' => [
                        'subKategori1' => [
                            'idSubKategori' => $this->Kategori_model->generateID('subkategori_produk'),
                            'idKategori' => $id_category,
                            'inputBy' => $this->_site_data['auth_userid'],
                            'namaSubKategori' => $post_input['subKategoriInput1'],
                            'linkSubKategori' => empty($post_input['subKategoriLinkInput1']) ? strToLink($post_input['subKategoriInput1']) : strtolower($post_input['subKategoriLinkInput1']),
                            'createdAt' => $this->_current_date,
                            'modifiedAt' => $this->_current_date
                        ]
                    ],
                    'inputType' => $input_type
                ];

                if($input_type != 'add_subcategory_input') {
                    $data['kategori']['createdAt'] = $this->_current_date;
                }

                array_push($sub_category_name_input_value_list, $post_input['subKategoriInput1']);

                array_push($sub_category_link_input_value_list, $post_input['subKategoriLinkInput1']);
                // Cek data inputan

                // $data_category = $this->Kategori_model->getCategory($post_input['kategoriInput']);

                $data_category_name = $this->Kategori_model->getCategory($post_input['kategoriInput']);

                $data_category_link = $this->Kategori_model->getCategory($post_input['kategoriLinkInput']);
                // Cek nama kategori unik?
                if($data_category_name != false && $input_type == 'add_category_input') {
                    $this->notify->setNotify('notifyStatusError', true);
                    $this->notify->setNotify('notifyInputError|kategoriInput', "Nama kategori ini sudah ada!");
                }
                
                // Cek link kategori unik?
                if($data_category_link != false && $input_type == 'add_category_input') {
                    $this->notify->setNotify('notifyStatusError', true);
                    $this->notify->setNotify('notifyInputError|kategoriLinkInput', "Link kategori ini sudah ada!");
                }

                if(array_key_exists('dynamic_subKategoriInput', $post_input) || array_key_exists('dynamic_subKategoriLinkInput', $post_input)) {
                    for($x = 0; $x < count($post_input['dynamic_subKategoriLinkInput']); $x++) {
                        $index = ((int)$x + 2);
                        $data['subkategori']["subKategori{$index}"] = [
                            'idSubKategori' => $this->Kategori_model->generateID('subkategori_produk'),
                            'idKategori' => $id_category,
                            'inputBy' => $this->_site_data['auth_userid'],
                            'namaSubKategori' => $post_input['dynamic_subKategoriInput'][$x],
                            'linkSubKategori' => empty($post_input['dynamic_subKategoriLinkInput'][$x]) ? strToLink($post_input['dynamic_subKategoriInput'][$x]) : strtolower($post_input['dynamic_subKategoriLinkInput'][$x]),
                            'createdAt' => $this->_current_date,
                            'modifiedAt' => $this->_current_date
                        ];


                        if(in_array($data['subkategori']["subKategori{$index}"]['namaSubKategori'], $sub_category_name_input_value_list)) {
                            $this->notify->setNotify('notifyStatusError', true);
                            $this->notify->setNotify("notifyInputError|subKategoriInput{$index}", "Input Nama Sub Kategori ini duplikat");  
                        }
                        if(in_array($data['subkategori']["subKategori{$index}"]['linkSubKategori'], $sub_category_link_input_value_list)) {
                            $this->notify->setNotify('notifyStatusError', true);
                            $this->notify->setNotify("notifyInputError|subKategoriLinkInput{$index}", "Input Link Sub Kategori ini duplikat");  
                        }

                        array_push($sub_category_link_input_value_list, $post_input['dynamic_subKategoriInput'][$x]);
                        array_push($sub_category_name_input_value_list, $post_input['dynamic_subKategoriLinkInput'][$x]);
                    }
                } 
                if(!$this->notify->getNotify('notifyStatusError')) {
                    $query = $this->Kategori_model->inputCategoryAndSubCategory($data);
                    if(!$query['error']) {
                        $this->notify->setNotify('notifyStatusError', false);
                        $this->notify->setNotify("notifySuccess", $query['msg']);
                    }
                    else {
                        $this->notify->setNotify('notifyStatusError', true);
                        $this->notify->setNotify("notifyFailed", $query['msg']);
                    }
                }
            }
            else {
                $this->notify->setNotify('notifyStatusError', true);
                if(trim(form_error('idKategoriInput')) != "") {
                    $this->notify->setNotify('notifyFailed', form_error('idKategoriInput'));
                }
                $this->notify->setNotify('notifyInputError|kategoriInput', form_error("kategoriInput"));
                $this->notify->setNotify('notifyInputError|kategoriLinkInput', form_error('kategoriLinkInput'));
                $this->notify->setNotify('notifyInputError|subKategoriInput1', form_error("subKategoriInput1"));
                $this->notify->setNotify('notifyInputError|subKategoriLinkInput1', form_error("subKategoriLinkInput1"));
                if(array_key_exists('dynamic_subKategoriLinkInput', $post_input)) {
                    for($x = 0; $x < count($post_input['dynamic_subKategoriLinkInput']); $x++) {
                        $this->notify->setNotify('notifyInputError|subKategoriInput' . ((int)$x + 2), form_error("subKategoriInput" . ((int)$x + 2)));
                        $this->notify->setNotify('notifyInputError|subKategoriLink' . ((int)$x + 2), form_error("subKategoriLinkInput" . ((int)$x + 2)));
                    }
                }
            }
            echo json_encode($this->notify->getNotifyList());
        }
        else {
            $this->page403();
            return;
        }
    }
    // Hapus method kategori produk
    public function hapus_kategori_produk() {
        if(!$this->input->is_ajax_request()) {
            $this->page403();
            return;
        }
        $post_input = $this->input->post();

        $page_number = 1;
        $id_category = '';

        $page_number = $post_input['page'];
        $total_category_element = 0;

        if(array_key_exists('page', $post_input)) {
            $page_number = $post_input['page'];
            if(is_numeric($page_number) == 1) {
                $page_number = round($page_number);
            }
        }

        if(array_key_exists('kategoriElementTotal', $post_input)) {
            $total_category_element = $post_input['kategoriElementTotal'];
        }

        if(array_key_exists('idKategori', $post_input)) {
            $id_category = $post_input['idKategori'];
    
            $delete = $this->Kategori_model->deleteCategory($id_category);

            if($delete) {
                $url = base_url("kategori_produk/daftar_kategori_produk");
                $total_rows = $this->Kategori_model->getTotalCategory(); // Ambil total baris kategori

                $page_number = $page_number > ceil($total_rows / 5) ? ceil($total_rows / 5) : $page_number;

                $per_page = 1; // Atur jumlah konten per halaman
                $element = "";
                // Cek apakah masih ada category element pada DOM?
                if($total_category_element == 0) {
                    $element = $this->Kategori_model->pagination_categoryAndSubCategory($page_number + 1, $total_rows, $per_page, 5, true); // Kalo tidak ada $page_number + 1 untuk agar data tidak ditemukan!
                }
                else {
                    $element = $this->Kategori_model->pagination_categoryAndSubCategory($page_number, $total_rows, $per_page, 5, true);
                }
                $this->pagepagination->setDefaultConfig($url, $total_rows, 5);
                $this->notify->setNotify('notifyStatusError', false);
                $this->notify->setNotify('notifySuccess', "Berhasil menghapus kategori!");
                if($element == "") {
                    $page_number -= 1;
                    $this->notify->setNotify('notifyRedirect', base_url("kategori_produk/daftar_kategori_produk?page={$page_number}"));
                    $this->notify->setNotify('notifyRedirectTimeout', 4000);
                }
                else {
                    $this->notify->setNotify('notifyContent', $element);
                    $this->notify->setNotify('notifyPagination', $this->pagepagination->create_links());
                }
            }
            else {
                $this->notify->setNotify('notifyStatusError', true);
                $this->notify->setNotify('notifyFailed', "Gagal menghapus data kategori!"); // Atur pesan error
            }
        }
        else {
            $this->notify->setNotify('notifyStatusError', true);
            $this->notify->setNotify('notifyFailed', "Gagal menghapus data kategori!");
        }

        echo json_encode($this->notify->getNotifyList());
        exit();
    }
    // Hapus method subkategori produk
    public function hapus_subkategori_produk() {
        if(!$this->input->is_ajax_request()) {
            $this->page403();
            return;
        }
        $post_input = $this->input->post();

        $page_number = 1;
        $id_subcategory = '';

        $page_number = $post_input['page'];
        $total_category_element = 0;

        if(array_key_exists('page', $post_input)) {
            $page_number = $post_input['page'];
            if(is_numeric($page_number) == 1) {
                $page_number = round($page_number);
            }
        }

        if(array_key_exists('kategoriElementTotal', $post_input)) {
            $total_category_element = $post_input['kategoriElementTotal'];
        }
        if(array_key_exists('idSubKategori', $post_input)) {
            $id_subcategory = $post_input['idSubKategori'];
            $id_category = $this->Kategori_model->getIDCategoryByIDSubCategory($id_subcategory);
            $delete_sub_category = $this->Kategori_model->deleteSubCategory($id_subcategory);
            $total_sub_category = $id_category != false ? $this->Kategori_model->getTotalSubCategory($id_category) : 0;
            if($delete_sub_category) {
                $this->notify->setNotify('notifyStatusError', false);
                $this->notify->setNotify('notifySuccess', "Berhasil menghapus subkategori!");
                if($total_sub_category == 0) {
                    $delete_category = $this->Kategori_model->deleteCategory($id_category);
                    if($delete_category) {
                        $url = base_url("kategori_produk/daftar_kategori_produk");
                        $total_rows = $this->Kategori_model->getTotalCategory(); // Ambil total baris kategori
        
                        $page_number = $page_number > ceil($total_rows / 5) ? ceil($total_rows / 5) : $page_number;
        
                        $per_page = 1; // Atur jumlah konten per halaman
                
                        $element = "";
                        // Cek apakah masih ada category element pada DOM?
                        if($total_category_element == 0) {
                            $element = $this->Kategori_model->pagination_categoryAndSubCategory($page_number + 1, $total_rows, $per_page, 5, true); // Kalo tidak ada $page_number + 1 untuk agar data tidak ditemukan!
                        }
                        else {
                            $element = $this->Kategori_model->pagination_categoryAndSubCategory($page_number, $total_rows, $per_page, 5, true);
                        }
                        $this->pagepagination->setDefaultConfig($url, $total_rows, 5);
                        if($element == "" && $total_category_element == 0) {
                            $page_number -= 1;
                            $this->notify->setNotify('notifyRedirect', base_url("kategori_produk/daftar_kategori_produk?page={$page_number}"));
                            $this->notify->setNotify('notifyRedirectTimeout', 4000);
                        }
                        else {
                            $this->notify->setNotify('notifyContent', $element);
                            $this->notify->setNotify('notifyPagination', $this->pagepagination->create_links());
                        }
                    }
                    else {
                        $this->notify->setNotify('notifyStatusError', true);
                        $this->notify->setNotify('notifyFailed', "Gagal menghapus data kategori!"); // Atur pesan error
                    }
                }
            }
            else {
                $this->notify->setNotify('notifyStatusError', true);
                $this->notify->setNotify('notifyFailed', "Gagal menghapus data subkategori!"); // Atur pesan error
            }
            
        }
        else {
            $this->notify->setNotify('notifyStatusError', true);
            $this->notify->setNotify('notifyFailed', "Gagal menghapus data subkategori!"); // Atur pesan error
        }
        echo json_encode($this->notify->getNotifyList());
        exit();
    }
    // View method tambah subkategori produk
    public function tambah_subkategori_produk() {
        $get_input = $this->input->get();
        if(array_key_exists("idKategori", $get_input)) {
            $id_category = $get_input['idKategori'];
            $category = $this->Kategori_model->getCategoryByCategoryID($id_category);
            if($category !== false) {
                $this->_site_data['idKategori'] = $category->idKategori;
                $this->_site_data['namaKategori'] = $category->namaKategori;
                $this->_site_data['linkKategori'] = $category->linkKategori;
                $this->_site_data['site_title'] = "Tambah Sub Kategori";
                $this->load->view('main-template/header', $this->_site_data);
                $this->load->view('main-template/navbar', $this->_site_data);
                $this->load->view('main-template/sidebar', $this->_site_data);
                $this->load->view('kategori/tambah_subkategori', $this->_site_data);
                $this->load->view('main-template/footer', $this->_site_data);
            }
            else {
                $this->page404();
            }
        }
        else {
            $this->page404();
        }
    }
    // View Method edit kategori produk
    public function edit_kategori_produk() {
        $get_input = $this->input->get();
        $this->_site_data['site_title'] = "Edit Kategori";
        $id_category = "";

        if(array_key_exists('idKategori', $get_input)) {
            $id_category = $get_input['idKategori'];
        }
        
        $category = $this->Kategori_model->getCategoryByCategoryID($id_category);
        if($category != false) {
            $this->_site_data['idKategori'] = $id_category;
            $this->_site_data['namaKategori'] = $category->namaKategori;
            $this->load->view('main-template/header', $this->_site_data);
            $this->load->view('main-template/navbar', $this->_site_data);
            $this->load->view('main-template/sidebar', $this->_site_data);
            $this->load->view('kategori/edit_kategori', $this->_site_data);
            $this->load->view('main-template/footer', $this->_site_data);
        }
    }

    // Input edit kategori produk

    public function input_edit_kategori_produk() {
        $post_input = $this->input->post();
        $validation_config = [
            [
                'field' => 'idKategoriInput',
                'label' => 'ID Kategori',
                'rules' => 'required|max_length[30]|validation_alpha_dash_num'
            ],
            [
                // Kategori input rule validation
                'field' => 'namaKategoriInput',
                'label' => 'Kategori',
                'rules' => 'required|max_length[30]|validation_no_special_character'
            ], 
            [
                // Kategori link input rule validation
                'field' => 'kategoriLinkInput',
                'label' => 'Kategori Link',
                'rules' => 'max_length[40]|validation_alpha_dash_num'
            ]
        ];

        $this->form_validation->set_rules($validation_config);

        if($this->form_validation->run()) {
            $data = [
                'idKategori' => $post_input['idKategoriInput'],
                'inputBy' => $this->_site_data['auth_userid'],
                'namaKategori' => $post_input['namaKategoriInput'],
                'linkKategori' => empty($post_input['kategoriLinkInput']) ? strToLink($post_input['namaKategoriInput']) : strtolower($post_input['kategoriLinkInput']),
                'inputType' => 'edit_category_input',
                'modifiedAt' => $this->_current_date
            ];

            $data_category_name = $this->Kategori_model->getCategory($post_input['namaKategoriInput'], 'nama');

            $data_category_link = $this->Kategori_model->getCategory($data['linkKategori']);


            // Cek nama kategori sudah ada?
            if($data_category_name != false) {
                $this->notify->setNotify('notifyStatusError', true); // Atur status error jadi true
                if($data_category_link != false){
                    // Cek jika id kategori link sama dengan yang akan di edit dan id kategori nama sama dengan yang akan di edit?
                    if($data_category_link->idKategori == $data['idKategori'] && $data_category_name->idKategori == $data['idKategori'])  {
                        $this->notify->setNotify('notifyInfo', 'Berhasil mengupdate data!'); // Jika sama maka kembalikan pesan ini!
                    }
                    else {
                        // Jika salah satu tidak ada yang sama maka jalankan statement dibawah ini

                        // Cek jika id kategori nama sama dengan id yang akan di edit?
                        if($data_category_name->idKategori != $data['idKategori']) {
                            $this->notify->setNotify('notifyInputError|namaKategoriInput', "Nama kategori sudah ada!");
                        }
                        // Cek jika id kategori link sama dengan id yang akan di edit?
                        if($data_category_link->idKategori != $data['idKategori']) {
                            $this->notify->setNotify("notifyInputError|kategoriLinkInput", "Link kategori sudah ada!");
                        }
                    }
                }
                // Cek id kategori nama tidak sama dengan id kategori yang akan di edit?
                else if($data_category_name->idKategori != $data['idKategori']) {
                    $this->notify->setNotify("notifyInputError|namaKategoriInput", "Nama kategori sudah ada!");
                }
                else {
                    $this->notify->setNotify('notifyInfo', 'Berhasil mengupdate data!'); // Buat pesan berhasil update data jika iya
                }
            }
            else if($data_category_link != false) {
                // Kalo misalkan data_category_name sama dengan false berarti tidak ada data nama kategori yang sama / unik
                // Maka jalankan statement dibawah ini


                $this->notify->setNotify('notifyStatusError', true); // Atur status error
                if($data_category_link->idKategori == $data['idKategori']) {
                    $this->notify->setNotify('notifyInfo', 'Berhasil mengupdate data!'); // Buat pesan berhasil update data
                }
                else {
                    $this->notify->setNotify('notifyInputError|kategoriLinkInput', "Link kategori sudah ada!"); // Atur pesan error kategori link input
                }
            }

            if(!$this->notify->getNotify('notifyStatusError')) { // Cek status error
                // Kalo NOT FALSE jalankan statement dibawah ini
                $query = $this->Kategori_model->inputCategoryAndSubCategory($data);
                if(!$query['error']) {
                    $this->notify->setNotify('notifyStatusError', false);
                    $this->notify->setNotify('notifySuccess', $query['msg']);
                }
                else {
                    $this->notify->setNotify('notifyStatusError', true);
                    $this->notify->setNotify('notifyFailed', $query['msg']);
                }
            }
        }
        else {
            $this->notify->setNotify('notifyStatusError', true);
            $this->notify->setNotify('notifyInputError|idKategoriInput', form_error('idKategoriInput'));
            if(form_error('idKategoriInput') != "") {
                $this->notify->setNotify('notifyFailed', form_error('idKategoriInput'));
            }
            $this->notify->setNotify('notifyInputError|namaKategoriInput', form_error('namaKategoriInput'));
            $this->notify->setNotify('notifyInputError|kategoriLinkInput', form_error('kategoriLinkInput'));
        }
        echo json_encode($this->notify->getNotifyList());
        exit();
    }
    // View method edit subkategori produk
    public function edit_subkategori_produk() {
        $get_input = $this->input->get();
        $id_subcategory = "";
        $this->_site_data['site_title'] = 'Edit Sub Kategori';
        if(array_key_exists('idSubKategori', $get_input)) {
            $id_subcategory = $get_input['idSubKategori'];
        }
        $sub_category = $this->Kategori_model->getSubCategoryBySubCategoryID($id_subcategory);
        if($sub_category != false) {
            $this->_site_data['idKategori'] = $sub_category->idKategori;
            $this->_site_data['idSubKategori'] = $sub_category->idSubKategori;
            $this->_site_data['namaSubKategori'] = $sub_category->namaSubKategori;
            $this->_site_data['linkSubKategori'] = $sub_category->linkSubKategori;
        }
        $this->load->view('main-template/header', $this->_site_data);
        $this->load->view('main-template/navbar', $this->_site_data);
        $this->load->view('main-template/sidebar', $this->_site_data);
        $this->load->view('kategori/edit_subkategori', $this->_site_data);
        $this->load->view('main-template/footer', $this->_site_data);
    }
    // Input edit subkategori produk
    public function input_edit_subkategori_produk() {
        if(!$this->input->is_ajax_request()) {
            $this->page403();
            return;
        }
        $post_input = $this->input->post();
        $validation_config = [
            [
                // ID Kategori Input rule validation
                'field' => 'idKategoriInput',
                'label' => 'ID Kategori',
                'rules' => 'required|max_length[30]|validation_alpha_dash_num'
            ],
            [
                // Id Sub Kategori input rule validation
                'field' => 'idSubKategoriInput',
                'label' => 'ID Sub Kategori',
                'rules' => 'required|max_length[30]|validation_alpha_dash_num'
            ], 
            [
                // Nama Sub Kategori input rule validation
                'field' => 'namaSubKategoriInput',
                'label' => 'Nama Sub Kategori',
                'rules' => 'required|max_length[30]|validation_no_special_character'
            ],
            [
                // Link Sub Kategori input rule validation
                'field' => 'subKategoriLinkInput',
                'label' => 'Link Sub Kategori',
                'rules' => 'max_length[30]|validation_no_special_character'
            ]
        ];

        $this->form_validation->set_rules($validation_config);
        if($this->form_validation->run()) {
            $id_category = $post_input['idKategoriInput'];
            $id_subcategory = $post_input['idSubKategoriInput'];
            $data = [
                   'namaSubKategori' => $post_input['namaSubKategoriInput'],
                   'linkSubKategori' => empty($post_input['subKategoriLinkInput']) ? strToLink($post_input['namaSubKategoriInput']) : strtolower($post_input['subKategoriLinkInput']),
                   'modifiedAt' => $this->_current_date
            ];
            $sub_category = $this->Kategori_model->getSubCategory($post_input  ['namaSubKategoriInput'], [
                $post_input['idKategoriInput'], $data['namaSubKategori']
            ]);
            if($sub_category != false) {
                if($sub_category->idKategori == $id_category) {
                    $this->notify->setNotify('notifyStatusError', true);
                    if($sub_category->idSubKategori == $id_subcategory) {
                        $this->notify->setNotify('notifyInfo', "Berhasil mengubah data!");
                    }
                    else {
                        $this->notify->setNotify('notifyInputError|namaSubKategoriInput', "Nama Sub Kategori ini sudah ada!");
                    }
                }

                if($post_input['subKategoriLinkInput'] != "") {
                    if($sub_category->linkSubKategori == $data['linkSubKategori']) {
                        $this->notify->setNotify('notifyStatusError', true);
                        $this->notify->setNotify('notifyInputError|subKategoriLinkInput', "Link Sub Kategori ini sudah ada!");
                    }
                }
            }


            if(!$this->notify->getNotify('notifyStatusError')) {
                $query = $this->Kategori_model->editSubCategory($id_subcategory, $data);
                if(!$query['error']) {
                    $this->notify->setNotify('notifyStatusError', false);
                    $this->notify->setNotify('notifySuccess', $query['msg']);
                }
                else {
                    $this->notify->setNotify('notifyStatusError', false);
                    $this->notify->setNotify('notifyFailed', $query['msg']);
                }
            }
        }
        else{
            $this->notify->setNotify('notifyStatusError', true);
            $this->notify->setNotify('notifyInputError|idKategoriInput', form_error('idKategoriInput'));
            $this->notify->setNotify('notifyInputError|idSubKategoriInput', form_error('idSubKategoriInput'));
            if(form_error('idKategoriInput') != "") {
                $this->notify->setNotify('notifyFailed', form_error('idKategoriInput'));
            }

            else if(form_error('idSubKategoriInput') != "") {
                $this->notify->setNotify('notifyFailed', form_error('idKategoriInput'));  
            }

            $this->notify->setNotify('notifyInputError|namaSubKategoriInput', form_error('namaKategoriInput'));
            $this->notify->setNotify('notifyInputError|subKategoriLinkInput', form_error('kategoriLinkInput'));
        }
        echo json_encode($this->notify->getNotifyList());
        exit();
    }
}