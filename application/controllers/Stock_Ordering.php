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

                $getProductData = $this->stock_ordering_model->getProductData($order_id);

                $data = array(
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

                $pagination = array(
                    "total_rows" => $getOrdersCount,
                    "per_page" => $per_page,
                  );

                  $response = array(
                    "message" => 'Successfully fetch Form questions',
                    "data" => array(
                        "pagination" => $pagination,
                        "orders" => $getOrders
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
                'commited_delivery_date' => $commited_delivery_date
            );

            $this->stock_ordering_model->updateOrderInfo($order_information_id, $order_information);

            if(isset($product_data)){
                foreach($product_data as $product){
                    $product_id = $product['productId'];

                    $order_item_data = array(
                        "commited_qty"   => $product['commitedQuantity']
                    );
                    
                    $this->stock_ordering_model->updateOrderItem($product_id, $order_item_data);
                
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