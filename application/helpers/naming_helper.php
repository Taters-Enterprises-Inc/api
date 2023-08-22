<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('clean_str_for_img'))
{
    function clean_str_for_img($string){
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
     
        return strtolower(preg_replace('/-+/', '-', $string)); // Replaces multiple hyphens with single one and make it lower
    }   
}

if ( ! function_exists('clean_str_for_decimal'))
{
    function clean_str_for_decimal($inputNumber){
        // Remove the comma from the number
        $cleanNumber = str_replace(",", "", $inputNumber);

        // Convert the cleaned number to a decimal with two decimal places
        $formattedNumber = number_format($cleanNumber, 2, '.', '');

        // Output the formatted number as a string
        return $formattedNumber;
    }   
}