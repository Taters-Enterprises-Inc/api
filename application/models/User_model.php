<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    
    public function __construct()
    {
        $this->load->database();
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