<?php defined('BASEPATH') OR exit('No direct script access allowed');

class audit_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('audit', TRUE, TRUE);

    }


    public function getFormQuestions($row_no, $row_per_page, $order_by,  $order, $search){
        $this->db->select("
            A.id,
            A.questions,
            C.section_name,
            D.sub_section_name,
            A.total_point,
            A.is_active as status

           
    
        ");
        $this->db->from('form_questions A');
        $this->db->join('form_questions_sections B', 'B.question_id = A.id', 'left');
        $this->db->join('form_sections C', 'C.id = B.section_id', 'left');
        $this->db->join('form_sub_section D', 'D.id = B.sub_section_id', 'left');
        
        
        if($search){
            $this->db->group_start();
            $this->db->like('A.questions', $search);
            $this->db->or_like("C.section_name", $search);
            $this->db->or_like('D.sub_section_name', $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();


    }

    public function getFormQuestionsCount($search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('form_questions A');
        $this->db->join('form_questions_sections B', 'B.question_id = A.id', 'left');
        $this->db->join('form_sections C', 'C.id = B.section_id', 'left');
        $this->db->join('form_sub_section D', 'D.id = B.sub_section_id', 'left');
        


        if($search){
            $this->db->group_start();
            $this->db->like('A.questions', $search);
            $this->db->or_like("C.section_name", $search);
            $this->db->or_like('D.sub_section_name', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }


    function updateQuestionStatus($id, $status, $type){
        switch($type){
            case 'status': 
                $this->db->set('is_active', $status);        
                $this->db->where("id", $id);
                $this->db->update("form_questions");

            break;

            case 'point':
                $this->db->set('total_point', $status);        
                $this->db->where("id", $id);
                $this->db->update("form_questions");
            break;
        }
            
    }
    


    
}