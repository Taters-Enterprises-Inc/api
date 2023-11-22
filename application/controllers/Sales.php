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
    function newInputData($keys, $data, $sales_id) {
        $result = array();
        $result['form_information_id'] = $sales_id;
        foreach ($keys as $key) {
            if (isset($data[$key]['value'])) {
                $result[$key] = $data[$key]['value'];
            }
        }
        return $result;
    }

    // Get form data from all tables
    public function form_data() {
		switch($this->input->server('REQUEST_METHOD')) {
            case 'GET':

                $form_id = $this->input->get('id');

                // Populates response array only if no form_id is provided
                if (isset($form_id)) {
                    // Fetch form data
                    $form_data = $this->sales_model->selectFormsData($form_id);
                    
                    $response = array(
                        "message" => "Successfully fetched data!",
                        "data" => $form_data,
                        
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
                
                // Insert into form_information, save return id for later
                $sales_id = $this->sales_model->insertSalesInformation($sales_information);
                
                // Prepare and insert data for form_general_information
                if(isset($this->input->post('formState')['General Information'])){
                    $general_information = $this->newInputData(array('entry_date', 'store', 'shift', 'cashier_name', 'email', 'declared_cash', 'calculated_cash', 'transaction_date', 'cash_deposit', 'other_deposit'), $this->input->post('formState')['General Information'], $sales_id);
                    $this->sales_model->insertSalesData('form_general_information', $general_information);
                }

                // Prepare and insert data for form_payment_method
                if(isset($this->input->post('formState')['Payment Method'])){
                    $payment_method = $this->newInputData(array('id', 'form_information_id', 'credit_card_sales', 'credit_card_change', 'cr_memo', 'gcash', 'paymaya', 'shopeepay', 'gc', 'century_shopaholic_vouchers', 'metrodeal', 'grab', 'foodpandaAR', 'lazada', 'shopee', 'booky', 'foodtrip', 'parahero', 'eatigo', 'madison', 'zalora', 'metromart', 'rare_food_shop', 'pickaroo', 'honestbee', 'sharetreats', 'vip', 'vip_sold', 'marketingAR', 'sm_online', 'other_sm_events'), $this->input->post('formState')['Payment Method'], $sales_id);
                    $this->sales_model->insertSalesData('form_payment_method', $payment_method);
                }

                // Prepare and insert data for form_special_sales
                if(isset($this->input->post('formState')['Special Sales'])){
                    $special_sales = $this->newInputData(array('id', 'form_information_id', 'bulk_whole_sale', 'others', 'catering', 'offsite_selling', 'reseller', 'snackshop', 'cart_sales', 'delivery_fee', 'consignment'), $this->input->post('formState')['Special Sales'], $sales_id);
                    $this->sales_model->insertSalesData('form_special_sales', $special_sales);
                }

                // Prepare and insert data for form_discount
                if(isset($this->input->post('formState')['Discount'])){
                    $discount_type = $this->newInputData(array('id', 'form_information_id', 'discount_id'), $this->input->post('formState')['Discount'], $sales_id);
                    $this->sales_model->insertSalesData('form_discount', $discount_type);
                }

                // Prepare and insert data for form_transactions
                if(isset($this->input->post('formState')['Transactions'])){
                    $transactions = $this->newInputData(array('id', 'form_information_id', 'transaction_count', 'originating_store', 'terminal_id', 'voids', 'serial_number'), $this->input->post('formState')['Transactions'], $sales_id);
                    $this->sales_model->insertSalesData('form_transactions', $transactions);
                }

                // Prepare and insert data for form_transactions
                if(isset($this->input->post('formState')['Itemized Sales'])){
                    $itemized_sales = $this->newInputData(array('id', 'form_information_id', 'offsite_selling', 'catering', 'delivery'), $this->input->post('formState')['Itemized sales'], $sales_id);
                    $this->sales_model->insertSalesData('form_itemized_sales', $itemized_sales);
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
            
            case 'PATCH': 

                $data =  json_decode(file_get_contents("php://input"), true);

                $sales_id = $data['id'];

                $sales_information = array(
                    'save_status' => 0, //update saved state
                    'tc_grade' => 3, //updated
                );

                $this->sales_model->updateForm('form_information', $sales_id,$sales_information);

                $general_information = $this->newInputData(array('entry_date', 'store', 'shift', 'cashier_name', 'email', 'declared_cash', 'calculated_cash', 'transaction_date', 'cash_deposit', 'other_deposit'), $data['General Information'], $sales_id);
                $this->sales_model->updateForm('form_general_information', $sales_id, $general_information);

                $payment_method = $this->newInputData(array('id', 'form_information_id', 'credit_card_sales', 'credit_card_change', 'cr_memo', 'gcash', 'paymaya', 'shopeepay', 'gc', 'century_shopaholic_vouchers', 'metrodeal', 'grab', 'foodpandaAR', 'lazada', 'shopee', 'booky', 'foodtrip', 'parahero', 'eatigo', 'madison', 'zalora', 'metromart', 'rare_food_shop', 'pickaroo', 'honestbee', 'sharetreats', 'vip', 'vip_sold', 'marketingAR', 'sm_online', 'other_sm_events'), $this->input->post('Payment Method'), $sales_id);
                $this->sales_model->updateForm('form_payment_method',$sales_id, $payment_method);

                $special_sales = $this->newInputData(array('id', 'form_information_id', 'bulk_whole_sale', 'others', 'catering', 'offsite_selling', 'reseller', 'snackshop', 'cart_sales', 'delivery_fee', 'consignment'), $this->input->post('Special Sales'), $sales_id);
                $this->sales_model->updateForm('form_special_sales',$sales_id, $special_sales);

                $discount_type = $this->newInputData(array('id', 'form_information_id', 'discount_id'), $this->input->post('Discount Type'), $sales_id);
                $this->sales_model->updateForm('form_discount',$sales_id, $discount_type);

                $transactions = $this->newInputData(array('id', 'form_information_id', 'transaction_count', 'originating_store', 'terminal_id', 'voids', 'serial_number'), $this->input->post('Transactions'), $sales_id);
                $this->sales_model->updateForm('form_transactions', $sales_id,$transactions);

                $itemized_sales = $this->newInputData(array('id', 'form_information_id', 'offsite_selling', 'catering', 'delivery'), $this->input->post('Itemized Sales'), $sales_id);
                $this->sales_model->updateForm('form_itemized_sales',$sales_id, $itemized_sales);


                $response = array(
                    "message" => 'Successfully updated form!',
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

                var_dump($_POST);

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

                $task =  $this->sales_model->manager_task();;

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

}