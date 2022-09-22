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

    if ($this->ion_auth->logged_in() === false){
      $this->output->set_status_header(401);
      header('content-type: application/json');
      echo json_encode(array("message" => 'Unauthorized user'));
      exit();
    }


		$this->load->helper('url');
		$this->load->model('admin_model');
		$this->load->model('user_model');
	}

  public function admin_privilege(){
    switch($this->input->server('REQUEST_METHOD')){
      case 'POST': 

        $password = $this->input->post('password');
        
        $transaction_id = $this->input->post('trans_id');

        $store_id = $this->input->post('store_id');
        $status = $this->input->post('status');
        
        $request = isset($store_id) ? 'store_transfer' : (isset($status) ? 'change_status' : null);
        
        $fetch_data = $this->admin_model->check_admin_password(
          $request,
          $password,
          $transaction_id,
          $store_id,
          $status
        );
            
        if ($fetch_data == 1) {
          header('content-type: application/json');
          echo json_encode(array( "message" => "Update success!"));
        }else{
          $this->output->set_status_header('401');
          echo json_encode(array( "message" => $fetch_data));
        }

        return;
    }
  }

  public function shop_update_status()
  {
    switch($this->input->server('REQUEST_METHOD')){
      case 'POST': 
            $trans_id = (int) $this->input->post('trans_id');
            $status = $this->input->post('status');
            $fetch_data = $this->admin_model->update_status($trans_id, $status);

            $update_on_click = $this->admin_model->update_on_click($trans_id, $_POST['status']);
            if ($status == 3) $generate_invoice = $this->admin_model->generate_invoice_num($trans_id);

            if ($status == 3) $tagname = "Confirm";
            elseif ($status == 4) $tagname = "Declined";
            elseif ($status == 6) $tagname = "Complete";
            elseif ($status == 7) $tagname = "Reject";
            elseif ($status == 8) $tagname = "Prepare";
            elseif ($status == 9) $tagname = "Dispatched";

            if ($fetch_data == 1) {
              header('content-type: application/json');
              echo json_encode(array( "message" => 'Successfully update status!'));
            } else {
              $this->output->set_status_header('401');
              echo json_encode(array( "message" => 'Failed update status!'));
            }
          return;
    }
  }
  
  public function reference_num()
  {
    $trans_id = $this->input->post('trans_id');
    $ref_num = $this->input->post('ref_num');
    $fetch_data = $this->admin_model->validate_ref_num($trans_id, $ref_num);

    if ($fetch_data == 1) {
      header('content-type: application/json');
      echo json_encode(array( "message" => 'Validation successful'));
    } else {
      $this->output->set_status_header('401');
      echo json_encode(array( "message" => 'Invalid Reference number'));
    }
  }


  
  public function payment()
  {
    switch($this->input->server('REQUEST_METHOD')){
      case 'POST': 

          $config['upload_path']          = './assets/upload/proof_payment/';
          $config['allowed_types']        = 'jpeg|jpg|png';
          $config['max_size']             = 2000;
          $config['max_width']            = 0;
          $config['max_height']           = 0;
          $config['encrypt_name']         = TRUE;

          $this->load->library('upload', $config);

          $trans_id = $_POST['trans_id'];

          if (!$this->upload->do_upload('payment_file')) { // Upload validation
            // Failed-Upload
            $error = $this->upload->display_errors();
            $this->output->set_status_header('401');
            echo json_encode(array( "message" => $error));
          } else {
            // File-Uploaded-Successfull
            $data = $this->upload->data(); // Get file details
            $file_name = $data['file_name'];

            $this->admin_model->uploadPayment($trans_id, $data, $file_name);

            header('content-type: application/json');
            echo json_encode(array( "message" => 'Succesfully upload payment'));
          }

        break;
    }
  }

  public function stores(){
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET': 
        $user_id = $this->input->get('user_id');

        if($user_id){
          $stores =  $this->user_model->get_store_group_order_set($user_id);
        }else{
          $stores = $this->admin_model->getStores();
        }

        $response = array(
          "message" => 'Successfully fetch user stores',
          "data" => $stores,
        );

        header('content-type: application/json');
        echo json_encode($response);
        return;
        
      case 'POST': 
				$stores = json_decode($_POST['stores'], true);

        $this->user_model->add_store_group($_POST['user_id'],$stores);

        $response = array(
          "message" => 'Successfully update user stores',
        );

        header('content-type: application/json');
        echo json_encode($response);
        return;
    }
  }

  public function groups(){
    
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET': 

        $groups =  $this->admin_model->getGroups();

        $response = array(
          "message" => 'Successfully fetch snackshop user',
          "data" => $groups,
        );

        header('content-type: application/json');
        echo json_encode($response);
        return;
    }
  }

  public function user($user_id){
    
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET': 

        $user =  $this->admin_model->getUser($user_id);
        $user->groups = $this->admin_model->getUserGroups($user->id);

        $response = array(
          "message" => 'Successfully fetch snackshop user',
          "data" => $user,
        );

        header('content-type: application/json');
        echo json_encode($response);
        return;
    }
  }

  public function users(){
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET': 
        $per_page = $this->input->get('per_page') ?? 25;
        $page_no = $this->input->get('page_no') ?? 0;
        $order = $this->input->get('order') ?? 'asc';
        $order_by = $this->input->get('order_by') ?? 'id';
        $search = $this->input->get('search');
        
        $users_count =  $this->admin_model->getUsersCount($search);
        $users = $this->admin_model->getUsers($page_no, $per_page, $order_by, $order, $search);

        foreach($users as $user){
          $user->groups = $this->admin_model->getUserGroups($user->id);
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

  public function shop_order($trackingNo){
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET': 
        $order = $this->admin_model->getSnackshopOrder($trackingNo);
        $order->items = $this->admin_model->getSnackshopOrderItems($order->id);

        $response = array(
          "message" => 'Successfully fetch snackshop order',
          "data" => $order,
        );
  
        header('content-type: application/json');
        echo json_encode($response);
        return;
    }
  }

  public function shop(){
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET':
        $per_page = $this->input->get('per_page') ?? 25;
        $page_no = $this->input->get('page_no') ?? 0;
        $status = $this->input->get('status') ?? null;
        $order = $this->input->get('order') ?? 'desc';
        $order_by = $this->input->get('order_by') ?? 'dateadded';
        $search = $this->input->get('search');

        if($page_no != 0){
          $page_no = ($page_no - 1) * $per_page;
        }

        $orders_count = $this->admin_model->getSnackshopOrdersCount($status, $search);
        $orders = $this->admin_model->getSnackshopOrders($page_no, $per_page, $status, $order_by, $order, $search);

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

  public function popclub_complete_redeem($redeemCode){
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET': 
        $this->admin_model->completeRedeem($redeemCode);

        $response = array(
          "message" => 'Successfully completed the redeem',
        );
  
        header('content-type: application/json');
        echo json_encode($response);
        return;
    }
  }

  public function popclub_redeem($redeemCode){
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET': 
        $redeem = $this->admin_model->getPopclubRedeem($redeemCode);
        $redeem->items = $this->admin_model->getPopclubRedeemItems($redeem->id);

        $response = array(
          "message" => 'Successfully fetch popclub redeem',
          "data" => $redeem,
        );
  
        header('content-type: application/json');
        echo json_encode($response);
        return;
    }
  }

  public function popclub(){
    switch($this->input->server('REQUEST_METHOD')){
      case 'GET':
        $per_page = $this->input->get('per_page') ?? 25;
        $page_no = $this->input->get('page_no') ?? 0;
        $status = $this->input->get('status') ?? null;
        $order = $this->input->get('order') ?? 'desc';
        $order_by = $this->input->get('order_by') ?? 'dateadded';
        $search = $this->input->get('search');

        if($page_no != 0){
          $page_no = ($page_no - 1) * $per_page;
        }

        $orders_count = $this->admin_model->getPopclubRedeemsCount($status, $search);
        $orders = $this->admin_model->getPopclubRedeems($page_no, $per_page, $status, $order_by, $order, $search);

        $pagination = array(
          "total_rows" => $orders_count,
          "per_page" => $per_page,
        );

        $response = array(
          "message" => 'Successfully fetch popclub redeems',
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

        if(!isset($this->session->user_details)){
          $data['user_details'] = $this->admin_model->getUser($this->session->user_id);
          $data['user_details']->groups = $this->admin_model->getUserGroups($this->session->user_id);
        }
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