<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Bsc extends CI_Controller
{
	public function __construct(){
		parent::__construct();
	}

	public function session(){
		switch($this->input->server('REQUEST_METHOD')){
		  case 'GET':
	
			$data = array(
				"bsc" => array(
					"identity" => $this->session->bsc['identity'],
					"email" => $this->session->bsc['email'],
					"user_id" => $this->session->bsc['user_id'],
					"old_last_login" => $this->session->bsc['old_last_login'],
					"last_check" => $this->session->bsc['last_check'],
				)
			);

			$response = array(
			  "message" => 'Successfully fetch bsc session',
			  "data" => $data,
			);
	  
			header('content-type: application/json');
			echo json_encode($response);
			return;
		}
	}
}