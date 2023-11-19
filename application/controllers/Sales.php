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

    // Construct data array
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

            
            case 'POST':
                $_POST =  json_decode(file_get_contents("php://input"), true);
    
                // Data to insert into form_information
                $sales_information = array(
                    'user_id' => $this->session->admin['user_id'],
                    'status' => 1
                );
                
                // Insert into form_information, save return id for later
                $sales_id = $this->sales_model->insertSalesInformation($sales_information);
                
                // Prepare and insert data for form_general_information
                $general_information = $this->newInputData(array('entry_date', 'store', 'shift', 'cashier_name', 'email', 'declared_cash', 'calculated_cash', 'transaction_date', 'cash_deposit', 'other_deposit'), $this->input->post('General Information'), $sales_id);
                $this->sales_model->insertSalesData('form_general_information', $general_information);

                // Prepare and insert data for form_payment_method
                $payment_method = $this->newInputData(array('id', 'form_information_id', 'credit_card_sales', 'credit_card_change', 'cr_memo', 'gcash', 'paymaya', 'shopeepay', 'gc', 'century_shopaholic_vouchers', 'metrodeal', 'grab', 'foodpandaAR', 'lazada', 'shopee', 'booky', 'foodtrip', 'parahero', 'eatigo', 'madison', 'zalora', 'metromart', 'rare_food_shop', 'pickaroo', 'honestbee', 'sharetreats', 'vip', 'vip_sold', 'marketingAR', 'sm_online', 'other_sm_events'), $this->input->post('Payment Method'), $sales_id);
                $this->sales_model->insertSalesData('form_payment_method', $payment_method);

                // Prepare and insert data for form_special_sales
                $special_sales = $this->newInputData(array('id', 'form_information_id', 'bulk_whole_sale', 'others', 'catering', 'offsite_selling', 'reseller', 'snackshop', 'cart_sales', 'delivery_fee', 'consignment'), $this->input->post('Special Sales'), $sales_id);
                $this->sales_model->insertSalesData('form_special_sales', $special_sales);

                // Prepare and insert data for form_discount
                $discount_type = $this->newInputData(array('id', 'form_information_id', 'discount_id'), $this->input->post('Discount Type'), $sales_id);
                $this->sales_model->insertSalesData('form_discount', $discount_type);

                // Prepare and insert data for form_transactions
                $transactions = $this->newInputData(array('id', 'form_information_id', 'transaction_count', 'originating_store', 'terminal_id', 'voids', 'serial_number'), $this->input->post('Transactions'), $sales_id);
                $this->sales_model->insertSalesData('form_transactions', $transactions);

                // Prepare and insert data for form_transactions
                $itemized_sales = $this->newInputData(array('id', 'form_information_id', 'offsite_selling', 'catering', 'delivery'), $this->input->post('Itemized Sales'), $sales_id);
                $this->sales_model->insertSalesData('form_itemized_sales', $itemized_sales);

                break;

		}
	}


}