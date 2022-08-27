<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Shared extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('shop_model');
		$this->load->model('contact_model');
		$this->load->model('user_model');
	}

    public function contacts(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
				$contacts = $this->contact_model->get_user_contact($get_fb_user_details->id);

				$response = array(
					'message' => 'Successfully add contact',
					'data' => $contacts,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
				
				$data = array(
					'fb_id' => $get_fb_user_details->id,
					'contact' => $post['contact']
				);
	
				$this->contact_model->add_contact($data);

				$response = array(
					'message' => 'Successfully add contact',
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
    }
	
    public function upload_payment()
    {
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

                $this->shop_model->upload_payment($data, $file_name, $tracking_no, $transaction_id);

                header('content-type: application/json');
                echo json_encode(array( "message" => 'Succesfully upload payment'));
            }
        } else {
			$this->output->set_status_header('401');
			echo json_encode(array( "message" => 'Failed upload payment check your image'));
        }
    }


	public function session(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$data = array(
					'popclub_data' 					=> $this->session->popclub_data,
					'cache_data' 					=> $this->session->cache_data,
					'customer_address' 				=> $this->session->customer_address,
					'userData' 						=> $this->session->userData,
					'orders'						=> $this->session->orders,
					'deals' 						=> $this->session->deals,
					"km_radius"						=> $this->session->km_radius,
					"km_min"						=> $this->session->km_min,
					"free_delivery"					=> $this->session->free_delivery,
					"free_min_delivery"				=> $this->session->free_min_delivery,
					"delivery_rate"					=> $this->session->delivery_rate,
					"minimum_rate"					=> $this->session->minimum_rate,
					"catering_delivery_rate"		=> $this->session->catering_delivery_rate,
					"catering_minimum_rate"			=> $this->session->catering_minimum_rate,
					"distance"						=> $this->session->distance,
					"distance_rate_id"				=> $this->session->distance_rate_id,
					"distance_rate_price"			=> $this->session->distance_rate_price,
					"distance_rate_price_before"	=> $this->session->distance_rate_price_before,
					"distance_routes"				=> $this->session->distance_routes,
					"distance_radius"				=> $this->session->distance_radius,
					"payops_list"					=> $this->session->payops_list,
					"cash_delivery"					=> $this->session->cash_delivery,
				);
		
				$response = array(
					'message' => 'Successfully fetch session',
					'data' => $data,
				);
				
				header('content-type: application/json');
				echo json_encode($response);
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
}
