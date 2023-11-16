<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Sales extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('sales_model');
        $this->load->model('admin_model');

	}
	
	public function get_fields(){
		switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

                $form_data = $this->sales_model->form_data();
                $discountType = $this->sales_model->discount_type();

                $storesIdByUserId = $this->admin_model->stores_by_user_id($user_id, $isAdmin);

                $stores = $this->admin_model->getStoreName($storesIdByUserId);


                $response = array(
                    "message" => 'Successfully fetch field data',
                    "data" => array(
                     'field_data' => $form_data,
                     'discount_type' => $discountType,
                     'list_of_stores' => $stores,
                    ),
                    );
            
                    header('content-type: application/json');
                    echo json_encode($response);
                    return;
            break;

		}
	}


}