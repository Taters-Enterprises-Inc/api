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

				if($address){
					$stores = $this->store_model->get_stores_available($this->google->geolocator($address)['lat'],$this->google->geolocator($address)['lng']);
				}else{
					$stores = $this->store_model->get_stores_available();
				}

				$response = array(
					'data' => $stores,
					'message' => 'Successfully fetch popclub_data'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				$address = $post['address'];
				$store_id = $post['storeId'];
                $store = $this->store_model->get_store_info($store_id);

				$check_surcharge = $this->store_model->check_surcharge($store_id);
				$surcharge = $check_surcharge->enable_surcharge;
				$surcharge_delivery_rate = $check_surcharge->surcharge_delivery_rate;
				$surcharge_minimum_rate = $check_surcharge->surcharge_minimum_rate;

                $_SESSION['cache_data'] = array(
                    'store_id'					=>	$store->store_id,
                    'region_id'					=>	$store->region_id,
                    'store_name'				=>	$store->name,
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
				$this->session->set_userdata('catering_delivery_rate', $opening->catering_delivery_rate);
				$this->session->set_userdata('catering_minimum_rate', $opening->catering_minimum_rate);
	
				$customer_address= $this->session->customer_address;
				$store_address = $opening->address;
	
				$get_routes = $this->google->get_distance($store_address, $customer_address);
				$delivery_charge = $this->distance_computation(round($get_routes));
	
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
				

				$response = array(
					'message' => 'Successfully set store data'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
			break;
		}
	}
	
    private function distance_computation($dist)
    {   
        //check if catering order
        if (isset($_SESSION['catering_data'])) {
            $delivery_rate = (isset($_SESSION['catering_delivery_rate'])) ? $_SESSION['catering_delivery_rate'] : 10;
            $minimum_rate = (isset($_SESSION['catering_minimum_rate'])) ? $_SESSION['catering_minimum_rate'] : 1000;
            $km_min = (isset($_SESSION['km_min'])) ? $_SESSION['km_min'] : 5;
        } else {
            $delivery_rate = (isset($_SESSION['delivery_rate'])) ? $_SESSION['delivery_rate'] : 8;
            $minimum_rate = (isset($_SESSION['minimum_rate'])) ? $_SESSION['minimum_rate'] : 80;
            $km_min = (isset($_SESSION['km_min'])) ? $_SESSION['km_min'] : 5;
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
                        if (isset($_SESSION['catering_data'])) {
                            $charge_per_km = $delivery_rate * $dist;
                            $comp = $charge_per_km + $minimum_rate;
                            $delivery_charge = $comp;
                        } else {
                            $charge_per_km = $_SESSION['cache_data']['surcharge_delivery_rate'] * $dist;
                            $comp = $charge_per_km + $_SESSION['cache_data']['surcharge_minimum_rate'];
                            $delivery_charge = $comp;
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
