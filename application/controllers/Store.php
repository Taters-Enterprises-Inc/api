<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Store extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->library('google');
		$this->load->model('store_model');
	}

	public function index(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$address = $this->input->get('address');
				$service = $this->input->get('service');
				$hash = $this->input->get('hash');
				$regions = array();
	

				if($hash){
					switch($service){
						case 'SNACKSHOP':
							$region_da_logs = $this->store_model->getSnackShopRegionDaLogProduct($hash);
							break;
						case 'CATERING':
							$region_da_logs = $this->store_model->getCateringRegionDaLogProduct($hash);
							break;
						case 'POPCLUB-STORE-VISIT':
							$region_da_logs = $this->store_model->getPopClubRegionDaLogProduct($hash);
							break;
						case 'POPCLUB-ONLINE-DELIVERY':
							$region_da_logs = $this->store_model->getPopClubRegionDaLogProduct($hash);
							break;
					}
					foreach($region_da_logs as $region_da_log){
						$regions[] = $region_da_log->store_id;
					}
				}

				if($address){
					$stores = $this->store_model->get_stores_available($this->google->geolocator($address)['lat'],$this->google->geolocator($address)['lng'],$service, $regions);
				}else{
					$stores = $this->store_model->get_stores_available(0, 0, $service, $regions);
				}

				$response = array(
					'data' => $stores,
					'message' => 'Select a store that is available near you'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);
				
				set_store_sessions(
					$this->input->post('storeId'),
					$this->input->post('address'),
					$this->input->post('service'),
					null,
					$this->input->post('cateringStartDate'),
					$this->input->post('cateringEndDate'),
				);

				$response = array(
					'message' => 'Successfully set store data'
				);
				
				header('content-type: application/json');
				echo json_encode($response);
			break;
		}
	}
	
	public function reset(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$this->session->unset_userdata('cache_data');
				$this->session->unset_userdata('orders');
				$this->session->unset_userdata('customer_address');
				
				$response = array(
					'message' => 'Reset user selection'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
		
	}
	
	
    public function get_night_diff($start_datetime, $end_datetime){
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