<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');


class Mobile_users extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('mobile_users_model');
  }
  
  // handle mobile user logins (for normal customers and store staffs)
  public function login_mobile_user()
  {
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
        $mobile_number = $post['mobile_num'];
        $query_result = $this->mobile_users_model->verify_login($mobile_number);
  
        // if $query_result has contents
        if (!empty($query_result)) {
          if (password_verify($post['login_password'], $query_result[0]->password)) {
            $validation_status = 'success';
            $user_data = array();
            $user_data['login_type']      = 'mobile';
            $user_data['mobile_user_id']  = $query_result[0]->id;
            $user_data['first_name']      = $query_result[0]->first_name;
            $user_data['last_name']       = $query_result[0]->last_name;
            $user_data['email']           = $query_result[0]->email;
            $user_data['mobile_number']   = $query_result[0]->phone;
  
            // for mobile users as store staff
            $store_user_id = $query_result[0]->store_user_id;
  
            if ($store_user_id) {  
              $user_data['store_user_id'] = $store_user_id;
              $store_name_and_region_id = $this->store_model->get_store_name_and_region_id($store_user_id);
              $region_id = $store_name_and_region_id[0]->region_id;
  
              // set timezone to PST
              date_default_timezone_set('Asia/Manila');
              $hash_key = md5(date('Y-m-d H:i:s'));
      
              $region = $this->product_model->select_region($region_id);
              foreach ($region as $key => $value) $region_name = $value->name;
      
              $opening = $this->product_model->get_store_schedule($store_user_id);
              foreach ($opening as $key => $value) {
                  $store_opening = $value->opening;
                  $store_closing = $value->closing;
                  $store_menu_type = $value->menu_type;
              }
      
              set_cookie('__teisid', $hash_key, '86400');
              $now = date("h:i A");
  
              if ($store_user_id == 107) $arr = array('11','12','15');
              else $arr = array();
  
              // check surcharge if enabled
              $check_surcharge = $this->store_model->check_surcharge($store_user_id);
              $surcharge = $check_surcharge[0]->enable_surcharge;
              $surcharge_delivery_rate = $check_surcharge[0]->surcharge_delivery_rate;
              $surcharge_minimum_rate = $check_surcharge[0]->surcharge_minimum_rate;
              $cache_data = array(
                'store_id'          => $store_user_id,
                'region_id'         => $region_id,
                'municipality_id'   => "",
                'client_id'         => 0,
                'distance'          => 0,
                'address'           => "",
                'region_name'       =>  $region_name,
                'municipality_name' => "",
                'hash_key'          => $hash_key,
                'opening'           => $store_opening,
                'closing'           => $store_closing,
                'store_menu_type'   => $store_menu_type,
                'start_order'       => $now,
                'hidden_category'   => $arr,
                'surcharge_delivery_rate' => $surcharge_delivery_rate,
                'surcharge_minimum_rate'  => $surcharge_minimum_rate,
                'surcharge'         => $surcharge
              );
      
              $this->session->set_userdata('cache_data', $cache_data);
              $_SESSION['customer_address'] = $store_name_and_region_id[0]->name;
              $_SESSION['moh'] = 1;   // '1' signifies mode of handling (moh) as 'pick-up'
  
              $store_option = $this->store_model->fetch_store_option();
              $this->session->set_userdata('store_option', $store_option);
            }
  
            // set user's data on current session
            $this->session->set_userdata('userData', $user_data);
          } 
          else {
            $this->output->set_status_header('401');
            echo json_encode(array( "message" => 'Incorrect Password'));
            return;
          }
        } 
        else {
          $this->output->set_status_header('401');
          echo json_encode(array( "message" => 'The Mobile Number is not in the database'));
          return;
        }
  
        header('content-type: application/json');
        $response = array(
          "message"    =>  'Successfully sign in mobile user'
        );
  
        echo json_encode($response);
        return;
    }
  }
}
