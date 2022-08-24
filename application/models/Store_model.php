<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_model extends CI_Model 
{
	

    public function fetch_ncr()
    {
    	$this->db->select('dotcom_stores.name as nameofstore, dotcom_stores.address as address,
    					   IFNULL(dotcom_stores.contact_number, "No contact number") as contactno,
    					   dotcom_stores.operating_hours as operatinghours, dotcom_stores.map_link as maplink, store_tb.store_image');
    	$this->db->from('dotcom_stores');
    	$this->db->join('dotcom_locale_tb', 'dotcom_stores.locale = dotcom_locale_tb.id' ,'right');
    	$this->db->join('store_tb', 'store_tb.store_id = dotcom_stores.store_id');
    	$this->db->where('dotcom_stores.status', 1);
        $this->db->where('dotcom_locale_tb.id', 1);
        $this->db->order_by('dotcom_stores.name');
    	$query = $this->db->get();
        return $query->result();
    }

    public function fetch_luzon()
    {
    	$this->db->select('dotcom_stores.name as nameofstore, dotcom_stores.address as address,
    					   IFNULL(dotcom_stores.contact_number, "No contact number") as contactno,
    					   dotcom_stores.operating_hours as operatinghours, dotcom_stores.map_link as maplink, store_tb.store_image');
    	$this->db->from('dotcom_stores');
    	$this->db->join('dotcom_locale_tb', 'dotcom_stores.locale = dotcom_locale_tb.id' ,'right');
    	$this->db->join('store_tb', 'store_tb.store_id = dotcom_stores.store_id');
    	$this->db->where('dotcom_stores.status', 1);
        $this->db->where('dotcom_locale_tb.id', 2);
        $this->db->order_by('dotcom_stores.name');
    	$query = $this->db->get();
        return $query->result();
    }

    public function fetch_visayas()
    {
    	$this->db->select('dotcom_stores.name as nameofstore, dotcom_stores.address as address,
    					   IFNULL(dotcom_stores.contact_number, "No contact number") as contactno,
    					   dotcom_stores.operating_hours as operatinghours, dotcom_stores.map_link as maplink, store_tb.store_image');
    	$this->db->from('dotcom_stores');
    	$this->db->join('dotcom_locale_tb', 'dotcom_stores.locale = dotcom_locale_tb.id' ,'right');
    	$this->db->join('store_tb', 'store_tb.store_id = dotcom_stores.store_id');
    	$this->db->where('dotcom_stores.status', 1);
        $this->db->where('dotcom_locale_tb.id', 3);
        $this->db->order_by('dotcom_stores.name');
    	$query = $this->db->get();
        return $query->result();
    }
	
    public function fetch_mindanao()
    {
    	$this->db->select('dotcom_stores.name as nameofstore, dotcom_stores.address as address,
    					   IFNULL(dotcom_stores.contact_number, "No contact number") as contactno,
    					   dotcom_stores.operating_hours as operatinghours, dotcom_stores.map_link as maplink, store_tb.store_image');
    	$this->db->from('dotcom_stores');
    	$this->db->join('dotcom_locale_tb', 'dotcom_stores.locale = dotcom_locale_tb.id' ,'right');
    	$this->db->join('store_tb', 'store_tb.store_id = dotcom_stores.store_id');
    	$this->db->where('dotcom_stores.status', 1);
        $this->db->where('dotcom_locale_tb.id', 4);
        $this->db->order_by('dotcom_stores.name');
    	$query = $this->db->get();
        return $query->result();
    }

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
			D.name as nameofstore, D.address as address,
    		IFNULL(D.contact_number, "No contact number") as contactno,
    		D.operating_hours as operatinghours, D.map_link as maplink , 
			( 3959 * acos( cos( radians('.$latitude.') ) * cos( radians( A.lat ) ) * cos( radians( A.lng ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( A.lat ) ) ) ) AS distance 
			FROM `store_tb` A join region_store_combination_tb B ON B.region_store_id = A.region_store_combination_id 
			join region_tb C ON C.id = B.region_id join dotcom_stores D ON D.store_id = A.store_id  WHERE A.status = 1 ORDER BY distance
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
			  'latitude'   		=> $value->distance,
			  'longitude'   	=> $value->distance,
			  'menu_type'        => $value->menu_type,
			  'store_image'      => $value->store_image,
			  'region_store_id'  => $value->region_store_id,
			  'action'           => 'delivery',
			  'opening_time'     => $value->opening,
			  'closing_time'     => $value->closing,
			  'disable_delivery' => $value->disable_delivery,
			  'disable_pickup'   => $value->disable_pickup,
			  'nameofstore'   => $value->nameofstore,
			  'contactno'   => $value->contactno,
			  'address'   => $value->address,
			  'operatinghours'   => $value->operatinghours,
			  'maplink'   => $value->maplink
			);  
		}
	  
		$reindex_data = array_values($region_data);
		return $reindex_data;

		
	}

}
