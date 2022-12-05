<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('temporary_giftcard_promo_this_is_a_rush_solution_will_be_remove_soon')){
    function temporary_giftcard_promo_this_is_a_rush_solution_will_be_remove_soon($product_id, $quantity){
        if($product_id === 369 && $quantity === 10){ // products_tb >> gift card
            return $quantity + 1;
        }
        return $quantity;
    }
}

