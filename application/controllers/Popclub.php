<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Popclub extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('deals_model');
	}

	public function platform()
	{
		$platforms = $this->deals_model->getDealsPlatform();

		$response = array(
			'data' => $platforms,
			'message' => 'Successfully fetch platforms'
		);

		header('content-type: application/json');
		echo json_encode($response);
	}

	public function category()
	{
		$platforms = $this->deals_model->getDealsPlatform();
		$active_platform_url_name =  $this->input->get('platform_url_name');

		foreach($platforms as $platform){
			if($active_platform_url_name == null){
				$categories = $this->deals_model->getDealsCategory($platform->id);
				break;
			}
			if($platform->url_name == $active_platform_url_name){
				$categories = $this->deals_model->getDealsCategory($platform->id);
			}
		}

		$response = array(
			'data' => $categories,
			'message' => 'Successfully fetch categories'
		);

		header('content-type: application/json');
		echo json_encode($response);

	}

	public function deals($platform){
		$category = $this->input->get('category');

		$deals = $this->deals_model->getDeals($platform,$category, true);
		
		$response = array(
			'data' => $deals,
			'message' => 'Successfully fetch deals'
		);
		
		header('content-type: application/json');
		echo json_encode($response);
	}

	
	public function deal($hash){
		$deal = $this->deals_model->getDeal($hash);
		
		$response = array(
			'data' => $deal,
			'message' => 'Successfully fetch deals'
		);
		
		header('content-type: application/json');
		echo json_encode($response, JSON_PRETTY_PRINT);
	}

	public function popclub_data(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$popclub_data = $this->session->popclub_data;
		
				$response = array(
					'data' => $popclub_data,
					'message' => 'Successfully fetch popclub_data'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				
				$_SESSION['popclub_data'] = [
					'platform' => $post['platform'],
				];
				
				$response = array(
					'message' => 'Successfully set popclub_data'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}

	}

	public function session(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$data = array(
					'cache_data' => $this->session->cache_data,
					'customer_address' => $this->session->customer_address,
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
}
