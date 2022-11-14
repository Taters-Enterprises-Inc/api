<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Client_model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }
    
    public function insertClientDetailsPopClub(){
        $client_details = null;
        $logon_type = null;

        $first_name = $this->session->userData['first_name'];
        $last_name = $this->session->userData['last_name'];
        $address = $this->session->customer_address;
        
        if(isset($this->session->userData['oauth_uid'])){
            $client_details = $this->getFacebook($this->session->userData['oauth_uid']);
            $logon_type = 'facebook';
        }else if (isset($this->session->userData['mobile_user_id'])){
            $client_details = $this->getMobile($this->session->userData['mobile_user_id']);
            $logon_type = 'mobile';
        }
        

        $client_details_to_be_inserted = array(
            'email'             => $client_details->email,
			'address'           => $address,
			'contact_number'    => "NA",
            'fname'             => $first_name,
            'lname'             => $last_name,
            'moh'               => 1,
            'payops'            => 0,
            'add_name'          => $first_name.' '.$last_name,
			'add_contact'       => $address,
			'add_address'       => "NA"
        );

        switch($logon_type){
            case 'facebook':
                $client_details_to_be_inserted['fb_user_id'] = $client_details->id;
                break;
            case 'mobile':
                $client_details_to_be_inserted['mobile_user_id'] = $client_details->id;
                break;
        }

        $this->db->trans_start();
        $this->db->insert('deals_client_tb', $client_details_to_be_inserted);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return array(
            'id'=>$insert_id,
            'status' =>$this->db->trans_status(),
            "logon_type" => $logon_type,
        );
    }

    public function insertClientDetailsCatering(
        $first_name,
        $last_name,
        $address,
        $phone_number,
        $payops,
        $email
    ){
        $client_details = null;
        $logon_type = null;
        
        if(isset($this->session->userData['oauth_uid'])){
            $client_details = $this->getFacebook($this->session->userData['oauth_uid']);
            $logon_type = 'facebook';
        }else if (isset($this->session->userData['mobile_user_id'])){
            $client_details = $this->getMobile($this->session->userData['mobile_user_id']);
            $logon_type = 'mobile';
        }

        $client_details_to_be_inserted = array(
            'email'             => $email,
            'address'           => $address,
            'contact_number'    => $phone_number,
            'fname'             => $first_name,
            'lname'             => $last_name,
            'moh'               => 2,
            'payops'            => $payops,
            'add_name'          => $first_name.' '.$last_name,
            'add_contact'       => $phone_number,
            'add_address'       => $address,
        );

        switch($logon_type){
            case 'facebook':
                $client_details_to_be_inserted['fb_user_id'] = $client_details->id;
                break;
            case 'mobile':
                $client_details_to_be_inserted['mobile_user_id'] = $client_details->id;
                break;
        }

        $this->db->trans_start();
        $this->db->insert('catering_client_tb', $client_details_to_be_inserted);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return array(
            'id'=>$insert_id,
            'status' =>$this->db->trans_status(),
            "logon_type" => $logon_type,
        );
    }
    
    public function insertClientDetailsShop(
        $first_name,
        $last_name,
        $address,
        $phone_number,
        $payops,
        $full_address,
        $email
    ){
        $client_details = null;
        $logon_type = null;
        
        if(isset($this->session->userData['oauth_uid'])){
            $client_details = $this->getFacebook($this->session->userData['oauth_uid']);
            $logon_type = 'facebook';
        }else if (isset($this->session->userData['mobile_user_id'])){
            $client_details = $this->getMobile($this->session->userData['mobile_user_id']);
            $logon_type = 'mobile';
        }

        $client_details_to_be_inserted = array(
            'email'             => $email,
            'address'           => $address,
            'contact_number'    => $phone_number,
            'fname'             => $first_name,
            'lname'             => $last_name,
            'moh'               => 2,
            'payops'            => $payops,
            'add_name'          => $first_name.' '.$last_name,
            'add_contact'       => $phone_number,
            'add_address'       => $full_address,
        );

        switch($logon_type){
            case 'facebook':
                $client_details_to_be_inserted['fb_user_id'] = $client_details->id;
                break;
            case 'mobile':
                $client_details_to_be_inserted['mobile_user_id'] = $client_details->id;
                break;
        }

        $this->db->trans_start();
        $this->db->insert('client_tb', $client_details_to_be_inserted);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return array(
            'id'=>$insert_id,
            'status' =>$this->db->trans_status(),
            "logon_type" => $logon_type,
        );
    }
    
    public function getFacebook($oauth_id){
        $this->db->select('id, first_name, last_name, email');
        $this->db->where('oauth_uid', $oauth_id);
        $query = $this->db->get('fb_users');
        $data = $query->row();
        return $data;
    }
    
    public function getMobile($id){
        $this->db->select('id, first_name, last_name, email');
        $this->db->where('id', $id);
        $query = $this->db->get('mobile_users');
        $data = $query->row();
        return $data;
    }
}