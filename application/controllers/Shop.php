<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Shop extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('store_model');
		$this->load->model('shop_model');
		$this->load->model('user_model');
		$this->load->library('images');
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
                    $grand_total = (int)$subtotal + (int)$delivery_fee + (int)$cod_fee - (double)$voucher_amount - (double)$giftcard_amount;
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
				
				$product_size = $this->shop_model->getProductVariantsSize($product->id);
				$flavors = $this->shop_model->getProductVariantsFlavor($product->id);
				$product_images = $this->images->product_images(
					'assets/images/shared/products/500',
					basename($product->product_image, '.jpg')
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
