<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
  1 - New
  4 - Declined
  5 - Forfeited
  6 - Completed
*/

class Deals_model extends CI_Model 
{
	
    public function __construct(){
        $this->load->database();

		$this->load->model('client_model');
    }

	public function getUserDeals($hash_key)
    {   
		$this->db->select('
			A.client_id,
			A.id, 
			A.redeem_code,
			A.purchase_amount,
			A.dateadded,
			A.expiration,
			A.status,
			D.name as type_of_service,
		');

		$this->db->from('deals_redeems_tb A');
		$this->db->join('dotcom_deals_platform D','D.id = A.platform_id');


		$this->db->where('A.hash_key',$hash_key);
		$query_deal_order_details = $this->db->get();
		$deal_order_details = $query_deal_order_details->row();

		$this->db->select('
			A.add_name,
			A.add_contact,
			A.add_address,
			A.email,
			A.payops,
			B.id,
			B.dateadded,
			B.store,
			B.hash_key,
			C.store_id,
			C.name as store_name,
			C.address as store_address,
			C.contact_number as store_contact_number,
			C.email as store_email,
		');

		$this->db->from('deals_client_tb A');
		$this->db->join('deals_redeems_tb B', 'B.client_id = A.id');
		$this->db->join('store_tb C', 'C.store_id = B.store');
		$this->db->where('A.id',$deal_order_details->client_id);
	

		$query_client_info = $this->db->get();
		$client_info = $query_client_info->row();

		$this->db->select('
			A.id,
			A.redeems_id,
			A.deal_id,
			A.price,
			A.product_price,
			A.remarks as deal_item_with_flavor,
			A.quantity,
			B.name,
			B.product_image,
		');

		$this->db->from('deals_order_items A');
		$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
		$this->db->where('A.redeems_id',$deal_order_details->id);
	

		$query_deal_order_items = $this->db->get();
		$deal_order_items = $query_deal_order_items->result();

		$join_data['deals_redeems']=$deal_order_details;
		$join_data['clients_info'] = $client_info;
		$join_data['deal_order_items']=$deal_order_items;

		return $join_data;

    }
	
	public function getDealsPromoDiscountDeals($store_id, $date_now){
		$this->db->select('
			B.id,
			B.hash,
			B.name,
			B.description,
			B.product_image,
			B.promo_discount_percentage,
			B.minimum_purchase,
			B.is_free_delivery,
			B.available_start_time,
			B.available_end_time,
			B.available_start_datetime,
			B.available_end_datetime,
			B.available_days,
			B.seconds_before_expiration,
		');

		$this->db->from('deals_region_da_log A');
		$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
		$this->db->where('A.store_id', $store_id);
		$this->db->where('B.promo_discount_percentage !=', null);


		$this->db->group_start();
		$this->db->where('B.available_end_datetime >=', $date_now);
		$this->db->or_where('B.available_end_datetime',null);		
		$this->db->group_end();


		$query = $this->db->get();
		return $query->result();
	}

	public function getDealProductsPromoExclude($deal_id){
		$this->db->select('product_id');
		$this->db->from('deals_product_promo_exclude');
		$this->db->where('deal_id',$deal_id);

		$query = $this->db->get();

		return $query->result();
	}
	
	public function getDealProductsPromoInclude($deal_id){
		$this->db->select('
			A.id, 
			A.product_id,
			A.product_variant_option_tb_id,
			A.quantity,
			A.promo_discount_percentage,
			B.product_hash,
		');
		$this->db->from('deals_product_promo_include A');
		$this->db->join('products_tb B','B.id = A.product_id');
		$this->db->where('A.deal_id',$deal_id);

		$query_deals_product_promo_includes = $this->db->get();
		$deals_product_promo_includes = $query_deals_product_promo_includes->result();

		foreach($deals_product_promo_includes as $deals_product_promo_include){
			$this->db->select('
				A.product_id, 
				A.product_variant_option_tb_id, 
				A.promo_discount_percentage,
				D.price,
			');
			$this->db->from('deals_product_promo_include_obtainable A');
			$this->db->join('product_variant_option_combinations_tb C','C.product_variant_option_id = A.product_variant_option_tb_id');
			$this->db->join('product_skus_tb D','D.id = C.sku_id');
			$this->db->where('A.deals_product_promo_include_id',$deals_product_promo_include->id);
	
			$query_deals_product_promo_includes_obtainable = $this->db->get();
			$deals_product_promo_includes_obtainable = $query_deals_product_promo_includes_obtainable->result();

			$deals_product_promo_include->obtainable = $deals_product_promo_includes_obtainable; 
		}
		
		return $deals_product_promo_includes;
	}

	public function getUserRedeems(){
		if(!isset($_SESSION['userData'])){
			return[];
		}

		if(isset($_SESSION['userData']['oauth_uid'])){
			$fb_user = $this->client_model->getFacebook($this->session->userData['oauth_uid']);
			$this->db->select('id');
			$this->db->from('deals_client_tb');
			$this->db->where('fb_user_id', $fb_user->id);
		}else if (isset($_SESSION['userData']['mobile_user_id'])){
			$mobile_user = $this->client_model->getMobile($this->session->userData['mobile_user_id']);
			$this->db->select('id');
			$this->db->from('deals_client_tb');
			$this->db->where('mobile_user_id', $mobile_user->id);
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
            $this->db->group_start();
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('B.lname', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getUserPopclubRedeemHistory($type, $id, $row_no, $row_per_page, $order_by,  $order, $search){

        $this->db->select('
			A.id,
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

        
        if($search){
            $this->db->group_start();
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('B.lname', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

	public function forfeit_redeem_deal($id, $today){
        $this->db->set('status',5);
        $this->db->set('cancelled_date',$today);
        $this->db->where('id', $id);
        $this->db->update('deals_redeems_tb');
        $this->db->trans_complete();
	}

	public function complete_redeem_deal($id, $today){
        $this->db->set('status',6);
        $this->db->set('completed_date',$today);
        $this->db->where('id', $id);
        $this->db->update('deals_redeems_tb');
        $this->db->trans_complete();
	}

	public function getRedeem($deal_id = null){
		if($_SESSION['userData'] === null){
			return;
		}

		if(isset($_SESSION['userData']['oauth_uid'])){
			$fb_user = $this->client_model->getFacebook($this->session->userData['oauth_uid']);
			$this->db->select('id');
			$this->db->from('deals_client_tb');
			$this->db->where('fb_user_id', $fb_user->id);
		}else if (isset($_SESSION['userData']['mobile_user_id'])){
			$mobile_user = $this->client_model->getMobile($this->session->userData['mobile_user_id']);
			$this->db->select('id');
			$this->db->from('deals_client_tb');
			$this->db->where('mobile_user_id', $mobile_user->id);
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
				A.hash_key as redeem_hash,
				B.name,
				B.hash AS deal_hash,
				B.product_image,
				B.description,
				B.original_price,
				B.promo_discount_percentage,
				B.subtotal_promo_discount,
				B.minimum_purchase,
				B.is_free_delivery,
				B.promo_price,
				C.url_name as platform_url_name,
				D.address,
				E.referral_code
			');
			$this->db->from('deals_redeems_tb A');
			$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
			$this->db->join('dotcom_deals_platform C', 'C.id = A.platform_id');
			$this->db->join('deals_client_tb D', 'D.id = A.client_id');
			$this->db->join('influencer_deals E', 'E.id = A.influencer_deal_id', 'left');
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
		$this->db->order_by('sequence', 'ASC');
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
			A.is_free_delivery,
			A.promo_discount_percentage,
			A.description,
			A.seconds_before_expiration,
			A.available_start_time,
			A.available_end_time,
			A.available_start_datetime,
			A.available_end_datetime,
			A.available_days,
			A.status,
			A.hash,
			A.subtotal_promo_discount,
			C.name as category_name,
			C.dotcom_deals_platform_id AS platform_id,
		  ');
		  $this->db->from('dotcom_deals_tb A');
		  $this->db->join('dotcom_deals_platform_combination B', 'B.deal_id = A.id');
		  $this->db->join('dotcom_deals_category C', 'C.id = B.platform_category_id');
		  $this->db->where('A.hash',$hash);
		  $this->db->group_start();
		  $this->db->where('A.available_end_datetime >=',date('Y-m-d H:i:s'));
		  $this->db->or_where('A.available_end_datetime',null);
		  $this->db->group_end();
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
			A.available_start_datetime,
			A.available_end_datetime,
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
			$this->db->order_by('sequence','ASC');
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
				$this->db->where('A.influencer_discount', null);
				$this->db->where('B.platform_category_id',$deal_category->id);
				$this->db->group_start();
				$this->db->where('A.available_end_datetime >=',date('Y-m-d H:i:s'));
				$this->db->or_where('A.available_end_datetime',null);
				$this->db->group_end();

				if($store_id != null){
					$this->db->join('deals_region_da_log D','D.deal_id = A.id');
					$this->db->where('D.platform_category_id',$deal_category->id);
					$this->db->where('D.status',1);
					$this->db->where('D.store_id',$store_id);
				}
				$this->db->order_by('A.dateadded','ASC');

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
		  $this->db->where('A.influencer_discount', null);
		  $this->db->where('B.platform_category_id',$deals_category->id);
		  
		  $this->db->group_start();
		  $this->db->where('A.available_end_datetime >=',date('Y-m-d H:i:s'));
		  $this->db->or_where('A.available_end_datetime',null);
		  $this->db->group_end();

		  if($store_id != null){
			$this->db->join('deals_region_da_log D','D.deal_id = A.id');
			$this->db->where('D.platform_category_id',$deals_category->id);
			$this->db->where('D.status',1);
			$this->db->where('D.store_id',$store_id);
		  }

		  $this->db->order_by('A.dateadded','ASC');
	
		  $query = $this->db->get();
		  return $query->result();
		}
	
		return null;
	
	  }
	  
}
