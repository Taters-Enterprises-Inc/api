<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Stock_Ordering extends CI_Controller
{
	public function __construct(){
		parent::__construct();

        $this->load->library('form_validation');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->bsc_auth->set_message_delimiters('', '');
        $this->bsc_auth->set_error_delimiters('', '');

        $this->lang->load('auth');
		$this->load->model('stock_ordering_model');

		
	}

    public function stores(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $store = $this->stock_ordering_model->getStore();

                $data = array(
                    "stores" => $store,
                );
                
                $response = array(
                    "message" => 'Successfully fetch all stores',
                    "data"    => $data, 

                  );
            
                  header('content-type: application/json');
                  echo json_encode($response);
            break;
        }
    }

    
    public function products(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $category = $this->input->get('category');
                $store_info = json_decode($this->input->get('store_information'));

                $products = $this->stock_ordering_model->getProduct($category, $store_info->store_name);
                if($category){
                    $schedule = $this->stock_ordering_model->getSchedule($category);
                }
                $data = array(
                    "products" => $products,
                    "schedule" => $schedule,
                    
                );
                
                $response = array(
                    "message" => 'Successfully fetch all products',
                    "data"    => $data, 

                  );
            
                  header('content-type: application/json');
                  echo json_encode($response);
            break;
        }
    }

    public function new_order(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);

            $store_id = $this->input->post('selectedStoreId');
            $delivery_date = date('Y-m-d H:i:s', strtotime($this->input->post('deliverydate')));;
            $category_id = $this->input->post('category')['category_id'];
            $product_data = $this->input->post('OrderData');    
            $orderPlacementDate = date('Y-m-d H:i:s');

            $order_information = array(
                'store_id' => $store_id,
                'requested_delivery_date' => $delivery_date,
                'order_type_id' => $category_id,
                'order_placement_date' => $orderPlacementDate,
                'status_id' => 1, //For process id since its new order it is 0.
                'payment_status_id' => 1
            );

            $new_order_id = $this->stock_ordering_model->insertNewOrders($order_information);

            if(isset($product_data)){
                foreach($product_data as $products){

                    $order_product_data = array(
                        "order_information_id"   => $new_order_id,
                        "product_id"   => $products['productId'],
                        'order_qty'     => $products['orderQty'],
                    );
                    
                    $this->stock_ordering_model->insertNewOrdersProducts($order_product_data);
                
                }

                $message = 'Successfully created new order';

            }else {
                $message = "No Product/s data";
            }

            $response = array(
                "message" => $message,
              );
        
              header('content-type: application/json');
              echo json_encode($response);

            break;
        }
    }

    public function getProductData(){

        switch($this->input->server('REQUEST_METHOD')){
            case 'GET': 

                $order_id = $this->input->get('orderId');

                $getOrderData = $this->stock_ordering_model->getOrderData($order_id);
                $getProductData = $this->stock_ordering_model->getProductData($order_id);


                $data = array(
                    "order_information" => $getOrderData,
                    "product_data" => $getProductData,
                  );
            
                $response = array(
                    "message" => 'Successfully fetch Form questions',
                    "data" => $data,
                );

                header('content-type: application/json');
                echo json_encode($response);
    
            break;
        }

    }

    public function getOrders(){
        switch($this->input->server('REQUEST_METHOD')){
            
            case 'GET':

                
                $currentTab = $this->input->get('current_tab') + 1;

                $per_page = $this->input->get('per_page') ?? 25;
                $page_no = $this->input->get('page_no') ?? 0;
                $order = $this->input->get('order') ?? 'asc';
                $order_by = $this->input->get('order_by') ?? 'id';
                $search = $this->input->get('search');
                
                if($page_no != 0){
                    $page_no = ($page_no - 1) * $per_page;
                  }

                $getOrdersCount = $this->stock_ordering_model->getOrdersCount($search, $currentTab);
                $getOrders = $this->stock_ordering_model->getOrders($page_no, $per_page, $order_by, $order, $search, $currentTab);

                $newOrder = $this->stock_ordering_model->getOrdersCount("", 1);
                $reviewOrder = $this->stock_ordering_model->getOrdersCount("", 2);
                $confirmOrder = $this->stock_ordering_model->getOrdersCount("", 3);
                $dispatchOrder = $this->stock_ordering_model->getOrdersCount("", 4);
                $enRouteOrder = $this->stock_ordering_model->getOrdersCount("", 5);
                $receiveOrder = $this->stock_ordering_model->getOrdersCount("", 6);
                $updateBilling = $this->stock_ordering_model->getOrdersCount("", 7);
                $payBilling = $this->stock_ordering_model->getOrdersCount("", 8);
                $confirmPayment = $this->stock_ordering_model->getOrdersCount("", 9);
                $ordersComplete = $this->stock_ordering_model->getOrdersCount("", 10);

                
                $tabBadge = array(
                    'neworder' => $newOrder,
                    'revieworder' => $reviewOrder,
                    'confirmorder' => $confirmOrder,
                    'dispatchorder' => $dispatchOrder,
                    'enrouteorder' => $enRouteOrder,
                    'receiveOrder' => $receiveOrder,
                    'updateBilling' => $updateBilling,
                    'payBilling' => $payBilling,
                    'confirmPayment' => $confirmPayment,
                    'ordersComplete' => $ordersComplete,
                );

                $pagination = array(
                    "total_rows" => $getOrdersCount,
                    "per_page" => $per_page,                    
                  );

                  $response = array(
                    "message" => 'Successfully fetch Form questions',
                    "data" => array(
                        "pagination" => $pagination,
                        "orders" => $getOrders,
                        "tab" => array_values($tabBadge)
                    ),
                );
            
                header('content-type: application/json');
                echo json_encode($response);
    
            break;
            
        }
    }

    public function update_order(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $commited_delivery_date = date('Y-m-d H:i:s', strtotime($this->input->post('commitedDelivery')));
            $product_data = $this->input->post('product_data');

            $order_information = array(
                'commited_delivery_date' => $commited_delivery_date,
                'status_id' => 2
            );

            $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

            if(isset($product_data)){
                foreach($product_data as $product){
                    $product_id = $product['productId'];

                    $order_item_data = array(
                        "commited_qty"   => $product['commitedQuantity']
                    );
                    
                    $this->stock_ordering_model->updateOrderItem($order_information_id, $product_id, $order_item_data);
                
                }

                $message = 'Success!';

            }else {
                $message = "No data!";
            }

            $response = array(
                "message" => $message,
              );
        
              header('content-type: application/json');
              echo json_encode($response);

            break;
        }
    }

    public function review_order(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $reviewed_date = date('Y-m-d H:i:s');
            $status = 3;

            $order_information = array(
                'reviewed_date' => $reviewed_date,
                'status_id' => $status
            );

            $reviewed_order = $this->stock_ordering_model->reviewOrder($order_information_id, $order_information);

            if (!$reviewed_order) {
                $message = "Success!";
            } else {
                $message = "There's an error!";
            }

            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function confirm_order(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $order_confirmation_date = date('Y-m-d H:i:s');
            $status = 4;

            $order_information = array(
                'order_confirmation_date' => $order_confirmation_date,
                'status_id' => $status
            );

            $confirmed_order = $this->stock_ordering_model->confirmOrder($order_information_id, $order_information);

            if (!$confirmed_order) {
                $message = "Success!";
            } else {
                $message = "There's an error!";
            }

            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function dispatch_order(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $data =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $_POST['id'];
            $dispatch_date = date('Y-m-d H:i:s');
            $status = 5;
            $delivery_receipt_image_name = clean_str_for_img($this->input->post('deliveryReceipt'). '-' . time() ) . '.jpg';
    
            $deliveryReceipt_error = upload('deliveryReceipt','./assets/uploads/screenshots/',$delivery_receipt_image_name, 'jpg');
            if($deliveryReceipt_error){
              $this->output->set_status_header('401');
              echo json_encode(array( "message" => $deliveryReceipt_error));
              return;
            }

            $order_information = array(
                'delivery_receipt' => $delivery_receipt_image_name,
                'dispatch_date' => $dispatch_date,
                'status_id' => $status,
            );

            $dispatch_order = $this->stock_ordering_model->dispatchOrder($order_information_id, $order_information);

            if (!$dispatch_order) {
                $message = "Success!";
            } else { 
                $message = "There's an error!";
            }
            
            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function order_en_route(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $transportation_id = $this->input->post('transport');
            $reviewed_date = date('Y-m-d H:i:s');
            $status = 6;

            $order_information = array(
                'enroute_date' => $reviewed_date,
                'status_id' => $status,
                'transportation_id' => $transportation_id
            );

            $order_en_route = $this->stock_ordering_model->orderEnRoute($order_information_id, $order_information);

            if (!$order_en_route) {
                $message = "Success!";
            } else {
                $message = "There's an error!";
            }

            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function receive_order_delivery(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $data =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $delivery_receipt = $this->input->post('updatedDeliveryReceipt');
            $actual_delivery_date = date('Y-m-d H:i:s', strtotime($this->input->post('actualDeliveryDate')));
            $status = 7;

            $updated_delivery_receipt_image_name = clean_str_for_img($this->input->post('updatedDeliveryReceipt'). '-' . time() ) . '.jpg';
    
            $updatedDeliveryReceipt_error = upload('updatedDeliveryReceipt','./assets/uploads/screenshots/',$updated_delivery_receipt_image_name, 'jpg');
            if($updatedDeliveryReceipt_error){
              $this->output->set_status_header('401');
              echo json_encode(array( "message" => $updatedDeliveryReceipt_error));
              return;
            }

            $order_information = array(
                'updated_delivery_receipt' => $updated_delivery_receipt_image_name,
                'actual_delivery_date' => $actual_delivery_date,
                'status_id' => $status
            );

            $this->stock_ordering_model->updateActualDeliveryDate($order_information_id, $order_information);

            
            $productData = array();
            $index = 0;
            $hasData = false;
            while ($this->input->post('product_data_'.$index.'_id')) {
                $deliveryQuantity = $this->input->post('product_data_'.$index.'_deliveryQuantity');
                $productId = $this->input->post('product_data_'.$index.'_productId');

                $delivered_qty_data = array(
                    'delivered_qty' => $deliveryQuantity,
                );

                $this->stock_ordering_model->updateDeliveredQty($order_information_id, $productId, $delivered_qty_data);

                $index++;
                $hasData = true;
                
            }

            if($hasData){
                $message = "success!";
            }else {
                $message = "No data!";
            }

            $response = array(
                "message" => $message,
              );
        
              header('content-type: application/json');
              echo json_encode($response);

            break;
        }
    }

    public function update_billing(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $billing_information_id = $this->input->post('billingInformationId');
            $billing_id = $this->input->post('billingInformationId');
            $billing_amount = $this->input->post('billingAmount');
            $status = 8;

            $billing_information = array(
                'billing_id' => $billing_id,
                'billing_amount' => $billing_amount
            );


            //needs to be changed when user is implemented
            $billing_id = $this->stock_ordering_model->insertBllingInfo($billing_information);

            if ($billing_id) {
            

                $order_information_data = array(
                    "billing_information_id"   => $billing_id,
                    "status_id"   => $status
                );

                $this->stock_ordering_model->updateBillingInformationId($order_information_id, $order_information_data);
                
                $message = "Success!";
            } else {
                $message = "There's an error!";
            }

            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function pay_billing(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $data =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $payment_detail_image = $this->input->post('paymentDetailImage');
            $status = 9;

            $payment_detail_image_name = clean_str_for_img($this->input->post('paymentDetailImage'). '-' . time() ) . '.jpg';
    
            $paymentDetailImage_error = upload('paymentDetailImage','./assets/uploads/screenshots/',$payment_detail_image_name, 'jpg');
            if($paymentDetailImage_error){
              $this->output->set_status_header('401');
              echo json_encode(array( "message" => $paymentDetailImage_error));
              return;
            }


            $order_information = array(
                'payment_detail_image' => $payment_detail_image_name,
                "status_id"   => $status
            );

            $upload_payment_img = $this->stock_ordering_model->uploadPaymentDetailImage($order_information_id, $order_information);

            if (!$upload_payment_img) {
                $message = "Success!";
            } else {
                $message = "There's an error!";
            }

            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function confirm_payment(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $payment_confirmation_date = date('Y-m-d H:i:s');
            $status = 10;

            $order_information = array(
                'payment_confirmation_date' => $payment_confirmation_date,
                'status_id' => $status,
                'payment_status_id' => 2,
            );

            $payment_confirmation_date = $this->stock_ordering_model->confirmPayment($order_information_id, $order_information);

            if (!$payment_confirmation_date) {
                $message = "Success!";
            } else {
                $message = "There's an error!";
            }

            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }
    


	public function login(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);
		        $this->data['title'] = $this->lang->line('login_heading');
                $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
                $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
                
		        if ($this->form_validation->run() === TRUE) {
                    $remember = (bool) $this->input->post('remember');

                    if ($this->ion_auth->audit_login($this->input->post('identity'), $this->input->post('password'), $remember)) {

                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->ion_auth->messages()));
                        return;
                    } else {

				        $this->output->set_status_header(401);
                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->ion_auth->errors()));
                        return;
                    }

                }else{ 
                    $this->output->set_status_header(401);
                    header('content-type: application/json');
                    echo json_encode(array("message" =>  validation_errors()));
                    return;
                }

                break;
        }
    }


    public function logout(){
		$this->data['title'] = "Logout";
		$this->bsc_auth->logout();
        
        header('content-type: application/json');
        echo json_encode(array("message" => 'Successfully logout user'));
        return;
	}

	
}