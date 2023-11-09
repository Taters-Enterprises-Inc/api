<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_model extends CI_Model {

	public function __construct(){
        $this->db =  $this->load->database('default', TRUE, TRUE);
        $this->hrDB = $this->load->database('hr', TRUE, TRUE);
    }

    public function getDirectUserLatestActionItem($direct_user_id, $item_id){
        $this->hrDB->select('
            id,
        ');

        $this->hrDB->from('action_items');

		$this->hrDB->where('user_id', $direct_user_id);
		$this->hrDB->where('item_id', $item_id);

        $query = $this->hrDB->get();
        return $query->result();
    }


    public function getUserKrasByActionItemId($id){
        $this->hrDB->select('
            id,
            details,
        ');

        $this->hrDB->from('appraisal_kras_or_kpi');

		$this->hrDB->where('action_item_id', $id);

        $query = $this->hrDB->get();
        return $query->result();
    }


    public function getUserActionItemByItemIdAnhrDByDate($user_id){
        $this->hrDB->select('
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

        $this->hrDB->from('user_direct_reports A');
        $this->hrDB->join('user_profile B', 'B.user_id = A.user_id');
        $this->hrDB->join('action_items C', 'C.user_id = A.user_id');
        $this->hrDB->join('action_item_status D', 'D.id = C.status');
        $this->hrDB->join('items E', 'E.id = C.item_id');

		$this->hrDB->where('A.direct_user_id', $user_id);
        $this->hrDB->where('C.item_id', 1);
        $this->hrDB->where('C.dateadded <=', date('Y-m-d H:i:s'));

        $this->hrDB->order_by('C.dateupdated', 'DESC');

        $query = $this->hrDB->get();
        return $query->result();
    }
    

    public function getActionItemById($id){
        $this->hrDB->select('
            A.id,
            C.name as action_item_name,
            A.status as status_id,
            B.name as status,
            A.user_id,
        ');

        $this->hrDB->from('action_items A');
		$this->hrDB->join('action_item_status B', 'B.id = A.status');
		$this->hrDB->join('items C', 'C.id = A.item_id');

		$this->hrDB->where('A.id', $id);

        $query = $this->hrDB->get();
        return $query->row();
    }


	public function insertActionItem($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('action_items', $data);
        $this->hrDB->trans_complete();
	}

    function updateActionItemStatus($id, $status){
		$this->hrDB->set('status', $status);
        $this->hrDB->where('id', (int)$id);
        $this->hrDB->update('action_items');
    }
    

    function updateKras($id, $data){
        $this->hrDB->where('id', (int)$id);
        $this->hrDB->update('appraisal_kras_or_kpi', $data);
    }

    public function getActionItems($user_id){
        $this->hrDB->select('A.id, C.name as module, B.name as item, D.name as status, A.status as status_id, A.item_id');

        $this->hrDB->from('action_items A');
		$this->hrDB->join('items B', 'B.id = A.item_id');
		$this->hrDB->join('modules C', 'C.id = A.module_id');
		$this->hrDB->join('action_item_status D', 'D.id = A.status');

		$this->hrDB->where('A.user_id', $user_id);

        $this->hrDB->order_by('A.dateupdated', 'DESC');

        $query = $this->hrDB->get();
        return $query->result();
    }

    public function getKras($user_id){
        $this->hrDB->select('id, details, action_item_id');
        $this->hrDB->from('appraisal_kras_or_kpi');

		$this->hrDB->where('user_id', $user_id);

        $query = $this->hrDB->get();
        return $query->result();
    }

	public function insertKraOrKpi($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert_batch('appraisal_kras_or_kpi', $data);
        $this->hrDB->trans_complete();
	}
    
	public function insertAppraisalComments($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('appraisal_response_comments', $data);
        $this->hrDB->trans_complete();
	}
	public function insertAppraisalFunctionalGradeAnswers($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('appraisal_response_functional_competency_grade_answers', $data);
        $this->hrDB->trans_complete();
	}
    
	public function insertAppraisalFunctionalGrades($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('appraisal_response_functional_competency_grades', $data);
		$insert_id = $this->hrDB->insert_id();
        $this->hrDB->trans_complete();
		return $insert_id;
	}

	public function insertAppraisalCoreCompetencyGradeAnswers($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('appraisal_response_core_competency_grade_answers', $data);
        $this->hrDB->trans_complete();
	}
    
	public function insertAppraisalCoreCompetencyGrades($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('appraisal_response_core_competency_grades', $data);
		$insert_id = $this->hrDB->insert_id();
        $this->hrDB->trans_complete();
		return $insert_id;
	}

	public function insertAppraisalKraOrKpiGradeAnswers($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('appraisal_response_kra_or_kpi_grade_answers', $data);
        $this->hrDB->trans_complete();
	}
    
	public function insertAppraisalKraOrKpiGrades($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('appraisal_response_kra_or_kpi_grades', $data);
		$insert_id = $this->hrDB->insert_id();
        $this->hrDB->trans_complete();
		return $insert_id;
	}

	public function insertAppraisalResponses($data){
        $this->hrDB->trans_start();
		$this->hrDB->insert('appraisal_responses', $data);
		$insert_id = $this->hrDB->insert_id();
        $this->hrDB->trans_complete();
		return $insert_id;
	}

    public function getPerformanceCriteria(){
        $this->hrDB->select('name, minimum_score, maximum_score');
        $this->hrDB->from('performance_criteria');

        $query = $this->hrDB->get();
        return $query->result();
    }

    public function getRatingScale(){
        $this->hrDB->select('name, description, rate');
        $this->hrDB->from('rating_scale');

        $query = $this->hrDB->get();
        return $query->result();
    }

    public function getKraKpiGrade(){
        $this->hrDB->select('*');
        $this->hrDB->from('kra_kpi_grade');

        $query = $this->hrDB->get();
        return $query->result();
    }

    public function getCoreCompetencyGrade(){
        $this->hrDB->select('*');
        $this->hrDB->from('core_competency_grade');

        $query = $this->hrDB->get();
        return $query->result();
    }

    public function getFunctionalCompetencyAndPunctualityGrade(){
        $this->hrDB->select('*');
        $this->hrDB->from('functional_competency_and_punctuality_grade');

        $query = $this->hrDB->get();
        return $query->result();
    }

    public function getAttendanceAndPunctuality(){
        $this->hrDB->select('name, absences, tardiness');
        $this->hrDB->from('attendance_and_punctuality');

        $query = $this->hrDB->get();
        return $query->result();
    }
    
    public function getUser($user_id){
        $this->hrDB->select('
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

        $this->hrDB->from('users A');
        $this->hrDB->join('user_profile B', 'B.user_id = A.id');
        $this->hrDB->join('user_direct_reports C', 'C.user_id = A.id' , 'left');
        $this->hrDB->where('A.id', $user_id);

        $query = $this->hrDB->get();
        return $query->row();
    }
    

    public function getUserProfile($user_id){
        $this->hrDB->select('
            user_status_id,
        ');

        $this->hrDB->from('user_profile');
        $this->hrDB->where('user_id', $user_id);
        $query = $this->hrDB->get();
        return $query->row();
    }


}
