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
		$this->load->model('contact_model');
	}

	public function contact($id){
		switch($this->input->server('REQUEST_METHOD')){
			case 'PUT':

				$post = json_decode(file_get_contents("php://input"), true);

				if(isset($_SESSION['userData']['oauth_uid'])){

					$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
					$user_id = $get_fb_user_details->id;
					$isFbUser = true;
					
				}else if(isset($_SESSION['userData']['mobile_user_id'])){

					$get_mobile_user_details = $this->user_model->get_mobile_user_details($_SESSION['userData']['mobile_user_id']);
					$user_id = $get_mobile_user_details->id;
					$isFbUser = false;


				}else{
					$this->output->set_status_header(401);
					echo json_encode(array('message'=>'User not found...'));
					return;
				}
				

				$data = array(
					'contact' => $post['contact'],
				);

				$this->contact_model->update_contact($id,$user_id,$data,$isFbUser);

				$response = array(
					'message' => 'Contact updated.'
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;

			case 'DELETE':

				if(isset($_SESSION['userData']['oauth_uid'])){

					$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
					$user_id = $get_fb_user_details->id;
					$isFbUser = true;
					
				}else if(isset($_SESSION['userData']['mobile_user_id'])){

					$get_mobile_user_details = $this->user_model->get_mobile_user_details($_SESSION['userData']['mobile_user_id']);
					$user_id = $get_mobile_user_details->id;
					$isFbUser = false;


				}else{
					$this->output->set_status_header(401);
					echo json_encode(array('message'=>'User not found...'));
					return;
				}

				$this->contact_model->delete_contact($id,$user_id,$isFbUser);

				$response = array(
					'message' => 'Contact deleted.'
				);

				header('content-type: application/json');
				echo json_encode($response);
				return;
		}
	}

	public function snackshop_orders(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$logon_type = isset($_SESSION['userData']['oauth_uid']) ? 'facebook' :
					(isset($_SESSION['userData']['mobile_user_id']) ? 'mobile' : null);

				if(!isset($logon_type)){

					$response = array(
						'message' => 'Error user not found',
					);

					header('content-type: application/json');
					echo json_encode($response);
					return;
				}
				

				switch($logon_type){
					case 'facebook':
						$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
						$snackshop_orders = $this->shop_model->get_user_order_history($get_fb_user_details->id,'facebook');
						break;
					case 'mobile':
						$get_mobile_user_details = $this->user_model->get_mobile_user_details($_SESSION['userData']['mobile_user_id']);
						$snackshop_orders = $this->shop_model->get_user_order_history($get_mobile_user_details->id,'mobile');

						break;
				}

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