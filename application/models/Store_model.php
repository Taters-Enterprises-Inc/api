<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Store_model extends CI_Model 
{
	public function getUsersStoreGroupsByStoreId($store_id){
		$this->db->select('user_id');
		
		$this->db->from('users_store_groups');	
		$this->db->where('store_id', $store_id);
		$query = $this->db->get();
		return $query->result();
	}
    public function get_store_id_by_hash_key($hash_key){
        $this->db->select('store');
        $this->db->where('hash_key', $hash_key);
        $query = $this->db->get('transaction_tb');
        $data = $query->result_array();
        return $data[0]['store'];
    }
	
    public function get_delivery_hours($store_id){
        $this->db->select('delivery_hours');
        $this->db->where('store_id', $store_id);
        $query = $this->db->get('store_tb');
        $data = $query->result_array();
        return $data[0]['delivery_hours'];
    }

    function select_region($id){
		$this->db->select('name');
		$this->db->where('id', $id);
		$query = $this->db->get('region_tb');
		return $query->row();
    }
	
    public function fetch_bank_details($id)
    {
        $this->db->select('indicator AS id,moh_type AS type,bank_name AS name, bank_account_num AS acct, bank_account_name AS acct_name, qr_code');
        $this->db->where('store_id', $id);
        $query = $this->db->get('bank_account_tb');
        return $query->result();
    }

    function get_store_schedule($store_id){
        $this->db->select('name, address, delivery_rate, minimum_rate, catering_delivery_rate, catering_minimum_rate, opening, closing, menu_type');
        $this->db->where('store_id', $store_id);
        $query = $this->db->get('store_tb');
        return $query->row();
    }

    function fetch_moh_setup($region_id){
        $this->db->select('*');
        $this->db->where('region_id', $region_id);
        $query = $this->db->get('area_moh_tb');
        return $query->row();
    }

    function check_surcharge($id){
        $this->db->select('enable_surcharge,surcharge_delivery_rate,surcharge_minimum_rate');
        $this->db->where('store_id', $id);
        $query = $this->db->get('store_tb');
        return $query->row();
    }

	public function get_store_info($id){
	  $this->db->select('store_id,region_id,name,delivery_hours,address,moh_notes');
	  $this->db->from('store_tb');
	  $this->db->where('store_id',$id);
	  $query = $this->db->get();
	  return $query->row();
	}

	public function get_stores_available(
		$latitude = 0,
		$longitude = 0,
		$service='SNACKSHOP'
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
			A.available_start_time,
			A.available_end_time,
			B.region_id,
			B.region_store_id,
			E.name as menu_name,
			F.locale_name,
			F.id as locale_id,
			A.contact_number as contactno,
			A.operating_hours as operatinghours, 
			A.map_link as maplink, 
			( 3959 * acos( cos( radians('.$latitude.') ) * cos( radians( A.lat ) ) * cos( radians( A.lng ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( A.lat ) ) ) ) AS distance 
		');

		$this->db->from('store_tb A');
		$this->db->join('region_store_combination_tb B', 'B.region_store_id = A.region_store_combination_id');
		$this->db->join('region_tb C', 'C.id = B.region_id');
		$this->db->join('store_menu_tb E', 'E.id = A.store_menu_type_id');
		$this->db->join('dotcom_locale_tb F', 'F.id = A.locale');

		switch($service){
			case 'BRANCHES':
				$this->db->where('A.branch_status', 1);
				$this->db->order_by('locale', 'ASC');
				break;
			case 'SNACKSHOP':
				$this->db->where('A.status', 1);
				break;
			case 'CATERING':
				$this->db->where('A.catering_status', 1);
				break;
			case 'POPCLUB-STORE-VISIT':
				$this->db->where('A.popclub_walk_in_status', 1);
				break;
			case 'POPCLUB-ONLINE-DELIVERY':
				$this->db->where('A.popclub_online_delivery_status', 1);
				break;
		}
		
		$this->db->order_by('distance', 'ASC');
		$query = $this->db->get();  
		$query_data = $query->result();
		$region_data = array();
		foreach ($query_data as $key => $value) {
			$region_data[$value->locale_id]['region_name']  = $value->locale_name;
			$region_data[$value->locale_id]['stores'][] = array(
			  'store_id'         => $value->store_id,
			  'store_name'       => $value->store_name,
			  'menu_name'        => $value->menu_name,
			  'store_address'    => $value->address,
			  'store_distance'   => $value->distance,
			  'latitude'   		 => $value->distance,
			  'longitude'   	 => $value->distance,
			  'menu_type'        => $value->menu_type,
			  'store_image'      => $value->store_image,
			  'region_store_id'  => $value->region_store_id,
			  'action'           => 'delivery',
			  'opening_time'     => $value->opening,
			  'closing_time'     => $value->closing,
			  'disable_delivery' => $value->disable_delivery,
			  'disable_pickup'   => $value->disable_pickup,
			  'nameofstore'   => $value->store_name,
			  'contactno'   => $value->contactno,
			  'address'   => $value->address,
			  'operatinghours'   => $value->operatinghours,
			  'maplink'   => $value->maplink,
			  'available_start_time' => $value->available_start_time,
			  'available_end_time' => $value->available_end_time,
			);  
		}
	  
		$reindex_data = array_values($region_data);
		return $reindex_data;		
	}

	
    function getAllStores(){
        $this->db->select('
            A.store_id,
            A.name,
            B.name as menu_name,
        ');
        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
        $query = $this->db->get();
        return $query->result();
    }

}
