<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Transaction extends CI_Controller {
    
	public function __construct()
	{
		parent::__construct();
		$this->load->model('transaction_model');
		$this->load->model('shop_model');
		$this->load->model('catering_model');
	}

    public function get_facebook_client_id($oauth_id){
        $this->db->select('id');
        $this->db->where('oauth_uid', $oauth_id);
        $query = $this->db->get('fb_users');
        $data = $query->result_array();
        return $data[0]['id'];
    }
    
    public function catering(){
        
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
                
                $hash_key = substr(md5(uniqid(mt_rand(), true)), 0, 20);
                $tracking_no = substr(md5(uniqid(mt_rand(), true)), 0, 6);

                
                $insert_client_details = $this->catering_model->insert_client_details($post);
                if($insert_client_details->status == true){

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
                    if($this->session->moh == 2 && $payops == '3'){
                        $cod_fee = $this->session->cash_delivery;
                    }

                    
                    if (isset($this->session->userData['oauth_uid'])) {
                        $logon_type = 'facebook';
                    } elseif(isset($this->session->userData['mobile_user_id'])){
                        $logon_type = 'mobile';
                    } else {
                        $logon_type = 'guest';
                    }
                    
					$str_serving_time = strtotime($post['catering_serving_time']);
					$serving_time = date($str_serving_time);
                    
                    $catering_start_date = $_SESSION['catering_start_date'];
                    $str_start_datetime = strtotime($catering_start_date);
                    $start_datetime = date($str_start_datetime);
                    
                    $catering_end_date = $_SESSION['catering_end_date'];
                    $str_end_datetime = strtotime($catering_end_date);
                    $end_datetime = date($str_end_datetime);
                    
                    $client_id = $insert_client_details->id;
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
						'company_name'		=> isset($post['catering_company_name']) ? $post['catering_company_name'] : '',
						'message'			=> isset($post['other_details']) ? $post['other_details'] : '',
						'serving_time'		=> $serving_time,
                        'start_datetime'    => $catering_start_date,
                        'end_datetime'      => $catering_end_date,
                        'event_class'       => isset($post['event_class']) ? $post['event_class'] : '',
                        'service_fee'       => $comp_total * 0.1,
                        'night_diff_fee'    => $this->get_night_diff((int)$start_datetime, (int)$end_datetime),
                        'additional_hour_charge' => $this->get_succeeding_hour_charge((int)$start_datetime, (int)$end_datetime),
                        'distance'          => '2',
                        'distance_id'       => $distance_rate_id,
                        'distance_price'    => $distance_rate_price,
                        'cod_fee'           => $cod_fee,
                        'payops'            => $payops,
                        'payment_plan'      => $post['payment_plan'],
                        'discount'          => 0,
                        'custom_message'    => '',
                        'logon_type'        => $logon_type,
                    );

                        
                    $query_transaction_result = $this->catering_model->insert_transaction_details($transaction_data);
                    
                    if($query_transaction_result->status){
                        
                        if(!empty($this->session->orders)){
                            $comp_total = 0;

                            foreach ($this->session->orders as $k => $value) {
                                $remarks = (empty($value['prod_multiflavors'])) ? $value['prod_flavor'] : $value['prod_multiflavors'];
                                $type = (isset($value['addon_base_product_id']) && ($value['addon_base_product_id']) ? 'addon' : 'main');

                                $order_data[] = array(
                                    'transaction_id'      => $query_transaction_result->id,
                                    'combination_id'      => $k,
                                    'product_id'          => $value['prod_id'],
                                    'quantity'            => $value['prod_qty'],
                                    'remarks'             => $remarks,
                                    'type'                => isset($value['is_free_item']) && $value['is_free_item'] === true ? "freebie" : "main",
                                    'type'                => $type,
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
                            $query_orders_result = $this->catering_model->insert_client_orders($order_data);
                        }
                    }
                    

                }
                
                $response = array(
                    "data" => array(
                        "hash" => $hash_key,
                    ),
                    "message" => "Succesfully checkout order"
                );
                
                header('content-type: application/json');
                echo json_encode($response);


                break;
        }
    }

    public function shop(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);

                $hash_key = substr(md5(uniqid(mt_rand(), true)), 0, 20);
                $tracking_no = substr(md5(uniqid(mt_rand(), true)), 0, 6);

                $insert_client_details = $this->transaction_model->insert_client_details($post);

                if($insert_client_details->status == true){

                    if(!empty($this->session->orders)){
                        $comp_total = 0;
                        foreach ($this->session->orders as $row => $val) {
							if($val['prod_id']){
								$comp_total += $val['prod_calc_amount'];
							}else if($val['deal_id']){
								$comp_total += $val['deal_promo_price'];
							}
                        }
                    }
                    
                    $distance_rate_id = (empty($this->session->distance_rate_id)) ? 0 : $this->session->distance_rate_id;
					$deals = $this->session->deals;
                    $distance_rate_price = (empty($this->session->distance_rate_price)) ? 0 : $this->session->distance_rate_price;

					if(isset($deals)){
						foreach($deals as $deal){
							if($deal['minimum_purchase'] <= $comp_total){
								$distance_rate_price = 0;
							}
						}
					}

                    $payops = $post['payops'];

                    
                    $cod_fee = "0";
                    if($this->session->moh == 2 && $payops == '3'){
                        $cod_fee = $this->session->cash_delivery;
                    }
                    
                    $voucher_id = 0;
                    $voucher_discount = 0;

                    if(isset($this->session->voucher_data) && isset($this->session->voucher_status) && $this->session->voucher_status == 1){
                        $voucher_id = $this->session->voucher_data[0]['id'];
                        $offer_type = $this->session->voucher_data[0]['offer_type'];
                        $voucher_max_discount = $this->session->voucher_data[0]['voucher_max_discount'];

                        if ($offer_type == 1) {
                            if ($voucher_max_discount != 0) {
                                $comp = $this->session->voucher_data[0]['voucher_value'] * $comp_total / 100 ;
                                if ($comp > $voucher_max_discount) {
                                    $voucher_discount = $voucher_max_discount;
                                } else {
                                    $voucher_discount = $comp;
                                }
                            } else {
                                $voucher_discount = $this->session->voucher_data[0]['voucher_value'] * $comp_total / 100;
                            }
                        } elseif($offer_type == 6 || $offer_type == 9 || $offer_type == 16){
                            foreach ($this->session->orders as $key => $value) {
                                $voucher_discount += $this->session->orders[$key]['prod_discount'];
                            }
                        } elseif($offer_type == 7){
                            $compute_discount = $this->session->voucher_data[0]['voucher_value'] * $comp_total;
                            if ($compute_discount >= 75) {
                                $voucher_discount = 75;
                            } else {
                                $voucher_discount = $compute_discount;
                            }
                        } else {
                            $voucher_discount = $this->session->voucher_data[0]['voucher_value'];
                        }

                    }

                    if (isset($this->session->table_number)) {
                        $table_number = $this->session->table_number;
                    } else {
                        $table_number = null;
                    }


                    
                    if (isset($this->session->userData['oauth_uid'])) {
                        $logon_type = 'facebook';
                    } elseif(isset($this->session->userData['mobile_user_id'])){
                        $logon_type = 'mobile';
                    } else {
                        $logon_type = 'guest';
                    }

                    $payops_ref_no = '';
                    $discount_type = '';
                    $discount_ref_no = '';
                    $discount_value = '';

                    $client_id = $insert_client_details->id;
                    $transaction_data = array(
                        'tracking_no' 		=> $tracking_no,
                        'hash_key'          => $hash_key,
                        'client_id' 	   	=> $client_id,
                        'purchase_amount'   => $comp_total,
                        'remarks' 		    => '',
                        'status' 		    => 1,
                        'store'             => $this->session->cache_data['store_id'],
                        'dateadded'         => date('Y-m-d H:i:s'),
                        'distance'          => '2',
                        'distance_id'       => $distance_rate_id,
                        'distance_price'    => $distance_rate_price,
                        'cod_fee'           => $cod_fee,
                        'reseller_id'       => 0,
                        'reseller_discount' => "",
                        'payops'            => $payops,
                        'discount'          => $discount_value,
                        'giftcard_discount' => "",
                        'giftcard_number'   => "",
                        'voucher_id'        => $voucher_id,
                        'table_number'      => $table_number,
                        'custom_message'    => '',
                        'logon_type'        => $logon_type,
                        'store_payops'      => 0,
                        'store_payops_ref_no'=> $payops_ref_no,
                        'store_discount_type'=> $discount_type,
                        'store_discount_ref_no'=> $discount_ref_no
                    );

                    $query_transaction_result = $this->transaction_model->insert_transaction_details($transaction_data);
                    
                    if($query_transaction_result->status){
                        $trans_id = $query_transaction_result->id;
                        
                        if(!empty($this->session->orders)){
                            $comp_total = 0;

                            foreach ($this->session->orders as $k => $value) {
								if($value['prod_id']){
									$remarks = (empty($value['prod_multiflavors'])) ? $value['prod_flavor'] : $value['prod_multiflavors'];
									$type = (isset($value['addon_base_product_id']) && ($value['addon_base_product_id']) ? 'addon' : 'main');
	
									$order_data_product[] = array(
										'transaction_id'      => $query_transaction_result->id,
										'combination_id'      => $k,
										'product_id'          => $value['prod_id'],
										'quantity'            => $value['prod_qty'],
										'remarks'             => $remarks,
										'type'                => $type,
										'status'              => 1,
										'promo_id'            => "",
										'promo_price'         => "",
										'sku'                 => $value['prod_sku'],
										'sku_id'              => $value['prod_sku_id'],
										'price'               => $value['prod_calc_amount'],
										'product_price'       => $value['prod_price'],
										'product_label'       => $value['prod_size'],
										'product_discount'    => $value['prod_discount'],
										// 'addon_base_product'        => $value['addon_base_product_id'],
										// 'addon_base_product_name'   => $this->shop_model->fetch_product_name($value['addon_base_product_id'])[0]->name,
									);
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
								}else if($value['deal_id']){
									$order_data_deal[] = array(
										'redeems_id'  => $query_transaction_result->id,
										'deal_id'         => $value['deal_id'],
										'price'			  => $value['deal_promo_price'],
										'product_price'   => $value['deal_promo_price'],
										'remarks'		  => $value['deal_remarks'],
										'quantity'	      => 1,
										'status'	      => 0,
									);
								}
                            }
							
							if(!empty($order_data_product))
								$query_orders_result = $this->transaction_model->insert_client_orders($order_data_product);
								
							if(!empty($order_data_deal))
								$query_orders_result = $this->transaction_model->insert_client_orders_deal($order_data_deal);
                        }
                        
                    
                        if($query_orders_result){
                            $sms_stat =  $this->summary_actions('confirm');
                            // set_cookie('_teitn',$tracking_no,'86400');
                        }

                    }

                }
                
                $this->session->unset_userdata('orders');

                $response = array(
                    "data" => array(
                        "hash" => $hash_key,
                    ),
                    "message" => "Succesfully checkout order"
                );
                
                header('content-type: application/json');
                echo json_encode($response);

                break;
        }
    }


    private function summary_actions($request)
    {
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
                        // $notification_status = $this->status_notification($transaction_id,5,0);

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