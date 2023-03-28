<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Influencer_model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }
    
    function cashout($data){
        $this->db->trans_start();
        $this->db->insert('influencer_cashouts', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;
    }

    function uploadContract($data,$influencer_id)
    { 
        $file_name = $data['file_name'];
		$this->db->set('contract', $file_name);
		$this->db->set('status', 6);

        $this->db->where("id", $influencer_id);
        $this->db->update("influencers");
		
        $return_data['upload_status'] = ($this->db->affected_rows() != 1) ? false : true;

        return $return_data;
    }

    public function getInfluencerRefereesCount( $influencer_id, $search){
        $this->db->select('count(*) as all_count');   

        $this->db->from('transaction_tb A');
        $this->db->join('influencer_promos B','B.id = A.influencer_promo_id', 'left');
        $this->db->join('client_tb C', 'C.id = A.client_id', 'left');
        $this->db->where('B.influencer_id', $influencer_id);
        $this->db->where('A.status', 6);


        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('C.client_name', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getInfluencerReferees($influencer_id,$row_no, $row_per_page, $order_by,  $order, $search){

        $this->db->select('
            A.id,
            A.tracking_no,
            A.discount,
            A.influencer_discount,
            A.dateadded,
            C.add_name as client_name,
        ');

        $this->db->from('transaction_tb A');
        $this->db->join('influencer_promos B', 'B.id = A.influencer_promo_id');
        $this->db->join('client_tb C', 'C.id = A.client_id');
        $this->db->where('B.influencer_id', $influencer_id);
        $this->db->where('A.status', 6);


        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('C.client_name', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

    public function getInfluencerDealByReferralCode($referral_code){
        $this->db->select('id');

        $this->db->from('influencer_deals');
        $this->db->where('referral_code', $referral_code);

        $query = $this->db->get();
        return $query->row();
    }

    public function insertInfluencerProfile($data){
        $this->db->trans_start();
        $this->db->insert('influencer_profiles', $data);
        $this->db->trans_complete();
    }

    public function insertInfluencer($data){
        $this->db->trans_start();
        $this->db->insert('influencers', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;
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
            B.payable,
        ');

        $this->db->from('influencers A');
        $this->db->join('influencer_profiles B', 'B.influencer_id = A.id');
        
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