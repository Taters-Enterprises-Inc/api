<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Admin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

    if (!$this->ion_auth->logged_in()){
      $this->output->set_status_header('401');
      header('content-type: application/json');
      echo json_encode(array("message" => 'Unauthorized user'));
      exit();
    }


		$this->load->helper('url');
		$this->load->model('admin_model');
	}

  public function shop(){
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET':
        $per_page = $this->input->get('per_page') ?? 5;
        $page_no = $this->input->get('page_no') ?? 0;
        $status = $this->input->get('status') ?? null;

        if($page_no != 0){
          $page_no = ($page_no - 1) * $per_page;
        }

        $orders_count = $this->admin_model->getSnackshopOrdersCount($status);
        $orders = $this->admin_model->getSnackshopOrders($page_no, $per_page, $status);

        $pagination = array(
          "total_rows" => $orders_count,
          "per_page" => $per_page,
        );

        $response = array(
          "message" => 'Successfully fetch snackshop orders',
          "data" => array(
            "pagination" => $pagination,
            "orders" => $orders
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
        $data = array(
          "identity" => $this->session->identity,
          "email" => $this->session->email,
          "user_id" => $this->session->user_id,
          "old_last_login" => $this->session->old_last_login,
          "last_check" => $this->session->last_check,
        );
  
        $response = array(
          "message" => 'Successfully fetch admin session',
          "data" => $data,
        );
  
        header('content-type: application/json');
        echo json_encode($response);
        return;
    }
  }

}