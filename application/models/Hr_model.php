<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('hr', TRUE, TRUE);
    }

    #Temporarly
    public function insertTable($data, $table_name){
		$this->db->insert($table_name, $data);
    }

    public function getStaffsIfActionItemConditionMeets($item_id, $status, $direct_user_id){
        $this->db->select('
            A.user_id
        ');

        $this->db->from('user_direct_reports A');
        $this->db->join('action_items B', 'B.user_id = A.user_id');

		$this->db->where('A.direct_user_id', $direct_user_id);
		$this->db->where('B.item_id', $item_id);
		$this->db->where('B.status', $status);

        $query = $this->db->get();
        return $query->result();
    }

    public function getOverallSectionGrade(){
        $this->db->select('name, weight');
        $this->db->from('overall_section_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getAttendanceAndTardinessSelfRatings($user_id){
        $this->db->select('
            A.absences,
            A.tardiness
        ');

        $this->db->from('appraisal_response_functional_competency_grades A');
        $this->db->join('appraisal_responses B', "B.id = A.appraisal_response_id");
        $this->db->join('appraisal_self_responses C', "C.appraisal_response_id = B.id");

		$this->db->where('C.user_id', $user_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getAttendanceAndTardinessEvaluatorRatings($evaluatee_id){
        $this->db->select('
        A.absences,
        A.tardiness
        ');

        $this->db->from('appraisal_response_functional_competency_grades A');
        $this->db->join('appraisal_responses B', "B.id = A.appraisal_response_id");
        $this->db->join('appraisal_evaluate_responses C', "C.appraisal_response_id = B.id");

		$this->db->where('C.evaluatee_id', $evaluatee_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getFunctionalCompetencyAndPunctualityEvaluatorRatings($evaluatee_id){
        $this->db->select('
            A.rating
        ');

        $this->db->from('appraisal_response_functional_competency_grade_answers A');
        $this->db->join('appraisal_response_functional_competency_grades B', "B.id = A.appraisal_response_functional_competency_and_punctuality_grade_i");
        $this->db->join('appraisal_responses C', "C.id = B.appraisal_response_id");
        $this->db->join('appraisal_evaluate_responses D', "D.appraisal_response_id = C.id");

		$this->db->where('D.evaluatee_id', $evaluatee_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getFunctionalCompetencyAndPunctualitySelfRatings($user_id){
        $this->db->select('
            A.rating
        ');

        $this->db->from('appraisal_response_functional_competency_grade_answers A');
        $this->db->join('appraisal_response_functional_competency_grades B', "B.id = A.appraisal_response_functional_competency_and_punctuality_grade_i");
        $this->db->join('appraisal_responses C', "C.id = B.appraisal_response_id");
        $this->db->join('appraisal_self_responses D', "D.appraisal_response_id = C.id");

		$this->db->where('D.user_id', $user_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getFunctionalCompetencyAndPunctualityGrades(){
        $this->db->select('title, description');
        $this->db->from('functional_competency_and_punctuality_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getCoreCompetencyEvaluatorRatings($evaluatee_id){
        $this->db->select('
            A.rating
        ');

        $this->db->from('appraisal_response_core_competency_grade_answers A');
        $this->db->join('appraisal_response_core_competency_grades B', "B.id = A.appraisal_response_core_competency_grade_id");
        $this->db->join('appraisal_responses C', "C.id = B.appraisal_response_id");
        $this->db->join('appraisal_evaluate_responses D', "D.appraisal_response_id = C.id");

		$this->db->where('D.evaluatee_id', $evaluatee_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getCoreCompetencySelfRatings($user_id){
        $this->db->select('
            A.rating
        ');

        $this->db->from('appraisal_response_core_competency_grade_answers A');
        $this->db->join('appraisal_response_core_competency_grades B', "B.id = A.appraisal_response_core_competency_grade_id");
        $this->db->join('appraisal_responses C', "C.id = B.appraisal_response_id");
        $this->db->join('appraisal_self_responses D', "D.appraisal_response_id = C.id");

		$this->db->where('D.user_id', $user_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getCoreCompetencyGrades(){
        $this->db->select('title, description');
        $this->db->from('core_competency_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getAppraisalGroupWeight($group_id){
        $this->db->select('
            weight
        ');

        $this->db->from('appraisal_group_weight');

		$this->db->where('group_id', $group_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getKraOrKpiGradeEvaluatorRatings($evaluatee_id){
        $this->db->select('
            A.rating
        ');

        $this->db->from('appraisal_response_kra_or_kpi_grade_answers A');
        $this->db->join('appraisal_response_kra_or_kpi_grades B', "B.id = A.appraisal_response_kra_or_kpi_grade_id");
        $this->db->join('appraisal_responses C', "C.id = B.appraisal_response_id");
        $this->db->join('appraisal_evaluate_responses D', "D.appraisal_response_id = C.id");

		$this->db->where('D.evaluatee_id', $evaluatee_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getKraOrKpiGradeSelfRatings($user_id){
        $this->db->select('
            A.rating
        ');

        $this->db->from('appraisal_response_kra_or_kpi_grade_answers A');
        $this->db->join('appraisal_response_kra_or_kpi_grades B', "B.id = A.appraisal_response_kra_or_kpi_grade_id");
        $this->db->join('appraisal_responses C', "C.id = B.appraisal_response_id");
        $this->db->join('appraisal_self_responses D', "D.appraisal_response_id = C.id");

		$this->db->where('D.user_id', $user_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getStaffs($direct_user_id){
        $this->db->select('
            user_id
        ');

        $this->db->from('user_direct_reports');

		$this->db->where('direct_user_id', $direct_user_id);

        $query = $this->db->get();
        return $query->result();
    }
    public function getGroupId($user_id){
        $this->db->select('group_id as id');

        $this->db->from('users_groups');

		$this->db->where('user_id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }


    public function getDirectReport($user_id){
        $this->db->select('A.direct_user_id as id, B.first_name, B.last_name, C.position');

        $this->db->from('user_direct_reports A');
        $this->db->join('user_personal_details B', 'B.user_id = A.direct_user_id', 'left');
        $this->db->join('user_job_details C', 'C.user_id = A.direct_user_id', 'left');

		$this->db->where('A.user_id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getDepartments() {
        $this->db->select("id, name");
        $this->db->from("departments");
        return $this->db->get()->result();
    }

    function getActionItemStatus($item_id, $user_id){
        $this->db->select('status');
        $this->db->from('action_items');
        
		$this->db->where('user_id', $user_id);
		$this->db->where('item_id', $item_id);

        return $this->db->get()->row();
    }

    function getEmployees($row_no, $row_per_page, $order_by, $order, $search, $department_id) {
        $this->db->select('A.id, B.first_name, B.middle_name, B.last_name, C.employee_number, C.position');
        $this->db->from('users A');
        $this->db->join('user_personal_details B', 'B.user_id = A.id', 'left');
        $this->db->join('user_job_details C', 'C.user_id = A.id', 'left');

        if($search){
            $this->db->group_start();
            $this->db->like('CONCAT(B.first_name," ",B.last_name)', $search);
            $this->db->group_end();
        }

        if(isset($department_id) && $department_id != 'all') $this->db->where('C.department_id', $department_id);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }

    function getEmployeesCount($search, $department_id) {
        $this->db->select('count(*) as all_count');

        $this->db->from('users A');
        $this->db->join('user_personal_details B', 'B.user_id = A.id', 'left');
        $this->db->join('user_job_details C', 'C.user_id = A.id', 'left');

        if($search){
            $this->db->group_start();
            $this->db->like('B.first_name', $search);
            $this->db->or_like('B.middle_name', $search);
            $this->db->or_like('B.last_name', $search);
            $this->db->group_end();
        }
        
        if(isset($department_id) && $department_id != 'all') $this->db->where('C.department_id', $department_id);

        $query = $this->db->get();
        return $query->row()->all_count;
    }

	public function insertAppraisalEvaluationResponses($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_evaluate_responses', $data);
        $this->db->trans_complete();
	}


    public function getDirectReportStaff($staff_id){
        $this->db->select('
            A.first_name,
            A.last_name,
            B.position,
            B.employee_number,
            B.hiring_date,
            C.name as department,
        ');

        $this->db->from('user_personal_details A');
        $this->db->join('user_job_details B', 'B.user_id = A.user_id', 'left');
        $this->db->join('departments C', 'C.id = B.department_id', 'left');

		$this->db->where('A.user_id', $staff_id);

        $query = $this->db->get();
        return $query->row();
    }

	public function insertAppraisalSelfResponses($data){
        $this->db->trans_start();
		$this->db->insert('appraisal_self_responses', $data);
        $this->db->trans_complete();
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


    public function getUserActionItemByItemIdAndByDate($user_id, $item_id){
        $this->db->select('
            C.id,
            C.item_id,
            E.name as item_name,
            C.status as status_id,
            D.name as status,

            B.user_id as staff_id,
            CONCAT(B.first_name," ",B.last_name) as staff_name, 
            F.position as staff_position,
            F.employee_number as staff_employee_number,
            F.hiring_date as staff_hiring_date,
            G.email as staff_email,
        ');

        $this->db->from('user_direct_reports A');
        $this->db->join('user_personal_details B', 'B.user_id = A.user_id', 'left');
        $this->db->join('action_items C', 'C.user_id = A.user_id');
        $this->db->join('action_item_status D', 'D.id = C.status');
        $this->db->join('items E', 'E.id = C.item_id');
        $this->db->join('user_job_details F', 'F.user_id = A.user_id', 'left');
        $this->db->join('user_contact_details G', 'G.user_id = A.user_id', 'left');

		$this->db->where('A.direct_user_id', $user_id);
        $this->db->where('C.item_id', $item_id);
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

    public function getActionItemSubmit180DegreeAssessment($user_id){
        $this->db->select('A.id, C.name as module, B.name as item, D.name as status, A.status as status_id, A.item_id');

        $this->db->from('action_items A');
		$this->db->join('items B', 'B.id = A.item_id');
		$this->db->join('modules C', 'C.id = A.module_id');
		$this->db->join('action_item_status D', 'D.id = A.status');

		$this->db->where('A.user_id', $user_id);
		$this->db->where('A.item_id', 5);
		$this->db->where('A.status', 1);

        $query = $this->db->get();
        return $query->row();
    }

    public function getActionItemSubmitManagementAssessment($user_id){
        $this->db->select('A.id, C.name as module, B.name as item, D.name as status, A.status as status_id, A.item_id');

        $this->db->from('action_items A');
		$this->db->join('items B', 'B.id = A.item_id');
		$this->db->join('modules C', 'C.id = A.module_id');
		$this->db->join('action_item_status D', 'D.id = A.status');

		$this->db->where('A.user_id', $user_id);
		$this->db->where('A.item_id', 4);
		$this->db->where('A.status', 1);

        $query = $this->db->get();
        return $query->row();
    }

    public function getActionItemSubmitSelfAssessment($user_id){
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
        $this->db->select('id, weight');
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

    public function getterKraKpiGrade($user_id, $evaluatee_id, $type){
        $this->db->select('A.id, E.weight,
                           A.key_result_areas_or_key_performance_indicators,
                           A.result_achieved_or_not_achieved,
                           A.rating');
        $this->db->from('appraisal_response_kra_or_kpi_grade_answers A');
        $this->db->join('appraisal_response_kra_or_kpi_grades B', 'A.appraisal_response_kra_or_kpi_grade_id = B.id', 'left');
        $this->db->join('appraisal_responses C', 'B.appraisal_response_id = C.id', 'left');
        $this->db->join('kra_kpi_grade E', 'A.kra_kpi_grade_id = E.id', 'left');

        switch($type){
            case "self":
                $this->db->join('appraisal_self_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.user_id', $user_id);
                break;
            case "management":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
            case "180":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
            case "view-180":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
        }

        $this->db->order_by('C.dateadded', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function getterCoreCompetencyGrade($user_id, $evaluatee_id, $type){
        $this->db->select('A.id, E.title, E.description, A.critical_incidents_or_comments, A.rating');
        $this->db->from('appraisal_response_core_competency_grade_answers A');
        $this->db->join('appraisal_response_core_competency_grades B', 'A.appraisal_response_core_competency_grade_id = B.id', 'left');
        $this->db->join('appraisal_responses C', 'B.appraisal_response_id = C.id', 'left');
        $this->db->join('core_competency_grade E', 'A.core_competency_grade_id = E.id', 'left');
        
        switch($type){
            case "self":
                $this->db->join('appraisal_self_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.user_id', $user_id);
                break;
            case "management":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
            case "180":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
            case "view-180":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
        }


        $query = $this->db->get();
        return $query->result();
    }

    public function getterFunctionalCompetencyAndPunctualityGrade($user_id, $evaluatee_id, $type){
        $this->db->select('A.id, E.title, E.description, A.rating, A.critical_incidents_or_comments');
        $this->db->from('appraisal_response_functional_competency_grade_answers A');
        $this->db->join('appraisal_response_functional_competency_grades B', 'A.appraisal_response_functional_competency_and_punctuality_grade_i = B.id', 'left');
        $this->db->join('appraisal_responses C', 'B.appraisal_response_id = C.id', 'left');
        $this->db->join('functional_competency_and_punctuality_grade E', 'A.functional_competency_and_punctuality_grade_id = E.id', 'left');
         
        switch($type){
            case "self":
                $this->db->join('appraisal_self_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.user_id', $user_id);
                break;
            case "management":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
            case "180":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
            case "view-180":
                $this->db->join('appraisal_evaluate_responses D', 'C.id = D.appraisal_response_id', 'left');
                $this->db->where('D.evaluator_id', $user_id);
                $this->db->where('D.evaluatee_id', $evaluatee_id);
                break;
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function getterAttendanceAndPunctualityGrade($user_id, $evaluatee_id, $type){
        $this->db->select('C.absences, C.tardiness');
        $this->db->from('appraisal_responses A');
        $this->db->join('appraisal_response_functional_competency_grades C', 'C.appraisal_response_id = A.id', 'left');

        switch($type){
            case "self":
                $this->db->join('appraisal_self_responses B', 'A.id = B.appraisal_response_id', 'left');
                $this->db->where('B.user_id', $user_id);
                break;
            case "management":
                $this->db->join('appraisal_evaluate_responses B', 'A.id = B.appraisal_response_id', 'left');
                $this->db->where('B.evaluator_id', $user_id);
                $this->db->where('B.evaluatee_id', $evaluatee_id);
                break;
            case "180":
                $this->db->join('appraisal_evaluate_responses B', 'A.id = B.appraisal_response_id', 'left');
                $this->db->where('B.evaluator_id', $user_id);
                $this->db->where('B.evaluatee_id', $evaluatee_id);
                break;
            case "view-180":
                $this->db->join('appraisal_evaluate_responses B', 'A.id = B.appraisal_response_id', 'left');
                $this->db->where('B.evaluator_id', $user_id);
                $this->db->where('B.evaluatee_id', $evaluatee_id);
                break;
        }

        $this->db->order_by('A.dateadded', 'DESC');

        $query = $this->db->get();
        return $query->row();
    }

    public function getterComments($user_id, $evaluatee_id, $type){
        $this->db->select('A.key_strengths, A.areas_for_development, A.major_development_plans_for_next_year, A.comments_on_your_overall_performance_and_development_plan');
        $this->db->from('appraisal_response_comments A');
        $this->db->join('appraisal_responses B', 'A.appraisal_response_id = B.id', 'left');
                 
        switch($type){
            case "self":
                $this->db->join('appraisal_self_responses C', 'B.id = C.appraisal_response_id', 'left');
                $this->db->where('C.user_id', $user_id);
                break;
            case "management":
                $this->db->join('appraisal_evaluate_responses C', 'B.id = C.appraisal_response_id', 'left');
                $this->db->where('C.evaluator_id', $user_id);
                $this->db->where('C.evaluatee_id', $evaluatee_id);
                break;
            case "180":
                $this->db->join('appraisal_evaluate_responses C', 'B.id = C.appraisal_response_id', 'left');
                $this->db->where('C.evaluator_id', $user_id);
                $this->db->where('C.evaluatee_id', $evaluatee_id);
                break;
            case "view-180":
                $this->db->join('appraisal_evaluate_responses C', 'B.id = C.appraisal_response_id', 'left');
                $this->db->where('C.evaluator_id', $user_id);
                $this->db->where('C.evaluatee_id', $evaluatee_id);
                break;
        }

        $this->db->order_by('B.dateadded', 'DESC');
        
        $query = $this->db->get();
        return $query->row();
    }

    public function getAppraisalResponse($user_id, $evaluatee_id, $type){
        $this->db->select('A.id');
        $this->db->from('appraisal_responses A');

        switch($type){
            case "self":
                $this->db->join('appraisal_self_responses B', 'A.id = B.appraisal_response_id', 'left');
                $this->db->where('B.user_id', $user_id);
                break;
            case "management":
                $this->db->join('appraisal_evaluate_responses B', 'A.id = B.appraisal_response_id', 'left');
                $this->db->where('B.evaluator_id', $user_id);
                $this->db->where('B.evaluatee_id', $evaluatee_id);
                break;
            case "180":
                $this->db->join('appraisal_evaluate_responses B', 'A.id = B.appraisal_response_id', 'left');
                $this->db->where('B.evaluator_id', $user_id);
                $this->db->where('B.evaluatee_id', $evaluatee_id);
                break;
            case "view-180":
                $this->db->join('appraisal_evaluate_responses B', 'A.id = B.appraisal_response_id', 'left');
                $this->db->where('B.evaluator_id', $user_id);
                $this->db->where('B.evaluatee_id', $evaluatee_id);
                break;
        }

        $query = $this->db->get();
        return $query->row();
    }
    
    public function getUserJobDetails($user_id){
        $this->db->select('
            A.id,

            B.hiring_date,
            B.tenure,
            B.company,
            C.name as department,
            C.id as department_id,
            B.position,
            B.employee_status,
        ');

        $this->db->from('users A');
        $this->db->join('user_job_details B', 'B.user_id = A.id');
        $this->db->join('departments C', 'C.id = B.department_id');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getUserPersonalDetails($user_id){
        $this->db->select('
            A.id,

            B.first_name,
            B.middle_name,
            B.last_name,
            B.gender,
            B.date_of_birth,
            B.education,
            B.marital_status,
            B.sss_no,
            B.tin_no,
            B.philhealth_no,
            B.pagibig_no,
        ');

        $this->db->from('users A');
        $this->db->join('user_personal_details B', 'B.user_id = A.id');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getUserContactDetails($user_id){
        $this->db->select('
            A.id,

            B.contact_number,
            B.email,
            B.address,
            B.city,
        ');

        $this->db->from('users A');
        $this->db->join('user_contact_details B', 'B.user_id = A.id');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }
    
    public function getUserEmergencyDetails($user_id){
        $this->db->select('
            A.id,

            B.emergency_contact_person,
            B.contact_info,
            B.emergency_contact_relationship,
            B.any_health_problem,
            B.blood_type,
        ');

        $this->db->from('users A');
        $this->db->join('user_emergency_details B', 'B.user_id = A.id');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    
    public function getUserSalaryDetails($user_id){
        $this->db->select('
            A.id,

            B.initial_salary,
            B.current_salary,
            B.bank_account_no,
        ');

        $this->db->from('users A');
        $this->db->join('user_salary_details B', 'B.user_id = A.id');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }
    
    public function getUserTerminationDetails($user_id){
        $this->db->select('
            A.id,

            B.active,
            B.termination_date,
            B.termination_reason,
        ');

        $this->db->from('users A');
        $this->db->join('user_termination_details B', 'B.user_id = A.id');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }
    
    public function getUserOtherDetails($user_id){
        $this->db->select('
            A.id,

            B.detail,
        ');

        $this->db->from('users A');
        $this->db->join('user_other_details B', 'B.user_id = A.id');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }
}
