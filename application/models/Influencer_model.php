<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Influencer_model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }

    public function insertInfluencer($data){
        $this->db->insert('influencers', $data);
    }
    
    public function getInfluencer($fb_user_id, $mobile_user_id){
        $this->db->select('
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.id_front,
            A.id_back,
            A.status,
        ');

        $this->db->from('influencers A');
        
        if(isset($fb_user_id)){
            $this->db->where('A.fb_user_id', $fb_user_id);
        }elseif(isset($mobile_user_id)){
            $this->db->where('A.mobile_user_id', $mobile_user_id);
        }

        $query = $this->db->get();
        return $query->row();
    }
    
    public function getInfluencerById($id){
        $this->db->select('
            id,
            first_name,
            middle_name,
            last_name,
            birthday,
            id_number,
            id_front,
            id_back,
            status
        ');

        $this->db->from('influencers');
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        return $query->row();
    }

}