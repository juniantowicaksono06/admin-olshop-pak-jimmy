<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Pagepagination {
    private $_config;
    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    public function setDefaultConfig($url = "", $total_rows = 0, $per_page = 0, $use_page_number = true) {
        $this->_config = [
            'url' => $url,
            'total_rows' => $total_rows,
            'per_page' => $per_page,
            'use_page_numbers' => $use_page_number,
            'enable_query_strings' => true,
            'page_query_string' => true,
            'query_string_segment' => 'page',
            'reuse_query_string' => true,
            'num_links' => 5,
            'num_links' => floor($total_rows / $per_page),
            'first_url' => '?page=1',
            // Styling The Link
            'first_link' => 'Pertama',
            'last_link' => 'Terakhir',
            'next_link' => 'Berikut',
            'prev_link' => 'Sebelumnya',
            'full_tag_open' => '<div class="pagging text-right"><nav><ul class="pagination justify-content-end">',
            'full_tag_close' => '</ul></nav></div>',
            'num_tag_open' => '<li class="page-item"><span class="page-link">',
            'num_tag_close' => '</span></li>',
            'cur_tag_open' => '<li class="page-item active"><span class="page-link" id="active-page">',
            'cur_tag_close' => '<span class="sr-only">(current)</span></span></li>',
            'next_tag_open' => '<li class="page-item"><span class="page-link">',
            'next_tagl_close'  => '<span aria-hidden="true">&raquo;</span></span></li>',
            'prev_tag_open' => '<li class="page-item"><span class="page-link">',
            'prev_tagl_close'  => '</span>Next</li>',
            'first_tag_open' => '<li class="page-item"><span class="page-link">',
            'first_tagl_close' => '</span></li>',
            'last_tag_open'  => '<li class="page-item"><span class="page-link">',
            'last_tagl_close' => '</span></li>'
        ];
    }

    public function setKeyConfig($key, $value) {
        $this->_config[$key] = $value;
    }

    public function getConfig() {
        return $this->_config;
    }

    public function create_links() {
        $this->CI->pagination->initialize($this->_config);
        return $this->CI->pagination->create_links();
    }
}