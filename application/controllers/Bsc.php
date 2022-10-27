<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Bsc extends CI_Controller
{
	public function __construct(){
		parent::__construct();

		if ($this->bsc_auth->logged_in() === false){
		  exit();
		}

		$this->load->model('store_model');
		$this->load->model('bsc_model');
	}

	
	public function users(){
		switch($this->input->server('REQUEST_METHOD')){
		  case 'GET': 
			$per_page = $this->input->get('per_page') ?? 25;
			$page_no = $this->input->get('page_no') ?? 0;
			$order = $this->input->get('order') ?? 'asc';
			$order_by = $this->input->get('order_by') ?? 'id';
			$search = $this->input->get('search');
	
			if($page_no != 0){
			  $page_no = ($page_no - 1) * $per_page;
			}
			
			$users_count =  $this->bsc_model->getUsersCount($search);
			$users = $this->bsc_model->getUsers($page_no, $per_page, $order_by, $order, $search);
	
			foreach($users as $user){
			  $user->groups = $this->bsc_model->getUserGroups($user->id);
			}
	
			$pagination = array(
			  "total_rows" => $users_count,
			  "per_page" => $per_page,
			);
	
			$response = array(
			  "message" => 'Successfully fetch snackshop orders',
			  "data" => array(
				"pagination" => $pagination,
				"users" => $users
			  ),
			);
			header('content-type: application/json');
			echo json_encode($response);
			return;
		}
	
	  }

	public function session(){
		switch($this->input->server('REQUEST_METHOD')){
		  case 'GET':

			$user_profile =  $this->bsc_model->getUserProfile($this->session->bsc['user_id']);
	
			$data = array(
				"bsc" => array(
					"identity" => $this->session->bsc['identity'],
					"email" => $this->session->bsc['email'],
					"user_id" => $this->session->bsc['user_id'],
					"old_last_login" => $this->session->bsc['old_last_login'],
					"last_check" => $this->session->bsc['last_check'],
					"is_bsc" => $this->bsc_auth->in_group(1),
					"user_status_id" => $user_profile->user_status_id,
				)
			);

			$response = array(
			  "message" => 'Successfully fetch bsc session',
			  "data" => $data,
			);
	  
			header('content-type: application/json');
			echo json_encode($response);
			return;
		}
	}
}