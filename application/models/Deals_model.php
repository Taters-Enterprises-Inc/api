<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
  1 - New
  4 - Declined
  5 - Forfeited
  6 - Completed
*/

class Deals_model extends CI_Model 
{
	
	public function getUserRedeems(){
		if(!isset($_SESSION['userData'])){
			return[];
		}

		if(isset($_SESSION['userData']['oauth_uid'])){
			$fb_user_id = $this->get_facebook_client_id($_SESSION['userData']['oauth_uid']);
			$this->db->select('id');
			$this->db->from('deals_client_tb');
			$this->db->where('fb_user_id', $fb_user_id);
		}else if (isset($_SESSION['userData']['mobile_user_id'])){
			$mobile_user_id = $this->get_mobile_client_id($_SESSION['userData']['mobile_user_id']);
			$this->db->select('id');
			$this->db->from('deals_client_tb');
			$this->db->where('mobile_user_id', $mobile_user_id);
		}

		$clients_query = $this->db->get();
		$clients = $clients_query->result();

		$deals_redeems = array();

		foreach($clients as $client){
			$this->db->select('
				A.id,
				A.deal_id,
				A.redeem_code,
				A.expiration,
				A.dateadded AS date_redeemed,
				A.status,
				A.remarks,
				A.platform_id,
				B.name,
				B.hash AS deal_hash,
				B.product_image,
				B.description,
				B.original_price,
				B.minimum_purchase,
				B.promo_price,
			');

			$this->db->from('deals_redeems_tb A');
			$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
			$this->db->where('A.client_id', $client->id);

			$redeems_query = $this->db->get();
			$result =  $redeems_query->result();
			if(!empty($result)){
				$deals_redeems = array_merge($deals_redeems,$result);
			}
		}


		return $deals_redeems;
	}
	
    public function getUserPopclubRedeemHistoryCount($type, $id, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'A.client_id = B.id' ,'left');
        
        if ($type == 'mobile') {
            $this->db->where('B.mobile_user_id', $id);
        } else if($type == 'facebook') {
            $this->db->where('B.fb_user_id', $id);
        }

            
        if($search){
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getUserPopclubRedeemHistory($type, $id, $row_no, $row_per_page, $order_by,  $order, $search){

        $this->db->select('
			A.status,
            A.dateadded,
            A.redeem_code,
			A.expiration,
            A.purchase_amount,
            A.hash_key,
        ');

        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'B.id = A.client_id' ,'left');

        if ($type == 'mobile') {
            $this->db->where('B.mobile_user_id', $id);
        } else if($type == 'facebook') {
            $this->db->where('B.fb_user_id', $id);
        }

        $this->db->order_by('A.dateadded','DESC');
        
        if($search){
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('B.lname', $search);
            $this->db->or_like('A.purchase_amount', $search);
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

	public function forfeit_redeem_deal($id){
        $this->db->set('status',5);
        $this->db->where('id', $id);
        $this->db->update('deals_redeems_tb');
        $this->db->trans_complete();
	}

	public function complete_redeem_deal($id){
        $this->db->set('status',6);
        $this->db->where('id', $id);
        $this->db->update('deals_redeems_tb');
        $this->db->trans_complete();
	}

	public function get_redeem($deal_id = null){
		if($_SESSION['userData'] === null){
			return;
		}

		if(isset($_SESSION['userData']['oauth_uid'])){
			$fb_user_id = $this->get_facebook_client_id($_SESSION['userData']['oauth_uid']);
			$this->db->select('id');
			$this->db->from('deals_client_tb');
			$this->db->where('fb_user_id', $fb_user_id);
		}else if (isset($_SESSION['userData']['mobile_user_id'])){
			$mobile_user_id = $this->get_mobile_client_id($_SESSION['userData']['mobile_user_id']);
			$this->db->select('id');
			$this->db->from('deals_client_tb');
			$this->db->where('mobile_user_id', $mobile_user_id);
		}

		$clients_query = $this->db->get();
		$clients = $clients_query->result();

		$deals_redeems = array();

		foreach($clients as $client){
			$this->db->select('
				A.id,
				A.deal_id,
				A.store,
				A.redeem_code,
				A.expiration,
				A.dateadded AS date_redeemed,
				A.status,
				A.remarks,
				A.platform_id,
				B.name,
				B.hash AS deal_hash,
				B.product_image,
				B.description,
				B.original_price,
				B.minimum_purchase,
				B.promo_price,
			');
			$this->db->from('deals_redeems_tb A');
			$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
			$this->db->where('A.client_id', $client->id);
			$this->db->where('A.status', 1);

			if($deal_id !== null){
				$this->db->where('A.deal_id', $deal_id);
			}
			
			$redeems_query = $this->db->get();
			$result =  $redeems_query->result();
			if(!empty($result)){
				$deals_redeems = array_merge($deals_redeems,$result);
			}
		}

		return $deals_redeems;
	}

	public function insert_client_orders($data){
		$this->db->trans_start();
		$this->db->insert_batch('deals_order_items', $data);
		$this->db->trans_complete();
		return  $this->db->trans_status();
	}

	public function insert_redeem_transaction($data){   
		$this->db->trans_start();
		$this->db->insert('deals_redeems_tb', $data);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		
		$id = ($this->db->trans_status() === FALSE) ? 0 : $insert_id;
		return  json_decode(json_encode(array('status'=>$this->db->trans_status(),'id'=>$id)), FALSE);
	}
  
	public function get_facebook_client_id($oauth_id){
	  $this->db->select('id');
	  $this->db->where('oauth_uid', $oauth_id);
	  $query = $this->db->get('fb_users');
	  $data = $query->result_array();
	  return $data[0]['id'];
	}

	public function insert_client_details(){
	  if (isset($_SESSION['userData']['oauth_uid'])) {
		$this->db->trans_start();
		$data = array(
			'fb_user_id'        => $this->get_facebook_client_id($_SESSION['userData']['oauth_uid']),
			'email'             => ($_SESSION['userData']['email'] == "" ? "NA" : $_SESSION['userData']['email']),
			'address'           => "NA",
			'contact_number'    => "NA",
			'moh'               => 1,
			'payops'            => 0,
			'add_name'          => $_SESSION['userData']['first_name'].' '.$_SESSION['userData']['last_name'],
			'add_contact'       => "NA",
			'add_address'       => "NA"
		);
		$this->db->insert('deals_client_tb', $data);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
	  } elseif(isset($_SESSION['userData']) && $_SESSION['userData']['login_type'] == 'mobile'){
		$this->db->trans_start();
		// $address = (empty($this->input->post('checkout_address'))) ? $this->session->customer_address : $this->input->post('checkout_address');
		$data = array(
			'mobile_user_id'    => $this->get_mobile_client_id($_SESSION['userData']['mobile_user_id']),
			'email'             => ($_SESSION['userData']['email'] == "" ? "NA" : $_SESSION['userData']['email']),
			'address'           => "NA",
			'contact_number'    => "NA",
			'moh'               => 1,
			'payops'            => 0,
			'add_name'          => $_SESSION['userData']['first_name'].' '.$_SESSION['userData']['last_name'],
			'add_contact'       => "NA",
			'add_address'       => "NA"
		);
		$this->db->insert('deals_client_tb', $data);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		}
	  return  json_decode(json_encode(array('status'=>$this->db->trans_status(),'id'=>$insert_id)), FALSE);
	}
	
	public function getDealsPlatform(){
	  $this->db->select('*');
	  $this->db->from('dotcom_deals_platform');
	  $query = $this->db->get();
	  return $query->result();
	}
	
	public function getDealsCategory($id){
		$this->db->select('*');
		$this->db->from('dotcom_deals_category');
		$this->db->where('dotcom_deals_platform_id',$id);
		$query = $this->db->get();
		return $query->result();
	}

	public function getDeal($hash=null){
		if($hash !== null){
		  $this->db->select('
			A.id,
			A.name,
			A.alias,
			A.product_image,
			A.original_price,
			A.promo_price,
			A.minimum_purchase,
			A.description,
			A.seconds_before_expiration,
			A.available_start_time,
			A.available_end_time,
			A.available_days,
			A.violator_free_item,
			A.violator_save,
			A.violator_free_product_id,
			A.violator_percentage_discount,
			A.status,
			A.hash,
			C.name as category_name,
			C.dotcom_deals_platform_id AS platform_id,
		  ');
		  $this->db->from('dotcom_deals_tb A');
		  $this->db->join('dotcom_deals_platform_combination B', 'B.deal_id = A.id');
		  $this->db->join('dotcom_deals_category C', 'C.id = B.platform_category_id');
		  $this->db->where('A.hash',$hash);
		  $query = $this->db->get();
		  return $query->row();
		}
	
		return null;
	}
	
	function getDealProductsWithVariants($deals_id=null){
		if($deals_id !== null){
		  $this->db->select('*');
		  $this->db->from('dotcom_deals_product_tb');
		  $this->db->where('deal_id',$deals_id);
		  $query = $this->db->get();
		  return $query->result();
		}
	
		return null;

	}
	
	function getDealProductVariantsWithSelectedOption($product_id=null, $selected_option=null){
		if($product_id !== null ){

			if($selected_option !== null){
				$this->db->select('*');
				$this->db->from('product_variant_options_tb');
				$this->db->where('id',$selected_option);
				$query_product_variant_option = $this->db->get();
				$product_variant_option = $query_product_variant_option->row();
			}

			$this->db->select('*');
			$this->db->from('product_variants_tb');
			if($selected_option !== null){
				$this->db->where('id !=',$product_variant_option->product_variant_id);
			}
			$this->db->where('product_id',$product_id);
			$query = $this->db->get();
			return $query->result();
		  }
	  
		  return null;
	}
	
	function getProduct($product_id=null){
		if($product_id !== null){
		  $this->db->select('*');
		  $this->db->from('products_tb');
		  $this->db->where('id',$product_id);
		  $query = $this->db->get();
		  return $query->row();
		}
	
		return null;
	}
	
	function getProductVariantOption($product_variant_options_id=null){
		if($product_variant_options_id !== null){
			$this->db->select('*');
			$this->db->from('product_variant_options_tb');
			$this->db->where('id',$product_variant_options_id);
			$query = $this->db->get();
			return $query->row();
		  }
	  
		  return null;
	}

	function getProductVariantOptions($product_variant_id=null){
		if($product_variant_id !== null){
			$this->db->select('*');
			$this->db->from('product_variant_options_tb');
			$this->db->where('product_variant_id',$product_variant_id);
			$this->db->where('status',1);
			$query = $this->db->get();
			return $query->result();
		  }
	  
		  return null;
	}

	public function getDeals($platform=null, $category=null, $is_available=null, $store_id=null){
    

		if($platform !== null && $category !== null && $is_available !== null){

	     $select = '
			A.id,
			A.name, 
			A.product_image,
			A.original_price,
			A.promo_price,
			A.minimum_purchase,
			A.description,
			A.available_start_time,
			A.available_end_time,
			A.available_days,
			A.status,
			A.hash,
			A.seconds_before_expiration,
			C.name AS category_name,
		';


		 if($category == 'all'){

			// Platform
			$this->db->select('id');
			$this->db->from('dotcom_deals_platform');
			$this->db->where('url_name',$platform);
			$query_deals_platform = $this->db->get();
			$deals_platform = $query_deals_platform->row();
	  
			
			if($deals_platform == null){
			  return 'platform-not-found';
			}
	
			// Category
			$this->db->select('id');
			$this->db->from('dotcom_deals_category');
			$this->db->where('dotcom_deals_platform_id',$deals_platform->id);
			$query_deals_category = $this->db->get();
			$deals_category = $query_deals_category->result();
		
			if($deals_category == null){
				return 'category-not-found';
			}

			$dotcom_deals = array();

			foreach($deals_category as $deal_category){
				$this->db->select($select);
				$this->db->from('dotcom_deals_tb A');
				$this->db->join('dotcom_deals_platform_combination B', 'B.deal_id = A.id');
				$this->db->join('dotcom_deals_category C', 'C.id = B.platform_category_id');
				$this->db->where('A.status',$is_available);
				$this->db->where('B.platform_category_id',$deal_category->id);

				if($store_id != null){
					$this->db->join('deals_region_da_log D','D.deal_id = A.id');
					$this->db->where('D.platform_category_id',$deal_category->id);
					$this->db->where('D.status',0);
					$this->db->where('D.store_id',$store_id);
				}

				$query = $this->db->get();
				$dotcom_deals = array_merge($dotcom_deals, $query->result());
			}

			return $dotcom_deals;
		 }
	
		  // Platform
		  $this->db->select('id');
		  $this->db->from('dotcom_deals_platform');
		  $this->db->where('url_name',$platform);
		  $query_deals_platform = $this->db->get();
		  $deals_platform = $query_deals_platform->row();
	
		  
		  if($deals_platform == null){
			return 'platform-not-found';
		  }
	
		  // Category
		  $this->db->select('id');
		  $this->db->from('dotcom_deals_category');
		  $this->db->where('dotcom_deals_platform_id',$deals_platform->id);
		  $this->db->where('url_name',$category);
		  $query_deals_category = $this->db->get();
		  $deals_category = $query_deals_category->row();
	
		  if($deals_category == null){
			return 'category-not-found';
		  }
	
		  $this->db->select($select);
		  $this->db->from('dotcom_deals_tb A');
		  $this->db->join('dotcom_deals_platform_combination B', 'B.deal_id = A.id');
		  $this->db->join('dotcom_deals_category C', 'C.id = B.platform_category_id');
		  $this->db->where('A.status',$is_available);
		  $this->db->where('B.platform_category_id',$deals_category->id);

		  if($store_id != null){
			$this->db->join('deals_region_da_log D','D.deal_id = A.id');
			$this->db->where('D.platform_category_id',$deals_category->id);
			$this->db->where('D.status',0);
			$this->db->where('D.store_id',$store_id);
		  }
	
		  $query = $this->db->get();
		  return $query->result();
		}
	
		return null;
	
	  }
	  
	  //jepoy get mobile client id
	  public function get_mobile_client_id($id){
		  $this->db->select('id');
		  $this->db->where('id', $id);
		  $query = $this->db->get('mobile_users');
		  $data = $query->result_array();
		  return $data[0]['id'];
	  }

}
