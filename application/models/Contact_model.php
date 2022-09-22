<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_model extends CI_Model 
{
    public function add_contact($data){
        $insert = $this->db->insert('fb_user_contact', $data);
        if ($insert) {
            return true;
        } else {
            return false;
        }
    }

    public function mobile_user_add_contact($data){
        $insert = $this->db->insert('mobile_user_contact', $data);
        if ($insert) {
            return true;
        } else {
            return false;
        }
    }

 

    public function update_contact($id,$fb_id,$data){
        $this->db->where('id', $id);
        $this->db->where('fb_id', $fb_id);
        $this->db->update('fb_user_contact',$data);
        $this->db->trans_complete();
    }

    public function delete_contact($id,$fb_id){
        $this->db->where('id', $id);
        $this->db->where('fb_id', $fb_id);
        $this->db->delete('fb_user_contact');
        $this->db->trans_complete();
    }
    public function get_user_contact($fb_id){
        $this->db->select('*');
        $this->db->where('fb_id', $fb_id);
        $query = $this->db->get('fb_user_contact');
        return $query->result();
    }

    public function get_mobile_user_contact($mobile_id){
        $this->db->select('*');
        $this->db->where('mobile_id', $mobile_id);
        $query = $this->db->get('mobile_user_contact');
        return $query->result();
    }

}