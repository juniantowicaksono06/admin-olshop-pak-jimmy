<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Pagepagination {
    private $_config;
    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    public function setDefaultConfig($url = "", $total_rows = 0, $per_page = 0, $use_page_number = true) {
        $query_string = explode("&", $_SERVER['QUERY_STRING']);
        for($x = 0; $x < count($query_string); $x++) {
            if(preg_match('/(^page=[2-9]$)/', $query_string[$x])) {
                unset($query_string[$x]);
            }
        }
        $query_string = join("&", $query_string);
        $this->_config = [
            'url' => $url,
            'total_rows' => $total_rows,
            'per_page' => $per_page,
            'use_page_numbers' => $use_page_number,
            'enable_query_strings' => true,
            'page_query_string' => true,
            'query_string_segment' => 'page',
            'reuse_query_string' => true,
            'uri_segment' => 4,
            'first_url' => !empty($query_string) ? "?{$query_string}&page=1" : "?page=1",
            'num_links' => floor($total_rows / $per_page),
            // Styling The Link
            'first_link' => 'Pertama',
            'last_link' => 'Terakhir',
            'next_link' => 'Berikut',
            'prev_link' => 'Sebelumnya',
            'full_tag_open' => '<div class="pagging text-right"><nav><ul class="pagination justify-content-end">',
            'full_tag_close' => '</ul></nav></div>',
            'num_tag_open' => '<li class="page-item"><span class="page-link rounded-0">',
            'num_tag_close' => '</span></li>',
            'cur_tag_open' => '<li class="page-item active"><span class="page-link rounded-0" id="active-page">',
            'cur_tag_close' => '<span class="sr-only">(current)</span></span></li>',
            'next_tag_open' => '<li class="page-item"><span class="page-link rounded-0">',
            'next_tagl_close'  => '<span aria-hidden="true">&raquo;</span></span></li>',
            'prev_tag_open' => '<li class="page-item"><span class="page-link rounded-0">',
            'prev_tagl_close'  => '</span>Next</li>',
            'first_tag_open' => '<li class="page-item"><span class="page-link rounded-0">',
            'first_tagl_close' => '</span></li>',
            'last_tag_open'  => '<li class="page-item"><span class="page-link rounded-0">',
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