<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mobile_users_model extends CI_Model {
    
    public function __construct()
    {
        $this->load->database();
    }

    public function registration($user_data, $temp_password){
        $data = array(
			'ip_address ' => $this->input->ip_address(),
			'username' 	  => $user_data['phoneNumber'],
            'email' 	  => $user_data['email'],
			'password'    => password_hash($temp_password,PASSWORD_DEFAULT),
			'created_on'  => time(),
			'first_name'  => $user_data['firstName'],
			'last_name'   => $user_data['lastName'],
			'phone'   	  => $user_data['phoneNumber'],
            'test_field'  => $temp_password
		);

        $status = $this->db->insert('mobile_users', $data);
        $id = $this->db->insert_id();        

        $dataContact = array(
            'mobile_id' => $id,
            'contact' => $user_data['phoneNumber']
        );
        
        $insert = $this->db->insert('mobile_user_contact', $dataContact);


        if ($status || $insert) {
            return true;
        } else {
            return false;
        }    
        
    }

    function verify_login($mobile_number) 
	{
		$this->db->select('*');
	    $this->db->where("username",$mobile_number);
	    $query = $this->db->get('mobile_users');
	    return $query->result();
	}

    function verify_otp_code($mobile_number){
        $this->db->select('forgot_password_code');
	    $this->db->where("username",$mobile_number);
	    $query = $this->db->get('mobile_users');
	    return $query->result();
    }

    function generate_forgot_password_code($id,$code,$code_validity){
        $this->db->set('forgot_password_code',$code);
        $this->db->set('forgot_password_time',$code_validity);
        $this->db->where('id',$id);
        $this->db->update('mobile_users');
        return ($this->db->affected_rows() > 0) ? true : false ;
    }

    function reset_password($mobile,$password){
        $this->db->select('forgot_password_time');
        $this->db->where('username',$mobile);
        $query              = $this->db->get('mobile_users');
	    $forgot_pass_time   = $query->result();
        if (strtotime($forgot_pass_time[0]->forgot_password_time) < strtotime(date('Y-m-d H:i:s'))) {
            return false;
        } else {
            $this->db->set('password',$password);
            $this->db->set('forgot_password_code',NULL);
            $this->db->set('forgot_password_time',NULL);
            $this->db->where('username',$mobile);
            $this->db->update('mobile_users');
            return ($this->db->affected_rows() > 0) ? true : false ;
        }

    }

	function reset_profile_info($user_id,$post){
		$this->db->set('first_name',$post['profile_first_name']);
		$this->db->set('last_name',$post['profile_last_name']);
		$this->db->set('email',$post['profile_email']);
		$this->db->set('phone',$post['profile_phone']);
		$this->db->set('username',$post['profile_phone']);
		$this->db->where('username',$user_id);
		$this->db->update('mobile_users');
		return ($this->db->affected_rows() > 0) ? true : false ;

	}
}
