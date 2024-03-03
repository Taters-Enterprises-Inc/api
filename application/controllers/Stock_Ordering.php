<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Stock_ordering extends CI_Controller
{
	public function __construct(){
		parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('excel');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->bsc_auth->set_message_delimiters('', '');
        $this->bsc_auth->set_error_delimiters('', '');

        $this->lang->load('auth');
		$this->load->model('stock_ordering_model');
        $this->load->model('report_model');
	}

    public function stores(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $store_id = $this->input->get('store_id');
                $user_id = $this->session->admin['user_id'];
                $is_Admin = $this->ion_auth->is_admin();


                $store = $this->stock_ordering_model->getStore($user_id, $is_Admin);
                $ship_to_address = $this->stock_ordering_model->getShipToAddress($store_id);
                $window_time = $this->stock_ordering_model->getWindowTime($store_id);
                

                $data = array(
                    "stores" => $store,
                    "address" => $ship_to_address,
                    "window_time" => $window_time
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

    public function get_schedule(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

                $store = $this->stock_ordering_model->getStoreIdByUserId($user_id, $isAdmin);


                $store_id = [];
                    foreach ($store as $item) {
                        $store_id[] = $item->store_id;
                    }

                
                $delivery = $this->stock_ordering_model->get_delivery_schedule($store_id);

                $response = array(
                    "message" => 'Successfully fetch delivery schedule',
                    "data"    => $delivery, 

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

                $store_info = $this->input->get('store_information');
                
                // $store_id = 18;
                // $category = 1;

                $products = $this->stock_ordering_model->getProduct($category, $store_info['store_id']);
              

                $data = array(
                    "products" => $products, 
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
            $delivery_date = date('Y-m-d H:i:s', strtotime($this->input->post('deliveryScheduleData')));;
            $category_id = $this->input->post('category')['category_id'];
            $product_data = $this->input->post('OrderData');    
            $orderPlacementDate = date('Y-m-d H:i:s');
            $ship_to_address = $this->input->post('selectedAddress');
            $remarks = $this->input->post('remarks');
            $logistic_type = $this->input->post('logisticType');
            $user_id = $this->session->admin['user_id'];

            $order_information = array(
                'store_id' => $store_id,
                'ship_to_address' => $ship_to_address['ship_to_address'],
                'requested_delivery_date' => $delivery_date,
                'order_type_id' => $category_id,
                'order_placement_date' => $orderPlacementDate,
                'status_id' => 1, //For process id since its new order it is 0.
                'payment_status_id' => 1,
                'logistic_type_id' => $logistic_type,
                'last_updated' => date('Y-m-d H:i:s'),
            );

            $new_order_id = $this->stock_ordering_model->insertNewOrders($order_information);
            
            $this->insert_tracking_log(1, $new_order_id);

            $this->transaction_log($new_order_id, 1, date('Y-m-d H:i:s'));

            if (isset($remarks) && !empty($remarks)) {

                $remarks_information = array(
                    'order_information_id' => $new_order_id,
                    'order_status_id' => 2,
                    'remarks' => $remarks,
                    'user_id' => $user_id,
                    'date'    => date('Y-m-d H:i:s'),
                );

                $this->stock_ordering_model->insertRemarks($remarks_information);

            }

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


            $this->realtime_badge();

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

    public function settings_products(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $per_page = $this->input->get('per_page') ?? 25;
                $page_no = $this->input->get('page_no') ?? 0;
                $order = $this->input->get('order') ?? 'asc';
                $order_by = $this->input->get('order_by') ?? 'id';
                $search = $this->input->get('search');
            
                $products = $this->stock_ordering_model->getProducts($page_no, $per_page, $order_by, $order, $search);
                $productsCount = $this->stock_ordering_model->getProductsCount($page_no, $per_page, $order_by, $order, $search);

                $pagination = array(
                    "total_rows" => $productsCount,
                    "per_page" => $per_page,                    
                  );

                $data = array(
                    "pagination" => $pagination,
                    "products" => $products,
                    
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

    public function settings_create_product(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST': 
                $_POST =  json_decode(file_get_contents("php://input"), true);

                $product_data = array(
                    'product_id'    => $this->input->post('productId'),
                    'product_name'  => $this->input->post('productName'),
                    'uom'           => $this->input->post('uom'),
                    'category_id'   => $this->input->post('categoryType'),
                    'cost'          => $this->input->post('cost'),
                    'active_status' => true,
                );

                $this->stock_ordering_model->insertNewProducts($product_data);

                foreach($this->input->post('store_id') as $store_ids){
                    $product_availability_data = array(
                        'product_id' => $this->input->post('productId'),
                        'store_id'   => $store_ids
                    );
                    $this->stock_ordering_model->insertNewProductsStore($product_availability_data);
                }


                $response = array(
                    "message" => 'Successfully added new a product',
                );

                header('content-type: application/json');
                echo json_encode($response);
    
            break;
        }
    }

    public function settings_edit_product($id){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $products_data = $this->stock_ordering_model->getProductDataById($id);
                $stores = $this->stock_ordering_model->getProductStore($products_data->product_id);
                
                $data = array(
                    "products_data" => $products_data,
                    "stores" => $stores,

                );
                
                $response = array(
                    "message" => 'Successfully fetch all products',
                    "data"    => $data, 

                  );
            
                  header('content-type: application/json');
                  echo json_encode($response);
            break;

            case 'POST':

                $_POST =  json_decode(file_get_contents("php://input"), true);

                $product_data = array(
                    'product_id'    => $this->input->post('productId'),
                    'product_name'  => $this->input->post('productName'),
                    'uom'           => $this->input->post('uom'),
                    'category_id'   => $this->input->post('categoryType'),
                    'cost'          => $this->input->post('cost'),
                );

                $this->stock_ordering_model->updateProductData($id, $product_data);

                $product_id = array('product_id' => $this->input->post('productId'));
                $this->stock_ordering_model->removeProductDataAvailability($product_id);
                
                foreach($this->input->post('store_id') as $store_ids){
                    $product_availability_data = array(
                        'product_id' => $this->input->post('productId'),
                        'store_id'   => $store_ids
                    );
                    $this->stock_ordering_model->insertNewProductsStore($product_availability_data);
                }

                $response = array(
                    "message" => 'Successfully edited a product',
                  );
            
                  header('content-type: application/json');
                  echo json_encode($response);
            break;
        }
    }


    public function settings_enable_product(){
        switch($this->input->server('REQUEST_METHOD')){
           
            case 'POST':

                $_POST =  json_decode(file_get_contents("php://input"), true);

                $active_status = array(
                    'active_status'    => $this->input->post('active_status'),
                );

                $this->stock_ordering_model->updateProductData($this->input->post('id'), $active_status);

                $response = array(
                    "message" => 'Successfully updated product active status',
                  );
            
                  header('content-type: application/json');
                  echo json_encode($response);
            break;
        }
    }

    

    public function getOrders(){
        switch($this->input->server('REQUEST_METHOD')){
            
            case 'GET':

                
                $currentTab = $this->input->get('tab') + 1;

                $per_page = $this->input->get('per_page') ?? 25;
                $page_no = $this->input->get('page_no') ?? 0;
                $order = $this->input->get('order') ?? 'asc';
                $order_by = $this->input->get('order_by') ?? 'last_updated';

                $store = json_decode($this->input->get('store'));
                $date_type = $this->input->get('dateType');
                $start_date = $this->input->get('startDate');
                $end_date = $this->input->get('endDate');
               
                //Format start date and time
                if(isset($start_date)){
                    $date_parts = explode(" ", $start_date);
                    $date_time_string = implode(" ", array_slice($date_parts, 1, 4));
                    $date = DateTime::createFromFormat("M d Y H:i:s", $date_time_string);
                    $start_date = $date->format("Y-m-d H:i:s");
                }

                
                //Format end date and time
                if(isset($end_date)){
                    $date_parts = explode(" ", $end_date);
                    $date_time_string = implode(" ", array_slice($date_parts, 1, 4));
                    $date = DateTime::createFromFormat("M d Y H:i:s", $date_time_string);
                    $end_date = $date->format("Y-m-d H:i:s");
                }

                $search = $this->input->get('search');

                $user_id = $this->session->admin['user_id'];
                $isAdmin = $this->ion_auth->is_admin();

      
                $user_store_id = array();

                $store_id = $this->stock_ordering_model->getStore($user_id, $isAdmin);

                foreach ($store_id as $id) {
                    $user_store_id[] = $id['store_id'];
                }

                $store_id = !isset($store) ? $user_store_id : $store;

             
                if($page_no != 0){
                    $page_no = ($page_no - 1) * $per_page;
                  }
                  
                $getOrders = $this->stock_ordering_model->getOrders($page_no, $per_page, $order_by, $order, $search, $currentTab, $store_id, $date_type, $start_date, $end_date);
                $getOrdersCount = $this->stock_ordering_model->getOrdersCount($search, $currentTab, $store_id, $date_type, $start_date, $end_date);

                $franchiseType = 1; //default company owned

                if (!empty($user_store_id)) {
                    $getFranchiseType = $this->stock_ordering_model->getFranchiseTypeByStoreId($user_store_id);

                    foreach($getFranchiseType as $type){
                        if($type->franchise_type_id == 2){
                            $franchiseType = $type->franchise_type_id;
                            break;
                        }
                    }
                }

                $getOrdersBadgeCount = array_fill(0, 10, 0);
                for($i=0; $i < 10; $i++){
                    $getOrdersBadgeCount[$i] += $this->stock_ordering_model->getOrdersBadge($i + 1, $user_store_id);
                }

                $pagination = array(
                    "total_rows" => $getOrdersCount,
                    "per_page" => $per_page,                    
                  );

                  $response = array(
                    "message" => 'Successfully fetch Form questions',
                    "data" => array(
                        "pagination" => $pagination,
                        "orders" => $getOrders,
                        "tab" => $getOrdersBadgeCount,
                        "franchise_type" => $franchiseType,
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
            $remarks = $this->input->post('remarks');
            $penalty   = $this->input->post('penalty');

            if(isset($product_data)){
                foreach($product_data as $product){
                    $product_id = $product['productId'];

                    $order_item_data = array(
                        "commited_qty"   => $product['commitedQuantity'],
                        "out_of_stock"   => $product['out_of_stock']

                    );
                    
                    $this->stock_ordering_model->updateOrderItem($order_information_id, $product_id, $order_item_data);
                    
                    
                }

            }else {
                $this->output->set_status_header('400');
                echo json_encode(array( "message" => 'Encountered a problem while processing your changes. Try again after a minutes'));
                return;
            }

            $order_information = array(
                'commited_delivery_date' => $commited_delivery_date,
                'status_id' => $penalty === true ? '7' : '2',
                'last_updated' => date('Y-m-d H:i:s'),
                'penalty' => $penalty,
            );

            $updateOrderInformation = $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

            $this->insert_tracking_log(2, $order_information_id);

            $this->insert_remarks($remarks, 2, $order_information_id);
            
            $this->transaction_log($order_information_id, 2, date('Y-m-d H:i:s'));

            $this->realtime_badge();

            $response = array(
                "message" => 'Sucessfully updated the order',
              );
        
            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function update_order_items(){
        switch($this->input->server('REQUEST_METHOD')){

            case 'POST':
                $_POST =  json_decode(file_get_contents("php://input"), true);

                $productData = array();
                $order_information_id = $_POST[0]['order_information_id'];

                if (empty($_POST)) {
                    $this->output->set_status_header('400');
                    echo json_encode(array( "message" => "No input was provided in the submission."));
                    return;
                }

                foreach ($_POST as $product) {
                    $productData[] = array(
                        'order_information_id' => $product['order_information_id'],
                        'product_id' => $product['productId'],
                        'order_qty' => $product['orderQty'],
                    );
                }

                $insertOrderItemError = $this->stock_ordering_model->insertNewOrderitem($productData);

                if($insertOrderItemError){
                    $this->output->set_status_header('500');
                    echo json_encode(array( "message" => "Sorry, we encountered an issue while updating."));
                    return;
                }

                $this->insert_tracking_log(14, $order_information_id);
                $this->transaction_log($order_information_id, 2, date('Y-m-d H:i:s'));

                $response = array(
                    "message" => 'Sucessfully edited the order item',
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
            $status = $this->input->post('status');

            $product_data = $this->input->post('product_data');

            $isFranchisee = $this->full_franchisee_check($order_information_id);
            $order_penalty_check = $this->stock_ordering_model->orderPenaltyCheck($order_information_id);


            // Revert the set commited quantity by supplier.
            if(isset($product_data) && $status === '1'){
                foreach($product_data as $product){
                    $product_id = $product['productId'];

                    $order_item_data = array(
                        "commited_qty"   => null
                    );
                    
                    $this->stock_ordering_model->updateOrderItem($order_information_id, $product_id, $order_item_data);

                }

            }

            if($isFranchisee && $status !== '1'){
                $status = '3';
            }
            // Just comment might need this in the future
            // else if($order_penalty_check && $status !== '1'){
            //     $status = '7';
            // }

            $order_information = array(
                'reviewed_date' => $reviewed_date,
                'status_id' => $status,
                'last_updated' => date('Y-m-d H:i:s'),
            );


            $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

            $this->insert_tracking_log($status == '1' ? 3 : 4, $order_information_id);


            $remarks = $this->input->post('remarks');
            $this->insert_remarks($remarks, $status, $order_information_id);

            $this->realtime_badge();
            $this->transaction_log($order_information_id, 3, date('Y-m-d H:i:s'));

            $response = array(
                "message" => 'Sucessfully updated the order',
            );

            
            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function franchisee_order(){
        switch($this->input->server('REQUEST_METHOD')){

            case 'POST':
                $data =  json_decode(file_get_contents("php://input"), true);

                $order_information_id = $_POST['id'];
                $remarks = $this->input->post('remarks');                
                $status = 4;


                //Franchisee paybill upload Billing
                $uploaded_billing_receipt_image_name = clean_str_for_img($this->input->post('uploadedBillingReceipt'). '-' . time());

                $uploadedBillingReceipt = explode(".", $_FILES['uploadedBillingReceipt']['name']);
                $ext = end($uploadedBillingReceipt);
                $uploaded_billing_receipt_image_name = 'franchisee-paybill' . $uploaded_billing_receipt_image_name . '.' . $ext;

                $uploadedBillingReceipt_error = upload('uploadedBillingReceipt','./assets/uploads/screenshots/',$uploaded_billing_receipt_image_name, $ext );

                if($uploadedBillingReceipt_error){
                    $this->output->set_status_header('401');
                    echo json_encode(array( "message" => $uploadedBillingReceipt_error));
                    return;
                }

                //Franchisee paybill update order tatus
                $order_information = array(
                    'franchisee_payment_detail_image' => $uploaded_billing_receipt_image_name,
                    'status_id' => $status,
                    'last_updated' => date('Y-m-d H:i:s'),
                );
                $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

                //Franchisee paybill remarks
                $this->insert_remarks($remarks, $status, $order_information_id);
                $this->realtime_badge();
                $this->insert_tracking_log($status, $order_information_id);


                $response = array(
                    "message" => 'Sucessfully updated the order',
                );
    
                header('content-type: application/json');
                echo json_encode($response);
            
            break;
        }
    }

    public function penalized_order(){
        switch($this->input->server('REQUEST_METHOD')){

            case 'POST':
                $data =  json_decode(file_get_contents("php://input"), true);

                $order_information_id = $_POST['id'];
                $remarks = $this->input->post('remarks');                
                $status = 4;


                //Penalized paybill upload Billing
                $uploaded_billing_receipt_image_name = clean_str_for_img($this->input->post('paymentFile'). '-' . time());

                $uploadedBillingReceipt = explode(".", $_FILES['paymentFile']['name']);
                $ext = end($uploadedBillingReceipt);
                $uploaded_billing_receipt_image_name = 'penalized-paybill' . $uploaded_billing_receipt_image_name . '.' . $ext;

                $uploadedBillingReceipt_error = upload('paymentFile','./assets/uploads/screenshots/',$uploaded_billing_receipt_image_name, $ext );

                if($uploadedBillingReceipt_error){
                    $this->output->set_status_header('401');
                    echo json_encode(array( "message" => $uploadedBillingReceipt_error));
                    return;
                }

                //Penalized paybill update order tatus
                $order_information = array(
                    'payment_detail_image' => $uploaded_billing_receipt_image_name,
                    'status_id' => $status,
                    'last_updated' => date('Y-m-d H:i:s'),
                );
                $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

                //Penalized paybill remarks
                $this->insert_remarks($remarks, $status, $order_information_id);
                $this->realtime_badge();
                $this->insert_tracking_log($status, $order_information_id);


                $response = array(
                    "message" => 'Sucessfully updated the order',
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
            $dispatch_date = $this->input->post('dispatchDeliveryDate');
            $transport_id = $this->input->post('transport');
            $remarks = $this->input->post('remarks');
            $user_id = $this->session->admin['user_id'];
            
            $status = 5;       
            
            $isFranchisee = $this->full_franchisee_check($order_information_id);

            if(!$isFranchisee){
                $file_name_prefix = $this->stock_ordering_model->filename_factory_prefix($order_information_id,'');

                $delivery_receipt_image_name = clean_str_for_img($this->input->post('deliveryReceipt'). '-' . time());
                $deliveryReceipt = explode(".", $_FILES['deliveryReceipt']['name']);
                $ext = end($deliveryReceipt);
                $delivery_receipt_image_name = $file_name_prefix . $delivery_receipt_image_name . '.' . $ext;
                $path = './assets/uploads/screenshots/'.$delivery_receipt_image_name;

                $deliveryReceipt_error = upload('deliveryReceipt','./assets/uploads/screenshots/', $delivery_receipt_image_name, $ext );

                if($deliveryReceipt_error){
                $this->output->set_status_header('401');
                echo json_encode(array( "message" => $deliveryReceipt_error));
                return;
                }
                
                $import_si = $this->import_si($order_information_id, $path, 'multim_si_tb');

                if ($import_si) {
                    $this->output->set_status_header('401');
                    echo json_encode(array( "message" => $import_si));
                    return;
                }
            }

            $dispatch_date = DateTime::createFromFormat('h:i:s a', $dispatch_date)->format('H:i:s');
            $get_commited_date = $this->stock_ordering_model->getCommitedDate($order_information_id);
            $get_commited_date =  date('Y-m-d H:i:s', strtotime($get_commited_date->commited_delivery_date));
            $dispatch_date = substr_replace($get_commited_date, $dispatch_date, 11, 8);

            $order_information = array(
                'delivery_receipt' => $delivery_receipt_image_name ?? null,
                'dispatch_date' => $dispatch_date,
                'status_id' => $status,
                'transportation_id' => $transport_id,
                'last_updated' => date('Y-m-d H:i:s'),
            );

            $productData = array();
            $index = 0;

            $dispatch_order = $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

            $this->insert_tracking_log(5, $order_information_id);

            $this->insert_remarks($remarks, $status, $order_information_id);
            $this->transaction_log($order_information_id, 4, date('Y-m-d H:i:s'));

            $this->realtime_badge();
            
            $response = array(
                "message" => 'Sucessfully updated the order',
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
            $actual_delivery_date = date('Y-m-d H:i:s', strtotime($this->input->post('actualDeliveryDate')));
            $status = 6;
            $remarks = $this->input->post('remarks');
            $user_id = $this->session->admin['user_id'];

            $file_name_prefix = $this->stock_ordering_model->filename_factory_prefix($order_information_id,'');

            $updated_delivery_receipt_image_name = clean_str_for_img($this->input->post('updatedDeliveryReceipt'). '-' . time());

            $updatedDeliveryReceipt = explode(".", $_FILES['updatedDeliveryReceipt']['name']);
            $ext = end($updatedDeliveryReceipt);
            $updated_delivery_receipt_image_name = $file_name_prefix . $updated_delivery_receipt_image_name . '.' . $ext;

            $updatedDeliveryReceipt_error = upload('updatedDeliveryReceipt','./assets/uploads/screenshots/',$updated_delivery_receipt_image_name, $ext );
 
            if($updatedDeliveryReceipt_error){
              $this->output->set_status_header('401');
              echo json_encode(array( "message" => $updatedDeliveryReceipt_error));
              return;
            }

            $productData = array();

            foreach($this->input->post('product_data') as $data){
                $total_cost = $this->compute_final_cost($data['productId'], $order_information_id, $data['deliveryQuantity'], true);
                $product_rate = $this->compute_final_cost($data['productId'], $order_information_id, $data['deliveryQuantity'], false);

                $data['total_cost'] = $total_cost;
                $data['product_rate'] = $product_rate;
                $data['product_id'] = $data['productId'];
                $data['delivered_qty'] = $data['deliveryQuantity'];
                unset($data['productId']); 
                unset($data['deliveryQuantity']); 

                $productData[] = $data;
            }

            $this->stock_ordering_model->updateOrderItemBatch($productData);           

            $order_information = array(
                'updated_delivery_receipt' => $updated_delivery_receipt_image_name,
                'actual_delivery_date' => $actual_delivery_date,
                'status_id' => $status,
                'last_updated' => date('Y-m-d H:i:s'),
            );

            $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);
            
            $this->insert_tracking_log(6, $order_information_id);

            $this->insert_remarks($remarks, $status, $order_information_id);
            $this->transaction_log($order_information_id, 5, date('Y-m-d H:i:s'));


            $sum_for_sipdf = $this->stock_ordering_model->getSumForSiPdf($order_information_id);
            $sum_of_dqty = $sum_for_sipdf->sum_dqty;
            $total_cost = $sum_for_sipdf->total_cost;
            $total_sales = $sum_of_dqty * $total_cost;
            $vatable_sales = $total_sales / 1.12;
            $vat_amount = $total_sales - $vatable_sales;
            $less_vat = $total_sales - $vatable_sales;
            $vat_ex_amount = $total_sales - $less_vat;
            $amount_due = $total_sales / 1.12;
            $add_vat = $total_sales - $vatable_sales;
            $total_amount_due = $sum_of_dqty * $total_cost;

            $order_information_v1 = array(
                'total_sales' => $total_sales,
                'vatable_sales' => $vatable_sales,
                'vat_amount' => $vatable_sales,
                'less_vat' => $less_vat,
                'vat_ex_amount' => $vat_ex_amount,
                'amount_due' => $amount_due,
                'add_vat' => $add_vat,
                'total_amount_due' => $total_amount_due
            );
            
            $this->stock_ordering_model->updateforSI($order_information_id, $order_information_v1);


            $this->realtime_badge();

            $response = array(
                "message" => 'Sucessfully updated the order',
              );
        
              header('content-type: application/json');
              echo json_encode($response);

            break;
        }
    }


    public function delivery_receive_approval(){

        switch($this->input->server('REQUEST_METHOD')){
            case 'POST': 
                $_POST =  json_decode(file_get_contents("php://input"), true);
                
                $order_information_id = $this->input->post('id');
                $status = $this->input->post('status');
                $remarks = $this->input->post('remarks');
                $user_id = $this->session->admin['user_id'];
                

                $order_information = array(
                    'status_id' => $status,
                    'last_updated' => date('Y-m-d H:i:s'),
                );

                $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

                $this->insert_tracking_log($status == '4' ? 7 : 8, $order_information_id);

                $this->transaction_log($order_information_id, $status, date('Y-m-d H:i:s'));

                $this->insert_remarks($remarks, $status, $order_information_id);
                
                $this->realtime_badge();

                $message = $status == 4 ? "Sucessfully Rejected" : "Order Approved";

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
            $data =  json_decode(file_get_contents("php://input"), true);

            $order_information_id = $this->input->post('id');
            $remarks = $this->input->post('remarks');
            $user_id = $this->session->admin['user_id'];
            $status = 8;
            $message = "";

            $uploadedGoodsReceipt_image_name = null;
            $uploadedRegionReceipt_image_name = null;
            $uploadedPenaltyReceipt_image_name = null;
            // Updated Sales Invoice
            if(isset($_FILES['uploadedGoodsReceipt'])){

                $file_name_prefix = $this->stock_ordering_model->filename_factory_prefix($order_information_id,'goods');
                
                $uploadedGoodsReceipt_image_name = clean_str_for_img($this->input->post('uploadedGoodsReceipt'). '-' . time());

                $uploadedGoodsReceipt = explode(".", $_FILES['uploadedGoodsReceipt']['name']);
                $ext = end($uploadedGoodsReceipt);
                $uploadedGoodsReceipt_image_name = $file_name_prefix . $uploadedGoodsReceipt_image_name . '.' . $ext;

                $uploadedGoodsReceipt_error = upload('uploadedGoodsReceipt','./assets/uploads/screenshots/',$uploadedGoodsReceipt_image_name, $ext );

                if($uploadedGoodsReceipt_error){
                $this->output->set_status_header('401');
                echo json_encode(array( "message" => $uploadedGoodsReceipt_error));
                return;
                }
            }

            if(isset($_FILES['uploadedRegionReceipt'])){

                $file_name_prefix = $this->stock_ordering_model->filename_factory_prefix($order_information_id,'region');

                $uploadedRegionReceipt_image_name = clean_str_for_img($this->input->post('uploadedRegionReceipt'). '-' . time());

                $uploadedRegionReceipt = explode(".", $_FILES['uploadedRegionReceipt']['name']);
                $ext = end($uploadedRegionReceipt);
                $uploadedRegionReceipt_image_name = $file_name_prefix . $uploadedRegionReceipt_image_name . '.' . $ext;

                $uploadedRegionReceipt_error = upload('uploadedRegionReceipt','./assets/uploads/screenshots/',$uploadedRegionReceipt_image_name, $ext );

                if($uploadedRegionReceipt_error){
                $this->output->set_status_header('401');
                echo json_encode(array( "message" => $uploadedRegionReceipt_error));
                return;
                }
            }

            if(isset($_FILES['uploadedPenaltyReceipt'])){

                $file_name_prefix = $this->stock_ordering_model->filename_factory_prefix($order_information_id,'penalty');

                $uploadedPenaltyReceipt_image_name = clean_str_for_img($this->input->post('uploadedPenaltyReceipt'). '-' . time());

                $uploadedPenaltyReceipt = explode(".", $_FILES['uploadedPenaltyReceipt']['name']);
                $ext = end($uploadedPenaltyReceipt);
                $uploadedPenaltyReceipt_image_name = $file_name_prefix . $uploadedPenaltyReceipt_image_name . '.' . $ext;

                $uploadedPenaltyReceipt_error = upload('uploadedPenaltyReceipt','./assets/uploads/screenshots/',$uploadedPenaltyReceipt_image_name, $ext );

                if($uploadedPenaltyReceipt_error){
                $this->output->set_status_header('401');
                echo json_encode(array( "message" => $uploadedPenaltyReceipt_error));
                return;
                }

                $path = './assets/uploads/screenshots/'.$uploadedPenaltyReceipt_image_name;
                $import_si = $this->import_si($order_information_id, $path, 'multim_si_tb');

                if ($import_si) {
                    $this->output->set_status_header('401');
                    echo json_encode(array( "message" => $import_si));
                    return;
                }
            }

            $order_information_data = array(
                "status_id"   => $status,
                'updated_delivery_goods_receipt' => $uploadedGoodsReceipt_image_name ?? null,
                'updated_delivery_region_receipt' => $uploadedRegionReceipt_image_name ?? null,
                'updated_delivery_penalty_receipt' => $uploadedPenaltyReceipt_image_name ?? null,
                'last_updated' => date('Y-m-d H:i:s'),
            );

            $insertError = $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information_data);
            
            $this->insert_tracking_log(9, $order_information_id);

            $this->transaction_log($order_information_id, 7, date('Y-m-d H:i:s'));

            if(isset($insertError)){
                $this->output->set_status_header('401');
                echo json_encode(array( "message" => "Error Inserting"));
                return;
            }

            
            $this->insert_remarks($remarks, $status, $order_information_id);
            $this->realtime_badge();

            $response = array(
                "message" => "Order sucessfully confirmed",
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function pay_billing(){
        switch($this->input->server('REQUEST_METHOD')){

        case 'GET': 
            $search = $this->input->get('invoiceSearch');
            
            $user_id = $this->session->admin['user_id'];
            $order_msi = $this->stock_ordering_model->getOrderMSI($search);

              $response = array(
                "message" => 'Successfully fetch',
                "data" => array(
                    "orders" => $order_msi,
                ),
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;

        case 'POST':
            $data =  json_decode(file_get_contents("php://input"), true);

            $payment_detail_image = $this->input->post('paymentDetailImage');
            $remarks = $this->input->post('remarks');
            $selectedData = $this->input->post('selectedData');
            $user_id = $this->session->admin['user_id'];
            $status = 9;

            $index = 0;

            $order_information_data = array();

            if(isset($selectedData)){
                foreach($selectedData as $data){
                    $order_information_data[] = $data;
                }
            }

            foreach($order_information_data as $orderData){
               if($orderData['orderId'] !== $order_information_data[0]['orderId']){
                    $this->output->set_status_header('400');
                    echo json_encode(array( "message" => "Invalid OrderId. Select Invoice Number with the same orderId"));
                    return;
               }
            }
            
            $order_information_OrderId = $order_information_data[0]['orderId'];

            $file_name_prefix = $this->stock_ordering_model->filename_factory_prefix($order_information_OrderId,'');
            $payment_detail_image_name = clean_str_for_img($this->input->post('paymentFile'). '-' . time());

            $payment_detail_image = explode(".", $_FILES['paymentFile']['name']);
            $ext = end($payment_detail_image);
            $payment_detail_image_name = $file_name_prefix. '_PAYMENT_' . $payment_detail_image_name . '.' . $ext;
            $path = './assets/uploads/screenshots/'.$payment_detail_image_name;

            $payment_detail_image_name_error = upload('paymentFile','./assets/uploads/screenshots/',$payment_detail_image_name, $ext );

            if($payment_detail_image_name_error){
                $this->output->set_status_header('401');
                echo json_encode(array( "message" => $payment_detail_image_name_error));
                return;
            }

            $import = $this->import_pay_bill_payment($path, $order_information_data);

            if($import != ""){
                $this->output->set_status_header('400');
                echo json_encode(array( "message" => $import));
                return;
            }


            $order_information = array(
                'payment_detail_image' => $payment_detail_image_name,
                "status_id"   => 9,
                'last_updated' => date('Y-m-d H:i:s'),
            );
            
            $this->stock_ordering_model->updateOrderInfo($order_information_OrderId, $order_information);
           
            $this->insert_tracking_log(10, $order_information_OrderId);

            $this->insert_remarks($remarks, $status, $order_information_OrderId);

            $this->transaction_log($order_information_OrderId, 8, date('Y-m-d H:i:s'));

            $this->realtime_badge();

            $response = array(
                "message" => 'Sucessfully updated the order',
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
            $remarks = $this->input->post('remarks');
            $user_id = $this->session->admin['user_id'];
            $status = $this->input->post('status');
            
            $panalty = $this->stock_ordering_model->orderPenaltyCheck($order_information_id);


            if($penalty && $status !== '8'){
                $status = '4';
            }

            $order_information = array(
                'payment_confirmation_date' => $payment_confirmation_date,
                'status_id' => $status,
                'payment_status_id' => $status === '9' ? 2 : 1,
                'last_updated' => date('Y-m-d H:i:s'),
            );

            $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

            $this->insert_tracking_log($status === '9' ? 12 : 11, $order_information_id);
            
            $this->transaction_log($order_information_id, 9, date('Y-m-d H:i:s'));
            $this->insert_remarks($remarks, $status, $order_information_id);
            $this->realtime_badge();

            $message = $status === '9' ? "Order has been completed" : "Order return to finance";

            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }


    public function cancelled_order(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);


            $order_information_id = $this->input->post('id');
            $remarks = $this->input->post('remarks');
            $user_id = $this->session->admin['user_id'];

            $order_information = array(
                'status_id' => 11,
            );

            $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

            $this->insert_tracking_log(13, $order_information_id);

            $this->transaction_log($order_information_id, 10, date('Y-m-d H:i:s'));
            $this->insert_remarks($remarks, 10, $order_information_id);
            $this->realtime_badge();

            $response = array(
                "message" => 'Successfully cancelled order',
            );
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

    public function transaction_log($order_id, $process_id, $t_date){
        $transaction_log_info = array(
            'order_id' => $order_id,
            'user_id' => $this->session->admin['user_id'],
            'process_id' => $process_id,
            'transaction_date' => $t_date,
        );

        $this->stock_ordering_model->insertTransactionLog($transaction_log_info);
    }

    public function get_product_list(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $product_list = $this->stock_ordering_model->getProductList();

            $data = array(
                "products" => $product_list
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

    public function get_product_info(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $product_id = $this->input->get('productId');
            $product_info = $this->stock_ordering_model->getProductInfo($product_id);

            $data = array(
                "product_info" => $product_info
            );

            $response = array(
                "message" => 'Successfully fetch all data.',
                "data"    => $data, 

            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;

            
        }
    }

    public function get_product_availability(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $product_id = $this->input->get('productId');
            $product_availability = $this->stock_ordering_model->getProductAvailablity($product_id);

            $data = array(
                "product_availability" => $product_availability
            );

            $response = array(
                "message" => 'Successfully fetch all data.',
                "data"    => $data, 

            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;

            
        }
    }

    public function add_product_availability(){
        switch($this->input->server('REQUEST_METHOD')){

            case 'POST':
            $_POST =  json_decode(file_get_contents("php://input"), true);

            $product_id = $this->input->post('productId');
            $store_data = $this->input->post('StoreData');
            $status = 1;

            if(isset($store_data)){
                foreach($store_data as $stores){

                    $product_availability_data = array(
                        "product_id"   => $product_id,
                        "store_id"   => $stores['store_id'],
                        'status'     => $status,
                    );
                    
                    $this->stock_ordering_model->insertProductAvailability($product_availability_data);

                }

                $message = 'Successfully added new product availability';

            }else {
                $message = "No data";
            }

            $response = array(
                "message" => $message,
            );

            header('content-type: application/json');
            echo json_encode($response);

            break;
        }
    }

    public function generate_report($file_name, $data){
        $file_name = $file_name . " " . date('F j, Y') . ".xls";

        // Headers for download 
        header("Content-Disposition: attachment; filename=\"$file_name\"");
        header("Content-Type: application/vnd.ms-excel");

        $flag = false;
        foreach($data as $row) {
            if(!$flag) {
            // display column names as first row
                echo implode("\t", array_keys($row)) . "\n";
                $flag = true;
            }

            // filter data
            echo implode("\t", array_values($row)) . "\n";
        }
        exit;
    }

    public function most_ordered_product($startDate, $endDate){
        $user_id = $this->session->admin['user_id'];

        /* For test only */
        // $user_id = 893;
        /* End */
        
        $store_ids = $this->report_model->getUserStoreIds($user_id);

        if (empty($store_ids)) {
            echo "No data was found on the date range you generated";
            exit();
        }

        // $start_date = $this->input->post('startDate');
        // $end_date = $this->input->post('endDate');

        /* For test only */
        // $start_date = '2023-07-01';
        // $end_date = '2023-07-25';
        $start_date = $startDate;
        $end_date = $endDate;
        /* End */

        // print_r($store_ids);

        // echo "\n";
        // echo $start_date . "\n" . $end_date . "\n";
        // echo "\n";

        $order_ids = $this->report_model->getOrderIds($store_ids, $start_date, $end_date);

        if (empty($order_ids)) {
            echo "No data was found on the date range you generated";
            exit();
        }
        
        // print_r($order_ids);

        $most_ordered_product = $this->report_model->getMostOrderedProduct($order_ids);
        // print_r($most_ordered_product);

        $file_name = "Most Ordered Product as of";

        $this->generate_report($file_name, $most_ordered_product);
    }

    public function import_view(){
        $this->load->view('stock_ordering/import_view');
    }

    public function import_si($order_information_id, $path, $table_name){

        if (isset($path)) {
            $object = PHPExcel_IOFactory::load($path);

            $worksheet = $object->getActiveSheet();
            /*countforA = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnA = $worksheet->getCell('A' . $row->getRowIndex())->getValue();

                if ($columnA != null || $columnA != '') {
                    $countforA = $countforA + 1;
                }
            }

            if ($countforA > 0) {
                return "There's an error in Column A. Import of the file will be aborted.";
            }*/

            $countforA = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnA = $worksheet->getCell('A' . $row->getRowIndex())->getValue();

                if ($columnA === null || $columnA === '') {
                    $countforA = $countforA + 1;
                }
            }

            if ($countforA > 0) {
                return "There's an error in Column A. Import of the file will be aborted.";
            }

            $countforB = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnB = $worksheet->getCell('B' . $row->getRowIndex())->getValue();

                if ($columnB === null || $columnB === '') {
                    $countforB = $countforB + 1;
                }
            }

            if ($countforB > 0) {
                return "There's an error in Column B. Import of the file will be aborted.";
            }

            $countforC = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnC = $worksheet->getCell('C' . $row->getRowIndex())->getValue();

                if ($columnC === null || $columnC === '') {
                    $countforC = $countforC + 1;
                }
            }

            if ($countforC > 0) {
                return "There's an error in Column C. Import of the file will be aborted.";
            }

            $countforD = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnD = $worksheet->getCell('D' . $row->getRowIndex())->getValue();

                if ($columnD === null || $columnD === '') {
                    $countforD = $countforD + 1;
                }
            }

            if ($countforD > 0) {
                return "There's an error in Column D. Import of the file will be aborted.";
            }

            $countforE = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnE = $worksheet->getCell('E' . $row->getRowIndex())->getValue();

                if ($columnE === null || $columnE === '') {
                    $countforE = $countforE + 1;
                }
            }

            if ($countforE > 1) {
                return "There's an error in Column E. Import of the file will be aborted.";
            }

            $countforF = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnF = $worksheet->getCell('F' . $row->getRowIndex())->getValue();

                if ($columnF === null || $columnF === '') {
                    $countforF = $countforF + 1;
                }
            }

            if ($countforF > 0) {
                return "There's an error in Column F. Import of the file will be aborted.";
            }

            $countforG = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnG = $worksheet->getCell('G' . $row->getRowIndex())->getValue();

                if ($columnG === null || $columnG === '') {
                    $countforG = $countforG + 1;
                }
            }

            if ($countforG > 0) {
                return "There's an error in Column G. Import of the file will be aborted.";
            }

            $countforH = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnH = $worksheet->getCell('H' . $row->getRowIndex())->getValue();

                if ($columnH === null || $columnH === '') {
                    $countforH = $countforF + 1;
                }
            }

            if ($countforH > 0) {
                return "There's an error in Column H. Import of the file will be aborted.";
            }

            /*$countforI = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnI = $worksheet->getCell('I' . $row->getRowIndex())->getValue();

                if ($columnI === null || $columnI === '') {
                    $countforI = $countforI + 1;
                }
            }

            if ($countforI > 0) {
                return "There's an error in Column I. Import of the file will be aborted.";
            }

            $countforJ = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $columnJ = $worksheet->getCell('J' . $row->getRowIndex())->getValue();

                if ($columnJ === null || $columnJ === '') {
                    $countforJ = $countforJ + 1;
                }
            }

            if ($countforJ > 0) {
                return "There's an error in Column J. Import of the file will be aborted.";
            }*/

            foreach($object->getWorksheetIterator() as $worksheet) {
                $highestRow     =    $worksheet->getHighestRow();
                $highestColumn  =    $worksheet->getHighestColumn();

                for ($row=2; $row<=$highestRow; $row++) { 
                    $requested_date      = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $si                  = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $store               = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $multim_product_code = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $multim_product_name = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $quantity            = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    //$uom                 = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $total               = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

                    // "quantity" => (preg_match('/(\d+\.\d+)\s+(.+)/', $unit_of_measure, $matches)) ? $matches[1] : "",
                    // "uom" => (preg_match('/(\d+\.\d+)\s+(.+)/', $unit_of_measure, $matches)) ? $matches[2] : "",

                    $data[] = array(
                        "order_id" => $order_information_id,
                        "invoice_date" => $requested_date,
                        "si" => preg_replace("/[^0-9]/", "", $si),
                        "store" => $store,
                        "multim_product_code" => $multim_product_code,
                        "multim_product_name" => $multim_product_name,
                        //"uom" => $uom,
                        "quantity" => $quantity,
                        "total" => (preg_match('/[\d,]+(?:\.\d+)?/', $total, $matches)) ? clean_str_for_decimal($matches[0]) : ""
                    );
                }
            }

            $import = $this->stock_ordering_model->insertSiTb($data, $table_name);

            if (!$import) {
                $message = "";
            }else{
                $message = "Failed!";
            }

        }

        return $message;
    }

    public function import_pay_bill_payment($path, $order_information_data){

        if (isset($path)) {
            $object = PHPExcel_IOFactory::load($path);

            foreach($object->getWorksheetIterator() as $worksheet) {
                $highestRow     =    $worksheet->getHighestRow();
                $highestColumn  =    $worksheet->getHighestColumn();

                for ($row=2; $row<=$highestRow; $row++) { 
                    $selected      = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $reference_no  = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $ap_total      = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $total_payment = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $wtax_amt      = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

                    $data[] = array(
                        "order_id" => $order_information_data[0]['orderId'],
                        "selected" => $selected,
                        "reference_no" => $reference_no,
                        "ap_total" => $ap_total,
                        "total_payment" => $total_payment,
                        "wtax_amt" => $wtax_amt
                    );
                }
            }

            $message = "";

            if(count($order_information_data) != count($data)){
                if(count($order_information_data) != count($data)){
                    $message = "Invoice row length does not match file selected length";
                    return $message;
                }
            }

            $matching_invoice = array_fill(0, count($order_information_data), false);
            $index = 0;

            foreach($order_information_data as $payment){
                foreach($data as $xlsData){
                    if($payment['invoice'] == $xlsData['reference_no']){
                        $matching_invoice[$index] = true;
                    }
                }
                $index++;
            }

            for($i=0; $i < count($matching_invoice); $i++){
                if($matching_invoice[$i] == false){
                    $message = 'Selected invoices does not match from the uploaded file';
                    break;
                }
            }

            $this->stock_ordering_model->insertPayBillPaymentTb($data);
        }


        return $message;
    }


    public function realtime_badge(){
        $user_id = $this->session->admin['user_id'];
        $isAdmin = $this->ion_auth->is_admin();

        $store = $this->stock_ordering_model->getStoreIdByUserId($user_id, $isAdmin);

        $real_time_notification = array(
            "store_id" => $store,
            "message" => ""
        );
        
        notify('admin-stock-ordering','stockorder-process', $real_time_notification);
    }

    public function insert_remarks($remarks, $order_status_id, $order_information_id){

        $user_id = $this->session->admin['user_id'];

        if (isset($remarks) && !empty($remarks)) {

            $remarks_information = array(
                'order_information_id' => $order_information_id,
                'order_status_id' => $order_status_id,
                'remarks' => $remarks,
                'user_id' => $user_id,
                'date'    => date('Y-m-d H:i:s'),
            );

           $this->stock_ordering_model->insertRemarks($remarks_information);

        }

    }

    public function insert_tracking_log($tracking_type_id, $order_information_id){

        $user_id = $this->session->admin['user_id'];

        $remarks_information = array(
            'order_id' => $order_information_id,
            'tracking_type_id' => $tracking_type_id,
            'datetime'    => date('Y-m-d H:i:s'),
            'user_id' => $user_id,
        );

        $this->stock_ordering_model->insertTracking($remarks_information);

    }


    public function full_franchisee_check($order_id){
        $franchise_type_id = $this->stock_ordering_model->getFranchiseType($order_id)->franchise_type_id;
        return $franchise_type_id === 2 ? true : false;
    }

    public function get_all_store(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $stores = $this->stock_ordering_model->getAllStore();
            
                $data = array(
                    "stores" => $stores,
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

    public function compute_final_cost($productId, $order_information_id, $deliveryQuantity, $isTotalCost){
        /* Code that computes the final cost per product */

         $product = $this->stock_ordering_model->getProductCost($productId);
         $product_cost = $product->cost ?? 1; //set to 1 if not exist in the database

         $store = $this->stock_ordering_model->getStoreId($order_information_id);
         $store_id = $store->store_id;
         $category_id = $store->order_type_id;

         $store_multiplier = $this->stock_ordering_model->getProductMultiplier($store_id, $category_id);

         $multiplier = $store_multiplier->product_multiplier ?? 1; //set to 1 if not exist in the database
        
         if($isTotalCost){
            return $product_cost * $multiplier * $deliveryQuantity;
         }else{
            return $product_cost * $multiplier;
         }

    }
   
  
}