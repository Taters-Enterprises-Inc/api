<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Singapore');

class Popclub extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('google');

		$this->load->model('deals_model');
		$this->load->model('transaction_model');
		$this->load->model('client_model');
		$this->load->model('notification_model');
		$this->load->model('user_model');
		$this->load->model('store_model');
	}

	private function unable_redeems(){
		$redeems = $this->deals_model->getUserRedeems();
		$today = date("Y-m-d H:i:s");
		
		$unable_redeems = array();
		$forfeit_array_counter_array = array();
		
		foreach($redeems as $redeem){
			$expire = date($redeem->expiration);
			$date_redeemed = date($redeem->date_redeemed);

			$is_the_same_day = date("Y-m-d H:i:s", strtotime('-1 day')) < $date_redeemed && 
			date("Y-m-d H:i:s", strtotime('+1 day')) > $date_redeemed;

			if($is_the_same_day && ($redeem->status === 5 || ( $redeem->status == 1 && $today >= $expire) ) ){
				if(!isset($forfeit_array_counter_array[$redeem->deal_id])){
					$forfeit_array_counter_array[$redeem->deal_id] = 1;
				}else{
					$forfeit_array_counter_array[$redeem->deal_id]++;
				}
			}

			if($is_the_same_day &&
				($redeem->status == 6 || (isset($forfeit_array_counter_array[$redeem->deal_id]) && $forfeit_array_counter_array[$redeem->deal_id] >= 3)) 
			){
				$unable_redeems[] = array(
					"deal_id" => $redeem->deal_id,
					"next_available_redeem" => date('Y-m-d H:i:s', strtotime("+1 day", strtotime($date_redeemed)))
				);
			} 
		}

		return $unable_redeems;
	}

	public function redeem_validators(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':

				$unable_redeems = $this->unable_redeems();

				if(!empty($unable_redeems)){
					
					$response = array(
						"message" => "You can't redeem this code ",
						"data" => $unable_redeems,
					);
					header('content-type: application/json');
					echo json_encode($response);
					return;

				}
					

				$response = array(
					'message' => 'Successfully fetch redeems',
				);
			
				header('content-type: application/json');
				echo json_encode($response);
				return;

		}
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

	public function redeem(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$deal_id = $this->input->get('deal_id');
				$redeems = $this->deals_model->getRedeem($deal_id);
				$latest_not_expired_redeem = null;
				$today = date("Y-m-d H:i:s");

				foreach($redeems as $redeem){
					$expire = date($redeem->expiration);

					if($today < $expire && $redeem->status == 1){	
						$latest_not_expired_redeem = $redeem;

						$products= array(	
							'id' => $redeem->id,
							'deal_id' => $redeem->deal_id,
							'deal_image_name' => $redeem->product_image,
							'deal_name' => $redeem->name,
							'description' => $redeem->description,
							'deal_qty' => 1, 
							'redeem_code'=> $redeem->redeem_code,
							'deal_remarks'=> $redeem->remarks,
							'promo_discount_percentage' => $redeem->promo_discount_percentage,
							'minimum_purchase' => $redeem->minimum_purchase,
							'deal_original_price' => $redeem->original_price,
							'deal_promo_price' => $redeem->promo_price,
							'deal_products_promo_exclude' => $this->deals_model->getDealProductsPromoExclude($redeem->deal_id),
						);

						$_SESSION['popclub_data'] = [
							'platform' => $redeem->platform_url_name,
						];
						
						set_store_sessions(
							$redeem->store,
							$redeem->address,
							'SNACKSHOP',
							$redeem->platform_id,
						);

						
						$this->session->set_userdata('redeem_data', $products);

						if(isset($_SESSION['deals'])){
							$exist = true;
							foreach($_SESSION['deals'] as $key => $value){
								if($value['id'] == $redeem->id){
									$exist = false;
									break;
								}
							}

							if($exist) {
								$products= array(	
									'id' => $redeem->id,
									'deal_id' => $redeem->deal_id,
									'deal_image_name' => $redeem->product_image,
									'deal_name' => $redeem->name,
									'description' => $redeem->description,
									'deal_qty' => 1, 
									'redeem_code'=> $redeem->redeem_code,
									'deal_remarks'=> $redeem->remarks,
									'promo_discount_percentage' => $redeem->promo_discount_percentage,
									'minimum_purchase' => $redeem->minimum_purchase,
									'deal_original_price' => $redeem->original_price,
									'deal_promo_price' => $redeem->promo_price,
									'deal_products_promo_exclude' => $this->deals_model->getDealProductsPromoExclude($redeem->deal_id),
								);

								if(
									$redeem->platform_id === 2 && 
									$redeem->minimum_purchase === null &&
									$redeem->promo_discount_percentage === null
									){
									$_SESSION['deals'][] = $products;
								}
								
							}
						}else {
							
							$products = array(	
								'id' => $redeem->id,
								'deal_id' => $redeem->deal_id,
								'deal_image_name' => $redeem->product_image,
								'deal_name' => $redeem->name,
								'description' => $redeem->description,
								'deal_qty' => 1, 
								'redeem_code'=> $redeem->redeem_code,
								'deal_remarks' => $redeem->remarks,
								'promo_discount_percentage' => $redeem->promo_discount_percentage,
								'minimum_purchase' => $redeem->minimum_purchase,
								'deal_original_price' => $redeem->original_price,
								'deal_promo_price' => $redeem->promo_price,
								'deal_products_promo_exclude' => $this->deals_model->getDealProductsPromoExclude($redeem->deal_id),
							);

							if(
								$redeem->platform_id === 2 && 
								$redeem->minimum_purchase === null && 
								$redeem->promo_discount_percentage === null
							){
								$this->session->set_userdata('deals',array($products));
							}
							
						}
					}else{

						if(isset($_SESSION['orders']) && isset($_SESSION['redeem_data']['promo_discount_percentage'])){
							foreach($_SESSION['orders'] as $key => $value){
								$_SESSION['orders'][$key]['promo_discount_percentage'] = null;
							}
						}

						if(isset($_SESSION['redeem_data'])){
							if($_SESSION['redeem_data']['id'] === $redeem->id){
								unset($_SESSION['redeem_data']);
							}
						}
						if(isset($_SESSION['deals'])){
							foreach($_SESSION['deals'] as $key => $value){
								if($value['id'] === $redeem->id){
									unset($_SESSION['deals'][$key]);
									$reindexed_array = array_values($_SESSION['deals']);
									$this->session->set_userdata('deals', $reindexed_array);
								}
							}
						}
					}
				}

				$response = array(
					'message' => 'Successfully fetch redeem',
					'data' => $latest_not_expired_redeem,
				);
			
				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'DELETE':
					if(!isset($_SESSION['redeem_data'])){
						$this->output->set_status_header(401);
						echo json_encode(array('message'=>'No redeem found'));
						return;
					}
				
					$reedem_id = $_SESSION['redeem_data']['id'];
					
					if(isset($_SESSION['deals'])){
						foreach($_SESSION['deals'] as $key => $value){
							if($value['id'] === $reedem_id){
								unset($_SESSION['deals'][$key]);
								$reindexed_array = array_values($_SESSION['deals']);
								$this->session->set_userdata('deals', $reindexed_array);
							}
						}
					}
					
					if(isset($_SESSION['orders']) && isset($_SESSION['redeem_data']['promo_discount_percentage'])){
						foreach($_SESSION['orders'] as $key => $value){
							$_SESSION['orders'][$key]['promo_discount_percentage'] = null;
						}
					}
					
					
					$this->deals_model->forfeit_redeem_deal($reedem_id);
					unset($_SESSION['redeem_data']);
					
					
					$response = array(
						'message' => 'Redeem deal forfeited',
					);
				
					header('content-type: application/json');
					echo json_encode($response);
					return;
					
		}
	}

	public function redeem_deal(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':

				if(
					(!isset($_SESSION['cache_data']) && 
					!isset($_SESSION['userData']) ) 
					){
					$response = array(
						"message" => 'Cannot redeem the code',
					);

					header('content-type: application/json');
					echo json_encode($response);
					break;
				}

				$post = json_decode(file_get_contents("php://input"), true);
				$hash = $post['hash'];
				
				$deal = $this->deals_model->getDeal($hash);	

				$client_details = $this->client_model->insertClientDetailsPopClub();
		
				if ($client_details['status'] == true) {
			
					$date_redeemed = date("Y-m-d H:i:s");
					$expiration_date = date("Y-m-d H:i:s", time()+($deal->seconds_before_expiration));
					$redeem_code = "DC" . substr(md5(uniqid(mt_rand(), true)), 0, 6);
					$trans_hash_key = substr(md5(uniqid(mt_rand(), true)), 0, 20);
					$store_id = $this->session->cache_data['store_id'];
		
					$client_id = $client_details['id'];
		
					$redeems_transaction_data = array(
							'redeem_code' 					=> $redeem_code,
							'deal_id'						=> $deal->id,
							'client_id' 	   				=> $client_id,
							'purchase_amount'   			=> $deal->promo_price === NULL? 0 :  $deal->promo_price,
							'remarks' 		    			=> $post['remarks'] === NULL? '' : $post['remarks'],
							'platform_id'					=> $deal->platform_id,
							'status' 		    			=> 1,
							'dateadded'         			=> $date_redeemed,
							'expiration'					=> $expiration_date,
							'hash_key'          			=> $trans_hash_key,
							'logon_type'        			=> "facebook",
							'store'							=> $_SESSION['cache_data']['store_id'],
					);
					
					$query_transaction_result = $this->transaction_model->insertPopClubTransactionDetails($redeems_transaction_data);
		
					if ($query_transaction_result->status == true) {      
						if($deal->platform_id === 1){
							$order_data[] = array(
								'redeems_id'  => $query_transaction_result->id,
								'deal_id'         => $deal->id,
								'price'			  => $deal->promo_price,
								'quantity'	      => 1,
								'status'	      => 0,
								'remarks'		  => $post['remarks'] === NULL? '' : $post['remarks'],
							);
	
							$this->transaction_model->insertPopClubClientOrders($order_data);
						}                                 

						$products= array(
							'id' => $query_transaction_result->id,
							'deal_id' => $deal->id,
							'deal_image_name' => $deal->product_image,
							'deal_name' => $deal->name,
							'description' => $deal->description,
							'deal_qty' => 1, 
							'redeem_code'=> $redeem_code,
							'deal_remarks' =>$post['remarks'],
							'promo_discount_percentage' => $deal->promo_discount_percentage,
							'minimum_purchase' => $deal->minimum_purchase,
							'deal_original_price' => $deal->original_price,
							'deal_promo_price' => $deal->promo_price,
							'deal_products_promo_exclude' => $this->deals_model->getDealProductsPromoExclude($deal->id),
						);
	
						if(
							$deal->platform_id === 2 && 
							$deal->minimum_purchase === null && 
							$deal->promo_discount_percentage === null
							){
							$_SESSION['deals'] = array($products);	
						}

						if($deal->platform_id === 1){

							$customer_name = $this->session->userData['first_name'] . " " . $this->session->userData['last_name'];
							$message =  $customer_name ." reedeem on popclub!";
							
							$notification_event_data = array(
								"notification_event_type_id" => 3,
								"deals_redeems_tb_id" => $query_transaction_result->id,
								"text" => $message
							);

							$notification_event_id = $this->notification_model->insertAndGetNotificationEvent($notification_event_data);
							
							//store group
							$users = $this->store_model->getUsersStoreGroupsByStoreId($store_id);
							foreach($users as $user){
								$notifications_data = array(
									"user_to_notify" => $user->user_id,
									"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
									"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
									'notification_event_id' => $notification_event_id,
									"dateadded" => date('Y-m-d H:i:s'),
								);

								$this->notification_model->insertNotification($notifications_data);   
							}
							
							//admin
							$admin_users = $this->user_model->getUsersByGroupId(1);
							foreach($admin_users as $user){
								$notifications_data = array(
									"user_to_notify" => $user->user_id,
									"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
									"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
									'notification_event_id' => $notification_event_id,
									"dateadded" => date('Y-m-d H:i:s'),
								);
								$this->notification_model->insertNotification($notifications_data);   
							}
							
							//csr admin
							$csr_admin_users = $this->user_model->getUsersByGroupId(10);
							foreach($csr_admin_users as $user){
								$notifications_data = array(
									"user_to_notify" => $user->user_id,
									"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
									"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
									'notification_event_id' => $notification_event_id,
									"dateadded" => date('Y-m-d H:i:s'),
								);
								$this->notification_model->insertNotification($notifications_data);   
							}

							//mobile or fb             
							$notifications_data = array(
								"fb_user_to_notify" => $this->session->userData['fb_user_id'] ?? null,
								"mobile_user_to_notify" => $this->session->userData['mobile_user_id'] ?? null,
								"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
								"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
								'notification_event_id' => $notification_event_id,
								"dateadded" => date('Y-m-d H:i:s'),
							);
							$this->notification_model->insertNotification($notifications_data);   

							$real_time_notification = array(
								"store_id" => $store_id,
								"message" => $message,
							);
	
							notify('admin-popclub','popclub-store-visit-transaction', $real_time_notification);
						}
	
						$_SESSION['redeem_data'] = $products;
		
						$response = array(
							"message" => 'Successfully Redeem Code',
							"data" => array(
								"redeem_data" => array($products),
								"deals" => $products,
								"orders" => $products,
							)
						);
						
				
						header('content-type: application/json');
						echo json_encode($response);
						return;

					}else{
                        $this->output->set_status_header(401);
                        echo json_encode(array('message'=>'Failed to insert transaction'));
                        return;
					}
				}else{
					$this->output->set_status_header(401);
					echo json_encode(array('message'=>'Client details cannot be inserted'));
					return;
				}
		
		}
	}

	public function platform(){
		$platforms = $this->deals_model->getDealsPlatform();

		$response = array(
			'data' => $platforms,
			'message' => 'Successfully fetch platforms'
		);

		header('content-type: application/json');
		echo json_encode($response);
	}

	public function category(){
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
			$deals = $this->deals_model->getDeals($platform,$category, true, $store_id);
		}else{
			$deals = $this->deals_model->getDeals($platform,$category, true, $store_id);
		}

		$current_datetime = date("Y-m-d H:i:s");
		$not_available_deals = array();
		$available_deals = array();
		$day_of_the_week = date('l');

		$unable_redeems = $this->unable_redeems();

		foreach($deals as $deal){
			if(!empty($unable_redeems)){
				$is_contain = false;
				foreach($unable_redeems as $unable_redeem){
					if($unable_redeem['deal_id'] === $deal->id){
						$is_contain = true;
						break;
					}
				}
				if($is_contain){
					$not_available_deals[] = $deal;
					continue;
				}
			}

			if(isset($deal->available_start_time) && isset($deal->available_end_time)){
				$available_start_time = date("Y-m-d H:i:s", strtotime($deal->available_start_time));
				$available_end_time = date("Y-m-d H:i:s", strtotime($deal->available_end_time));

				if($current_datetime > $available_end_time || $available_start_time > $current_datetime){
					$not_available_deals[] = $deal;
				}else{
					$available_deals[] = $deal;
				}
			}else if(isset($deal->available_days)){
				if(str_contains($deal->available_days, $day_of_the_week)){
					$available_deals[] = $deal;
				}else{
					$not_available_deals[] = $deal;
				}
			}else{
				$available_deals[] = $deal;
			}
		}
		
		$response = array(
			'data' => array_merge($available_deals,$not_available_deals),
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
		echo json_encode($response);
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
						
						$product = $this->deals_model->getProduct($value->product_id);
						
						
						if($value->product_variant_options_id){
							$product_variant_option =  $this->deals_model->getProductVariantOption($value->product_variant_options_id);
							$product->name = $product_variant_option->name . ' ' . $product->name;
						}

						$product_variants = $this->deals_model->getDealProductVariantsWithSelectedOption($value->product_id, $value->product_variant_options_id);

						foreach($product_variants as $product_variant){
							$product_variant->options = $this->deals_model->getProductVariantOptions($product_variant->id);
						}	
						

						array_push($deal_products, array(
							'option_id' => $value->product_variant_options_id,
							'quantity' => $value->quantity,
							'product_variants' =>$product_variants,
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
}
