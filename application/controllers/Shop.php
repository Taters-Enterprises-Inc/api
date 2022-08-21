<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Shop extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('store_model');
		$this->load->model('shop_model');
		$this->load->model('user_model');
		$this->load->library('images');
	}
	
    public function upload_payment()
    {
        if (is_uploaded_file($_FILES['uploaded_file']['tmp_name'])) {
			

            $config['upload_path'] = './assets/upload/proof_payment';   // Save Folder

			// Creates a proof_payment directory if not existing
			if(!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, TRUE);

            $config['allowed_types']    = 'gif|png|jpg|jpeg';       // File Type
            $config['max_size']         = 2000; // file size kb
            $config['max_width']        = 0;
            $config['max_height']       = 0;
            $config['encrypt_name']     = TRUE; // convert file name to hash

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('uploaded_file')) { // Upload validation
                $error = $this->upload->display_errors();
                header('content-type: application/json');
                echo json_encode(array("status" => 'failed', "message" => $error));
            } else {

                // File-Uploaded-Successfull
                $data = $this->upload->data(); // Get file details
                $file_name = $data['file_name'];
                $tracking_no = $_POST['tracking_no'];
                $transaction_id = $_POST['trans_id'];

                $query_result = $this->shop_model->upload_payment($data, $file_name, $tracking_no, $transaction_id);

                // $transaction_id = $_SESSION['transaction_id'];
                // $user_id = $this->session->userdata('user_id');
                // $user_id = (isset($_SESSION['client_id'])) ? $_SESSION['client_id'] : $_SESSION['user_id'];
                // $upload_notif = $this->status_notification($transaction_id, 2, 0);

                // if ($upload_notif) {
                $user_id = $query_result['client_data']->client_id;
                // if ($query_result['upload_status'] == 1) {
                //     snap($user_id, 1, $transaction_id, 'Uploading-notification-success');
                // } else {
                //     snap($user_id, 1, $transaction_id, 'Uploading-notification-failed');
                // }

                header('content-type: application/json');
                echo json_encode(array("status" => 'success', "message" => $query_result));
            }
        } else {
            $message = 'No file';
            header('content-type: application/json');
            echo json_encode(array("status" => 'error', "message" => $message));
        }
    }

	public function orders(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$hash = $this->input->get('hash');
				$order_details = $this->shop_model->view_order($hash);
				
                if ($order_details['clients_info']->reseller_id != 0) {
                    $subtotal = $order_details['clients_info']->purchase_amount;
                    $reseller_discount = $order_details['clients_info']->reseller_discount;
                    $grand_total = $subtotal - $reseller_discount;
                    $delivery_fee = 0;
                    $voucher_amount = 0;
                } else {
                    $subtotal = $order_details['clients_info']->purchase_amount;
                    $delivery_fee = $order_details['clients_info']->distance_price;
                    if($order_details['clients_info']->discount == NULL){
                        $voucher_amount = 0;
                    }else{
                        $voucher_amount = $order_details['clients_info']->discount;
                    }
                    if($order_details['clients_info']->giftcard_discount == NULL){
                        $giftcard_amount = 0;
                    }else{
                        $giftcard_amount = $order_details['clients_info']->giftcard_discount;
                    }
                    $cod_fee = $order_details['clients_info']->cod_fee;
                    $grand_total = (int)$subtotal + (int)$delivery_fee + (int)$cod_fee - (double)$voucher_amount - (double)$giftcard_amount;
                    // $grand_total = number_format($subtotal + $delivery_fee + $cod_fee - $voucher_amount,2);
                }


				$firstname = $this->shop_model->get_facebook_details($order_details['clients_info']->fb_user_id)[0]->first_name;
				$lastname = $this->shop_model->get_facebook_details($order_details['clients_info']->fb_user_id)[0]->last_name;

				$store_id = $this->store_model->get_store_id_by_hash_key($hash);
				$delivery_hours = $this->store_model->get_delivery_hours($store_id);

				
				$response = array(
					'data' => array(
						'order' => $order_details,
						'grand_total' => $grand_total,
						'subtotal' => $subtotal,
						'delivery_fee' => $delivery_fee,
						'cod_fee' => $cod_fee,
						'firstname' => $firstname,
						'lastname' => $lastname,
						'delivery_hours' => $delivery_hours,
					),
					'message' => 'Successfully fetch product'
				);

				header('content-type: application/json');
				echo json_encode($response, JSON_PRETTY_PRINT);
				break;
		}
	}
	
	public function product(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':

				$hash = $this->input->get('hash');

				$product = $this->shop_model->get_product($hash);
				
				$product_size = $this->shop_model->fetch_product_variants($product->id,'size');
				$product_flavor = $this->shop_model->fetch_product_variants($product->id,'flavor');
				$product_date = $this->shop_model->fetch_product_variants($product->id,'date');
				// $product_images = $this->images->product_images(basename($product->product_image, '.jpg'));
				$youtube_video_ads = $this->shop_model->youtube_video_ads($product->id);
				$suggested_products = $this->shop_model->get_suggested_product($product->id);

				
				$check_with_addons = $this->shop_model->get_product_addons($product->id);
				if ($check_with_addons != null) {
					$addons = $this->shop_model->get_product_addons_join($product->id);
				}
				
				$response = array(
					'data' => array(
						'product' => $product,
						'addons' => $addons,
						'product_size' => $product_size,
						'product_flavor' => $product_flavor,
						'product_date' => $product_date,
						// 'product_images' => $product_images,
						'youtube_video_ads' => $youtube_video_ads,
						'suggested_products' => $suggested_products,
					),
					'message' => 'Successfully fetch product'
				);

				header('content-type: application/json');
				echo json_encode($response, JSON_PRETTY_PRINT);
				return;
		}
	}

	public function products(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$region_id = $this->input->get('region_id');

				if($region_id == null){
					$response = array(
						'message' => 'Unable to process your request...'
					);
					header('content-type: application/json');
					echo json_encode($response, JSON_PRETTY_PRINT);
					break;
				}


				$products = $this->shop_model->fetch_category_products($region_id,'','','','','');

				
				$response = array(
					'data' => $products,
					'message' => "Successfully fetch products"
				);

				header('content-type: application/json');
				echo json_encode($response, JSON_PRETTY_PRINT);
				return;
		}
	}
}
