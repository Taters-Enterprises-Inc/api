<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Deals_model extends CI_Model 
{
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
