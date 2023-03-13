<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discount_model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }

    public function updateDiscountUser($id, $data){
        $this->db->where('id', (int)$id);
        $this->db->update('discount_users', $data);
    }

    public function insertDiscountUser($data){
        $this->db->insert('discount_users', $data);
    }

    public function getUserDiscountById($id){
        $this->db->select('
            id,
            first_name,
            middle_name,
            last_name,
            birthday,
            id_number,
            id_front,
            id_back,
            discount_id,
            status
        ');

        $this->db->from('discount_users');
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        return $query->row();
    }

    public function getUserDiscount($fb_user_id, $mobile_user_id){
        $this->db->select('
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.id_front,
            A.id_back,
            A.discount_id,
            A.status,

            B.name AS discount_name,
            B.percentage
        ');

        $this->db->from('discount_users A');
        $this->db->join('discount B','B.id = discount_id');
        
        if(isset($fb_user_id)){
            $this->db->where('A.fb_user_id', $fb_user_id);
        }elseif(isset($mobile_user_id)){
            $this->db->where('A.mobile_user_id', $mobile_user_id);
        }

        $query = $this->db->get();
        return $query->row();
    }

    

    public function getAvailableUserDiscount($fb_user_id, $mobile_user_id){
        $this->db->select('
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.id_front,
            A.id_back,
            A.discount_id,
            A.status,

            B.name AS discount_name,
            B.percentage
        ');

        $this->db->from('discount_users A');
        $this->db->join('discount B','B.id = discount_id');
        $this->db->where('A.status', 3);
        
        if(isset($fb_user_id)){
            $this->db->where('fb_user_id', $fb_user_id);
        }elseif(isset($mobile_user_id)){
            $this->db->where('mobile_user_id', $mobile_user_id);
        }

        $query = $this->db->get();
        return $query->row();
    }
}