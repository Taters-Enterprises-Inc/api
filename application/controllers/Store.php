<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Store extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->library('google');
		$this->load->model('store_model');
	}

	public function index(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$address = $this->input->get('address');
				$service = $this->input->get('service');

				if($address){
					$stores = $this->store_model->get_stores_available($this->google->geolocator($address)['lat'],$this->google->geolocator($address)['lng'],$service);
				}else{
					$stores = $this->store_model->get_stores_available(0, 0, $service);
				}

				$response = array(
					'data' => $stores,
					'message' => 'Select a store that is available near you'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				$address = $post['address'];
				$store_id = $post['storeId'];
				$region_id = $post['regionId'];
				$service = $post['service'];
				$catering_start_date = $post['cateringStartDate'];
				$catering_end_date = $post['cateringEndDate'];
                $store = $this->store_model->get_store_info($store_id);

				$check_surcharge = $this->store_model->check_surcharge($store_id);
				$surcharge = $check_surcharge->enable_surcharge;
				$surcharge_delivery_rate = $check_surcharge->surcharge_delivery_rate;
				$surcharge_minimum_rate = $check_surcharge->surcharge_minimum_rate;
				
				$region = $this->store_model->select_region($region_id);

                $_SESSION['cache_data'] = array(
                    'store_id'					=>	$store->store_id,
                    'region_id'					=>	$region_id,
                    'region_name'				=>	$region->name,
                    'store_name'				=>	$store->name,
					'moh_notes'					=>	$store->moh_notes,
					'store_address'				=> 	$store->address,
					'surcharge_delivery_rate'	=>	$surcharge_delivery_rate,
					'surcharge_minimum_rate'	=>	$surcharge_minimum_rate,
					'surcharge'					=>	$surcharge
                );

				if($address){
					$_SESSION['customer_address'] = $address;
				}

				$opening = $this->store_model->get_store_schedule($store_id);
				$moh_setup = $this->store_model->fetch_moh_setup($store->region_id);
				
				$this->session->set_userdata('km_radius', $moh_setup->km_radius);
				$this->session->set_userdata('km_min', $moh_setup->km_min);
				$this->session->set_userdata('free_delivery', $moh_setup->free_delivery);
				$this->session->set_userdata('free_min_delivery', $moh_setup->free_min_delivery);
	
				$this->session->set_userdata('delivery_rate', $opening->delivery_rate);
				$this->session->set_userdata('minimum_rate', $opening->minimum_rate);
				
				if($service == 'CATERING'){


					$this->session->set_userdata('catering_delivery_rate', $opening->catering_delivery_rate);
					$this->session->set_userdata('catering_minimum_rate', $opening->catering_minimum_rate);
	
					if(isset($catering_start_date)){
						$str_start_datetime = strtotime($catering_start_date);
						$start_datetime = date($str_start_datetime);
						$this->session->set_userdata('catering_start_date', $start_datetime);
					}
					
					if(isset($catering_end_date)){
						$str_end_datetime = strtotime($catering_end_date);
						$end_datetime = date($str_end_datetime);
						$this->session->set_userdata('catering_end_date', $end_datetime);
					}

					$night_diff = (int)$this->get_night_diff($start_datetime, $end_datetime);
					$this->session->set_userdata('catering_night_differential_fee', (int) $night_diff);

					$succeeding_hour_charge = $this->get_succeeding_hour_charge($start_datetime, $end_datetime);
					$this->session->set_userdata('catering_succeeding_hour_charge', (int) $succeeding_hour_charge);
				}
	
				$customer_address= $this->session->customer_address;
				$store_address = $opening->address;
	
				$get_routes = $this->google->get_distance($store_address, $customer_address);
				$delivery_charge = $this->distance_computation(round($get_routes), $service);
	
				$routes   = $get_routes;
				$distance = $get_routes;
	
				$this->session->set_userdata('distance', round($routes));
				$this->session->set_userdata('distance_rate_id', 0);
				$this->session->set_userdata('distance_rate_price', $delivery_charge);
				$this->session->set_userdata('distance_rate_price_before', $delivery_charge);
				$this->session->set_userdata('distance_routes', $routes);
				$this->session->set_userdata('distance_radius', $distance);
				
				$payops_list = $this->store_model->fetch_bank_details($store_id);
				$this->session->set_userdata('payops_list', $payops_list);
                $this->session->set_userdata('cash_delivery', '50');

				

				$response = array(
					'message' => 'Successfully set store data'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
			break;
		}
	}
	
	public function reset(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$this->session->unset_userdata('cache_data');
				$this->session->unset_userdata('orders');
				$this->session->unset_userdata('customer_address');
				
				$response = array(
					'message' => 'Reset user selection'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
		
	}
	
    private function distance_computation($dist, $service)
    {   
        //check if catering order

		switch($service){
			case 'SNACKSHOP':
				$delivery_rate = (isset($_SESSION['delivery_rate'])) ? $_SESSION['delivery_rate'] : 8;
				$minimum_rate = (isset($_SESSION['minimum_rate'])) ? $_SESSION['minimum_rate'] : 80;
				$km_min = (isset($_SESSION['km_min'])) ? $_SESSION['km_min'] : 5;
				break;
			case 'CATERING':
				$delivery_rate = (isset($_SESSION['catering_delivery_rate'])) ? $_SESSION['catering_delivery_rate'] : 10;
				$minimum_rate = (isset($_SESSION['catering_minimum_rate'])) ? $_SESSION['catering_minimum_rate'] : 1000;
				$km_min = (isset($_SESSION['km_min'])) ? $_SESSION['km_min'] : 5;
				break;
		}

        if(isset($_SESSION['free_delivery']) && isset($_SESSION['free_min_delivery'])) {
            if($_SESSION['free_delivery'] != 1) { // free delivery all location
                if($_SESSION['free_min_delivery'] == 1 && $dist <= $km_min) { // free at minimum distance
                    $delivery_charge = 0;
                }else{
                    if ($_SESSION['cache_data']['surcharge'] == 0) {
                        $charge_per_km = $delivery_rate * $dist;
                        $comp = $charge_per_km + $minimum_rate;
                        $delivery_charge = $comp;
                    } else {
						
						switch($service){
							case 'SNACKSHOP':
								$charge_per_km = $_SESSION['cache_data']['surcharge_delivery_rate'] * $dist;
								$comp = $charge_per_km + $_SESSION['cache_data']['surcharge_minimum_rate'];
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

	
    public function get_night_diff($start_datetime, $end_datetime){
        date_default_timezone_set('Asia/Manila');
        $start   = date('Y-m-d 22:00:00',$start_datetime);
        $end     = date('Y-m-d 06:00:00',$start_datetime + 86400);

        $event_start  = date('Y-m-d H:i:s',$start_datetime);
        $event_end    = date('Y-m-d H:i:s',$end_datetime);

        $night_diff = 0;
        if($event_end > $start AND $event_end <= $end AND $event_start <= $start){
            $night_diff = abs(strtotime($start) - strtotime($event_end)) / 3600;
        }else if($event_end <= $end AND $event_start > $start){
            $night_diff = abs(strtotime($event_start) - strtotime($event_end)) / 3600;
        }else if($event_end > $end AND $event_start > $start){
            $night_diff = abs(strtotime($event_start) - strtotime($end)) / 3600;
        }else if($event_end > $end AND $event_start <= $start){
            $night_diff = abs(strtotime($start) - strtotime($end)) / 3600;
        }else if($event_start < $end){  
            $night_diff=0;
            for ($i=date('H',$start_datetime); $i < date('H',$end_datetime); $i++) { 
               if ($i < 6) {
                $night_diff++;               
                }
            }
        }
        return $night_diff * 500;
    }

	
    public function get_succeeding_hour_charge($start_datetime, $end_datetime){
        $event_start = $start_datetime;
        $event_end = $end_datetime;
        $time_diff = $event_end - $event_start;

        $event_duration = ($time_diff/60)/60;
        
        if ($event_duration > 3) {
            $comp = $event_duration - 3;
            $additional_fee = $comp * 500;
        } else {
            $additional_fee = 0;
        }

        return $additional_fee;
    }

}