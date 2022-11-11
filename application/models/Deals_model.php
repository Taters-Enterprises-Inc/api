<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
  1 - New
  4 - Declined
  5 - Forfeited
  6 - Completed
*/

class Deals_model extends CI_Model 
{
	
    public function __construct()
    {
        $this->load->database();

		$this->load->model('client_model');
    }

	public function getDealProductsPromoExclude($deal_id){
		$this->db->select('product_id');
		$this->db->from('deals_product_promo_exclude');
		$this->db->where('deal_id',$deal_id);

		$query = $this->db->get();

		return $query->result();
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
				B.name,
				B.hash AS deal_hash,
				B.product_image,
				B.description,
				B.original_price,
				B.promo_discount_percentage,
				B.minimum_purchase,
				B.promo_price,
				C.url_name as platform_url_name,
				D.address,
			');
			$this->db->from('deals_redeems_tb A');
			$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
			$this->db->join('dotcom_deals_platform C', 'C.id = A.platform_id');
			$this->db->join('deals_client_tb D', 'D.id = A.client_id');
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
				$this->db->group_start();
				$this->db->where('A.available_end_datetime >=',date('Y-m-d H:i:s'));
				$this->db->or_where('A.available_end_datetime',null);
				$this->db->group_end();

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
		  
		  $this->db->group_start();
		  $this->db->where('A.available_end_datetime >=',date('Y-m-d H:i:s'));
		  $this->db->or_where('A.available_end_datetime',null);
		  $this->db->group_end();

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
