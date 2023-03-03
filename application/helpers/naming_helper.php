<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('clean_str_for_img'))
{
    function clean_str_for_img($string){
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
     
        return strtolower(preg_replace('/-+/', '-', $string)); // Replaces multiple hyphens with single one and make it lower
    }   
}