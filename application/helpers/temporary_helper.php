<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('temporary_giftcard_promo_this_is_a_rush_solution_will_be_remove_soon')){
    function temporary_giftcard_promo_this_is_a_rush_solution_will_be_remove_soon(){
        $CI = get_instance();

        $orders = $CI->session->orders;
        $total_product_qty = 0;
        $is_free_item_exist = false;

        foreach($orders as $order){
            if($order['prod_id'] === 369){
                $total_product_qty += $order['prod_qty'];

                if($order['prod_calc_amount'] === 0){
                    $is_free_item_exist = true;
                }
            }
        }

        if($total_product_qty >= 10 && $is_free_item_exist === false){
            $prod_id = $CI->input->post('prod_id');
            $product_details = $CI->shop_model->get_details($prod_id)[0];
            $prod_image_name = $CI->input->post('prod_image_name');
            

            $product_sku_price = 0;
            $prod_size = NULL;

            $product_price = (empty($varx)) ? $product_details->price : $product_sku_price;
            $prod_calc_amount   = $CI->input->post('prod_calc_amount');
            
            $set_value['prod_id']               = $prod_id;
            $set_value['prod_image_name']       = $prod_image_name;
            $set_value['prod_name']             = $product_details->name;
            $set_value['prod_qty']              = 1;
            $set_value['prod_price']            = $product_price;
            $set_value['prod_calc_amount']      = 0;
            $set_value['prod_flavor_id']        = $CI->input->post('prod_flavor') !== ""  ? $CI->input->post('prod_flavor'): '';
            $set_value['prod_with_drinks']      = $CI->input->post('prod_with_drinks') ? 1 : 0;
            $set_value['prod_size']             = (empty($prod_size)) ? '' : $prod_size->name;
            $set_value['prod_size_id']          = $CI->input->post('prod_size') !== ""  ? $CI->input->post('prod_size'): '';
            $set_value['prod_multiflavors']     = $CI->input->post('flavors_details') !== null  ? $CI->input->post('flavors_details'): '';
            $set_value['prod_sku_id']           = $CI->input->post('prod_sku_id');
            $set_value['prod_sku']              = $CI->input->post('prod_sku');
            $set_value['prod_discount']         = 0;
            $set_value['prod_category']         = $product_details->category;
            $set_value['prod_type']             = $CI->input->post('prod_type');
            $set_value['promo_discount_percentage'] = $CI->input->post('promo_discount_percentage');

            $_SESSION['orders'][] = $set_value;
            
        }else if ($total_product_qty < 10 && $total_product_qty > 0 && $is_free_item_exist === true){
            foreach($orders as $key => $order){
                if($order['prod_id'] === 369 &&  $order['prod_calc_amount'] === 0){
                    unset($_SESSION['orders'][$key]);
                    $reindexed_array = array_values($CI->session->orders);
                    $CI->session->set_userdata('orders', $reindexed_array);
                }
            }
        }
    }
}

