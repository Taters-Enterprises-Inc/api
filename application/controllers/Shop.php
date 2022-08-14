<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Shop extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('store_model');
		$this->load->model('product_model');
	}

	public function product(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':

				$hash = $this->input->get('hash');

				$product = $this->product_model->get_product($hash);

				
				$response = array(
					'data' => array(
						'product' => $product
					),
					'message' => 'Successfully fetch product'
				);

				header('content-type: application/json');
				echo json_encode($response, JSON_PRETTY_PRINT);
				return;
		}
	}

	public function products(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$region_id = $this->input->get('region_id');

				if($region_id == null){
					$response = array(
						'message' => 'Unable to process your request...'
					);
					header('content-type: application/json');
					echo json_encode($response, JSON_PRETTY_PRINT);
					break;
				}


				$products = $this->product_model->fetch_category_products($region_id,'','','','','');

				
				$response = array(
					'data' => $products,
					'message' => 'Successfully fetch products'
				);

				header('content-type: application/json');
				echo json_encode($response, JSON_PRETTY_PRINT);
				return;
		}
	}
}
