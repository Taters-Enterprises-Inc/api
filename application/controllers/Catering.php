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
	
	public function upload_contract(){
		
        if (is_uploaded_file($_FILES['uploaded_file']['tmp_name'])) {

            $config['upload_path'] = './assets/upload/catering_upload_contract'; 

			if(!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, TRUE);

            $config['allowed_types']    = 'pdf|doc|gif|jpg|jpeg|png';   
            $config['max_size']         = 2000; 
            $config['max_width']        = 0;
            $config['max_height']       = 0;
            $config['encrypt_name']     = TRUE; 

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('uploaded_file')) { 
                $error = $this->upload->display_errors();
				$this->output->set_status_header('401');
                echo json_encode(array( "message" => $error));
            } else {
                $data = $this->upload->data(); 
                $hash_key = $_POST['hash_key'];

                $store_id = $this->session->cache_data['store_id'];


                $this->catering_model->upload_contract(
					$data,
					$hash_key,
				);	

				
				$real_time_notification = array(
					"store_id" => $store_id,
					"message" => $this->session->userData['first_name'] . " " . $this->session->userData['last_name'] ." Upload Contract!"
				);

				notify('admin-catering','contract-booking', $real_time_notification);

                header('content-type: application/json');
                echo json_encode(array( "message" => 'Succesfully upload contract'));
            }
        } else {
			$this->output->set_status_header('401');
			echo json_encode(array( "message" => 'Failed upload contract check your file'));
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


				$products = $this->catering_model->getCateringProductsPerCategory($region_id,'','','','','');
				$addons = $this->catering_model->get_catering_addons($region_id);
				$product_addons = $this->catering_model->get_catering_product_addons($region_id);
				
				$response = array(
					'data' => array(
						"products" =>  $products,
						"addons" => $addons, 
						"product_addons" => $product_addons,
					),
					'message' => "Successfully fetch products"
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}


	public function orders(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$hash = $this->input->get('hash');
				$order_details = $this->catering_model->view_order($hash);
				

				$status_detail = $this->catering_model->fetch_status_detail($hash);

				if(!empty($status_detail)){
					$status = $status_detail->status;
				}
				
				$subtotal = $order_details['clients_info']->purchase_amount;
				$transportation_fee = $order_details['clients_info']->distance_price;

				
				$catering_start_date = $order_details['clients_info']->start_datetime;
				$catering_end_date = $order_details['clients_info']->end_datetime;
				
				$service_fee = 0;
				$service_fee_percentage = 0.1;
				$night_diff_charge = (int)$this->get_night_diff($catering_start_date,$catering_end_date);
				$additional_fee = (int)$this->get_succeeding_hour_charge($catering_start_date, $catering_end_date);
		
				if(isset($service_fee_percentage)){
					$service_fee = round($subtotal * $service_fee_percentage);
				}
	
				if($order_details['clients_info']->discount == NULL){
					$voucher_amount = 0;
				}else{
					$voucher_amount = $order_details['clients_info']->discount;
				}

				$cod_fee = $order_details['clients_info']->cod_fee;
				$grand_total = (int)$subtotal + (int)$transportation_fee + (int)$service_fee + (int)$night_diff_charge  + (int)$cod_fee - (double)$voucher_amount;
				
				$no_of_pax = 0;
				$package_price = 0;

				$all_of_orders = array_merge($order_details['order_details'],$order_details['products'], $order_details['addons']);

				foreach($all_of_orders  as $key => $order){
					if($order->status == 0){
						continue;
					}
					
					if($order->category != 11 && $order->category != 5){
						$no_of_pax += $order->quantity;
					}
					$package_price += $order->calc_price;

					$order->product_price = $order->product_price;
					$order->calc_price = $order->calc_price;
	
					
					$package_selection[] = $order;
					if(!empty($order->remarks)){
						$remarks = explode("<br/>",$order->remarks); 
	
						foreach($remarks as $remarks_key => $remark){
							$get_first_letter = substr($remark,8);
							$quantity = (int)strtok($get_first_letter, " - ");
							$name_first_letter_index = strpos($remark," - ") + 3;
							$remarks[$remarks_key] = array(
								'quantity' => $quantity,
								'name' => substr($remark,$name_first_letter_index),
							);
						}
		
						$package_selection[$key]->flavors = $remarks;
						array_pop($package_selection[$key]->flavors);
					}
				}

				$package_price = $package_price;	
				
				$start_date = date('l jS  F Y', $order_details['clients_info']->start_datetime);
				$end_date = date('l jS  F Y', $order_details['clients_info']->end_datetime);
				$date_of_event = $start_date;
				$serving_time = date('h:i A',$order_details['clients_info']->serving_time);
				$event_date_and_time = date('h:i A',$order_details['clients_info']->start_datetime) . ' to ' . date('h:i A',$order_details['clients_info']->end_datetime);
				if($start_date != $end_date){
					$date_of_event = $start_date . ' to ' . $end_date;
				}
				
				$query_logon  = $this->catering_model->get_logon_type($hash);
				$logon_type   = $query_logon->logon_type;

				switch($logon_type){
					case 'facebook':
						$facebook_details = $this->catering_model->get_facebook_details($order_details['clients_info']->fb_user_id);
						$firstname = $facebook_details->first_name;
						$lastname = $facebook_details->last_name;

						break;
					case 'mobile':
						$mobile_details = $this->catering_model->get_mobile_details($order_details['clients_info']->mobile_user_id);
						$firstname = $mobile_details->first_name;
						$lastname = $mobile_details->last_name;
						break;
				}

				$response = array(
					"message" => "Success",
					'data' => array(
						'status' => $status,
						'order' => $order_details,
						'package_selection' => $package_selection,

						'firstname' => $firstname,
						'lastname' => $lastname,

						'subtotal' => $subtotal,
						'transportation_fee' => $transportation_fee,
						'night_diff_charge' => $night_diff_charge,
						'service_fee' => $service_fee,
						'cod_fee' => $cod_fee,
						
						'grand_total' => $grand_total,
						'package_price' => $package_price,
						'transportation_fee' => $transportation_fee,
						'additional_hour_fee' => $additional_fee,
						'night_diff_charge' => $night_diff_charge,

						'no_of_pax' => $no_of_pax,
						'date_of_event' => $date_of_event,
						'event_date_and_time' => $event_date_and_time,
						'serving_time' => $serving_time,
					),
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

    public function packages(){
        
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
	
	public function package(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$hash = $this->input->get('hash');

				$product = $this->catering_model->get_product($hash);

				if(!isset($product)){
					$this->output->set_status_header(401);
					echo json_encode(array('message'=>'Product not found...'));
					return;
				}
				
				if(isset($_SESSION['cache_data'])){
					$region_id = $_SESSION['cache_data']['region_id'];
					$addons = $this->catering_model->get_catering_addons($region_id);
					$product_addons = $this->catering_model->get_catering_product_addons($region_id);
				}
				
				$product_flavor = array();
				$flavors = $this->catering_model->getPackageVariants($product->id);
				foreach($flavors as $key => $flavor){
					$product_flavor[$flavor->product_variant_id]['parent_name'] = $flavor->parent_name;
					$product_flavor[$flavor->product_variant_id]['flavors'][] =  $flavor;
				}
				
				$product_image_extension = '.' . pathinfo($product->product_image)['extension'];
				$product_images = $this->images->product_images(
					'assets/images/shared/products/500',
					basename($product->product_image,$product_image_extension),
					$product_image_extension
				);

				$product_prices = $this->catering_model->get_product_prices($product->id);
				$product->base_price = $product->price;


				$response = array(
					'data' => array(
						'region' => $region_id ?? null,
						'product' => $product,
						'product_flavor' => array_values($product_flavor),
						'addons' => $addons ?? null,
						'product_addons' => $product_addons ?? null,
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