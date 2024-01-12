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

    // Construct data array (utility function for POST)
    function newInputData($keys, $data, $sales_id, $ref_id, $verdict, $table_key) {
        $result = array();
        $result['form_information_id'] = $sales_id;
        $result['user_ref_id'] = $ref_id;
        if($table_key === 'General Information'){
            $result['dateadded'] = date('Y-m-d H:i:s');

            if($verdict){
                $store_id = $this->sales_model->getSalesStoreIdBySalesId($sales_id);
                $result['store_id'] = $store_id->store_id;
            }


        }
        
        foreach ($keys as $key) {
            if (isset($data[$key]['value'])) {
                $result[$key] = $data[$key]['value'];
            }
        }
        return $result;
    }

    public function userTypeToId($type){
        switch($type){
            case 'cashier': 
                return 1;
            case 'tc':
                return 2;
            case 'manager':
                return 3;

            default: 
                return 0;
        }
        
    }

    // Get form data from all tables
    public function form_data() {
		switch($this->input->server('REQUEST_METHOD')) {
            case 'GET':

                $form_id = $this->input->get('id');

                // Populates response array only if no form_id is provided
                if (isset($form_id)) {
                    // Fetch form data
                    $cahier_form_data = $this->sales_model->selectFormsData($form_id, 1);
                    $tc_form_data = $this->sales_model->selectFormsData($form_id, 2);
                    $manager_form_data = $this->sales_model->selectFormsData($form_id, 3);

                    $response = array(
                        "message" => "Successfully fetched data!",
                        "data" => array(
                            "cashier_data" => $cahier_form_data,
                            "tc_data" => $tc_form_data,
                            "manager_data" => $manager_form_data
                        ),
                        
                    );
                }
                else {
                    $response = array("message" => "Missing form_id.", "data" => array());
                }

                header('content-type: application/json');
                echo json_encode($response);
        }
    }

	public function field(){
		switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

                $form_data = $this->sales_model->form_data();
                $discountType = $this->sales_model->discount_type();

                $storesIdByUserId = $this->admin_model->stores_by_user_id($user_id, $isAdmin);
                $stores = array();

                if (!empty($storesIdByUserId)) {
                    $stores = $this->admin_model->getStoreName($storesIdByUserId);
                }
                
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

            
            //THIS IS FOR SUBMITTING THE FORM WITHOUT BEING ON SAVED STATE
            case 'POST': 
                $_POST =  json_decode(file_get_contents("php://input"), true);

                $save_status = $this->input->post('saveStatus');
    
                // Data to insert into form_information
                $sales_information = array(
                    'user_id' => $this->session->admin['user_id'],
                    'save_status' => $save_status,
                    'tc_grade' => $save_status ? 0 : 3,
                    'manager_grade' => 0
                );

                $sales_id = $this->sales_model->insertSalesInformation($sales_information);

                $sales_ref_information = array(
                    'user_id' => $this->userTypeToId('cashier'),
                    'form_information_id' => $sales_id,
                    'process_id' => $save_status ? '1' : $this->userTypeToId('cashier') + 1,
                );

                $user_ref_id = $this->sales_model->insertSalesUserFormIdCombination($sales_ref_information);

                $table_names = array('form_general_information', 
                                    'form_payment_method', 
                                    'form_special_sales', 
                                    'form_discount', 
                                    'form_transactions', 
                                    'form_itemized_sales'
                                );

                $key_names = array('General Information', 'Payment Method', 'Special Sales', 'Discount', 'Transactions', 'Itemized Sales');
                        
             
                foreach(array_keys($this->input->post('formState')) as $key){
                    $column_names = array_keys($this->input->post('formState')[$key]);
                    if(isset($key)){
                        $index = 0;
                        foreach($key_names as $name){
                            if($name === $key){
                                $data = $this->newInputData($column_names, $this->input->post('formState')[$key], $sales_id, $user_ref_id, false, $name);
                                $this->sales_model->insertSalesData($table_names[$index], $data);
                            }
                            $index++;
                        }
                    }
                }
                
                if($save_status){
                    $this->realtime_fetch('cashier');
                }else{
                    $this->realtime_fetch('tc');
                }

                $response = array(
                    "message" => 'Form successfully submitted!',
                );
            
                header('content-type: application/json');
                echo json_encode($response);
                return;

                break;

		}
	}


    public function save_form(){

        switch($this->input->server('REQUEST_METHOD')){

            case 'GET':
                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

                $form =  $this->sales_model->get_saved_form($user_id);

                $response = array(
                    "message" => 'Successfully fetch saved form',
                    "data" => array(
                     'saved_form' => $form,
                    ),
                    );
            
                    header('content-type: application/json');
                    echo json_encode($response);
                    return;
            break;
            
            

		}
    }


    public function tc_task(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

                $task =  $this->sales_model->tc_task();;

                $response = array(
                    "message" => 'Successfully fetch Team Captain Task\'s data',
                    "data" => array(
                     'task' => $task,
                    ),
                    );
            
                    header('content-type: application/json');
                    echo json_encode($response);
                    return;
            break;

            case 'POST': 
                $_POST =  json_decode(file_get_contents("php://input"), true); 

                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();
                $grade = $this->input->post('grade');
                $sales_id = $this->input->post('id');

                $table_names = array('form_general_information', 
                                    'form_payment_method', 
                                    'form_special_sales', 
                                    'form_discount', 
                                    'form_transactions', 
                                    'form_itemized_sales'
                                );

                $form_info_data = array(
                    'tc_user_id' => $user_id,
                    'tc_grade' => $grade,
                    'manager_grade' => '3'
                );

                $this->sales_model->updateForm('form_information', $sales_id, $form_info_data);

                $index = 0;
                foreach(array_keys($this->input->post('formState')) as $key){
                    $column_names = array_keys($this->input->post('formState')[$key]);
                    if(isset($key)){
                        $data = $this->newInputData($column_names, $this->input->post('formState')[$key], $sales_id, true, $key);
                        $this->sales_model->updateForm($table_names[$index], $sales_id, $data);
                    }
                    $index++;
                }


                $response = array(
                "message" => 'Form successfully submitted!',
                );
        
                header('content-type: application/json');
                echo json_encode($response);
                return;

                break;
        }
    }

    public function manager_task(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

                $task =  $this->sales_model->manager_task();

                $response = array(
                    "message" => 'Successfully fetch Manager\'s Task data',
                    "data" => array(
                     'task' => $task,
                    ),
                    );
            
                header('content-type: application/json');
                echo json_encode($response);
                return;
            break;

            
        }
    }

    public function cashier_saved_forms(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

                $saved_forms =  $this->sales_model->get_saved_form($user_id);


                $response = array(
                    "message" => 'Successfully fetch all saved forms',
                    "data" => array(
                     'saved_forms' => $saved_forms,
                    ),
                    );


                header('content-type: application/json');
                echo json_encode($response);
                return;
            break;
        }

    }


    public function submit_verdict(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':

                case 'POST': 
                    $_POST =  json_decode(file_get_contents("php://input"), true); 
    
                    $user_id = $this->session->admin['user_id'];
                    $isAdmin = $this->ion_auth->is_admin();
                    $type = $this->input->post('type');
                    $grade = $this->input->post('grade');
                    $sales_id = $this->input->post('id');
    
                    $table_names = array('form_general_information', 
                                        'form_payment_method', 
                                        'form_special_sales', 
                                        'form_discount', 
                                        'form_transactions', 
                                        'form_itemized_sales'
                                    );

                    $key_names = array('General Information', 'Payment Method', 'Special Sales', 'Discount', 'Transactions', 'Itemized Sales');


                    $form_info_data = array();
                    if($type === 'tc'){
                        $form_info_data = array(
                            'tc_user_id' => $user_id,
                            'tc_grade' => $grade,
                            'manager_grade' => '3'
                        );
                    }else if($type === 'manager'){
                        $form_info_data = array(
                            'manager_user_id' => $user_id,
                            'manager_grade' => $grade,
                        );
                    }else if($type === 'cashier'){
                        $form_info_data = array(
                            'save_status' => '0',
                            'tc_grade' => '3',
                        );
                    } 
                    
                    $this->sales_model->updateForm('form_information', $sales_id, $form_info_data);


                    $sales_ref_information = array(
                        'user_id' => $this->userTypeToId($type),
                        'form_information_id' => $sales_id,
                        'process_id' => $this->userTypeToId($type) + 1,
                    );
    
                    $user_ref_id = $this->sales_model->insertSalesUserFormIdCombination($sales_ref_information);
    
                    foreach(array_keys($this->input->post('formState')) as $key){
                        $column_names = array_keys($this->input->post('formState')[$key]);
                        if(isset($key)){
                            $index = 0;
                            foreach($key_names as $name){
                                if($name === $key){
                                    $data = $this->newInputData($column_names, $this->input->post('formState')[$key], $sales_id, $user_ref_id, true, $name);
                                    $this->sales_model->insertSalesData($table_names[$index], $data);
                                }
                                $index++;
                            }
                        }
                    }
                    if($type === 'tc'){
                        $this->realtime_fetch('tc');
                        $this->realtime_fetch('manager');
                    }else if($type === 'manager'){
                        $this->realtime_fetch('manager');
                        $this->realtime_fetch('dashboard');
                    }else if($type === 'cashier'){
                        $this->realtime_fetch('cashier');
                        $this->realtime_fetch('tc');
                    } 
    
                    $response = array(
                        "message" => 'Form successfully submitted!',
                        );
                
                        header('content-type: application/json');
                        echo json_encode($response);
                        return;
    
                    break;
        }
    }

    public function dashboard(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

                $completed = $this->sales_model->completed();
                $completed_approved = $this->sales_model->count_completed_approved();
                $completed_not_approved = $this->sales_model->count_completed_not_approved();

                $response = array(
                    "message" => 'Successfully fetch all saved forms',
                    "data" => array(
                     'approved_count' => $completed_approved,
                     'not_approved_count' => $completed_not_approved,
                     'total_count' => $completed_approved + $completed_not_approved,
                     'completed' => $completed,
                    ),
                    );
            
                header('content-type: application/json');
                echo json_encode($response);
                return;
            break;
        }
    }


    public function realtime_fetch($type){
        $user_id = $this->session->admin['user_id'];
        $isAdmin = $this->ion_auth->is_admin();

        $store = $this->admin_model->stores_by_user_id($user_id, $isAdmin);

        $real_time_notification = array(
            "store_id" => $store,
            "message" => ""
        );
        
        switch($type){
            case 'cashier':  
                notify('admin-sales','sales-fetch-cashier', $real_time_notification);
            break;

            case 'tc': 
                notify('admin-sales','sales-fetch-tc', $real_time_notification);
            break;

            case 'manager': 
                notify('admin-sales','sales-fetch-manager', $real_time_notification);
            break;

            case 'dashboard': 
                notify('admin-sales','sales-fetch-dashboard', $real_time_notification);
            break;

            default:
            break;
        }


    }

    public function form_check_duplicate(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $entry_date = $this->input->get('entryDate');
                $store_id = $this->input->get('store_id');
                $shift = $this->input->get('shift');

                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();
            
                $trimmedDate = substr($entry_date, (strpos($entry_date, ' ') + 1), (strpos($entry_date, 'GMT') - 1) - (strpos($entry_date, ' ') + 1));

                $date = DateTime::createFromFormat("M d Y H:i:s", $trimmedDate);
                $formattedDate = $date->format("Y-m-d");


                $checkDuplicate = $this->sales_model->duplicate($formattedDate, $store_id, $shift);;

                $response = array(
                    "message" => 'Successfully fetch all saved forms',
                    "data" => array(
                     'duplicate' => $checkDuplicate
                    ),
                    );
            
                header('content-type: application/json');
                echo json_encode($response);
                return;

            break;
        }
    }



}