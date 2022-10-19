<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discount_model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }

    public function insertDiscountUser($data){
        $insert = $this->db->insert('discount_users', $data);
    }

    public function getUserDiscount($fb_user_id, $mobile_user_id){
        $this->db->select('
            id,
            first_name,
            middle_name,
            last_name,
            birthday,
            id_number,
            id_front,
            id_back,
            discount_type_id,
            status
        ');

        $this->db->from('discount_users');
        
        if(isset($fb_user_id)){
            $this->db->where('fb_user_id', $fb_user_id);
        }elseif(isset($mobile_user_id)){
            $this->db->where('mobile_user_id', $mobile_user_id);
        }

        $query = $this->db->get();
        return $query->row();
    }
}