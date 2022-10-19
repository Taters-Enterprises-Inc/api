<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Profile extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('shop_model');
		$this->load->model('catering_model');
		$this->load->model('deals_model');
		$this->load->model('user_model');
		$this->load->model('contact_model');
		$this->load->model('discount_model');

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->ion_auth->set_message_delimiters('', '');
		$this->ion_auth->set_error_delimiters('', '');

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
					return;
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
						return;
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
							  "orders" => $snackshop_orders
							),
						);
		
						header('content-type: application/json');
						echo json_encode($response);
						return;
				}

				return;
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
						return;
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
						return;
				}
				return;
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

	public function update_user_discount(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$config['upload_path'] = './assets/upload/user_discount'; 
				
				if(!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, TRUE);
				
				$config['allowed_types']    = 'gif|png|jpg|jpeg'; 
				$config['max_size']         = 2000;
				$config['max_width']        = 0;
				$config['max_height']       = 0;
				$config['encrypt_name']     = TRUE;

				$this->load->library('upload', $config);

				
				$user_discount_data = array(
					'first_name' => $_POST['firstName'],
					'middle_name' => $_POST['middleName'],	
					'last_name' => $_POST['lastName'],
					'birthday' => $_POST['birthday'],
					'id_number' => $_POST['idNumber'],
					'dateadded' => date('Y-m-d H:i:s'),
					'discount_type_id' => $_POST['discountTypeId'],
					'fb_user_id' => $this->session->userData['fb_user_id'] ?? null,
					'mobile_user_id' => $this->session->userData['mobile_user_id'] ?? null,
					'status' => 1
				);

				$old_user_discount = $this->discount_model->getUserDiscountById($_POST['id']);


				if($this->upload->do_upload('idFront')){
					$id_front_data = $this->upload->data();
					unlink(  FCPATH . "assets/upload/user_discount/" . $old_user_discount->id_front);
					$user_discount_data['id_front'] = $id_front_data['file_name'];
				}
				
				if($this->upload->do_upload('idBack')){
					$id_back_data = $this->upload->data();
					unlink(  FCPATH . "assets/upload/user_discount/" . $old_user_discount->id_back);
					$user_discount_data['id_back'] = $id_back_data['file_name'];
				}
					
				$this->discount_model->updateDiscountUser($_POST['id'], $user_discount_data);

				$response = array(
					"message" => 'Edit application successfull'
				);
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function user_discount(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':


				$discount = $this->discount_model->getUserDiscount(
					$this->session->userData['fb_user_id'] ?? null,
					$this->session->userData['mobile_user_id'] ?? null
				);

				$response = array(
					"message" => 'Successfully fetch user discount',
					"data" => $discount,
				);
				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'POST':
				if(
					is_uploaded_file($_FILES['idFront']['tmp_name']) &&
					is_uploaded_file($_FILES['idBack']['tmp_name'])
				){
					$config['upload_path'] = './assets/upload/user_discount'; 
					
					if(!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, TRUE);
					
					$config['allowed_types']    = 'gif|png|jpg|jpeg'; 
					$config['max_size']         = 2000;
					$config['max_width']        = 0;
					$config['max_height']       = 0;
					$config['encrypt_name']     = TRUE;

					$this->load->library('upload', $config);

					$id_front_data = null;
					$id_back_data = null;

					if($this->upload->do_upload('idFront')){
						$id_front_data = $this->upload->data();
					}
					
					if($this->upload->do_upload('idBack')){
						$id_back_data = $this->upload->data();
					}

						
					$user_discount_data = array(
						'first_name' => $_POST['firstName'],
						'middle_name' => $_POST['middleName'],	
						'last_name' => $_POST['lastName'],
						'birthday' => $_POST['birthday'],
						'id_number' => $_POST['idNumber'],
						'id_front' => $id_front_data['file_name'],
						'id_back' => $id_back_data['file_name'],
						'dateadded' => date('Y-m-d H:i:s'),
						'discount_type_id' => $_POST['discountTypeId'],
						'fb_user_id' => $this->session->userData['fb_user_id'] ?? null,
						'mobile_user_id' => $this->session->userData['mobile_user_id'] ?? null,
						'status' => 1
					);

					$this->discount_model->insertDiscountUser($user_discount_data);

					$response = array(
						"message" => 'Application for user discount is successful!'
					);
					header('content-type: application/json');
					echo json_encode($response);
				}else{
					$this->output->set_status_header('401');
					echo json_encode(array( "message" => 'Application for user discount failed.'));
				}
				break;

			case 'PUT':

				break;
		}
		
	}
}