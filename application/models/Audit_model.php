<?php defined('BASEPATH') OR exit('No direct script access allowed');

class audit_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('audit', TRUE, TRUE);
        $this->newteishop_db = $this->load->database('', TRUE, TRUE);
    }


    public function getAuditEvaluationData($type){
         $this->db->select("
            A.type_name as audit_type_name,
            A.id as audit_type_id,

            C.id as question_id,
            C.questions,

            D.equivalent_point,

            E.id as section_id,
            E.section_name,

            F.id as sub_section_id,
            F.sub_section_name,

            G.level as urgency_level

        ");
        $this->db->from('form_audit_type A');
        $this->db->join('form_criteria_availability B', 'B.audit_id = A.id', 'left');
        $this->db->join('form_questions C', 'C.id = B.question_id', 'left');
        $this->db->join('form_questions_information D', 'D.question_id = C.id', 'left');
        $this->db->join('form_sections E', 'E.id = D.section_id', 'left');
        $this->db->join('form_sub_section F', 'F.id = D.sub_section_id', 'left');
        $this->db->join('form_urgency_level G', 'G.id = D.urgency_id', 'left');
       

        if($type){
            $this->db->group_start();
            $this->db->where('A.type_name', $type);           
            $this->db->group_end();
        }
        

        $query = $this->db->get();
        return $query->result();


    }


    public function getFormQuestions($row_no, $row_per_page, $order_by,  $order, $search){
        $this->db->select("
            A.id,
            A.questions,
            
            B.equivalent_point,

            C.section_name,
            D.sub_section_name,
            A.is_active as status,
            E.level as urgency_level

    
        ");
        $this->db->from('form_questions A');
        $this->db->join('form_questions_information B', 'B.question_id = A.id', 'left');
        $this->db->join('form_sections C', 'C.id = B.section_id', 'left');
        $this->db->join('form_sub_section D', 'D.id = B.sub_section_id', 'left');
        $this->db->join('form_urgency_level E', 'B.urgency_id = E.id', 'left');

        
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
        $this->db->join('form_questions_information B', 'B.question_id = A.id', 'left');
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
        }
            
    }

    function getStore(){
        $this->newteishop_db->select('
            store_id,
            name,
        ');

        $this->newteishop_db->from('store_tb');
        

        $query = $this->newteishop_db->get();
        return $query->result();
    }

    function getStoreType(){
        $this->db->select('
            id,
            type_name,
        ');

        $this->db->from('form_audit_type');

        $query = $this->db->get();
        return $query->result();
    }
    



    
}