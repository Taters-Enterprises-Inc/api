<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    
    public function __construct()
    {
        $this->load->database();
    }

    function getUsersByGroupId($group_id){
        $this->db->select('user_id');
        $this->db->from('users_groups');
        $this->db->where('group_id', $group_id);
        $query = $this->db->get();
        return $query->result();
    }
    
    function add_store_group($user_id, $stores){

        $this->db->where('user_id', $user_id);
        $this->db->delete('users_store_groups');


        foreach ($stores as $key => $value) {
            $data = array(
                'user_id'     => $user_id,
                'store_id'       => $value['store_id']
            );
            $this->db->insert('users_store_groups', $data);
            $insert_id[] = $this->db->insert_id();
        }
    }
     
    function get_all_store()
    {
        $this->db->select('b.store_id, b.name, c.name as menu_name');
        $this->db->from('store_tb b');
        $this->db->join('store_menu_tb c', 'c.id = b.store_menu_type_id');
        $this->db->where('b.status', 1);
        $query = $this->db->get();
        return $query->result();
    } 

    function get_store_group_order($user_id)
    {
        $this->db->select('
            a.store_id, 
            b.name, 
            b.status, 
            b.catering_status,
            b.popclub_walk_in_status, 
            b.popclub_online_delivery_status, 
            c.name as menu_name
        ');
        $this->db->from('users_store_groups a');
        $this->db->join('store_tb b', 'b.store_id = a.store_id' ,'left');
        $this->db->join('store_menu_tb c', 'c.id = b.store_menu_type_id');
        $this->db->where('a.user_id', $user_id);
        $this->db->where('b.status', 1);
        $query = $this->db->get();
        return $query->result();
    }


    function get_store_group_order_set($user_id)
    {
        $this->db->select('a.store_id, b.name ');
        $this->db->from('users_store_groups a');
        $this->db->join('store_tb b', 'b.store_id = a.store_id' ,'left');
        $this->db->where('a.user_id', $user_id);
        $query = $this->db->get();
        return $query->result();
    }

    function get_fb_user_details($id){
        $this->db->select('*');
        $this->db->from('fb_users');
        $this->db->where('oauth_uid', $id);
        $query = $this->db->get();
        return $query->row();
    }
    

    function get_mobile_user_details($id){
        $this->db->select('*');
        $this->db->from('mobile_users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
}