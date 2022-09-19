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
    $this->load->library('form_validation');
  }
  

  private function send_sms($to, $code, $type)
  {
    require FCPATH . 'vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
    $dotenv->load();

    $api_key = $_ENV['SMS_API_KEY'];
    $api_sec =  $_ENV['SMS_API_SEC'];
    $sender_name = $_ENV['SMS_SENDER_NAME'];

    switch ($type) {
      case 'temp_pass';
        $text = 'Congratulations your registration was successful! please use this temporary password to access your account ' . $code;
        break;
      case 'pass_reset';
        $text = 'Please dont share this to anyone, your OTP for password reset is ' . $code;
        break;
    }

    $new_text = urlencode($text);

    $url = 'https://rest-portal.promotexter.com/sms/send?apiKey=' . $api_key . '&apiSecret=' . $api_sec . '&from=' . $sender_name . '&to=' . $to . '&text=' . $new_text;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      $msg = "Error: " . curl_error($ch);
      $status = FALSE;
    } else {
      $jsonArrayResponse = json_decode($result, TRUE);
      curl_close($ch);
      $status = ($jsonArrayResponse['status'] == 'ok') ? TRUE : FALSE;
      $msg = ($status) ? "Sending Successful" : "Sending Failed";
    }

    // header('content-type: application/json');
    // echo json_encode(array("status"=>$status,'message' => $msg));

    // return $status;
  }
  
  // handle mobile user logins (for normal customers and store staffs)
  public function login_mobile_user()
  {
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('phoneNumber', 'Mobile Number', 'required|regex_match[/^[0-9]{11}$/]');
        $this->form_validation->set_rules('login_password', 'Password', 'required');
        
      if ($this->form_validation->run() === TRUE) { 
        $mobile_number = $_POST['phoneNumber'];

        $query_result = $this->mobile_users_model->verify_login($mobile_number);
    
        // if $query_result has contents
        if (!empty($query_result)) {
          if (password_verify($_POST['login_password'], $query_result[0]->password)) {
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
      } else {
        $message = "";

        foreach ($_POST as $key => $value) {
          if ($key !== 'form_action') {
            $message = $message . form_error($key);
          }
        }

        $this->output->set_status_header('401');
        header('content-type: application/json');
        $output = array(
          "message"    =>  $message
        );
  
        echo json_encode($output);
        return;
      }
    }
  }

  public function registration(){
    switch($this->input->server("REQUEST_METHOD")){
      case 'POST':
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('firstName', 'First Name', 'required|min_length[1]|max_length[20]|trim');
        $this->form_validation->set_rules('lastName', 'Last Name', 'required|min_length[1]|max_length[20]|trim');
        $this->form_validation->set_rules('phoneNumber', 'Mobile Number', 'required|is_unique[mobile_users.username]', array(
          'is_unique'  => '{field} already registered, please try different number.'
        ));
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');

    
        if ($this->form_validation->run() == FALSE) {
          $message = "";

          foreach ($_POST as $key => $value) {
            if ($key !== 'form_action') {
              $message = $message . form_error($key);
            }
          }

          $this->output->set_status_header('401');
          header('content-type: application/json');
          $output = array(
            "message"    =>  $message
          );
          echo json_encode($output);
          return;
        } else {
          $temp_password = substr(md5(uniqid(mt_rand(), true)), 0, 8);
          if ($this->mobile_users_model->registration($_POST, $temp_password) == true) {
            $this->send_sms($_POST['phoneNumber'], $temp_password, 'temp_pass');
          }
          
          header('content-type: application/json');
          $output = array(
            "message"    =>  'Successfully registered user!',
          );
          echo json_encode($output);
          return;
        }
        break;
    }
  }
  
  public function mobile_generate_forgot_pass_code()
  {
    switch($this->input->server("REQUEST_METHOD")){
      case 'POST':
        $this->form_validation->set_rules('phoneNumber', 'Mobile number', 'required|regex_match[/^[0-9]{11}$/]');
        
        if ($this->form_validation->run() === TRUE) {
          
          $mobile_number = $_POST['phoneNumber'];
          $check_if_mobile_exist = $this->mobile_users_model->verify_login($mobile_number);

          if (!empty($check_if_mobile_exist)) {

            $forgot_password_code_validity  = strtotime($check_if_mobile_exist[0]->forgot_password_time);
            $current_time                   = strtotime(date('Y-m-d H:i:s'));

            if ($current_time > $forgot_password_code_validity) {

              $mobile_user_id           = $check_if_mobile_exist[0]->id;
              $code                     = substr(md5(uniqid(mt_rand(), true)), 0, 8);
              $code_validity            = date('Y-m-d H:i:s', strtotime("+15 minutes"));
              $set_password_reset_code  = $this->mobile_users_model->generate_forgot_password_code($mobile_user_id, $code, $code_validity);
              //check if reset code is set
              if ($set_password_reset_code == true) {
                $this->send_sms($mobile_number, $code, 'pass_reset');
                
                header('content-type: application/json');
                $response = array(
                  "message"    =>  'forgot password code successfully generated'
                );
          
                echo json_encode($response);
              } else {
                $this->output->set_status_header('401');
                header('content-type: application/json');
                $response = array(
                  "message"    =>  'an error occured while generating forgot password code'
                );
                echo json_encode($response);
                return;
              }
            } else {
              $remaining_time = $forgot_password_code_validity - $current_time;
              $interval = date('i', $remaining_time);
              $status = 'error';
              $message = "You still have an active password reset code, Please try again later - Time remaining: $interval minute(s)";
              
              $this->output->set_status_header('401');
              header('content-type: application/json');
              $response = array(
                "message"    =>  $message
              );
              echo json_encode($response);
              return;
            }

          } else {
            $message = 'mobile number not registered';

            $this->output->set_status_header('401');
            header('content-type: application/json');
            $response = array(
              "message"    =>  $message
            );
            echo json_encode($response);
            return;
          }
        } else {
          $message = "";

          foreach ($_POST as $key => $value) {
            if ($key !== 'form_action') {
              $message = $message . form_error($key);
            }
          }
          
          $this->output->set_status_header('401');
          header('content-type: application/json');
          $output = array(
            "message"    =>  $message
          );
    
          echo json_encode($output);
          return;
        }
    }
  }

  
  public function validate_otp_code()
  {
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
          $mobile_number    = $_POST['phoneNumber'];
          $otp_code         = $_POST['otpCode'];
      
          $mobile_user_details            = $this->mobile_users_model->verify_login($mobile_number);
          $forgot_password_code_validity  = strtotime($mobile_user_details[0]->forgot_password_time);
          $current_time                   = strtotime(date('Y-m-d H:i:s'));
          $valid_otp_code                 = $mobile_user_details[0]->forgot_password_code;
      
          if ($current_time < $forgot_password_code_validity) {
            if ($valid_otp_code == $otp_code) {
              
      
              header('content-type: application/json');
              $response = array(
                'message' =>'OTP verification completed!'
              );
              echo json_encode($response);
              return;
            } else {
              
              $this->output->set_status_header('401');
              header('content-type: application/json');
              $response = array(
                'message' =>'OTP does not match!'
              );
              echo json_encode($response);
              return;
            }
          } else {
            $this->output->set_status_header('401');
            header('content-type: application/json');
            $response = array(
              'message' =>'OTP expired!'
            );
            echo json_encode($response);
            return;
          }
        break;
    }
  }
  
  public function change_password()
  {
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
        $this->form_validation->set_rules('newPassword', 'New Password', 'trim|required|min_length[6]|max_length[20]');
        $this->form_validation->set_rules('confirmNewPassword', 'Confirm Password', 'trim|required|min_length[6]|max_length[20]|matches[newPassword]', array(
          'matches'  => "{field} doesn't match"
        ));
    
        $message = array();
        if ($this->form_validation->run() === TRUE) {
    
          foreach ($_POST as $key => $value) {
            if ($key !== 'form_action') {
              $message[$key] = form_error($key);
            }
          }
          $mobile_number    = $_POST['phoneNumber'];
          $new_pass         = $_POST['newPassword'];
    
          $new_password = password_hash($new_pass, PASSWORD_DEFAULT);
          $reset_pass   = $this->mobile_users_model->reset_password($mobile_number, $new_password);
          if ($reset_pass == true) {
            header('content-type: application/json');
            $response = array(
              'message' => 'Reset password success'
            );
            echo json_encode($response);
            return;
          } else {
            $this->output->set_status_header('401');
            header('content-type: application/json');
            $response = array(
              'message' => 'Reset password fails'
            );
            echo json_encode($response);
            return;
          }
        } else {
          $message = "";

          foreach ($_POST as $key => $value) {
            if ($key !== 'form_action') {
              $message = $message . form_error($key);
            }
          }
          
          $this->output->set_status_header('401');
          header('content-type: application/json');
          $output = array(
            "message"    =>  $message
          );
    
          echo json_encode($output);
          return;
        }
      break;
    }
  }
}
