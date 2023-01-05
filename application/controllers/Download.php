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
		$this->load->model('client_model');
		$this->load->library('images');
	}

    public function contract($hash_key){
        date_default_timezone_set('Asia/Manila');
        
		if(!isset($_SESSION['userData'])){
			// show_error('Unauthorized you cannot view this page');
			// exit();
		}
		$data['hash_key'] = $hash_key;

		$query_result = $this->catering_model->view_order($hash_key);
		
		$status_detail = $this->catering_model->fetch_status_detail($hash_key);

		if(!empty($status_detail)){
			$status = $status_detail->status;
		}
        
        $catering_start_date = $query_result['clients_info']->start_datetime;
        
        $catering_end_date = $query_result['clients_info']->end_datetime;

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
			
		$night_diff_charge = (int)$this->get_night_diff($catering_start_date,$catering_end_date);
		
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
		$grand_total = (int)$subtotal + (int)$transportation_fee + (int)$service_fee + (int)$night_diff_charge + (int)$this->get_succeeding_hour_charge($catering_start_date,$catering_end_date) + (int)$cod_fee - (double)$voucher_amount ;


		if ($query_result == NULL) {
			show_error('Page is not available.');
			exit();
		} else {
			$succeeding_hour_charge =  (int)$this->get_succeeding_hour_charge($catering_start_date,$catering_end_date);

			$no_of_pax = 0;
			$package_price = 0;

			$all_of_orders = array_merge($query_result['order_details'],$query_result['products'], $query_result['addons']);

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
					$remarks = explode("<br>",$order->remarks); 

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
				'service_fee' => $service_fee,
				'transportation_fee' => $transportation_fee,
				'grand_total' => $grand_total,
				'succeeding_hour_charge' => $succeeding_hour_charge,
				'night_diff_charge' => $night_diff_charge,
				'cod_fee' => $cod_fee,
				'is_download' => true,
				'show_terms_and_condition' => $status == 1 ? false : true,
			);

			$file_name = $status == 1 ? 
				'taters-caters-booking-summary-'.$query_result['clients_info']->tracking_no :
				'taters-caters-contract-'.$query_result['clients_info']->tracking_no ;

			$this->load->library('pdf');
			$this->pdf->legalPotrait('contract_download',$contract_data);
			$this->pdf->render();
			$this->pdf->stream($file_name);
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