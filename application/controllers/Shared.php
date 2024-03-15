<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Shared extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('shop_model');
		$this->load->model('contact_model');
		$this->load->model('catering_model');
		$this->load->model('client_model');
		$this->load->model('user_model');
		$this->load->model('logs_model');
		$this->load->model('store_model');
		$this->load->model('bsc_model');
		$this->load->model('survey_model');
		$this->load->model('discount_model');
		$this->load->library('form_validation');
	}
	

	public function survey($service, $hash){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				if(!isset($hash) || !isset($service)){
					$this->output->set_status_header('401');
					echo json_encode(array( "message" => 'Missing queries!'));
					break;
				}

				$customer_survey = $this->survey_model->getCustomerSurveyResponseInOrderService($service,$hash);

				$response = array(
					'data' => $customer_survey,
					'message' => 'Successfully fetch deals'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

    public function contacts(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				if(isset($_SESSION['userData']['oauth_uid'])){

					$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
					$contacts = $this->contact_model->get_user_contact($get_fb_user_details->id);

				}else{
					$get_mobile_user_details = $this->user_model->get_mobile_user_details($_SESSION['userData']['mobile_user_id']);
					$contacts = $this->contact_model->get_mobile_user_contact($get_mobile_user_details->id);
				}


				$response = array(
					'message' => 'Successfully add contact',
					'data' => $contacts,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;

			case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);
				
				$this->form_validation->set_rules( 'contact' , 'Mobile Number', 'required|is_unique[mobile_user_contact.contact]|is_unique[fb_user_contact.contact]');

				if ($this->form_validation->run() === FALSE) { 
		
						$this->output->set_status_header('401');
						echo json_encode(array( "message" => 'Mobile already registered, please try different number.'));
						return;

				}else{

						if(isset($_SESSION['userData']['oauth_uid'])){

							$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
							$isFbUser = true;
							$data = array(
								'fb_id' => $get_fb_user_details->id,
								'contact' => $this->input->post('contact')
							);
		
		
						}else{
							$get_mobile_user_details = $this->user_model->get_mobile_user_details($_SESSION['userData']['mobile_user_id']);
							$isFbUser = false;
							$data = array(
								'mobile_id' => $get_mobile_user_details->id,
								'contact' => $this->input->post('contact')
							);

							
		
						}

						
						$this->contact_model->add_contact($data, $isFbUser);
		
						$response = array(
							'message' => 'Successfully add contact',
						);
		
						header('content-type: application/json');
						echo json_encode($response);

				}

			break;
		}
    }

    public function upload_payment(){
        if (is_uploaded_file($_FILES['uploaded_file']['tmp_name'])) {
            $config['upload_path'] = './assets/upload/proof_payment'; 

			if(!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, TRUE);

            $config['allowed_types']    = 'gif|png|jpg|jpeg'; 
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
                $file_name = $data['file_name'];
                $tracking_no = $_POST['tracking_no'];
                $transaction_id = $_POST['trans_id'];

                $query_result  = $this->shop_model->upload_payment($data, $file_name, $tracking_no, $transaction_id);
				
				$user_id = $query_result['client_data']->client_id;
				if ($query_result['upload_status'] == 1) {
					$store_id = $this->session->cache_data['store_id'];

					$customer_name = $this->session->userData['first_name'] . " " . $this->session->userData['last_name'];
					$message =  $customer_name . " payed #".$tracking_no." snackshop order!";

					$realtime_notification = array(
						"store_id" => $store_id,
						"message" => $message,
					);

					notify('admin-snackshop','payment-transaction', $realtime_notification);

					$this->logs_model->insertTransactionLogs($user_id, 1, $transaction_id, 'Uploading-notification-success');
				} else {
					$this->logs_model->insertTransactionLogs($user_id, 1, $transaction_id, 'Uploading-notification-failed');
				}

                header('content-type: application/json');
                echo json_encode(array( "message" => 'Successfully upload payment'));
            }
        } else {
			$this->output->set_status_header('401');
			echo json_encode(array( "message" => 'Failed upload payment check your image'));
        }
    }

    public function catering_upload_payment(){
        if (is_uploaded_file($_FILES['uploaded_file']['tmp_name'])) {
            $config['upload_path'] = './assets/upload/catering_proof_payment'; 

			if(!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, TRUE);

            $config['allowed_types']    = 'gif|png|jpg|jpeg'; 
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
                $file_name = $data['file_name'];
                $tracking_no = $_POST['tracking_no'];
                $transaction_id = $_POST['trans_id'];
                $payment_plan = $_POST['payment_plan'];
                $status = $_POST['status'];

                $query_result = $this->catering_model->upload_payment($data, $file_name, $tracking_no,$transaction_id,$payment_plan, $status);
				
				
				$user_id = $query_result['client_data']->client_id;

				
				if ($query_result['upload_status'] == 1) {
					$store_id = $this->session->cache_data['store_id'];

					$customer_name = $this->session->userData['first_name'] . " " . $this->session->userData['last_name'];
					$message =  $customer_name . " payed #".$tracking_no." catering booking!";

					$realtime_notification = array(
						"store_id" => $store_id,
						"message" => $message,
					);

					notify('admin-catering','payment-transaction', $realtime_notification);

					$this->logs_model->insertCateringTransactionLogs($user_id, 1, $transaction_id, 'Uploading-notification-success');
				} else {
					$this->logs_model->insertCateringTransactionLogs($user_id, 1, $transaction_id, 'Uploading-notification-failed');
				}


                header('content-type: application/json');
                echo json_encode(array( "message" => 'Successfully upload payment'));
            }
        } else {
			$this->output->set_status_header('401');
			echo json_encode(array( "message" => 'Failed upload payment check your image'));
        }
    }

	public function session(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
                // $query_result = $this->catering_model->getOverlappingTransaction(1711569600);
				// $data = array('query_result' => $query_result);
				$data = array(
					'popclub_data' 						=> $this->session->popclub_data,
					'cache_data' 						=> $this->session->cache_data,
					'customer_address' 					=> $this->session->customer_address,
					'userData' 							=> $this->session->userData,
					'orders'							=> $this->session->orders,
					'redeem_data' 						=> $this->session->redeem_data,
					"km_radius"							=> $this->session->km_radius,
					"km_min"							=> $this->session->km_min,
					"free_delivery"						=> $this->session->free_delivery,
					"free_min_delivery"					=> $this->session->free_min_delivery,
					"delivery_rate"						=> $this->session->delivery_rate,
					"minimum_rate"						=> $this->session->minimum_rate,
					"catering_delivery_rate"			=> $this->session->catering_delivery_rate,
					"catering_minimum_rate"				=> $this->session->catering_minimum_rate,
					"catering_type"						=> $this->session->catering_type,
					"catering_start_date"				=> $this->session->catering_start_date,
					"catering_end_date"					=> $this->session->catering_end_date,
					"catering_night_differential_fee"	=> $this->session->catering_night_differential_fee,
					"catering_succeeding_hour_charge"	=> $this->session->catering_succeeding_hour_charge,
					"distance"							=> $this->session->distance,
					"distance_rate_id"					=> $this->session->distance_rate_id,
					"distance_rate_price"				=> $this->session->distance_rate_price,
					"distance_rate_price_before"		=> $this->session->distance_rate_price_before,
					"distance_routes"					=> $this->session->distance_routes,
					"distance_radius"					=> $this->session->distance_radius,
					"payops_list"						=> $this->session->payops_list,
					"cash_delivery"						=> $this->session->cash_delivery,
				);
		
				
				$response = array(
					'message' => 'Successfully fetch session',
					'data' => $data,
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

	public function clear_redeems(){
		unset($_SESSION['redeem_data']);
		echo "<pre>";
		print_r($_SESSION);
	}

	public function available_user_discount(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':

				$available_user_discount = $this->discount_model->getAvailableUserDiscount(
					$this->session->userData['fb_user_id'] ?? null,
					$this->session->userData['mobile_user_id'] ?? null
				);

				$response = array(
					"message" => 'Successfully fetch user available discount',
					"data" => $available_user_discount,
				);
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function stores(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':

				$stores = $this->store_model->getAllStores();
		  
				$response = array(
					"message" => 'Successfully fetch stores',
					"data" => $stores,
				);
		
				header('content-type: application/json');
				echo json_encode($response);

				return;
		}
	}
	
	public function companies(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':

				$companies = $this->bsc_model->getAllCompanies();
		  
				$response = array(
					"message" => 'Successfully fetch companies',
					"data" => $companies,
				);
		
				header('content-type: application/json');
				echo json_encode($response);

				return;
		}
	}
}
