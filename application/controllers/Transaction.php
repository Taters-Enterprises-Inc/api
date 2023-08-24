<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Transaction extends CI_Controller {
    
	public function __construct(){
		parent::__construct();
        
		$this->load->model('transaction_model');
		$this->load->model('shop_model');
        $this->load->model('catering_model');
		$this->load->model('deals_model');
		$this->load->model('client_model');
        $this->load->model('notification_model');
        $this->load->model('store_model');
        $this->load->model('user_model');
        $this->load->model('discount_model');
        $this->load->model('influencer_model');
	}
    
    public function catering(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
                
                $hash_key = substr(md5(uniqid(mt_rand(), true)), 0, 20);
                $tracking_no = substr(md5(uniqid(mt_rand(), true)), 0, 6);
                $store_id = $this->session->cache_data['store_id'];

                
                $insert_client_details = $this->client_model->insertClientDetailsCatering(
                    $post['firstName'],
                    $post['lastName'],
                    $post['eventAddress'],
                    $post['phoneNumber'],
                    $post['payops'],
                    $post['eMail']
                );

                if($insert_client_details['status'] == true){

                    if(!empty($this->session->orders)){
                        $comp_total = 0;
                        foreach ($this->session->orders as $row => $val) {
                            $comp_total += $val['prod_calc_amount'];
                        }
                    }

                    
                    $remarks = empty($post['checkout_remarks']) ? '' : $post['checkout_remarks'];
                    $distance_rate_id = (empty($this->session->distance_rate_id)) ? 0 : $this->session->distance_rate_id;
                    $distance_rate_price = (empty($this->session->distance_rate_price)) ? 0 : $this->session->distance_rate_price;
                    $payops = $post['payops'];
                    
                    $cod_fee = "0";
                    if($payops == '3'){
                        $cod_fee = $this->session->cash_delivery;
                    }

					$str_serving_time = strtotime($post['servingTime']);
					$serving_time = date($str_serving_time);
                    
                    $catering_start_date = $_SESSION['catering_start_date'];
                    $catering_end_date = $_SESSION['catering_end_date'];

                    $discount = $this->discount_model->getAvailableUserDiscount(
                        $this->session->userData['fb_user_id'] ?? null,
                        $this->session->userData['mobile_user_id'] ?? null
                    );

                    $discount_value = "";
                    $discount_user_id = null;
                    
                    if(isset($discount)){
                        $discount_value = (int)$comp_total * (float)$discount->percentage;
                        $discount_user_id = $discount->id;
                    }
                    
                    $client_id = $insert_client_details['id'];
                    $transaction_data = array(
                        'tracking_no' 		=> $tracking_no,
                        'hash_key'          => $hash_key,
                        'client_id' 	   	=> $client_id,
                        'purchase_amount'   => $comp_total,
                        'remarks' 		    => $remarks,
                        'status' 		    => 1,
                        'contract'          => 0,
                        'store'             => $this->session->cache_data['store_id'],
                        'dateadded'         => date('Y-m-d H:i:s'),
						'company_name'		=> isset($post['companyName']) ? $post['companyName'] : '',
						'message'			=> isset($post['otherDetails']) ? $post['otherDetails'] : '',
						'serving_time'		=> $serving_time,
                        'start_datetime'    => $catering_start_date,
                        'end_datetime'      => $catering_end_date,
                        'event_class'       => isset($post['eventClass']) ? $post['eventClass'] : '',
                        'service_fee'       => $comp_total * 0.1,
                        'night_diff_fee'    => (int)$this->get_night_diff($catering_start_date, $catering_end_date),
                        'additional_hour_charge' => (int)$this->get_succeeding_hour_charge($catering_start_date, $catering_end_date),
                        'distance'          => '2',
                        'distance_id'       => $distance_rate_id,
                        'distance_price'    => $distance_rate_price,
                        'cod_fee'           => $cod_fee,
                        'payops'            => $payops,
                        'payment_plan'      => $post['paymentPlan'],
                        'custom_message'    => '',
                        'logon_type'        => $insert_client_details['logon_type'],
                        'discount'          => $discount_value,
                        'discount_user_id'       => $discount_user_id,
                    );

                        
                    $query_transaction_result = $this->transaction_model->insertCateringTransactionDetails($transaction_data);
                    
                    if($query_transaction_result['status'] == true){
                        $trans_id = $query_transaction_result['id'];
                        
                        if(!empty($this->session->orders)){
                            $comp_total = 0;

                            foreach ($this->session->orders as $k => $value) {

                                $order_data[] = array(
                                    'transaction_id'      => $trans_id,
                                    'combination_id'      => $k,
                                    'product_id'          => $value['prod_id'],
                                    'quantity'            => $value['prod_qty'],
                                    'remarks'             => $value['prod_multiflavors'],
                                    'type'                => $value['prod_type'],
                                    'status'              => 1,
                                    'promo_id'            => "",
                                    'promo_price'         => "",
                                    'sku'                 => $value['prod_sku'],
                                    'sku_id'              => $value['prod_sku_id'],
                                    'price'               => $value['prod_calc_amount'],
                                    'product_price'       => $value['prod_price'],
                                    'product_label'       => $value['prod_size'],
                                    'product_discount'    => $value['prod_discount'],
                                    'addon_base_product'  => (isset($value['addon_base_product'])) ? $value['addon_base_product'] : '',
                                );
                            }
                            $this->transaction_model->insertCateringClientOrders($order_data);
                        }

                        $message = $post['firstName'] . " " . $post['lastName'] ." book on catering!";

                        
                        $notification_event_data = array(
                            "notification_event_type_id" => 2,
                            "catering_transaction_tb_id" => $trans_id,
                            "text" => $message
                        );
                        
                        //Store User
                        $notification_event_id = $this->notification_model->insertAndGetNotificationEvent($notification_event_data);
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

                        //Admin user
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
                        
                        //CSR Admin
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

                        //Mobile and FB user Client        
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

                        notify('admin-catering','booking-transaction', $real_time_notification);
                        
                
                        $this->session->unset_userdata('orders');
                        
                        $response = array(
                            "data" => array(
                                "hash" => $hash_key,
                            ),
                            "message" => "Successfully checkout order"
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

                break;
        }
    }

    public function shop(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);

                $hash_key = substr(md5(uniqid(mt_rand(), true)), 0, 20);
                $tracking_no = substr(md5(uniqid(mt_rand(), true)), 0, 6);
                $store_id = $this->session->cache_data['store_id'];
                
                $insert_client_details = $this->client_model->insertClientDetailsShop(
                    $post['firstName'],
                    $post['lastName'],
                    $post['landmarkAddress'],
                    $post['phoneNumber'],
                    $post['payops'],
                    $post['completeDeliveryAddress'],
                    $post['eMail']
                );

                if($insert_client_details['status'] == true){
                    $comp_total = 0;

                    if(isset($_SESSION['orders'])){
                        if(!empty($_SESSION['orders'])){
                            foreach ($_SESSION['orders'] as $row => $val) {
                                
                                if(
                                    isset($_SESSION['redeem_data']['deal_products_promo_include']) &&
                                    !empty($_SESSION['redeem_data']['deal_products_promo_include'])
                                ){
                                    $added_obtainables = array();
                                    $obtainable_discount_price = 0;
                                    $obtainable_price= 0;
                                    $deal_products_promo_include_match = null;
                                    foreach($_SESSION['redeem_data']['deal_products_promo_include'] as $deal_products_promo_include){
                                        if($val['prod_id'] === $deal_products_promo_include->product_id){
                                            foreach($deal_products_promo_include->obtainable as $obtainable){
                                                if($obtainable->product_id === $val['prod_id']){
                                                    $is_exist = false;
                                                    foreach($added_obtainables as $added_obtainable){
                                                        if($added_obtainable->product_id === $obtainable->product_id ){
                                                            $is_exist = true;
                                                            break;
                                                        }
                                                    }
                                                    if($obtainable->price && $obtainable->promo_discount_percentage && $is_exist === false){
                                                        $obtainable_discount_price = $obtainable->price - $obtainable->price * $obtainable->promo_discount_percentage;
                                                        $obtainable_price += $obtainable->price;
                                                        $added_obtainables[] = $obtainable;
                                                    }
                                                }
                                            }
                                            $deal_products_promo_include_match =  $deal_products_promo_include;
                                        }
                                    }

                                    if($deal_products_promo_include_match){
                                        $comp_total += $obtainable_discount_price + $val['prod_calc_amount'] - $obtainable_price - ($val['prod_calc_amount']  * $deal_products_promo_include_match->promo_discount_percentage);
                                    }
                                }else{
                                    $promo_discount_percentage = $val['promo_discount_percentage'];

                                    if(isset($promo_discount_percentage )){
                                        $promo_discount = isset($promo_discount_percentage) ? $val['prod_calc_amount'] * $promo_discount_percentage : 0 ;
                                        $comp_total += $val['prod_calc_amount'] - $promo_discount;
                                    }else{
                                        $comp_total += $val['prod_calc_amount'];
                                    }
                                }
                            }
                        }
                    }


                    $distance_rate_id = (empty($this->session->distance_rate_id)) ? 0 : $this->session->distance_rate_id;
                    $distance_rate_price = (empty($this->session->distance_rate_price)) ? 0 : $this->session->distance_rate_price;
                    $discount_value = "";

                    $influencer_discount_value = null;

					if(isset($_SESSION['redeem_data'])){
                        if(
                            isset($_SESSION['redeem_data']['minimum_purchase']) && 
                            $_SESSION['redeem_data']['minimum_purchase'] <= $comp_total && 
                            $_SESSION['redeem_data']['is_free_delivery'] === 1
                        ){
                            $distance_rate_price = 0;
                        }

                        if(
                            isset($_SESSION['redeem_data']['minimum_purchase']) && 
                            isset($_SESSION['redeem_data']['promo_discount_percentage']) && 
                            $_SESSION['redeem_data']['minimum_purchase'] <= $comp_total && 
                            $_SESSION['redeem_data']['is_free_delivery'] === 0
                        ){
                            $discount_value = $comp_total *  (float) $_SESSION['redeem_data']['promo_discount_percentage'];
                        }
                        
                        if(
                            isset($_SESSION['redeem_data']['subtotal_promo_discount'])
                        ){
                            $discount_value = $comp_total *  (float) $_SESSION['redeem_data']['subtotal_promo_discount'];
                        }
                        
                        if(  isset($_SESSION['redeem_data']['deal_promo_price'])){
                            $comp_total += $_SESSION['redeem_data']['deal_promo_price'];
                        }
					}

                    $payops = $post['payops'];

                    $referral_code = $post['referralCode'];

                    $influencer_promo = $this->shop_model->getInfluencerPromoByReferralCode($referral_code);

                    if($influencer_promo){
                        $discount_value = $comp_total *  (float) $influencer_promo->customer_discount;
                        $influencer_discount_value =  $comp_total *  (float) $influencer_promo->influencer_discount;
                    }
                    
                    $cod_fee = "0";
                    if( $payops == '3'){
                        $cod_fee = $this->session->cash_delivery;
                    }
                    

                    $discount = $this->discount_model->getAvailableUserDiscount(
                        $this->session->userData['fb_user_id'] ?? null,
                        $this->session->userData['mobile_user_id'] ?? null
                    );

                    $payops_ref_no = '';
                    $discount_type = '';
                    $discount_ref_no = '';
                    $discount_user_id = null;
                    if(isset($discount)){
                        $discount_value = (int)$comp_total * (float)$discount->percentage;
                        $discount_user_id = $discount->id;
                    }
                    
                    $client_id = $insert_client_details['id'];
                

                    $transaction_data = array(
                        'tracking_no' 		=> $tracking_no,
                        'hash_key'          => $hash_key,
                        'client_id' 	   	=> $client_id,
                        'purchase_amount'   => $comp_total,
                        'remarks' 		    => '',
                        'status' 		    => 1,
                        'store'             => $store_id,
                        'dateadded'         => date('Y-m-d H:i:s'),
                        'distance'          => $this->session->distance,
                        'distance_id'       => $distance_rate_id,
                        'distance_price'    => $distance_rate_price,
                        'cod_fee'           => $cod_fee,
                        'reseller_id'       => 0,
                        'reseller_discount' => "",
                        'payops'            => $payops,
                        'discount'          => $discount_value,
                        'discount_user_id'  => $discount_user_id,
                        'influencer_discount' => $influencer_discount_value ?? null,
                        'influencer_promo_id' => $influencer_promo->id ?? null,
                        'giftcard_discount' => "",
                        'giftcard_number'   => "",
                        'custom_message'    => '',
                        'logon_type'        => $insert_client_details['logon_type'],
                        'store_payops'      => 0,
                        'store_payops_ref_no'=> $payops_ref_no,
                        'store_discount_type'=> $discount_type,
                        'store_discount_ref_no'=> $discount_ref_no,
                        'deals_redeems_id' => $this->session->redeem_data['id'],
                    );

                    $query_transaction_result = $this->transaction_model->insertSnackShopTransactionDetails($transaction_data);
                    
                    if($query_transaction_result['status'] == true){
                        $trans_id = $query_transaction_result['id'];

                        $orders = isset($_SESSION['orders']) ? $_SESSION['orders']:  [];
                        
                        if(!empty($orders)){
                            $comp_total = 0;

                            foreach ($orders as $k => $value) {
								if(isset($value['prod_id'])){
									$remarks = (empty($value['prod_multiflavors'])) ? null : $value['prod_multiflavors'];

                                    $order_product =  array(
										'transaction_id'      => $query_transaction_result['id'],
										'combination_id'      => $k,
										'product_id'          => $value['prod_id'],
										'quantity'            => $value['prod_qty'],
										'remarks'             => $remarks,
                                        'type'                => $value['prod_type'],
										'status'              => 1,
										'promo_id'            => "",
										'promo_price'         => "",
										'sku'                 => $value['prod_sku'],
										'sku_id'              => $value['prod_sku_id'],
										'price'               => $value['prod_calc_amount'],
										'product_price'       => $value['prod_price'],
										'product_label'       => $value['prod_size'],
										'product_discount'    => $value['prod_discount'],
                                        'deal_id'             => null,
									);

                                    
                                    $redeem_data = $this->session->redeem_data;
                                    $deal_products_promo_exclude = $redeem_data['deal_products_promo_exclude'];
                                    $deal_products_promo_include = $redeem_data['deal_products_promo_include'];
                                    $deal_id = null;

                                    if($deal_products_promo_exclude){
                                        $deal_id = $this->session->redeem_data['deal_id'];

                                        foreach($deal_products_promo_exclude as $promo){
                                            if($promo->product_id === $value['prod_id']){
                                                $deal_id = null;
                                                break;
                                            }
                                        }

                                        $order_product['deal_id'] = $deal_id;
                                    }
                                    
                                    if($deal_products_promo_include){
                                        $deal_id = null;
                                        $deal_discount_percentage = null;
                                        
                                        foreach($deal_products_promo_include as $promo){
                                            if($promo->product_id === $value['prod_id']){
                                                $deal_id = $this->session->redeem_data['deal_id'];
                                                $deal_discount_percentage = $promo->promo_discount_percentage;
                                                break;
                                            }
                                        }


                                        $order_product['deal_discount_percentage'] = $deal_discount_percentage;
                                        $order_product['deal_id'] = $deal_id;
                                    }
	
									$order_data_product[] = $order_product;

									if($value['prod_category'] == 17) {
										if (isset($this->session->userData['mobile_user_id'])) {
											$type = 'mobile';
											$user_id = $this->session->userData['mobile_user_id'];
										} elseif(isset($this->session->userData['oauth_uid'])) {
											$type = 'facebook';
											$user_id = $this->session->userData['oauth_uid'];
										}
										$data = array(
											'trans_id'          =>$trans_id,
											'user_id'           =>$user_id,
											'user_type'         =>$type,
											'prod_id'           =>$value['prod_id'],
											'status'            =>0,
											'giftcard_number'   =>null,
										);
										$this->shop_model->insert_giftcard_user($data);
									}
								}
                            }
							
							if(!empty($order_data_product))
								$this->transaction_model->insertSnackshopClientOrders($order_data_product);
								
                        }               
        
                        if(isset($_SESSION['orders'])){
                            $this->session->unset_userdata('orders');
                        }
                        

                        if(isset($_SESSION['redeem_data'])){
                            $deal_id = $_SESSION['redeem_data']['deal_id'];

                            $order_data_deal = array(
                                'transaction_id'  => $query_transaction_result['id'],
                                'deal_id'         => $deal_id,
                                'price'			  => $_SESSION['redeem_data']['deal_promo_price'],
                                'product_price'   => $_SESSION['redeem_data']['deal_promo_price'],
                                'remarks'		  => $_SESSION['redeem_data']['deal_remarks'],
                                'quantity'	      => 1,
                                'status'	      => 0,
                            );
                        
                            $this->transaction_model->insertPopClubClientOrder($order_data_deal);

                            $today = date("Y-m-d H:i:s");
                            $this->deals_model->complete_redeem_deal($_SESSION['redeem_data']['id'], $today);
                            $this->session->unset_userdata('redeem_data');
                        }
                        
                        $message = $post['firstName'] . " " . $post['lastName'] ." ordered on snackshop!";
                        
                        $notification_event_data = array(
                            "notification_event_type_id" => 1,
                            "transaction_tb_id" => $trans_id,
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

                        $realtime_notification = array(
                            "store_id" => $store_id,
                            "message" => $message,
                        );

                        notify('admin-snackshop','order-transaction', $realtime_notification);

                        $response = array(
                            "data" => array(
                                "hash" => $hash_key,
                            ),
                            "message" => "Successfully checkout order"
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
                break;
        }
    }

    private function summary_actions($request){
        switch ($request) {
            case 'edit':
                if (isset($_SESSION['transaction_id'])) {
                    $res = 'error';
                    $id = $_SESSION['transaction_id'];
                } else {
                    $res = 'edit';
                    $id = null;
                }
                header('content-type: application/json');
                echo json_encode(array('status' => $res, 'id' => $id));
                break;

            case 'confirm':
                if (isset($_SESSION['transaction_id'])) {
                    $query = $this->transaction_model->get_order_summary($_SESSION['transaction_id']);
                    $contact_no = $query['clients_info']->contact_number;
                    $cust_fname = $query['clients_info']->fname;
                    // added by aaron
                    $client_id = $query['clients_info']->client_id;
                    //
                    $payops = $query['clients_info']->payops;
                    $cust_tracking_no = $query['clients_info']->tracking_no;
                    $order_link = $query['clients_info']->hash_key;

                    // added by aaron
                    // $this->session->set_userdata('client_id', $client_id);
                    //

                    // $email_stat = $this->send_email($query['clients_info']);
                    // $res = ($email_stat) ? 'success' : 'error';
                    $res = 'success';
                    $id = $_SESSION['transaction_id'];
                    // MC HERE 08/01/2020
                    // $default_sms_message = 'Hi! Your Taters Snack Shop System Order tracking number is ' . $cust_tracking_no . '. You may check your order from our order-view portal. See link here: ' . base_url('order/check-order/').'. You may also use our order-view portal to check the status of your order anytime. Please be advised that delivery usually takes 1 to 2 working days upon confirmation of order. Thank you and stay safe.';

                    // $default_sms_message = 'Hi! Your Taters Snack Shop System Order tracking number is ' . $cust_tracking_no . '. You may check and track your order status anytime on our order-view portal. Gentle reminder to upload your proof of payment on this link ASAP. "\n" Order view portal: ' . base_url('order/check-order/').'. "\n" Please be advised that delivery usually takes 90 minutes upon order confirmation (within operating hours). Thank you.';

                    $default_sms_message = 'Hi! Your Taters Snack Shop System Order tracking number is ' . $cust_tracking_no . '. You may check and track your order status anytime on our order-view portal. Gentle reminder to upload your proof of payment on this link ASAP.' . "\n\n" . 'Order view portal: ' . base_url('order/check-order/'). "\n\n" . 'Please be advised that delivery usually takes 90 minutes upon order confirmation (within operating hours). Thank you.';

                    if (isset($_SESSION['reseller_data'])) {
                        // if($_SESSION['sms_notes_reseller'] != ''){
                        //     $message1 = $_SESSION['sms_notes_reseller'];
                        //     $replace1 = str_replace('<firstname>',$cust_fname,$message1);
                        //     $sms_message =  str_replace('<tracking>',$cust_tracking_no,$replace1);
                        // }else{
                            $sms_message = $default_sms_message;
                        // }
                    }else{
                        // if($_SESSION['sms_notes_customer'] != ''){
                        //     $message1 = $_SESSION['sms_notes_customer'];
                        //     $replace1 = str_replace('<firstname>',$cust_fname,$message1);
                        //     $sms_message =  str_replace('<tracking>',$cust_tracking_no,$replace1);
                        // }else{
                            $sms_message = $default_sms_message;
                        // }
                    }

                    // $sms_message = $default_sms_message;



                    // if ($payops == 3) {
                    //     $sms_message = 'Hi! Your Taters Snack Shop System Order tracking number is ' . $cust_tracking_no . '. You may check your order from our order-view portal. See link here: ' . base_url('order-view/') . '. You may also use our order-view portal to check the status of your order anytime. Please be advised that delivery usually takes 1 to 2 working days upon confirmation of order. Thank you and stay safe.';
                    // } else {
                    //     $sms_message = 'Hi! Your Taters Snack Shop System Order tracking number is ' . $cust_tracking_no . '. You may upload your payment from our order-view portal. See link here: ' . base_url('order-view/') . '. You may also use our order-view portal to check the status of your order anytime. Please be advised that delivery usually takes 1 to 2 working days upon uploading of payment screenshot. Thank you and stay safe.';
                    // }

                    // $send_text = $this->send_sms($contact_no, $sms_message, $id, $client_id);
                    // $sms_status = ($send_text) ? 'success' : 'error';

                    // $sms_status = 'success';
                    // $query = $this->shop_model->update_transaction_status($_SESSION['transaction_id'], 1);

                    // MC HERE 08/01/2020
                    $sms_note = '';
                    if(isset($_SESSION['reseller_data'])){
                        // $sms_note = $_SESSION['confirm_msg_reseller'];
                        $sms_note = $default_sms_message;
                    }else{
                        // $sms_note = $_SESSION['confirm_msg_customer'];
                        $sms_note = $default_sms_message;
                    }

                    // INSERT VOUCHER
                    if (isset($_SESSION['voucher_data']) && ($_SESSION['voucher_status'] == 1)) {
                        // if (isset($_SESSION['regular_status']) && $_SESSION['regular_status'] == 1) {
                            $this->load->model('voucher_model');
                            if ($_SESSION['voucher_data'][0]['offer_type'] == 1) {
                                $voucher_data = array(
                                    'id'            => $_SESSION['voucher_data'][0]['id'],
                                    'code'          => $_SESSION['voucher_data'][0]['code'],
                                    'offer_type'    => $_SESSION['voucher_data'][0]['offer_type'],
                                    'code_type'     => $_SESSION['voucher_data'][0]['code_type'],
                                    'status'        => $_SESSION['voucher_data'][0]['status'],
                                    'start'         => $_SESSION['voucher_data'][0]['start'],
                                    'end'           => $_SESSION['voucher_data'][0]['end'],
                                    'voucher_value' => $_SESSION['voucher_data'][0]['voucher_value'] * $_SESSION['orders'][0]['prod_calc_amount'] / 100,
                                    'date_added'    => $_SESSION['voucher_data'][0]['date_added'],
                                    'logs_value'    => $_SESSION['voucher_data'][0]['logs_value']
                                );
                            } else {
                                $voucher_data = $_SESSION['voucher_data'][0];
                            }
                            $this->voucher_model->add_voucher_user($cust_tracking_no, $_SESSION['transaction_id'], $voucher_data);
                        // }
                    }

                    // CONFIRM MESSAGE
                    $confirm_msgx = 'Thank your for ordering from the Taters Snack Shop. An SMS/text notification will be sent to you now for additional instructions, including where you can upload the screenshot for your bank or GCash payment. Please watch out for it. <br/><br/> Our Taters representative will call you once your order is ready for pick-up and/or delivery. Thank you, and stay safe!';

                    // if(isset($_SESSION['reseller_data']) && isset($_SESSION['confirm_msg_reseller']) && $_SESSION['confirm_msg_reseller'] != ""){
                    //     $confirm_msgx = $_SESSION['confirm_msg_reseller'];
                    // }else{
                    //     if(isset($_SESSION['confirm_msg_customer']) && $_SESSION['confirm_msg_customer'] != ""){
                    //         $confirm_msgx = $_SESSION['confirm_msg_customer'];
                    //     }
                    // }

                    // Update final total amount purchase
                    // $this->summary_cart_update(1);

                    // Update
                    if(isset($_SESSION['reseller_data'])){
                        if($_SESSION['reseller_data']['info']->first_order_status != 1){
                            $this->reseller_model->update_first_order_status($_SESSION['user_id']);
                            // set also here first_order_date
                        }
                        unset($_SESSION['transaction_id']);
                        unset($_SESSION['moh_setup']);
                        unset($_SESSION['km_radius']);
                        unset($_SESSION['km_notes']);
                        unset($_SESSION['km_min']);
                        unset($_SESSION['free_delivery']);
                        unset($_SESSION['free_min_delivery']);
                        unset($_SESSION['sub_notes_status']);
                        unset($_SESSION['sub_notes']);
                        unset($_SESSION['welcome_msg']);
                        unset($_SESSION['del_add_note']);
                        unset($_SESSION['customer_hotline']);
                        unset($_SESSION['area_banner']);
                        unset($_SESSION['regular_banner']);
                        unset($_SESSION['voucher_status']);
                        unset($_SESSION['error_voucher']);
                        unset($_SESSION['payops_list']);
                        unset($_SESSION['confirm_msg']);
                        unset($_SESSION['sms_notes_customer']);
                        unset($_SESSION['sms_notes_reseller']);
                        unset($_SESSION['confirm_msg_customer']);
                        unset($_SESSION['confirm_msg_reseller']);
                        unset($_SESSION['client_id']);
                        unset($_SESSION['cache_data']);
                        unset($_SESSION['region_id']);
                        unset($_SESSION['store_option']);
                        unset($_SESSION['orders']);
                        unset($_SESSION['item_id']);
                        unset($_SESSION['item_count']);

                        unset($_SESSION['store_id']);
                        unset($_SESSION['category_id']);
                        unset($_SESSION['moh_notes']);
                        unset($_SESSION['payops']);
                        unset($_SESSION['distance']);
                        unset($_SESSION['distance_rate_id']);
                        unset($_SESSION['distance_rate_price']);
                        unset($_SESSION['promo']);
                        unset($_SESSION['moh']);
                        $user = 2;

                    }else{
                        // $this->session->unset_userdata('orders');
                        // $this->session->sess_destroy();
                        $user = 1;

                    }
                } else {
                    $res = 'error';
                    $sms_status = 'error';
                    $confirm_msgx = 'error';
                    $sms_note = 'error';
                    $id = null;
                    $user = 0;

                }

                // CONFIRM MESSAGE
                $confirm_msg = isset($_SESSION['confirm_msg']) ? $_SESSION['confirm_msg'] : 'Thank your for ordering from the Taters Snack Shop. An SMS/text notification will be sent to you now for additional instructions, including where you can upload the screenshot for your bank or GCash payment. Please watch out for it. <br/><br/> Our Taters representative will call you once your order is ready for pick-up and/or delivery. Thank you, and stay safe!';

                $confirm_msg = 'Confirm-message';

                // header('content-type: application/json');
                // echo json_encode(array('status' => $res, 'id' => $id, 'sms_status' => $sms_status, 'confirm_msg' => $confirm_msgx, 'sms_note' => $sms_note, 'user' => $user ));
                // echo json_encode(array('status' => $res, 'id' => $id, 'sms_status' => $sms_status));
                return $sms_status;
                break;

            case 'cancel':
                if (isset($_SESSION['transaction_id'])) {
                    $query = $this->shop_model->update_transaction_status($_SESSION['transaction_id'], 5);
                    if ($query['status']) {
                        // $transaction_id = $_SESSION['transaction_id'];

                        $this->session->unset_userdata('orders');
                        // $this->session->sess_destroy();
                        $res = "success";
                    } else {
                        $res = "error";
                    }
                    $id = $_SESSION['transaction_id'];
                } else {
                    $res = 'error';
                    $id = null;
                }
                header('content-type: application/json');
                echo json_encode(array('status' => $res, 'id' => $id));
                break;

            default:
                header('content-type: application/json');
                echo json_encode(array('status' => 'error', 'id' => null));
                break;
        }
    }

    private function get_night_diff($start_datetime, $end_datetime){
        date_default_timezone_set('Asia/Manila');
        $start   = date('Y-m-d 22:00:00',$start_datetime);
        $end     = date('Y-m-d 06:00:00',$start_datetime + 86400);

        $event_start  = date('Y-m-d H:i:s',$start_datetime);
        $event_end    = date('Y-m-d H:i:s',$end_datetime);

        $night_diff = 0;
        if($event_end > $start AND $event_end <= $end AND $event_start <= $start){
            $night_diff = abs(strtotime($start) - strtotime($event_end)) / 3600;
        }else if($event_end <= $end AND $event_start > $start){
            $night_diff = abs(strtotime($event_start) - strtotime($event_end)) / 3600;
        }else if($event_end > $end AND $event_start > $start){
            $night_diff = abs(strtotime($event_start) - strtotime($end)) / 3600;
        }else if($event_end > $end AND $event_start <= $start){
            $night_diff = abs(strtotime($start) - strtotime($end)) / 3600;
        }else if($event_start < $end){  
            $night_diff=0;
            for ($i=date('H',$start_datetime); $i < date('H',$end_datetime); $i++) { 
               if ($i < 6) {
                $night_diff++;               
                }
            }
        }
        return $night_diff * 500;
    }
    
    public function get_succeeding_hour_charge($start_datetime, $end_datetime){
        $event_start = $start_datetime;
        $event_end = $end_datetime;
        $time_diff = $event_end - $event_start;

        $event_duration = ($time_diff/60)/60;
        
        if ($event_duration > 3) {
            $comp = $event_duration - 3;
            $additional_fee = $comp * 500;
        } else {
            $additional_fee = 0;
        }

        return $additional_fee;
    }

}