<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Catering extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('store_model');
		$this->load->model('catering_model');
		$this->load->library('images');
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
					echo json_encode($response);
					break;
				}
                
				$products = $this->catering_model->fetch_category_products($region_id,'','','','','');
                
				$response = array(
					'data' => $products,
					'message' => "Successfully fetch products"
				);

				header('content-type: application/json');
				echo json_encode($response);
                
                break;
        }
    }
	
	public function product(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$hash = $this->input->get('hash');

				$product = $this->catering_model->get_product($hash);

				if(!isset($product)){
					$this->output->set_status_header(401);
					echo json_encode(array('message'=>'Product not found...'));
					return;
				}
				
				$region_id = $_SESSION['cache_data']['region_id'];
				
				$product_flavor = array();
				$flavors = $this->catering_model->get_product_variants($product->id);
				foreach($flavors as $key => $flavor){
					$product_flavor[$flavor->product_variant_id]['parent_name'] = $flavor->parent_name;
					$product_flavor[$flavor->product_variant_id]['flavors'][] =  $flavor;
				}

				$product_images = $this->images->product_images(
					'assets/images/shared/products/500',
					basename($product->product_image, '.jpg')
				);

				$addons = $this->catering_model->get_catering_addons($region_id);
				$product_addons = $this->catering_model->get_catering_product_addons($region_id);
				$product_prices = $this->catering_model->get_product_prices($product->id);
				$product->base_price = $product->price;


				$response = array(
					'data' => array(
						'product' => $product,
						'product_flavor' => array_values($product_flavor),
						'addons' => $addons,
						'product_addons' => $product_addons,
						'product_prices' => $product_prices,
						'product_images' => $product_images,
					),
					'message' => 'Successfully fetch product'
				);

				header('content-type: application/json');
				echo json_encode($response, JSON_PRETTY_PRINT);
				return;
		}
	}

}