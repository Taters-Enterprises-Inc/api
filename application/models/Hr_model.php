<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('hr', TRUE, TRUE);
    }

    public function getDirectUserLatestActionItem($direct_user_id, $item_id){
        $this->db->select('
            id,
        ');

        $this->db->from('action_items');

		$this->db->where('user_id', $direct_user_id);
		$this->db->where('item_id', $item_id);

        $query = $this->db->get();
        return $query->result();
    }


    public function getUserKrasByActionItemId($id){
        $this->db->select('
            id,
            details,
        ');

        $this->db->from('appraisal_kras_or_kpi');

		$this->db->where('action_item_id', $id);

        $query = $this->db->get();
        return $query->result();
    }


    public function getUserActionItemByItemIdAndByDate($user_id){
        $this->db->select('
            C.id,
            C.item_id,
            E.name as item_name,
            C.status as status_id,
            D.name as status,

            CONCAT(B.first_name," ",B.last_name) as staff_name, 
            B.position as staff_position,
            B.employee_number as staff_employee_number,
            B.date_hired as staff_date_hired,
            B.email as staff_email,
        ');

        $this->db->from('user_direct_reports A');
        $this->db->join('user_profile B', 'B.user_id = A.user_id');
        $this->db->join('action_items C', 'C.user_id = A.user_id');
        $this->db->join('action_item_status D', 'D.id = C.status');
        $this->db->join('items E', 'E.id = C.item_id');

		$this->db->where('A.direct_user_id', $user_id);
        $this->db->where('C.item_id', 1);
        $this->db->where('C.dateadded <=', date('Y-m-d H:i:s'));

        $this->db->order_by('C.dateupdated', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }
    

    public function getActionItemById($id){
        $this->db->select('
            A.id,
            C.name as action_item_name,
            A.status as status_id,
            B.name as status,
            A.user_id,
        ');

        $this->db->from('action_items A');
		$this->db->join('action_item_status B', 'B.id = A.status');
		$this->db->join('items C', 'C.id = A.item_id');

		$this->db->where('A.id', $id);

        $query = $this->db->get();
        return $query->row();
    }


	public function insertActionItem($data){
        $this->db->trans_start();
		$this->db->insert('action_items', $data);
        $this->db->trans_complete();
	}

    function updateActionItemStatus($id, $status){
		$this->db->set('status', $status);
        $this->db->where('id', (int)$id);
        $this->db->update('action_items');
    }
    

    function updateKras($id, $data){
        $this->db->where('id', (int)$id);
        $this->db->update('appraisal_kras_or_kpi', $data);
    }

    public function getActionItemSubmitKra($user_id){
        $this->db->select('A.id, C.name as module, B.name as item, D.name as status, A.status as status_id, A.item_id');

        $this->db->from('action_items A');
		$this->db->join('items B', 'B.id = A.item_id');
		$this->db->join('modules C', 'C.id = A.module_id');
		$this->db->join('action_item_status D', 'D.id = A.status');

		$this->db->where('A.user_id', $user_id);
		$this->db->where('A.item_id', 3);
		$this->db->where('A.status', 1);

        $query = $this->db->get();
        return $query->row();
    }

    public function getActionItems($user_id){
        $this->db->select('A.id, C.name as module, B.name as item, D.name as status, A.status as status_id, A.item_id');

        $this->db->from('action_items A');
		$this->db->join('items B', 'B.id = A.item_id');
		$this->db->join('modules C', 'C.id = A.module_id');
		$this->db->join('action_item_status D', 'D.id = A.status');

		$this->db->where('A.user_id', $user_id);

        $this->db->order_by('A.dateupdated', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function getKras($user_id){
        $this->db->select('id, details, action_item_id');
        $this->db->from('appraisal_kras_or_kpi');

		$this->db->where('user_id', $user_id);

        $query = $this->db->get();
        return $query->result();
    }

	public function insertKraOrKpi($data){
        $this->db->trans_start();
		$this->db->insert_batch('appraisal_kras_or_kpi', $data);
        $this->db->trans_complete();
	}
    
	public function insertAppraisalComments($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_response_comments', $data);
        $this->db->trans_complete();
	}
	public function insertAppraisalFunctionalGradeAnswers($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_response_functional_competency_grade_answers', $data);
        $this->db->trans_complete();
	}
    
	public function insertAppraisalFunctionalGrades($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_response_functional_competency_grades', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
		return $insert_id;
	}

	public function insertAppraisalCoreCompetencyGradeAnswers($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_response_core_competency_grade_answers', $data);
        $this->db->trans_complete();
	}
    
	public function insertAppraisalCoreCompetencyGrades($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_response_core_competency_grades', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
		return $insert_id;
	}

	public function insertAppraisalKraOrKpiGradeAnswers($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_response_kra_or_kpi_grade_answers', $data);
        $this->db->trans_complete();
	}
    
	public function insertAppraisalKraOrKpiGrades($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_response_kra_or_kpi_grades', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
		return $insert_id;
	}

	public function insertAppraisalResponses($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_responses', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
		return $insert_id;
	}

    public function getPerformanceCriteria(){
        $this->db->select('name, minimum_score, maximum_score');
        $this->db->from('performance_criteria');

        $query = $this->db->get();
        return $query->result();
    }

    public function getRatingScale(){
        $this->db->select('name, description, rate');
        $this->db->from('rating_scale');

        $query = $this->db->get();
        return $query->result();
    }

    public function getKraKpiGrade(){
        $this->db->select('*');
        $this->db->from('kra_kpi_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getCoreCompetencyGrade(){
        $this->db->select('*');
        $this->db->from('core_competency_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getFunctionalCompetencyAndPunctualityGrade(){
        $this->db->select('*');
        $this->db->from('functional_competency_and_punctuality_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getAttendanceAndPunctuality(){
        $this->db->select('name, absences, tardiness');
        $this->db->from('attendance_and_punctuality');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getUser($user_id){
        $this->db->select('
            A.id,

            B.first_name,
            B.last_name,
            B.designation,
            B.phone_number,
            B.user_status_id,
            B.email,
            B.position,
            B.employee_number,
            B.date_hired,

            C.direct_user_id,
        ');

        $this->db->from('users A');
        $this->db->join('user_profile B', 'B.user_id = A.id');
        $this->db->join('user_direct_reports C', 'C.user_id = A.id' , 'left');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }
    

    public function getUserProfile($user_id){
        $this->db->select('
            user_status_id,
        ');

        $this->db->from('user_profile');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getterKraKpiGrade($user_id){
        $this->db->select('A.id, D.weight,
                           A.key_result_areas_or_key_performance_indicators,
                           A.result_achieved_or_not_achieved,
                           A.rating');
        $this->db->from('appraisal_response_kra_or_kpi_grade_answers A');
        $this->db->join('appraisal_response_kra_or_kpi_grades B', 'A.appraisal_response_kra_or_kpi_grade_id = B.id', 'left');
        $this->db->join('appraisal_responses C', 'B.appraisal_response_id = C.id', 'left');
        $this->db->join('kra_kpi_grade D', 'A.kra_kpi_grade_id = D.id', 'left');
        $this->db->where('C.user_id', $user_id);

        $this->db->order_by('C.dateadded', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function getterCoreCompetencyGrade($user_id){
        $this->db->select('A.id, D.title, D.description, A.critical_incidents_or_comments, A.rating');
        $this->db->from('appraisal_response_core_competency_grade_answers A');
        $this->db->join('appraisal_response_core_competency_grades B', 'A.appraisal_response_core_competency_grade_id = B.id', 'left');
        $this->db->join('appraisal_responses C', 'B.appraisal_response_id = C.id', 'left');
        $this->db->join('core_competency_grade D', 'A.core_competency_grade_id = D.id', 'left');
        $this->db->where('C.user_id', $user_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getterFunctionalCompetencyAndPunctualityGrade($user_id){
        $this->db->select('A.id, D.title, D.description, A.rating, A.critical_incidents_or_comments');
        $this->db->from('appraisal_response_functional_competency_grade_answers A');
        $this->db->join('appraisal_response_functional_competency_grades B', 'A.appraisal_response_functional_competency_and_punctuality_grade_i = B.id', 'left');
        $this->db->join('appraisal_responses C', 'B.appraisal_response_id = C.id', 'left');
        $this->db->join('functional_competency_and_punctuality_grade D', 'A.functional_competency_and_punctuality_grade_id = D.id', 'left');
        $this->db->where('C.user_id', $user_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getterAttendanceAndPunctualityGrade($user_id){
        $this->db->select('B.absences, B.tardiness');
        $this->db->from('appraisal_responses A');
        $this->db->join('appraisal_response_functional_competency_grades B', 'B.appraisal_response_id = A.id', 'left');
        $this->db->where('A.user_id', $user_id);

        $this->db->order_by('A.dateadded', 'DESC');

        $query = $this->db->get();
        return $query->row();
    }

    public function getterComments($user_id){
        $this->db->select('A.key_strengths, A.areas_for_development, A.major_development_plans_for_next_year, A.comments_on_your_overall_performance_and_development_plan');
        $this->db->from('appraisal_response_comments A');
        $this->db->join('appraisal_responses B', 'A.appraisal_response_id = B.id', 'left');
        $this->db->where('B.user_id', $user_id);

        $this->db->order_by('B.dateadded', 'DESC');
        
        $query = $this->db->get();
        return $query->row();
    }

    public function getAppraisalResponse($user_id){
        $this->db->select('id');
        $this->db->from('appraisal_responses');
        $this->db->where('user_id', $user_id);
        
        $query = $this->db->get();
        return $query->row();
    }


}
