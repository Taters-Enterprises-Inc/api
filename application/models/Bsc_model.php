<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bsc_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('bsc', TRUE, TRUE);
    }
    

    public function getUserGroups($user_id){
        
        $this->db->select("
            B.id,
            B.name,
            B.description,
        ");

        $this->db->from('users_groups A');
        $this->db->join('groups B', 'B.id = A.group_id');
        $this->db->where('A.user_id',$user_id);
        

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getUsers($row_no, $row_per_page, $order_by,  $order, $search){
        $this->db->select("
            A.id,
            A.active,
            A.first_name,
            A.last_name,
            A.email
        ");

        $this->db->from('users A');

        if($search){
            $this->db->group_start();
            $this->db->like('A.first_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like('A.email', $search);
            $this->db->group_end();
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();

        return $query->result();
    }
    
    public function getUsersCount($search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('users A');

        if($search){
            $this->db->group_start();
            $this->db->like('A.first_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like('A.email', $search);
            $this->db->group_end();
        }
            
        $query = $this->db->get();
        return $query->row()->all_count;
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