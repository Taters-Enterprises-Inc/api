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
		$this->load->model('deals_model');
		$this->load->model('user_model');
		$this->load->model('contact_model');
		$this->load->library('form_validation');

	}

	public function contact($id){
		switch($this->input->server('REQUEST_METHOD')){
			case 'PUT':

				$post = json_decode(file_get_contents("php://input"), true);
				$this->form_validation->set_data($post);

				$this->form_validation->set_rules( 'contact' , 'Mobile Number', 'required|is_unique[mobile_user_contact.contact]|is_unique[fb_user_contact.contact]');


				if ($this->form_validation->run() === FALSE) { 

					
					$this->output->set_status_header('401');
					echo json_encode(array( "message" => 'Mobile already registered, please try different number.'));
					return;

				}else{


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


				}
				break;


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
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');
		
				$logon_type = isset($_SESSION['userData']['oauth_uid']) ? 'facebook' :
					(isset($_SESSION['userData']['mobile_user_id']) ? 'mobile' : null);

				if(!isset($logon_type)){

					$response = array(
						'message' => 'Error user not found',
					);

					header('content-type: application/json');
					echo json_encode($response);
					break;
				}

				switch($logon_type){
					case 'facebook':
						$get_fb_user_details = $this->user_model->get_fb_user_details($_SESSION['userData']['oauth_uid']);
						$snackshop_orders_count = $this->shop_model->getUserOrderHistoryCount('facebook',$get_fb_user_details->id, $search);
						$snackshop_orders = $this->shop_model->getUserOrderHistory('facebook',$get_fb_user_details->id, $page_no, $per_page, $order_by,  $order, $search);
						

						$pagination = array(
							"total_rows" => $snackshop_orders_count,
							"per_page" => $per_page,
						);		
		
						$response = array(
							'message' => 'Succesfully fetch history of orders',
							"data" => array(
							  "pagination" => $pagination,
							  "orders" => $snackshop_orders
							),
						);
		
						header('content-type: application/json');
						echo json_encode($response);
						break;
					case 'mobile':
						$get_mobile_user_details = $this->user_model->get_mobile_user_details($_SESSION['userData']['mobile_user_id']);
						$snackshop_orders_count = $this->shop_model->getUserOrderHistoryCount('mobile',$get_mobile_user_details->id, $search);
						$snackshop_orders = $this->shop_model->getUserOrderHistory('mobile',$get_mobile_user_details->id, $page_no, $per_page, $order_by,  $order, $search);
						
						$pagination = array(
							"total_rows" => $snackshop_orders_count,
							"per_page" => $per_page,
						);		
		
						$response = array(
							'message' => 'Succesfully fetch history of orders',
							"data" => array(
							  "pagination" => $pagination,
							  "orders" => $snackshop_orders,
							),
						);
		
						header('content-type: application/json');
						echo json_encode($response);
						break;
				}
				break;
		}
	}



	public function catering_bookings(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');

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
						$catering_bookings_count = $this->catering_model->getUserCateringBookingHistoryCount('facebook',$get_fb_user_details->id, $search);
						$catering_bookings = $this->catering_model->getUserCateringBookingHistory('facebook',$get_fb_user_details->id, $page_no, $per_page, $order_by,  $order, $search);
						
						$pagination = array(
							"total_rows" => $catering_bookings_count,
							"per_page" => $per_page,
						);		
		
						$response = array(
							'message' => 'Succesfully fetch history of bookings',
							"data" => array(
							  "pagination" => $pagination,
							  "bookings" => $catering_bookings
							),
						);
		
						header('content-type: application/json');
						echo json_encode($response);
						break;
					case 'mobile':
						$get_mobile_user_details = $this->user_model->get_mobile_user_details($_SESSION['userData']['mobile_user_id']);
						$catering_bookings_count = $this->catering_model->getUserCateringBookingHistoryCount('mobile',$get_mobile_user_details->id, $search);
						$catering_bookings = $this->catering_model->getUserCateringBookingHistory('mobile',$get_mobile_user_details->id, $page_no, $per_page, $order_by,  $order, $search);
						
						$pagination = array(
							"total_rows" => $catering_bookings_count,
							"per_page" => $per_page,
						);		
		
						$response = array(
							'message' => 'Succesfully fetch history of bookings',
							"data" => array(
							  "pagination" => $pagination,
							  "bookings" => $catering_bookings
							),
						);
		
						header('content-type: application/json');
						echo json_encode($response);
						break;
				}
				break;
		}
	}

	public function popclub_redeems(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$per_page = $this->input->get('per_page') ?? 25;
				$page_no = $this->input->get('page_no') ?? 0;
				$order = $this->input->get('order') ?? 'desc';
				$order_by = $this->input->get('order_by') ?? 'dateadded';
				$search = $this->input->get('search');
				
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
						$popclub_redeems_count = $this->deals_model->getUserPopclubRedeemHistoryCount('facebook',$get_fb_user_details->id, $search);
						$popclub_redeems = $this->deals_model->getUserPopclubRedeemHistory('facebook',$get_fb_user_details->id, $page_no, $per_page, $order_by,  $order, $search);
						

						$pagination = array(
							"total_rows" => $popclub_redeems_count,
							"per_page" => $per_page,
						);		
		
						$response = array(
							'message' => 'Succesfully fetch history of orders',
							"data" => array(
							  "pagination" => $pagination,
							  "redeems" => $popclub_redeems
							),
						);
		
						header('content-type: application/json');
						echo json_encode($response);
						return;
					case 'mobile':
						$get_mobile_user_details = $this->user_model->get_mobile_user_details($_SESSION['userData']['mobile_user_id']);
						$popclub_redeems_count = $this->deals_model->getUserPopclubRedeemHistoryCount('mobile',$get_mobile_user_details->id, $search);
						$popclub_redeems = $this->deals_model->getUserPopclubRedeemHistory('mobile',$get_mobile_user_details->id, $page_no, $per_page, $order_by,  $order, $search);
						

						$pagination = array(
							"total_rows" => $popclub_redeems_count,
							"per_page" => $per_page,
						);		
		
						$response = array(
							'message' => 'Succesfully fetch history of orders',
							"data" => array(
							  "pagination" => $pagination,
							  "redeems" => $popclub_redeems
							),
						);
		
						header('content-type: application/json');
						echo json_encode($response);
						return;
				}


				return;

		}

	}
}