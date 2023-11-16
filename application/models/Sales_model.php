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

    
    public function form_data(){

        $this->db->select("
            A.id,
            A.field_name,
            A.description,
            A.section,
            A.status,
            A.is_dropdown,
            A.payment_sub_section,
            A.name,
            A.is_required,
            A.is_date_field,

            B.section_name,

            C.sub_section_name,

        ");
        $this->db->from('field_availability A');        
        $this->db->join('field_section B', 'B.id = A.section', 'left');
        $this->db->join('payments_sub_section C', 'C.id = A.payment_sub_section', 'left');
        $this->db->where('A.status', '1');
        

        $query = $this->db->get();
        $fields = $query->result();

        $this->db->select('section_name');
        $this->db->from('field_section');
        $section_query = $this->db->get();
        $sections = $section_query->result();

        $this->db->select('id, sub_section_name');
        $this->db->from('payments_sub_section');
        $sub_section_query = $this->db->get();
        $sub_sections = $sub_section_query->result();

        $index = 0;
        foreach($sections as $section){
                
            $subIndex=0;

                foreach ($sub_sections as $sub_section) {
                    $matching_queries = array();

                    foreach ($fields as $field) {
                        if($section->section_name == $field->section_name){
                            if ($sub_section->id == $field->payment_sub_section) {
                                $matching_queries[] = $field;
                            }
                        
                        }
                    }
                    $join_data[$index]['field'][$subIndex]['sub_section'] = $sub_section->sub_section_name;
                    $join_data[$index]['field'][$subIndex]['field_data'] =  $matching_queries;
                    $subIndex++;
                }
                
            $join_data[$index]['section'] = $section->section_name;
            $index++;


        }

       
        
        return $join_data;


    }

    public function discount_type(){
        $this->db->select('name');
        $this->db->from('discount_type');
        $query = $this->db->get();
        return $query->result();

    }


   
}
