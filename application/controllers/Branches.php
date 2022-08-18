<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Branches extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('store_model');
	}


	public function index(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':

				$store_per_region = array(
					'ncr' => $this->store_model->fetch_ncr(),
					'luzon' => $this->store_model->fetch_luzon(),
					'visayas' => $this->store_model->fetch_visayas(),
					'mindanao' => $this->store_model->fetch_mindanao(),
				);

				$response = array(
					'data' => $store_per_region,
					'message' => 'Successfully fetch branches store'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}
}
