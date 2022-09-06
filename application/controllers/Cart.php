<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Cart extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('shop_model');
        $this->load->library('images');
		$this->load->model('catering_model');
	}
    
	public function shop(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				
                $prod_id = (int)$post['prod_id'];
                $product_details = $this->shop_model->get_details($prod_id)[0];
				$prod_image_name = $post['prod_image_name'];

                $product_sku_price = 0;
                $prod_flavor = NULL;
                $prod_size = NULL;
                if(isset($post['prod_flavor']) || isset($post['prod_size'])){

                    if(isset($post['prod_flavor']) ){
                        $varx[] = (int)$post['prod_flavor'];
                        $prod_flavor = $this->shop_model->fetch_variants_details($post['prod_flavor']);
                    }
                    
                    if(isset($post['prod_size']) ){
                        $varx[] = (int)$post['prod_size'];
                        $prod_size = $this->shop_model->fetch_variants_details($post['prod_size']);
                    }

                    $varxz = array_values(array_filter($varx));
                    $product_sku = $this->shop_model->fetch_product_sku($varxz);
                    if(!empty($product_sku)){
                        $product_sku_price = $product_sku->price;
                    }else{
                        $product_sku_price = $product_details->price;
                    }
                }

                $product_price = (empty($varx)) ? $product_details->price : $product_sku_price;
                $prod_calc_amount   = $post['prod_calc_amount'];



                $set_value['prod_id']               = $prod_id;
                $set_value['prod_image_name']       = $prod_image_name;
                $set_value['prod_name']             = $product_details->name;
                $set_value['prod_qty']              = (int)$post['prod_qty'];
                $set_value['prod_price']            = (int)$product_price;
                $set_value['prod_calc_amount']      = $prod_calc_amount;
                $set_value['prod_flavor']           = (empty($prod_flavor)) ? '' : $prod_flavor->name;
                $set_value['prod_flavor_id']        = isset($post['prod_flavor']) ? $post['prod_flavor']: '';
                $set_value['prod_with_drinks']      = $post['prod_with_drinks'] ? 1 : 0;
                $set_value['prod_size']             = (empty($prod_size)) ? '' : $prod_size->name;
                $set_value['prod_size_id']          = isset($post['prod_size']) ? $post['prod_size']: '';
                $set_value['prod_multiflavors']     = isset($post['flavors_details']) ? $post['flavors_details']: '';
                $set_value['prod_sku_id']           = $post['prod_sku_id'];
                $set_value['prod_sku']              = $post['prod_sku'];
                $set_value['prod_discount']         = 0;
                $set_value['prod_category']         = $product_details->category;

                $_SESSION['orders'][] = $set_value;

				$response = array(
					'message' => 'Successfully add to cart item'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
            case 'GET':
                    $id = $this->input->get('id');
                    $order_item = $_SESSION['orders'][$id];
                    $product = $this->shop_model->get_product_by_id($order_item['prod_id']);
                    $product_addson = $this->shop_model->get_product_addons_join($order_item['prod_id']);
                    $product_size =  $this->shop_model->fetch_product_variants($order_item['prod_id'] ,'size');
                    $product_flavor = $this->shop_model->fetch_product_variants($order_item['prod_id'],'flavor');
                    $product_date = $this->shop_model->fetch_product_variants($order_item['prod_id'],'date');
                    $suggested_products = $this->shop_model->get_suggested_product($order_item['prod_id']);
                    $product_images = $this->images->product_images(
                        'assets/images/shared/products/500',
                        basename($product->product_image,'.jpg')
                    );
        
                    
                $data = array(
                    "product"=>$product,
                    "order_item"=>$order_item,
                    "product_addson"=>$product_addson,
                    "product_size"=> $product_size,
                    "product_flavor"=>$product_flavor,
                    "product_date"=>$product_date,
                    "suggested_products"=>$suggested_products,
                    "product_images"=>$product_images,
                    "_SESSION"=>  $_SESSION['orders'][$id]
                );
        
                $response = array("message"=>"successfully fetch data","data"=>$data);
                
                header('content-type: application/json');
                echo json_encode($response);
                break;
            case "PUT":
                $put = json_decode(file_get_contents("php://input"), true);
                $order_item = $_SESSION['orders'][$put['product_id']];

                $_SESSION['orders'][$put['product_id']]['prod_qty'] = $put['quantity'];
                $_SESSION['orders'][$put['product_id']]['prod_size_id'] = $put['currentFlavor'];
                $_SESSION['orders'][$put['product_id']]['prod_flavor_id'] = $put['currentSize'];
                $_SESSION['orders'][$put['product_id']]['prod_flavor'] = $put['flavorName'];
                $_SESSION['orders'][$put['product_id']]['prod_size'] = $put['sizeName'];
                $_SESSION['orders'][$put['product_id']]['prod_calc_amount'] = $put['total_amount'];

                $response = array("message"=>"Edit Successfully ","data"=> array("order"=>$_SESSION['orders'][$put['product_id']]['prod_flavor_id'] ,"f"=>$put['currentFlavor']));
                header('content-type: application/json');
                echo json_encode($response);
            
            break;
            case 'DELETE':
				    $item_index = $this->input->get('item-index');
                    
                    if(isset($_SESSION['orders'])){
                        unset($_SESSION['orders'][$item_index]);
                        $reindexed_array = array_values($_SESSION['orders']);
                        $this->session->set_userdata('orders', $reindexed_array);
                    }
            
                    $response = array(
                        'message' => 'Successfully remove item from cart'
                    );

                    header('content-type: application/json');
                    echo json_encode($response);
                break;
		}
	}
    
    
	public function catering(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				
                $prod_id = (int)$post['prod_id'];
                $product_details = $this->catering_model->get_details($prod_id);
				$prod_image_name = $post['prod_image_name'];
                $is_free_item = isset($post['is_free_item']) && $post['is_free_item'] === true  ? true : false;
                $package_price = $is_free_item === true ? 0 : $product_details->price;
                
                foreach ($this->catering_model->get_package_prices($prod_id) as $price) {
                    if ((int) $post['prod_qty']>= $price['min_qty']) {
                        $package_price = $price['price'];
                    }
                }
                
                $calc_price   = (int)$package_price * (int) $post['prod_qty'];

                $prod_flavor = NULL;
                $prod_size = NULL;
                
                if(isset($post['prod_flavor']) ){
                    $prod_flavor = $this->catering_model->fetch_variants_details($post['prod_flavor']);
                }
                
                if(isset($post['prod_size']) ){
                    $prod_size = $this->catering_model->fetch_variants_details($post['prod_size']);
                }


                $set_value['prod_id']               = $prod_id;
                $set_value['prod_image_name']       = $prod_image_name;
                $set_value['prod_name']             = $product_details->name;
                $set_value['prod_qty']              = (int)$post['prod_qty'];
                $set_value['prod_price']            = (int)$package_price;
                $set_value['prod_calc_amount']      = $calc_price;
                $set_value['prod_flavor']           = (empty($prod_flavor)) ? '' : $prod_flavor->name;
                $set_value['prod_flavor_id']        = isset($post['prod_flavor']) ? $post['prod_flavor']: '';
                $set_value['prod_with_drinks']      = $post['prod_with_drinks'] ? 1 : 0;
                $set_value['prod_size']             = (empty($prod_size)) ? '' : $prod_size->name;
                $set_value['prod_size_id']          = isset($post['prod_size']) ? $post['prod_size']: '';
                $set_value['prod_multiflavors']     = isset($post['flavors_details']) ? $post['flavors_details']: '';
                $set_value['prod_sku_id']           = $post['prod_sku_id'];
                $set_value['prod_sku']              = $post['prod_sku'];
                $set_value['prod_discount']         = 0;
                $set_value['prod_category']         = $product_details->category;

                if($is_free_item === true){
                    $found_existing_free_item = false;
                    $set_value['is_free_item']      = $is_free_item;
                    $set_value['free_threshold']    = $product_details->free_threshold;
                    foreach($_SESSION['orders'] as $key => $order){
                        if(isset($order['is_free_item']) && $order['is_free_item'] === true){
                            $_SESSION['orders'][$key] = $set_value;
                            $found_existing_free_item = true;
                            break;
                        }
                    }
                    if($found_existing_free_item === false){
                        $_SESSION['orders'][] = $set_value;
                    }
                }else{
                    $_SESSION['orders'][] = $set_value;
                }


				$response = array(
					'message' => 'Successfully add to cart item'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
            case 'DELETE':
				    $item_index = $this->input->get('item-index');
                    
                    if(isset($_SESSION['orders'])){
                        unset($_SESSION['orders'][$item_index]);
                        $reindexed_array = array_values($_SESSION['orders']);
                        $this->session->set_userdata('orders', $reindexed_array);
                    }
            
                    $response = array(
                        'message' => 'Successfully remove item from cart'
                    );

                    header('content-type: application/json');
                    echo json_encode($response);
                break;
		}
	}
}
