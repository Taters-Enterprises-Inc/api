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
					$stores = $this->store_model->get_stores_available($this->google->geolocator('Adamson')['lat'],$this->google->geolocator('Adamson')['lng']);
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
				
                $_SESSION['cache_data'] = array(
                    'store_id'			=>$store->store_id,
                    'region_id'	=>$store->active_reseller_region_id,
                    'store_name'		=>$store->name,
                );

				if($address){
					$_SESSION['customer_address'] = $address;
				}
				

				$response = array(
					'message' => 'Successfully set store data'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

}
