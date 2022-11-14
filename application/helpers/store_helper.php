<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('set_store_sessions'))
{
    function set_store_sessions(
        $store_id,
        $address,
        $service,
        $catering_start_date=null,
        $catering_end_date=null
    ){
        $CI = get_instance();

        $store = $CI->store_model->get_store_info($store_id);

        $check_surcharge = $CI->store_model->check_surcharge($store_id);
        $surcharge = $check_surcharge->enable_surcharge;
        $surcharge_delivery_rate = $check_surcharge->surcharge_delivery_rate;
        $surcharge_minimum_rate = $check_surcharge->surcharge_minimum_rate;
        
        $region = $CI->store_model->select_region($store->region_store_id);

        $CI->session->cache_data = array(
            'store_id'					=>	$store_id,
            'region_id'					=>	$store->region_store_id,
            'region_name'				=>	$region->name,
            'store_name'				=>	$store->name,
            'moh_notes'					=>	$store->moh_notes,
            'store_address'				=> 	$store->address,
            'surcharge_delivery_rate'	=>	$surcharge_delivery_rate,
            'surcharge_minimum_rate'	=>	$surcharge_minimum_rate,
            'surcharge'					=>	$surcharge
        );

        $CI->session->customer_address = $address;

        $opening = $CI->store_model->get_store_schedule($store_id);
        $moh_setup = $CI->store_model->fetch_moh_setup($store->region_store_id);
        
        $CI->session->set_userdata('km_radius', $moh_setup->km_radius);
        $CI->session->set_userdata('km_min', $moh_setup->km_min);
        $CI->session->set_userdata('free_delivery', $moh_setup->free_delivery);
        $CI->session->set_userdata('free_min_delivery', $moh_setup->free_min_delivery);

        $CI->session->set_userdata('delivery_rate', $opening->delivery_rate);
        $CI->session->set_userdata('minimum_rate', $opening->minimum_rate);
        
        if($service == 'CATERING'){
            
            $CI->session->set_userdata('catering_delivery_rate', $opening->catering_delivery_rate);
            $CI->session->set_userdata('catering_minimum_rate', $opening->catering_minimum_rate);

            if(isset($catering_start_date)){
                $str_start_datetime = strtotime($catering_start_date);
                $start_datetime = date($str_start_datetime);
                $CI->session->set_userdata('catering_start_date', $start_datetime);
            }
            
            if(isset($catering_end_date)){
                $str_end_datetime = strtotime($catering_end_date);
                $end_datetime = date($str_end_datetime);
                $CI->session->set_userdata('catering_end_date', $end_datetime);
            }

            $night_diff = (int)$CI->get_night_diff($start_datetime, $end_datetime);
            $CI->session->set_userdata('catering_night_differential_fee', (int) $night_diff);

            $succeeding_hour_charge = $CI->get_succeeding_hour_charge($start_datetime, $end_datetime);
            $CI->session->set_userdata('catering_succeeding_hour_charge', (int) $succeeding_hour_charge);
        }

        $customer_address= $CI->session->customer_address;
        $store_address = $opening->address;

        $get_routes = $CI->google->get_distance($store_address, $customer_address);
        $delivery_charge = distance_computation(round($get_routes), $service);

        $routes   = $get_routes;
        $distance = $get_routes;

        $CI->session->set_userdata('distance', round($routes));
        $CI->session->set_userdata('distance_rate_id', 0);
        $CI->session->set_userdata('distance_rate_price', $delivery_charge);
        $CI->session->set_userdata('distance_rate_price_before', $delivery_charge);
        $CI->session->set_userdata('distance_routes', $routes);
        $CI->session->set_userdata('distance_radius', $distance);
        
        $payops_list = $CI->store_model->fetch_bank_details($store_id);
        $CI->session->set_userdata('payops_list', $payops_list);
        $CI->session->set_userdata('cash_delivery', '50');
    }   
}


if ( ! function_exists('distance_computation')){
    function distance_computation($dist, $service){   
        $CI = get_instance();

        switch($service){
            case 'SNACKSHOP':
                $delivery_rate = $CI->session->delivery_rate ? $CI->session->delivery_rate : 8;
                $minimum_rate = $CI->session->minimum_rate ? $CI->session->minimum_rate : 80;
                $km_min = $CI->session->km_min ? $CI->session->km_min : 5;
                break;
            case 'CATERING':
                $delivery_rate = $CI->session->catering_delivery_rate ? $CI->session->catering_delivery_rate : 10;
                $minimum_rate = $CI->session->catering_minimum_rate ? $CI->session->catering_minimum_rate : 1000;
                $km_min = $CI->session->km_min ? $CI->session->km_min : 5;
                break;
        }

        if(isset($CI->session->free_delivery) && isset($CI->session->free_min_delivery)) {
            if($CI->session->free_delivery != 1) { // free delivery all location
                if($CI->session->free_min_delivery == 1 && $dist <= $km_min) { // free at minimum distance
                    $delivery_charge = 0;
                }else{
                    if ($CI->session->cache_data['surcharge'] == 0) {
                        $charge_per_km = $delivery_rate * $dist;
                        $comp = $charge_per_km + $minimum_rate;
                        $delivery_charge = $comp;
                    } else {
                        
                        switch($service){
                            case 'SNACKSHOP':
                                $charge_per_km = $CI->session->cache_data['surcharge_delivery_rate'] * $dist;
                                $comp = $charge_per_km + $CI->session->cache_data['surcharge_minimum_rate'];
                                $delivery_charge = $comp;
                                break;
                            case 'CATERING':
                                $charge_per_km = $delivery_rate * $dist;
                                $comp = $charge_per_km + $minimum_rate;
                                $delivery_charge = $comp;
                                break;
                        }
                    }
                }
            }else{
                $delivery_charge = 0;
            }
        }else{
            $delivery_charge = $minimum_rate;
        }

        return $delivery_charge;
    }
}