<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_model extends CI_Model 
{
	public function get_store_info($id){
	  $this->db->select('store_id,active_reseller_region_id,name,delivery_hours');
	  $this->db->from('store_tb');
	  $this->db->where('status',1);
	  $this->db->where('store_id',$id);
	  $query = $this->db->get();
	  return $query->row();
	}

	public function get_stores_available(
		$latitude = 0,
		$longitude = 0
	){
		$this->db->select('
			A.id,
			A.name AS store_name, 
			A.address, 
			A.lat, 
			A.lng, 
			A.store_id, 
			A.region_store_combination_id, 
			A.menu_type, 
			A.store_image, 
			A.opening, 
			A.closing, 
			A.disable_pickup, 
			A.disable_delivery, 
			B.region_id, 
			B.region_store_id, 
			C.name AS region_name, 
			C.sequence, 
			( 3959 * acos( cos( radians('.$latitude.') ) * cos( radians( A.lat ) ) * cos( radians( A.lng ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( A.lat ) ) ) ) AS distance 
			FROM `store_tb` A join region_store_combination_tb B ON B.region_store_id = A.region_store_combination_id 
			join region_tb C ON C.id = B.region_id WHERE A.status = 1 ORDER BY distance
		');

		$query = $this->db->get();  
		$query_data = $query->result();
		$region_data = array();
	
		foreach ($query_data as $key => $value) {
			$region_data[$value->region_id]['region_name']  = $value->region_name;
			$region_data[$value->region_id]['stores'][] = array(
			  'store_id'         => $value->store_id,
			  'store_name'       => $value->store_name,
			  'store_address'    => $value->address,
			  'store_distance'   => $value->distance,
			  'menu_type'        => $value->menu_type,
			  'store_image'      => $value->store_image,
			  'region_store_id'  => $value->region_store_id,
			  'action'           => 'delivery',
			  'opening_time'     => $value->opening,
			  'closing_time'     => $value->closing,
			  'disable_delivery' => $value->disable_delivery,
			  'disable_pickup'   => $value->disable_pickup
			);  
		}
	  
		$reindex_data = array_values($region_data);
		return $reindex_data;

		
	}

}
