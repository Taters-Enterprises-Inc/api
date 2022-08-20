<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Profile extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('shop_model');
		$this->load->model('catering_model');
		$this->load->model('user_model');
	}

	public function snackshop_orders(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				if(!isset($_SESSION['userData']['oauth_uid'])){

					$response = array(
						'message' => 'Error user not found',
					);

					header('content-type: application/json');
					echo json_encode($response);
					return;
				}

				$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
				$snackshop_orders = $this->shop_model->get_user_order_history($get_fb_user_details->id,'facebook');
				
				$response = array(
					'message' => 'Succesfully fetch history of orders',
					'data' => $snackshop_orders,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function catering_bookings(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				if(!isset($_SESSION['userData']['oauth_uid'])){

					$response = array(
						'message' => 'Error user not found',
					);

					header('content-type: application/json');
					echo json_encode($response);
					return;
				}

				$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
				$catering_bookings = $this->catering_model->get_user_booking_history($get_fb_user_details->id,'facebook');
				
				$response = array(
					'message' => 'Succesfully fetch history of orders',
					'data' => $catering_bookings,
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}
}