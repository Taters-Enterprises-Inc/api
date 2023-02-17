<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('upload')){
    function upload($file_name, $path, $name, $allowed_types){
        $CI = get_instance();

        $config['upload_path'] = $path; 
        if(!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, TRUE);
        
        $config['allowed_types']    = $allowed_types; 
        $config['max_size']         = 2000;
        $config['max_width']        = 0;
        $config['max_height']       = 0;
        $config['file_name']        = $name;

        $CI->load->library('upload', $config, 'uploadFile');
        $CI->uploadFile->initialize($config);
        
        if(!$CI->uploadFile->do_upload($file_name)){
            return $CI->uploadFile->display_errors();
        }

        return  null;
    }
}

