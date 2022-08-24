<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Catering_model extends CI_Model 
{
    
    public function get_user_booking_history($id,$type){
        $this->db->select('
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.status,
            A.hash_key,
        ');
        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'A.client_id = B.id' ,'left');
        // $this->db->join('raffle_coupon_code_tb D', 'A.raffle_coupon_code = D.raffle_coupon_code' ,'left');
        if ($type == 'mobile') {
            $this->db->where('B.mobile_user_id', $id);
        } else {
            $this->db->where('B.fb_user_id', $id);
        }
        $this->db->order_by('A.dateadded','DESC');
        $query = $this->db->get();
        return $query->result();
    }
}