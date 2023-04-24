<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Shop extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('store_model');
		$this->load->model('shop_model');
		$this->load->model('user_model');
		$this->load->model('deals_model');
		$this->load->model('logs_model');
		$this->load->library('images');
	}

	public function product_view_log(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);

                $this->logs_model->insertSnackshopProductViewLogs(
                    $this->session->cache_data['store_id'] ?? null,
                    $post['product_id'],
					$post['product_variant_option_id'] ?? null,
                    $this->session->userData['fb_user_id'] ?? null,
                    $this->session->userData['mobile_user_id'] ?? null,
                );

				$response = array(
					'message' => 'Successfully viewed product'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function initial_checkout_log(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);

                $this->logs_model->insertSnackshopInitialCheckoutLogs(
                    $this->session->cache_data['store_id'] ?? null,
                    $post['subtotal'],
					$post['discount'],
					$post['deliveryFee'],
                    $this->session->userData['fb_user_id'] ?? null,
                    $this->session->userData['mobile_user_id'] ?? null,
                );

				$response = array(
					'message' => 'Successfully viewed initial checkout'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function influencer_promo(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				$fb_user_id = $this->session->userData['fb_user_id'] ?? '';
				$mobile_user_id = $this->session->userData['mobile_user_id'] ?? '';

				$referral_code = $post['referralCode'];

				$validation = $this->shop_model->getInfluencerPromo($referral_code);

				if($validation){
					$this->output->set_status_header('401');
					echo json_encode(array( "message" => 'You already redeem this promo'));
					return;
				}

				$influencer_promo = $this->shop_model->getInfluencerPromoByReferralCode($referral_code);

				if(	
					$influencer_promo && 
					$influencer_promo->fb_user_id !== $fb_user_id && 
					$influencer_promo->mobile_user_id !== $mobile_user_id
					){
					$response = array(
						'data' => $influencer_promo,
						'message' => 'Successfully applied referral'
					);
	
					header('content-type: application/json');
					echo json_encode($response);
				}else{
					
					$this->output->set_status_header('401');
					echo json_encode(array( "message" => "You can't redeem this promo"));
					return;
				}
				break;
		}
	}

	public function deals(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$store_id = $this->session->cache_data['store_id'];
				$date_now = date('Y-m-d H:i:s');

				$deals = $this->deals_model->getDealsPromoDiscountDeals($store_id, $date_now);

				$response = array(
					'data' => $deals,
					'message' => 'Successfully fetch deals'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}
	
    public function get_product_sku(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);

				if(isset($post['prod_size'])){
					$variant[] = (int)$post['prod_size'];
				}
				
				if(isset($post['prod_flavor'])){
					$variant[] = (int)$post['prod_flavor'];
				}

				if(isset($post['selected_drink'])){
					$drink_id = (int)$post['selected_drink'];
					if($drink_id == 232){
						$variant[] = 165;
					}
					if($drink_id == 233){
						$variant[] = 166;
					}
					if($drink_id == 234){
						$variant[] = 167;
					}
				}
				if(isset($post['sel_extra_flavor'])){
					$variant[] = (int)$post['sel_extra_flavor'];
				}
				if(isset($post['sel_extra_butter'])){
					$variant[] = (int)$post['sel_extra_butter'];
				}
	
				$variants = array_values(array_filter($variant));
	
				$query_result = $this->shop_model->fetch_product_sku($variants);

				header('content-type: application/json');
				echo json_encode(array(
					'message'=> 'Successfully get product sku',
					'data'=> $query_result,
				));
				break;
		}
    }

	public function orders(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$hash = $this->input->get('hash');
				$order_details = $this->shop_model->view_order($hash);
				
                if ($order_details['clients_info']->reseller_id != 0) {
                    $subtotal = $order_details['clients_info']->purchase_amount;
                    $reseller_discount = $order_details['clients_info']->reseller_discount;
                    $grand_total = $subtotal - $reseller_discount;
                    $delivery_fee = 0;
                    $voucher_amount = 0;
                } else {
                    $subtotal = $order_details['clients_info']->purchase_amount;
                    $delivery_fee = $order_details['clients_info']->distance_price;
					
                    if($order_details['clients_info']->discount == NULL){
                        $voucher_amount = 0;
                    }else{
                        $voucher_amount = $order_details['clients_info']->discount;
                    }
                    if($order_details['clients_info']->giftcard_discount == NULL){
                        $giftcard_amount = 0;
                    }else{
                        $giftcard_amount = $order_details['clients_info']->giftcard_discount;
                    }

                    $cod_fee = $order_details['clients_info']->cod_fee;
                    $grand_total = $subtotal + $delivery_fee + $cod_fee - $voucher_amount - $giftcard_amount;
                }
				
				$query_logon  = $this->shop_model->get_logon_type($hash);
				$logon_type   = $query_logon->logon_type;

				switch($logon_type){
					case 'facebook':
						$facebook_details = $this->shop_model->get_facebook_details($order_details['clients_info']->fb_user_id);
						$firstname = $facebook_details->first_name;
						$lastname = $facebook_details->last_name;
						break;
					case 'mobile':
						$mobile_details = $this->shop_model->get_mobile_details($order_details['clients_info']->mobile_user_id);
						$firstname = $mobile_details->first_name;
						$lastname = $mobile_details->last_name;
						break;
				}

				$store_id = $this->store_model->get_store_id_by_hash_key($hash);
				$delivery_hours = $this->store_model->get_delivery_hours($store_id);

				if(isset($order_details['deals_details']) && !empty($order_details['deals_details'])){
					$deal = $order_details['deals_details'][0];

					$deal_products_promo_exclude = $this->deals_model->getDealProductsPromoExclude($deal->id);
					$deal_products_promo_include = $this->deals_model->getDealProductsPromoInclude($deal->id);

					$promo_discount_percentage = null;

					foreach($order_details['order_details'] as $key => $product){
						if($deal_products_promo_exclude){
							foreach($deal_products_promo_exclude as $value){
								if($value->product_id === $product->product_id){
									$order_details['order_details'][$key]->promo_discount_percentage = null;
								}else{
									$order_details['order_details'][$key]->promo_discount_percentage = (float)$deal->promo_discount_percentage;
								}
							}
						}else if($deal_products_promo_include){
							foreach($deal_products_promo_include as $value){
								if($value->product_id === $product->product_id){
									$order_details['order_details'][$key]->promo_discount_percentage = (float)$value->promo_discount_percentage;
								}else{
									$order_details['order_details'][$key]->promo_discount_percentage = null;
								}
							}
							$order_details['deals_details'][0]->deal_products_promo_include = $deal_products_promo_include;
						}
					}
				}


				
				$response = array(
					'data' => array(
						'order' => $order_details,
						'grand_total' => $grand_total,
						'subtotal' => $subtotal,
						'delivery_fee' => $delivery_fee,
						'cod_fee' => $cod_fee,
						'firstname' => $firstname,
						'lastname' => $lastname,
						'delivery_hours' => $delivery_hours,
					),
					'message' => 'Successfully fetch product'
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

				$product = $this->shop_model->get_product($hash);
				
                $redeem_data = $this->session->redeem_data;
                $deal_products_promo_exclude = $redeem_data['deal_products_promo_exclude'];
                $deal_products_promo_include = $redeem_data['deal_products_promo_include'];
				$promo_discount_percentage = null;

                if($deal_products_promo_exclude){
                    $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];

                    foreach($deal_products_promo_exclude as $value){
                        if($value->product_id === $product->id){
                            $promo_discount_percentage = null;
                            break;
                        }
                    }

					$product->promo_discount_percentage = $promo_discount_percentage;
                }
				if($deal_products_promo_include){

                    foreach($deal_products_promo_include as $value){
                        if($value->product_id === $product->id && empty($value->obtainable)){
							$promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];
                            break;
                        }
                    }

					$product->promo_discount_percentage = $promo_discount_percentage;
                }
				
				$product_size = $this->shop_model->getProductVariantsSize($product->id);
				$flavors = $this->shop_model->getProductVariantsFlavor($product->id);
				
				$product_image_extension = '.' . pathinfo($product->product_image)['extension'];
				$product_images = $this->images->product_images(
					'assets/images/shared/products/500',
					basename($product->product_image,$product_image_extension),
					$product_image_extension
				);

				$product_flavor = array();
				foreach($flavors as $key => $flavor){
					$product_flavor[$flavor->product_variant_id]['parent_name'] = $flavor->parent_name;
					$product_flavor[$flavor->product_variant_id]['flavors'][] =  $flavor;
				}

				$youtube_video_ads = $this->shop_model->youtube_video_ads($product->id);
				$suggested_products = $this->shop_model->get_suggested_product($product->id);

				
				$check_with_addons = $this->shop_model->get_product_addons($product->id);
				$addons = null;
				if ($check_with_addons != null) {
					$addons = $this->shop_model->get_product_addons_join($product->id);
				}
				
				$response = array(
					'data' => array(
						'product' => $product,
						'addons' => $addons,
						'product_size' => $product_size,
						'product_flavor' => array_values($product_flavor),
						'product_images' => $product_images,
						'youtube_video_ads' => $youtube_video_ads,
						'suggested_products' => $suggested_products,
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
					echo json_encode($response);
					break;
				}


				$products = $this->shop_model->fetch_category_products($region_id,'','','','','');

				
				$response = array(
					'data' => $products,
					'message' => "Successfully fetch products"
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}
}
