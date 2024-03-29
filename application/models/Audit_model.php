<?php defined('BASEPATH') OR exit('No direct script access allowed');

class audit_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('audit', TRUE, TRUE);
    }


    public function getAuditEvaluationData($type){
         $this->db->select("
            D.section_name,
            E.sub_section_name,
            F.level,
            G.questions,
            G.id,
            C.equivalent_point,
            C.category_id,
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


    public function getDefaultWeight($type){

         $this->db->select('
            category_id,
            type_id,
            weight,');
        $this->db->from('form_audit_type A');
        $this->db->join('form_category_weight B', 'A.id = B.type_id', 'left');

        if($type){
        $this->db->where('A.type_name', $type);
        }
        $default_weight_query = $this->db->get();
        $default_weight = $default_weight_query->result();


        return $default_weight;

    }

    public function getAuditFormInformation($hash){
        $this->db->select('
            A.id,
            A.attention,
            A.auditor,
            A.audit_period,
            A.dateadded,
            A.isacknowledged,
            A.signature_img,
            A.acknowledged_by,

            B.id as type_id,
            B.type_name,

            C.store_name,
        ');
        $this->db->from('form_responses A');
        $this->db->join('form_audit_type B', 'B.id = A.audit_type_id', 'left');
        $this->db->join('store C', 'C.id = A.store_id');

        $this->db->where("A.hash", $hash);

        $info_query = $this->db->get();
        $info = $info_query->row();

        $this->db->select('
            A.question_id as id,
            B.questions,
            C.rating,
            A.remarks,
            A.equivalent_point,
            D.section_id,
            A.urgency_rating,
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
            category_id,
            grade,
            weight,
            final_score,
        ');
        $this->db->from('form_responses_result');
        $this->db->where("response_id", $info->id);
        $cat_query = $this->db->get();
        $cat = $cat_query->result();


        $this->db->select('section_name, id');
        $this->db->from('form_sections');
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


    function getAuditResult($StoreName){

        $this->db->select('id');
        $this->db->from("store");
        $this->db->where("store_name", $StoreName);
        $store_query = $this->db->get();
        $store_id = $store_query->row();


        $this->db->select('
            A.id,
            A.audit_period,

            B.id,
            B.grade,
            B.weight,
            B.final_score,

            C.Name as category_name,
            
        ');
        $this->db->from("form_responses A");
        $this->db->join("form_responses_result B", "B.response_id = A.id", "left");
        $this->db->join("form_category C", "C.id = B.category_id", "left");
        $this->db->where("store_id", $store_id->id);     
        $response_query = $this->db->get();
        $response_data = $response_query->result();


        $joinData = [];

        foreach($response_data as $response){
            $auditPeriod = $response->audit_period;
            $response = [
                'id' => $response->id,
                'category_name' => $response->category_name,
                'grade' => $response->grade * 100,
                'weight' => $response->weight * 100,
                'final_score' => $response->final_score * 100,
                'target' => 90,
            ];
            
            if (!isset($joinData[$auditPeriod])) {
                $joinData[$auditPeriod] = [];
            }

            $joinData[$auditPeriod][] = $response;

        }
        return $joinData;
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

    public function insertAuditResult($data){
        $this->db->trans_start();
		$this->db->insert('form_responses_result', $data);
        $this->db->trans_complete();
	}

    public function getSections(){
        $this->db->select('section_name');
        $this->db->from('form_sections');
        

        $query = $this->db->get();
        return $query->result();
    }

    public function getAuditResponseInformation($row_no, $row_per_page, $order_by,  $order, $search){
            $this->db->select('
            A.id,
            A.attention,
            A.audit_period,
            A.dateadded,
            A.hash,
            B.type_name,
            C.store_name,
        ');
        $this->db->from('form_responses A');
        $this->db->join('form_audit_type B', 'B.id = A.audit_type_id', 'left');
        $this->db->join('store C', 'C.id = A.store_id', 'left');

        
        if($search){
            $this->db->group_start();
            $this->db->like('A.attention', $search);
            $this->db->or_like("A.audit_period", $search);
            $this->db->or_like('A.dateadded', $search);
            $this->db->or_like('B.type_name', $search);
            $this->db->or_like('C.store_name', $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();


    }

    public function getAuditResponseInformationCount($search){
        $this->db->select('count(*) as all_count');      
        $this->db->from('form_responses A');
        $this->db->join('form_audit_type B', 'B.id = A.audit_type_id', 'left');
        $this->db->join('store C', 'C.id = A.store_id', 'left');
        


        if($search){
            $this->db->group_start();
            $this->db->like('A.attention', $search);
            $this->db->or_like("A.audit_period", $search);
            $this->db->or_like('A.dateadded', $search);
            $this->db->or_like('B.type_name', $search);
            $this->db->or_like('C.store_name', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
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

    function acknowledgeResult($hash, $data){

        $this->db->set($data);
        $this->db->where("hash", $hash);
        $this->db->update("form_responses");

    }

    function getStore(){
        $this->db->select('
            A.id,
            A.store_type_id,
            
            B.store_code,
            B.store_name,

            C.type_name,

            D.mall_type
        ');

        $this->db->from('store_information A');
        $this->db->join('store B', 'B.id = A.store_id', 'left');
        $this->db->join('form_audit_type C', 'C.id = A.store_type_id', 'left');
        $this->db->join('store_mall_type D', "D.id = A.ownership_id", 'left');

        $query = $this->db->get();
        return $query->result();
    }

    


    
}