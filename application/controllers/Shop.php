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
		$this->load->library('images');
	}
	

	public function product(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':

				$hash = $this->input->get('hash');

				$product = $this->product_model->get_product($hash);
				
				$product_size = $this->product_model->fetch_product_variants($product->id,'size');
				$product_flavor = $this->product_model->fetch_product_variants($product->id,'flavor');
				$product_date = $this->product_model->fetch_product_variants($product->id,'date');
				// $product_images = $this->images->product_images(basename($product->product_image, '.jpg'));
				$youtube_video_ads = $this->product_model->youtube_video_ads($product->id);

				
				$check_with_addons = $this->product_model->get_product_addons($product->id);
				if ($check_with_addons != null) {
					$addons = $this->product_model->get_product_addons_join($product->id);
				}
				
				$response = array(
					'data' => array(
						'product' => $product,
						'addons' => $addons,
						'product_size' => $product_size,
						'product_flavor' => $product_flavor,
						'product_date' => $product_date,
						// 'product_images' => $product_images,
						'youtube_video_ads' => $youtube_video_ads,
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
