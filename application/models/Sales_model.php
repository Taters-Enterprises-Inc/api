<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_model extends CI_Model{

    public function __construct()
    {
		$this->db =  $this->load->database('sales', TRUE, TRUE);
    }
    

    public function getSalesGroup(){
      $this->db->select("
          id,
          group_name as name,
          description,
      ");

      $this->db->from('user_groups');
      
      $query = $this->db->get();
      return $query->result();
  }

      public function getUserGroups($user_id){

        if($user_id == 1) {
            $this->db->select('*');
            $this->db->from('user_groups');

            $query = $this->db->get();
            return $query->result();
        }
        
        $this->db->select("
            B.id,
            B.group_name,
            B.description,
        ");

        $this->db->from('user_group_combination A');
        $this->db->join('user_groups B', 'B.id = A.group_id', 'left');
        $this->db->where('A.user_id',$user_id);
        

        $query = $this->db->get();
        return $query->result();
    }
    
}
