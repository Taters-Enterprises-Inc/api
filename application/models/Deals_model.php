<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Deals_model extends CI_Model 
{
	
  	//inset order data
	public function insert_client_orders($data)
	{
		$this->db->trans_start();
		$this->db->insert_batch('deals_order_items', $data);
		$this->db->trans_complete();
		return  $this->db->trans_status();
	}

	//insert transaction data
	public function insert_pickup_transaction_details($data)
	{   
		$this->db->trans_start();
		$this->db->insert('deals_redeems_tb', $data);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		
		$id = ($this->db->trans_status() === FALSE) ? 0 : $insert_id;
		return  json_decode(json_encode(array('status'=>$this->db->trans_status(),'id'=>$id)), FALSE);
	}
  
	//get client id
	public function get_facebook_client_id($oauth_id){
	  $this->db->select('id');
	  $this->db->where('oauth_uid', $oauth_id);
	  $query = $this->db->get('fb_users');
	  $data = $query->result_array();
	  return $data[0]['id'];
	}

	//insert client 
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
		  $this->db->select('*');
		  $this->db->from('dotcom_deals_tb');
		  $this->db->where('hash',$hash);
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
		  $this->db->where('product_variant_options_id is NOT NULL');
		  $query = $this->db->get();
		  return $query->result();
		}
	
		return null;

	}
	
	function getDealProductVariantsWithSelectedOption($product_id=null, $selected_option=null){
		if($product_id !== null && $selected_option !== null){
			
			$this->db->select('*');
			$this->db->from('product_variant_options_tb');
			$this->db->where('id',$selected_option);
			$query_product_variant_option = $this->db->get();
			$product_variant_option = $query_product_variant_option->row();

			$this->db->select('*');
			$this->db->from('product_variants_tb');
			$this->db->where('id !=',$product_variant_option->product_variant_id);
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
			A.status,
			A.hash,
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

}
