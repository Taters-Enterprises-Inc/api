<?php defined('BASEPATH') OR exit('No direct script access allowed');

class audit_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('audit', TRUE, TRUE);
        $this->newteishop_db = $this->load->database('', TRUE, TRUE);
    }


    public function getAuditEvaluationData($type){
         $this->db->select("
            D.section_name,
            E.sub_section_name,
            F.level,
            G.questions,
            G.id,
            C.equivalent_point,
        ");
        $this->db->from('form_audit_type A');
        $this->db->join('form_criteria_availability B', 'B.audit_id = A.id', 'left');
        $this->db->join('form_questions_information C', 'C.id = B.question_id', 'left');
        $this->db->join('form_sections D', 'D.id = C.section_id', 'left');
        $this->db->join('form_sub_section E', 'E.id = C.sub_section_id', 'left');
        $this->db->join('form_urgency_level F', 'F.id = C.urgency_id', 'left');
        $this->db->join('form_questions G', 'G.id = C.question_id', 'left');

        if($type){
            $this->db->group_start();
            $this->db->where('A.type_name', $type);           
            $this->db->group_end();
        }

        $query = $this->db->get();
        $query = $query->result();


        $this->db->select('H.section_name');
        $this->db->from('form_sections H');
        $section_query = $this->db->get();
        $sections = $section_query->result();

        $index = 0;

        foreach ($sections as $section) {
            $matching_queries = array();
            foreach ($query as $querys) {
                if ($section->section_name == $querys->section_name) {
                    $matching_queries[] = $querys;
                }
            }
            $join_data[$index]['section'] = $section->section_name;
            $join_data[$index]['criteria'] = $matching_queries;
            $index++;
        }

            
        return $join_data;


    }

    public function getAuditFormInformation($hash){
        $this->db->select('
            A.id,
            A.attention,
            A.audit_period,
            A.dateadded,

            B.id as type_id,
            B.type_name,

            C.name as store_name,
        ');
        $this->db->from('form_responses A');
        $this->db->join('form_audit_type B', 'B.id = A.audit_type_id', 'left');
        $this->db->join($this->newteishop_db->database.'.store_tb C', 'C.store_id = A.store_id');

        $this->db->where("A.hash", $hash);

        $info_query = $this->db->get();
        $info = $info_query->row();

        $this->db->select('
            A.question_id as id,
            B.questions,
            C.rating,
            A.remarks,
            D.equivalent_point,
            D.section_id,
            E.level,
            F.section_name,
            G.sub_section_name,
            H.id as category_id,
            
        ');
        $this->db->from('form_responses_answers A');
        $this->db->join('form_questions B', 'B.id = A.question_id', 'left');
        $this->db->join('form_rating C', 'C.id = A.rating_id', 'left');
        $this->db->join('form_questions_information D', 'D.id = A.question_id', 'left');
        $this->db->join('form_urgency_level E', 'E.id = D.urgency_id', 'left');
        $this->db->join('form_sections F', 'F.id = D.section_id', 'left');
        $this->db->join('form_sub_section G', 'G.id = D.sub_section_id', 'left');
        $this->db->join('form_category H', 'H.id = D.category_id', 'left');


        $this->db->where("A.response_id", $info->id);
        $ans_query = $this->db->get();
        $ans = $ans_query->result();

        
        $this->db->select('
            A.category_id,
            A.weight,
            B.name,
        ');
        $this->db->from('form_category_weight A');
        $this->db->join('form_category B', 'B.id = A.category_id', 'left');
        $this->db->where("A.type_id", $info->type_id);
        $cat_query = $this->db->get();
        $cat = $cat_query->result();


        $this->db->select('H.section_name, h.id');
        $this->db->from('form_sections H');
        $section_query = $this->db->get();
        $sections = $section_query->result();

        $index = 0;


        $join_data['information'] = $info;
        $join_data['default_weight_info'] = $cat;
        // $join_data['answers'] = $ans;

        foreach ($sections as $section) {
            $matching_queries = array();
            if($section->id == 1) continue;
            foreach ($ans as $answers) {
                if ($section->section_name == $answers->section_name) {
                    $matching_queries[] = $answers;
                }
            }
            $join_data['answers'][$index]['section'] = $section->section_name;
            $join_data['answers'][$index]['criteria'] = $matching_queries;

            $answerData = array(
                $index => array(
                    'criteria' => $matching_queries,
                ),
                
            );

            $index++;
        }


        
       

        return $join_data;
    }

 



    public function insertAuditResponse($data){
        $this->db->trans_start();
		$this->db->insert('form_responses', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
		return $insert_id;
	}

    public function insertAuditAnswer($data){
        $this->db->trans_start();
		$this->db->insert('form_responses_answers', $data);
        $this->db->trans_complete();
	}

    public function getSections(){
        $this->db->select('section_name');
        $this->db->from('form_sections');
        

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