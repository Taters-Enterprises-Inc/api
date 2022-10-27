<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bsc_model extends CI_Model {
	public function __construct(){
        $this->db = $this->load->database('bsc', TRUE, TRUE);
    }

    public function getAllCompanies(){
        $this->db->select('
            id,
            name,
        ');

        $this->db->from('companies');
        $query = $this->db->get();
        return $query->result();
    }

    public function insertUserProfile($user_details){
		$this->db->insert('user_profile', $user_details);
    }

    public function insertUserStore($user_id, $store_id){
        $user_store = array(
            "user_id" => $user_id,
            "store_id" => $store_id,
        );
		$this->db->insert('user_stores', $user_store);
    }
    
    public function insertUserCompany($user_id, $company_id){
        $user_company = array(
            "user_id" => $user_id,
            "company_id" => $company_id,
        );
		$this->db->insert('user_companies', $user_company);
    }

    public function getUserProfile($user_id){
        $this->db->select('
            user_status_id,
        ');

        $this->db->from('user_profile');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }

}