<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Download extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('store_model');
		$this->load->model('catering_model');
		$this->load->model('shop_model');
		$this->load->library('images');
	}

    public function contract($hash_key){
        date_default_timezone_set('Asia/Manila');
        
		if(!isset($_SESSION['userData'])){
			show_error('Unauthorized you cannot view this page');
			exit();
		}
		$data['hash_key'] = $hash_key;

		$query_result = $this->catering_model->view_order($hash_key);
		
		$status_detail = $this->catering_model->fetch_status_detail($hash_key);

		if(!empty($status_detail)){
			$data['status'] = $status_detail->status;
		}
        
        $catering_start_date = $query_result['clients_info']->start_datetime;
        $str_start_datetime = strtotime($catering_start_date);
        $start_datetime = date($str_start_datetime);
        
        $catering_end_date = $query_result['clients_info']->end_datetime;
        $str_end_datetime = strtotime($catering_end_date);
        $end_datetime = date($str_end_datetime);
            
		
        if (isset($_SESSION['userData']['oauth_uid'])) {
            $get_fb_id = $this->shop_model->get_facebook_client_id($_SESSION['userData']['oauth_uid']);

			if($get_fb_id != $query_result['clients_info']->fb_user_id){
				show_error('Unauthorized you cannot view this page');
				exit();
			}
        }

		$query_logon  = $this->catering_model->get_logon_type($hash_key);
		$logon_type   = $query_logon->logon_type;

		if ($logon_type == 'guest') {
			$this->session->unset_userdata('orders');
			// $this->session->sess_destroy();
			$data['link'] = false;
		} else {
			$this->session->unset_userdata('orders');
			$data['link'] = true;
		}


		$subtotal = $query_result['clients_info']->purchase_amount;
		$transportation_fee = $query_result['clients_info']->distance_price;
			
		$night_diff_charge = $this->get_night_diff((int)$start_datetime,(int)$end_datetime);
		
		$service_fee = 0;
		$service_fee_percentage = 0.1;

		if(isset($service_fee_percentage)){
			$service_fee = round($subtotal * $service_fee_percentage);
		}

		if($query_result['clients_info']->discount == NULL){
			$voucher_amount = 0;
		}else{
			$voucher_amount = $query_result['clients_info']->discount;
		}
		$cod_fee = $query_result['clients_info']->cod_fee;
		$grand_total = (int)$subtotal + (int)$transportation_fee + (int)$service_fee + (int)$night_diff_charge + (int)$this->get_succeeding_hour_charge((int)$start_datetime,(int)$end_datetime) + (int)$cod_fee - (double)$voucher_amount ;


		if ($query_result == NULL) {
			show_error('Page is not available.');
			exit();
		} else {
			$data['info'] = $query_result['clients_info'];

			$order_design = array();

            foreach ($query_result['addons'] as $k => $order){
				if($order->type == 'addon'){
					$order_design[$k]['main']['product_id'] = $order->product_id;
					$order_design[$k]['main']['combination_id'] = $order->combination_id;
					$order_design[$k]['main']['type'] = $order->type;
					$order_design[$k]['main']['quantity'] = $order->quantity;
					$order_design[$k]['main']['status'] = $order->status;
					$order_design[$k]['main']['remarks'] = $order->remarks;
					$order_design[$k]['main']['promo_id'] = $order->promo_id;
					$order_design[$k]['main']['promo_price'] = $order->promo_price;
					$order_design[$k]['main']['sku'] = $order->sku;
					$order_design[$k]['main']['sku_id'] = $order->sku_id;
					$order_design[$k]['main']['calc_price'] = $order->calc_price;
					$order_design[$k]['main']['product_price'] = $order->product_price;
					$order_design[$k]['main']['product_image'] = $order->product_image;
					$order_design[$k]['main']['name'] = $order->name;
					$order_design[$k]['main']['description'] = $order->description;
					$order_design[$k]['main']['product_label'] = $order->product_label;
					$order_design[$k]['main']['freebie_prod_name'] = $order->freebie_prod_name;
					$order_design[$k]['main']['addon_base_product'] = $this->product_model->fetch_product_name($order->addon_base_product)[0]->name;
				}
			}

			$data['orders'] = $order_design;
			$data['personnel'] = $query_result['personnel'];
			$data['bank'] = $query_result['bank'];
			$data['subtotal'] = $subtotal;
			$data['night_diff_charge'] = $night_diff_charge;

			$data['voucher_amount'] = $voucher_amount;
			$data['transportation_fee'] = $transportation_fee;
			$data['service_fee'] = $service_fee;
			$data['cod_fee'] = $cod_fee;

			if ($logon_type == 'facebook') {
				$facebook_details = $this->catering_model->get_facebook_details($query_result['clients_info']->fb_user_id);
				$data['firstname'] = $facebook_details->first_name;
				$data['lastname'] = $facebook_details->last_name;
			}

			$succeeding_hour_charge =  $this->get_succeeding_hour_charge((int)$start_datetime,(int)$end_datetime);

            $data['succeeding_hour_charge'] = $succeeding_hour_charge;
			$data['grand_total'] = $grand_total;


			$no_of_pax = 0;
			$package_price = 0;

			$all_of_orders = array_merge($query_result['order_details'], $query_result['addons']);

			foreach($all_of_orders  as $key => $order){
				if($order->status == 0){
					continue;
				}
				if($order->category != 11 && $order->category != 5){
					$no_of_pax += $order->quantity;
				}
				$package_price += $order->calc_price;

				$order->product_price = number_format($order->product_price  ,2,'.',',');
				$order->calc_price = number_format($order->calc_price  ,2,'.',',');

				$package_selection[$key] = $order;
				$remarks = explode("<br/>",$order->remarks); 

				foreach($remarks as $remarks_key => $remark){
					$get_first_letter = substr($remark,8);
					$quantity = (int)strtok($get_first_letter, " - ");
					$remarks[$remarks_key] = array(
						'quantity' => $quantity,
						'name' => substr($remark,21),
					);
				}

				$package_selection[$key]->flavors = $remarks;
				array_pop($package_selection[$key]->flavors);
			}

			$package_price = number_format($package_price ,2,'.',',');

			$start_date = date('l jS  F Y', $query_result['clients_info']->start_datetime);
			$end_date = date('l jS  F Y', $query_result['clients_info']->end_datetime);
			$date_of_event = $start_date;

			if($start_date != $end_date){
				$date_of_event = $start_date . ' to ' . $end_date;
			}
			
			$contract_data = array(
				'store_name' => $query_result['clients_info']->store_name,
				'contact_person' => $query_result['clients_info']->add_name,
				'company_name' => $query_result['clients_info']->company_name,
				'email' => $query_result['clients_info']->email,
				'venue' => $query_result['clients_info']->address,
				'type_of_function' =>  $query_result['clients_info']->event_class,
				'date_of_event' => $date_of_event,
				'payment_terms' => $query_result['clients_info']->payment_plan,
				'special_arrangements' => $query_result['clients_info']->message,
				'company_name' => $query_result['clients_info']->company_name,
				'serving_time' => date('h:i A',$query_result['clients_info']->serving_time),
				'contact_number' => $query_result['clients_info']->contact_number,
				'no_of_pax' => $no_of_pax,
				'event_date_and_time' => date('h:i A',$query_result['clients_info']->start_datetime) . ' to ' . date('h:i A',$query_result['clients_info']->end_datetime),
				'tracking_number' => $query_result['clients_info']->tracking_no,
				'package_selection' => $package_selection,
				'package_price' => $package_price,
				'service_charge' => number_format($service_fee, 2),
				'transportation_fee' => $transportation_fee,
				'grand_total' => $grand_total,
				'succeeding_hour_charge' => $succeeding_hour_charge,
				'night_diff_charge' => $night_diff_charge,
				'is_download' => true,
				'show_terms_and_condition' => $data['status'] == 1 ? false : true,
			);
			
			$data['contract_data'] = $contract_data;

			$this->load->library('pdf');
			$this->pdf->legalPotrait('contract_download',$contract_data);
			$this->pdf->render();
			$this->pdf->stream($hash_key);
		}

		show_error('Page is not available.');

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