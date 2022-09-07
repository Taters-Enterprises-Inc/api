<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Catering_model extends CI_Model 
{
    
    function upload_payment($data,$file_name,$tracking_no,$transaction_id,$payment_plan, $status)
    { 
        date_default_timezone_set('Asia/Manila');
        $upload_time = date('Y-m-d H:i:s');
        $file_name = $data['file_name'];

        if ($payment_plan == 'half') {
			if($status == 4 || $status == 22){
				$this->db->set('initial_payment_proof', $file_name);
				$this->db->set('status', 5);
				$this->db->set('initial_payment_upload_date', $upload_time);
			}elseif($status == 6 || $status == 23 ){
				$this->db->set('final_payment_proof', $file_name);
				$this->db->set('status', 7);
				$this->db->set('final_payment_upload_date', $upload_time);
			}
        } elseif($payment_plan == 'full') {
			if($status == 4 || $status == 23){
				$this->db->set('final_payment_proof', $file_name);
				$this->db->set('status', 7);
				$this->db->set('final_payment_upload_date', $upload_time);
			}
        }
        $this->db->where("tracking_no", $tracking_no);
        $this->db->update("catering_transaction_tb");
        // return ($this->db->affected_rows() != 1) ? false : true;
        $return_data['upload_status'] = ($this->db->affected_rows() != 1) ? false : true;

        if($return_data['upload_status'] > 0){
            
            $client_query = $this->db->select('catering_client_id')
                ->get_where('catering_transaction_tb', array('id' => $transaction_id))
                ->result();
            $return_data['client_data'] = $client_query[0];
        }
        return $return_data;
    }

    function upload_contract($data,$hash_key)
    { 

        $file_name = $data['file_name'];
		$this->db->set('uploaded_contract', $file_name);
		$this->db->set('status', 3);

        $this->db->where("hash_key", $hash_key);
        $this->db->update("catering_transaction_tb");
		
        $return_data['upload_status'] = ($this->db->affected_rows() != 1) ? false : true;

        return $return_data;
    }

    
	public function fetch_status_detail($hash_key){
        $this->db->select('status');
        $this->db->from('catering_transaction_tb');
        $this->db->where('hash_key', $hash_key);
        $query = $this->db->get();

		return $query->row();
	}
    
    public function get_logon_type($hash_key){
        $this->db->select('logon_type');
        $this->db->from('catering_transaction_tb');
        $this->db->where('hash_key', $hash_key);
        $query_logon_type = $this->db->get();
        return $query_logon_type->row();
    }
    
    public function get_facebook_details($id){
        $this->db->select("*");
        $this->db->from('fb_users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function view_order($hash_key)
    {   
        $this->db->select('hash_key');
        $this->db->from('catering_transaction_tb');
        $this->db->where('hash_key', $hash_key);
        $check_hash = $this->db->get();
        $result = $check_hash->result();

        
        if(!empty($result)){
            $table = "catering_client_tb A";
            $select_column = array("A.fb_user_id","A.fname", "A.lname", "A.email","A.address", "A.contact_number","B.id", "B.tracking_no","B.purchase_amount","B.distance_price","B.cod_fee","A.moh","A.payops","B.remarks", "B.status","B.company_name", "B.message", "B.serving_time", "B.event_class","B.dateadded","B.hash_key","B.store", "B.invoice_num","B.discount","B.payment_plan","B.initial_payment","B.final_payment","B.final_payment_proof","B.contract","B.uploaded_contract","B.start_datetime","B.end_datetime","B.night_diff_fee","Z.name AS store_name","Z.address AS store_address","Z.contact_number AS store_contact","Z.contact_person AS store_person","Z.email AS store_email","A.add_name","A.add_contact","A.add_address");
            $join_A = "A.id = B.client_id";
            $this->db->select($select_column);  
            $this->db->from($table);
            $this->db->join('catering_transaction_tb B', $join_A ,'left');
            $this->db->join('store_tb Z', 'Z.store_id = B.store' ,'left');
            $this->db->where('B.hash_key', $hash_key);

            $query_info = $this->db->get();
            $info = $query_info->result();

            $select_col = array("O.product_id","O.combination_id","O.type","O.quantity","O.status","O.remarks","O.promo_id","O.promo_price","O.sku","O.sku_id","O.price AS calc_price","O.product_price","P.product_image","P.name","P.description","P.delivery_details","P.uom","P.add_details","P.add_remarks","P.product_hash","P.note","P.product_code", "P.category","O.product_label","U.name AS freebie_prod_name");
            $this->db->from('catering_packages_tb P');
            $this->db->select($select_col);
            $this->db->join('catering_order_items O', 'P.id = O.product_id' ,'left');
            $this->db->join('catering_transaction_tb T', 'O.transaction_id = T.id' ,'left');
            $this->db->join('freebie_products_tb U', 'U.id = O.product_id' ,'left');
            $this->db->where('T.hash_key', $hash_key);
            $this->db->order_by('type','DESC');
            $query_orders = $this->db->get();
            $orders = $query_orders->result();

            $select_addon_col = array("O.product_id","O.combination_id","O.type","O.quantity","O.status","O.remarks","O.promo_id","O.promo_price","O.sku","O.sku_id","O.price AS calc_price","O.product_price","P.product_image","P.name","P.description","P.delivery_details","P.uom","P.add_details","P.add_remarks","P.product_hash","P.status", "P.category","P.note","P.product_code","O.product_label");
            $this->db->from('products_tb P');
            $this->db->select($select_addon_col);
            $this->db->join('catering_order_items O', 'P.id = O.product_id' ,'left');
            $this->db->join('catering_transaction_tb T', 'O.transaction_id = T.id' ,'left');
            $this->db->where('T.hash_key', $hash_key);
            $this->db->order_by('type','DESC');
            $query_addons = $this->db->get();
            $addons = $query_addons->result();

            $this->db->from('personnel_tb');
            $this->db->select('name,contact_number');
            $this->db->where('reference_code', $info[0]->moh);
            $this->db->where('assigned_store', $info[0]->store);
            $query_orders = $this->db->get();
            $personnel = $query_orders->result();

            $this->db->from('bank_account_tb');
            $this->db->select('*');
            $this->db->where('store_id', $info[0]->store);
            $this->db->where('indicator', $info[0]->payops);
            $query_orders = $this->db->get();
            $bank = $query_orders->result();

            $join_data['clients_info'] = $info[0];
            $join_data['order_details'] = $orders;
            $join_data['addons'] = $addons;
            $join_data['personnel'] = $personnel[0];
            $join_data['bank'] = $bank[0];
            
            return $join_data;
        }else{
            return $join_data = array();
        }

    }
    
    public function insert_transaction_details($data)
    {   
        $this->db->trans_start();
            $this->db->insert('catering_transaction_tb', $data);
            $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        $id = ($this->db->trans_status() === FALSE) ? 0 : $insert_id;
        return  json_decode(json_encode(array('status'=>$this->db->trans_status(),'id'=>$id)), FALSE);
    }
    
    public function insert_client_orders($data)
    {
        $this->db->trans_start();
        $this->db->insert_batch('catering_order_items', $data);
        $this->db->trans_complete();
        return  $this->db->trans_status();
    }

    public function insert_client_details($post)
    {  
        if (isset($_SESSION['userData']['oauth_uid'])) {
            $this->db->trans_start();
                // $address = (empty($this->input->post('checkout_address'))) ? $this->session->customer_address : $this->input->post('checkout_address');
                $data = array(
                    'fb_user_id'        => $this->get_facebook_client_id($_SESSION['userData']['oauth_uid']),
                    'email'             => $post['eMail'],
                    'address'           => $post['address'],
                    'contact_number'    => $post['phoneNumber'],
                    'moh'               => 2,
                    'payops'            => $post['payops'],
                    'add_name'          => $post['firstName'].' '.$post['lastName'],
                    'add_contact'       => $post['phoneNumber'],
                    'add_address'       => $post['address']
                );
                $this->db->insert('catering_client_tb', $data);
                $insert_id = $this->db->insert_id();
            $this->db->trans_complete();

        } elseif(isset($_SESSION['userData']) && $_SESSION['userData']['login_type'] == 'mobile'){
            $this->db->trans_start();
                $data = array(
                    'mobile_user_id'    => $this->get_mobile_client_id($_SESSION['userData']['mobile_user_id']),
                    'fname'             => $post['firstName'],
                    'lname'             => $post['lastName'],
                    'email'             => $post['eMail'],
                    'address'           => $post['address'],
                    'contact_number'    => $post['phoneNumber'],
                    'moh'               => 2,
                    'payops'            => $post['payops'],
                    'add_name'          => $post['firstName'].' '.$post['lastName'],
                    'add_contact'       => $post['phoneNumber'],
                    'add_address'       => $post['address']
                );
                $this->db->insert('catering_client_tb', $data);
                $insert_id = $this->db->insert_id();
            $this->db->trans_complete();
        } else {
            $this->db->trans_start();
                $data = array(
                    'fname'             => $post['firstName'],
                    'lname'             => $post['lastName'],
                    'email'             => $post['eMail'],
                    'address'           => $post['address'],
                    'contact_number'    => $post['phoneNumber'],
                    'moh'               => 2,
                    'payops'            => $post['payops'],
                    'add_name'          => $post['firstName'].' '.$post['lastName'],
                    'add_contact'       => $post['phoneNumber'],
                    'add_address'       => $post['address']
                );
                $this->db->insert('catering_client_tb', $data);
                $insert_id = $this->db->insert_id();
            $this->db->trans_complete();
        }
        
        require FCPATH . 'vendor/autoload.php';

        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );
        $pusher = new Pusher\Pusher(
            '8a62b17c8a9baa690edb',
            '0e16bc6f7b22f371826b',
            '1188170',
            $options
        );

        $data['message'] = ''; //put any message here
        $data['store_name'] = substr($_SESSION['cache_data']['region_name'],strpos($_SESSION['cache_data']['region_name'], "-") + 1);
        $pusher->trigger('dashboard_notifs', 'my-event', $data);

        $id = ($this->db->trans_status() === FALSE) ? 0 : $insert_id;
        return  json_decode(json_encode(array('status'=>$this->db->trans_status(),'id'=>$id)), FALSE);
    }
    
    //jepoy get mobile client id
    public function get_mobile_client_id($id){
        $this->db->select('id');
        $this->db->where('id', $id);
        $query = $this->db->get('mobile_users');
        $data = $query->result_array();
        return $data[0]['id'];
    }

    
    public function get_facebook_client_id($oauth_id){
        $this->db->select('id');
        $this->db->where('oauth_uid', $oauth_id);
        $query = $this->db->get('fb_users');
        $data = $query->result_array();
        return $data[0]['id'];
    }
    
    function get_product_prices($id){
        $this->db->select('*');
        $this->db->from('catering_package_prices_tb');
        $this->db->where('package_id',$id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_catering_product_addons($region_id){
        $this->db->select('*');
        $this->db->from('catering_product_addons_tb a');
        $this->db->join('products_tb b','b.id = a.product_id');
        $this->db->where('a.region_id',$region_id);
        $this->db->where('a.status','0');
        $query = $this->db->get();
        return $query->result();
    }

    function get_catering_addons($region_id){
        $this->db->select('*');
        $this->db->from('catering_package_addons_tb a');
        $this->db->join('catering_packages_tb b','b.id = a.product_id');
        $this->db->where('a.region_id',$region_id);
        $this->db->where('a.status','0');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_product_variants($product_id)
    {
        $this->db->select("B.id,B.name,B.product_variant_id, A.name as parent_name");
        $this->db->from('catering_package_variants_tb A');
        $this->db->join('catering_package_variant_options_tb B', 'B.product_variant_id = A.id','left');
        $this->db->where('A.product_id', $product_id);
        $this->db->where('B.status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    function fetch_variants_details($id)
    {
        $this->db->select('name');
        $this->db->where('id', $id);
        $query = $this->db->get('catering_package_variant_options_tb');
        return $query->row();
    }
    

    function get_package_prices($id){
        $this->db->select('*');
        $this->db->from('catering_package_prices_tb');
        $this->db->where('package_id',$id);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_details($id){
        $this->db->select("*");
        $this->db->from('catering_packages_tb');
        $this->db->where('id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    public function get_product($hash){
        
        $this->db->select('
            A.id, 
            A.product_image, 
            A.name, 
            A.description, 
            A.add_details, 
            A.delivery_details, 
            A.price, A.category, 
            A.num_flavor, 
            A.add_remarks,
            A.note, 
            B.category_name, 
            A.to_gc_value
        ');
        $this->db->from('catering_packages_tb A');
        $this->db->join('catering_category_tb B', 'B.id= A.category');
        $this->db->where('A.product_hash', $hash);

        $query = $this->db->get();

        return $query->row();    
    } 
    
    function fetch_category_products($region,$category,$sort_id,$min,$max,$name){
        if($region!=0){
          $region = $this->db
              ->select('product_id')
              ->get_where('catering_region_da_log', array('region_id' => $region,'status' => 1))
              ->result();
          foreach ($region as $row) {
              $disable_region_items[] = $row->product_id;
          }
          $to_disable = empty($disable_region_items) ? 0 : $disable_region_items;
        }
  
        $this->db->select("
            A.sequence,
            A.id AS category_id,
            A.category_name,
            A.description AS category_details,
            A.img AS category_image,
            A.card_bgcolor AS category_background,
            A.visibility AS visibility,
            B.id AS product_id,
            B.name AS product_name,
            B.product_image,
            B.description AS product_description,
            B.price AS product_price,
            B.product_hash,
        ");
  
        $this->db->from('catering_category_tb A');
        $this->db->join('catering_packages_tb B', 'B.category = A.id','left');
        $this->db->join('catering_package_category_tb C', 'C.product_id = B.id');
        $this->db->group_by('C.product_id');
  
        $this->db->where('A.status', 1);
        $this->db->where('B.status', 1);
  
  
        if($region != 0 && $to_disable != 0){
            $this->db->where_not_in('B.id', $to_disable);
        }
        if ($name != null) {
            $this->db->like('B.name', $name, 'both');
        }
        if ( ($min == 0 || $min != 0) && $max != 0) {
            $this->db->where("price BETWEEN '$min' AND '$max'");
        }
        if($category != 0 && $category != 14) {
            $this->db->where('A.id', $category);
        }
        if ($sort_id == 'low_high') {
            $this->db->order_by('B.price', 'ASC');
        }
        if ($sort_id == 'high_low') {
            $this->db->order_by('B.price', 'DESC');
        }
        if ($sort_id == 'a_z') {
            $this->db->order_by('B.name', 'ASC');
        }
        if ($sort_id == 'z_a') {
            $this->db->order_by('B.name', 'DESC');
        }
        $this->db->order_by("A.sequence", "asc");
        $this->db->order_by("B.name", "asc"); 
        $query = $this->db->get();
        $query_data = $query->result();
  
  
        $return_data = array();
        if($category != 14 || $category == 'prod_category_page') {
          foreach ($query_data as $key => $val) {
              $return_data[$val->sequence]['category_id']          = $val->category_id;
              $return_data[$val->sequence]['category_name']        = $val->category_name;
              $return_data[$val->sequence]['category_details']     = $val->category_details;
              $return_data[$val->sequence]['category_image']       = $val->category_image;
              $return_data[$val->sequence]['category_background']  = $val->category_background;
              $return_data[$val->sequence]['visibility']  = $val->visibility;
              $return_data[$val->sequence]['category_products'][] = array(
                  'id' => $val->product_id,
                  'name' => $val->product_name,
                  'image' => $val->product_image,
                  'description' => $val->product_description,
                  'price' => $val->product_price,
                  'hash' => $val->product_hash,
              );
          }
      }else if ($category == 14){
          foreach ($query_data as $key => $value) {
  
              $return_data[]['category_products'][] = array(
                  'category_id'      => $value->category_id,
                  'category_name'    => $value->category_name,
                  'category_details' => $value->category_details,
                  'category_image'   => $value->category_image,
                  'id'     => $value->product_id,
                  'name'   => $value->product_name,
                  'image'  => $value->product_image,
                  'description'  => $value->product_description,
                  'price'  => $value->product_price,
                  'hash'  => $value->product_hash,
              );
  
          }
      }
  
        // return re-arrange key values of array and convert to object
        $reindex = array_values($return_data);
        return $reindex;
    }
    
    public function get_user_booking_history($id,$type){
        $this->db->select('
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.status,
            A.hash_key,
        ');
        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'A.client_id = B.id' ,'left');
        // $this->db->join('raffle_coupon_code_tb D', 'A.raffle_coupon_code = D.raffle_coupon_code' ,'left');
        if ($type == 'mobile') {
            $this->db->where('B.mobile_user_id', $id);
        } else {
            $this->db->where('B.fb_user_id', $id);
        }
        $this->db->order_by('A.dateadded','DESC');
        $query = $this->db->get();
        return $query->result();
    }
}