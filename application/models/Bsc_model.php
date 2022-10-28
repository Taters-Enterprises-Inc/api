<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bsc_model extends CI_Model {

	public function __construct(){
        $this->newteishopDB =  $this->load->database('default', TRUE, TRUE);
        $this->db = $this->load->database('bsc', TRUE, TRUE);
    }
    
    public function getUser($user_id){
        $this->db->select('
            A.id,
            B.first_name,
            B.last_name,
            B.designation,
            B.phone_number,
        ');

        $this->db->from('users A');
        $this->db->join('user_profile B', 'B.user_id = A.id');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
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
    
    
    public function getUserCompanies($user_id){
        
        $this->db->select("
            B.name,
        ");

        $this->db->from('user_companies A');
        $this->db->join('companies B', 'B.id = A.company_id');
        $this->db->where('A.user_id',$user_id);
        

        $query = $this->db->get();
        return $query->result();
    }

    function updateUserStores($user_id, $stores){

        $this->db->where('user_id', $user_id);
        $this->db->delete('user_stores');

        foreach ($stores as $key => $value) {
            $data = array(
                'user_id'     => $user_id,
                'store_id'       => $value['store_id']
            );
            $this->db->insert('user_stores', $data);
            $insert_id[] = $this->db->insert_id();
        }
    }

    public function getUserStores($user_id){
        
        $this->db->select("
            B.name,
            B.store_id,
            B.available_start_time,
            B.available_end_time,
        ");

        $this->db->from('user_stores A');
        $this->db->join($this->newteishopDB->database.'.store_tb B', 'B.store_id = A.store_id');
        $this->db->where('A.user_id',$user_id);
        

        $query = $this->db->get();
        return $query->result();
    }

    public function getUsers($row_no, $row_per_page, $order_by,  $order, $search){
        $this->db->select("
            A.id,
            A.active,
            A.email,
            
            B.first_name,
            B.last_name,
            B.designation,
            B.user_status_id,
        ");

        $this->db->from('users A');
        $this->db->join('user_profile B', 'B.user_id = A.id');

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