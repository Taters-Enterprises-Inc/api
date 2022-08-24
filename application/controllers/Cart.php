<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Cart extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('shop_model');
	}
	public function index(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				
                $prod_id = (int)$post['prod_id'];
                $product_details = $this->shop_model->get_details($prod_id)[0];
				$prod_image_name = $post['prod_image_name'];

                $product_sku_price = 0;
                if(isset($post['prod_flavor']) || isset($post['prod_size'])){
                    $varx[] = (int)$post['prod_flavor'];
                    $varx[] = (int)$post['prod_size'];
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

                $prod_flavor = $this->shop_model->fetch_variants_details($post['prod_flavor']);
                $prod_size = $this->shop_model->fetch_variants_details($post['prod_size']);


                $set_value['prod_id']               = $prod_id;
                $set_value['prod_image_name']       = $prod_image_name;
                $set_value['prod_name']             = $product_details->name;
                $set_value['prod_qty']              = (int)$post['prod_qty'];
                $set_value['prod_price']            = (int)$product_price;
                $set_value['prod_calc_amount']      = $prod_calc_amount;
                $set_value['prod_flavor']           = (empty($prod_flavor)) ? '' : $prod_flavor->name;
                $set_value['prod_flavor_id']        = $post['prod_flavor'];
                $set_value['prod_with_drinks']      = $post['prod_with_drinks'] ? 1 : 0;
                $set_value['prod_size']             = (empty($prod_size)) ? '' : $prod_size->name;
                $set_value['prod_size_id']          = $post['prod_size'];
                $set_value['prod_multiflavors']     = $post['flavors_details'];
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
		}
	}
}
