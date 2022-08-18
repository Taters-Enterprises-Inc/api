<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Shared extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function session(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$data = array(
					'popclub_data' 					=> $this->session->popclub_data,
					'cache_data' 					=> $this->session->cache_data,
					'customer_address' 				=> $this->session->customer_address,
					'userData' 						=> $this->session->userData,
					'orders'						=> $this->session->orders,
					'deals' 						=> $this->session->deals,
					"km_radius"						=> $this->session->km_radius,
					"km_min"						=> $this->session->km_min,
					"free_delivery"					=> $this->session->free_delivery,
					"free_min_delivery"				=> $this->session->free_min_delivery,
					"delivery_rate"					=> $this->session->delivery_rate,
					"minimum_rate"					=> $this->session->minimum_rate,
					"catering_delivery_rate"		=> $this->session->catering_delivery_rate,
					"catering_minimum_rate"			=> $this->session->catering_minimum_rate,
					"distance"						=> $this->session->distance,
					"distance_rate_id"				=> $this->session->distance_rate_id,
					"distance_rate_price"			=> $this->session->distance_rate_price,
					"distance_rate_price_before"	=> $this->session->distance_rate_price_before,
					"distance_routes"				=> $this->session->distance_routes,
					"distance_radius"				=> $this->session->distance_radius,
					"payops_list"					=> $this->session->payops_list,
				);
		
				$response = array(
					'message' => 'Successfully fetch session',
					'data' => $data,
				);
				
				header('content-type: application/json');
				echo json_encode($response, JSON_PRETTY_PRINT);
				break;
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);

				foreach($post['session'] as $key => $session){
					$_SESSION[$key] = $session;
				}
				
				$response = array(
					'message' => 'Successfully update session'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}
	
	
	public function clear_all_session(){
		$this->session->sess_destroy();
		echo "<pre>";
		print_r($_SESSION);
	}

	public function clear_redeems(){
		unset($_SESSION['redeem_data']);
		echo "<pre>";
		print_r($_SESSION);
	}
}
