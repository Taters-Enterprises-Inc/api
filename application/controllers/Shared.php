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
					'popclub_data' => $this->session->popclub_data,
					'cache_data' => $this->session->cache_data,
					'customer_address' => $this->session->customer_address,
					'userData' => $this->session->userData,
					'orders' => $this->session->orders,
					'deals' => $this->session->deals,
				);
		
				$response = array(
					'message' => 'Successfully set popclub_data',
					'data' => $data,
					'session' => $_SESSION,
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
