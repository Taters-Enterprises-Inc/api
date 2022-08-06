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

	public function redeems(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$response = array(
					'message' => 'Successfully fetch redeems',
					'data' => $this->session->redeem_data,
				);
			
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function redeem_deal(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':

				if(!isset($_SESSION['cache_data']) && !isset($_SESSION['userData'])){
					$response = array(
						"message" => 'Cannot redeem code',
					);

					header('content-type: application/json');
					echo json_encode($response);
					break;
				}
				
				//get deals to insert on transaction
				$client_details = $this->deals_model->insert_client_details();
		
				if ($client_details) {
					
					// Modify this to corressponding time
					date_default_timezone_set('Asia/Singapore');
					$post = json_decode(file_get_contents("php://input"), true);

					$hash = $post['hash'];
					$deal = $this->deals_model->getDeal($hash);	
			
					$date_redeemed = date("Y-m-d H:i:s");
					$expiration_date = date("Y-m-d H:i:s", time()+(1*30));
					$redeem_code = "DC" . substr(md5(uniqid(mt_rand(), true)), 0, 6);
					$trans_hash_key = substr(md5(uniqid(mt_rand(), true)), 0, 20);
		
					$client_id = $client_details->id;
		
					$transaction_data = array(
							'redeem_code' 					=> $redeem_code,
							'client_id' 	   				=> $client_id,
							'purchase_amount'   			=> $deal->promo_price == NULL? 0 :  $deal->promo_price,
							'remarks' 		    			=> '',
							'platform_id'					=> $deal->platform_id,
							'status' 		    			=> 1,
							'dateadded'         			=> $date_redeemed,
							'expiration'					=> $expiration_date,
							'hash_key'          			=> $trans_hash_key,
							'logon_type'        			=> "facebook",
							'store'							=> $_SESSION['cache_data']['store_id']
					);
					
					$query_transaction_result = $this->deals_model->insert_redeem_transaction($transaction_data);
		
					if ($query_transaction_result->status == true) {
						$order_data[] = array(
							'transaction_id'  => $query_transaction_result->id,
							'deal_id'         => $deal->id,
							'price'			  => $deal->original_price,
							'quantity'	      => 1,
							'status'	      => 0,
						);
					}
		
					$query_orders_result = $this->deals_model->insert_client_orders($order_data);
		
					if ($query_orders_result) {
						$redeem_session = array(
							'deal_id' => $deal->id,
							'deal_hash' => $hash,
							'date_redeemed' => $date_redeemed,
							'expiration' => $expiration_date,
							'redeem_code'=> $redeem_code,
						);
						$_SESSION['redeem_data'][]=$redeem_session;
					}

					$products= array(
						'deal_id' => $deal->id,
						'deal_image_name' => $deal->product_image,
						'deal_name' => $deal->name,
						'description' => $deal->description,
						'deal_qty' => 1, 
					);
					
			
					if($deal->minimum_purchase != null){
						$products['minimum_purchase'] = $deal->minimum_purchase;
					}else{
						$products['deal_original_price'] = $deal->original_price;
						$products['deal_promo_price'] = $deal->promo_price;
						$products['deal_remarks'] = $post['remarks'];
	
						$_SESSION['orders'] = array($products);	
					}
	
					$_SESSION['deals'] = array($products);	
	
					$response = array(
						"message" => 'Successfully Redeem Code',
					);
					
			
					header('content-type: application/json');
					echo json_encode($response);
				}
		
				break;
		}
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
		$store_id = $this->session->cache_data['store_id'];

		if($platform == 'store-visit'){
			$deals = $this->deals_model->getDeals($platform,$category, true);
		}else{
			$deals = $this->deals_model->getDeals($platform,$category, true, $store_id);
		}
		
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

	public function check_product_variant_deals(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				return show_404();
			case 'POST':
					$post = json_decode(file_get_contents("php://input"), true);
					$deal = $this->deals_model->getDeal($post['hash']);
			
					$deal_products_with_variants = $this->deals_model->getDealProductsWithVariants($deal->id);
					$deal_products = array();
			
					foreach($deal_products_with_variants as $value){
						
						$product_variants = $this->deals_model->getDealProductVariantsWithSelectedOption($value->product_id, $value->product_variant_options_id);
						$product = $this->deals_model->getProduct($value->product_id);
						$product_variant_option =  $this->deals_model->getProductVariantOption($value->product_variant_options_id);
						$product->name = $product_variant_option->name . ' ' . $product->name;
				
						foreach($product_variants as $product_variant){
							$product_variant->options = $this->deals_model->getProductVariantOptions($product_variant->id);
						}	
			
						array_push($deal_products, array(
							'option_id' => $value->product_variant_options_id,
							'quantity' => $value->quantity,
							'product_variants' => $product_variants,
							'product' => $product,
						));
					}
			
					$response = array(
						'message'=> 'Successfully add to cart deals',
						'data' => $deal_products,
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
					'popclub_data' => $this->session->popclub_data,
					'cache_data' => $this->session->cache_data,
					'customer_address' => $this->session->customer_address,
					'userData' => $this->session->userData,
					'orders' => $this->session->orders,
					'redeem_data' => $this->session->redeem_data,
					'deals' => $this->session->orders,
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
