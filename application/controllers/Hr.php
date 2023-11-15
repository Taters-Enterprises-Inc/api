<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Hr extends CI_Controller
{
	public function __construct(){
		parent::__construct();
        
		if ($this->hr_auth->logged_in() === false){
            if($this->input->server('REQUEST_METHOD') == 'GET'){
                $this->output->set_status_header('401');        
            }
            
            exit();
        }

		$this->load->model('hr_model');
	}

    
    public function direct_report_staff_kras($action_item_id){
        
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $direct_report_staff_kras = $this->hr_model->getUserKrasByActionItemId($action_item_id);

                $response = array(
                    "message" => 'Successfully fetch direct reprot staff kras',
                    "data" => array(
                        "kras" => $direct_report_staff_kras,
                    )
                );
                
                header('content-type: application/json');
                echo json_encode($response);
                break;

        }
    }

    public function direct_report_staff_action_items(){
        
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $user_id = $this->session->hr['user_id'];

                $direct_report_staff_action_items = $this->hr_model->getUserActionItemByItemIdAndByDate($user_id);


                $response = array(
                    "message" => 'Successfully fetch direct reprot staff action items',
                    "data" => array(
                        "action_items" => $direct_report_staff_action_items,
                    )
                );
                
                header('content-type: application/json');
                echo json_encode($response);
                break;

        }
    }

    public function action_items(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $user_id = $this->session->hr['user_id'];

                $action_items = $this->hr_model->getActionItems($user_id);

                $response = array(
                    "message" => 'Successfully fetch action items',
                    "data" => array(
                        "action_items" => $action_items,
                    )
                );
                
                header('content-type: application/json');
                echo json_encode($response);
                break;
            case 'PUT':
                $put = json_decode(file_get_contents("php://input"), true);

                $action_item_id = $put['action_item_id'];
                $item_id = $put['item_id'];
                $status = $put['status'];

                $action_item = $this->hr_model->getActionItemById($action_item_id);
                $user_details = $this->hr_model->getUser($this->session->hr['user_id']);

                if($status == 2 && $item_id == 1){ // Submit KRA - Status To Complete
                    if($user_details->direct_user_id != null){
                        $direct_user = $this->hr_model->getUser($user_details->direct_user_id);
                        
                        $direct_user_latest_action_item_for_review_and_approve_kra = $this->hr_model->getDirectUserLatestActionItem($direct_user->id, 2);

                        if(empty($direct_user_latest_action_item_for_review_and_approve_kra)){
                            $new_action_item = array(
                                'user_id' => $direct_user->id,
                                'module_id' => 1,
                                'item_id' => 2,
                                'status' => 1,
                                'dateupdated' => date('Y-m-d H:i:s',time() + 1)
                            );
        
                            $this->hr_model->insertActionItem($new_action_item);
                        }
                        
                    }
                }else if($status == 3 && $item_id == 1){ // Submit Self Assessment - Status To Approved
                    $new_action_item = array(
                        'user_id' => $action_item->user_id,
                        'module_id' => 1,
                        'item_id' => 3,
                        'status' => 1,
                        'dateupdated' => date('Y-m-d H:i:s',time() + 1)
                    );

                    $this->hr_model->insertActionItem($new_action_item);
                }
                

                $this->hr_model->updateActionItemStatus($action_item_id, $status);

                $response = array(
                    "message" => 'Successfully update action item',
                    "data" =>  $user_details->direct_user_id,
                );
            
                header('content-type: application/json');
                echo json_encode($response);
                break;
        }
    }

    public function kra_or_kpi(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $user_id = $this->session->hr['user_id'];

                $kras = $this->hr_model->getKras($user_id);
                
                $response = array(
                    "message" => 'Successfully fetch kra or kpi',
                    "data" => array(
                        "kras" =>$kras
                    ),
                );
                
                header('content-type: application/json');
                echo json_encode($response);
            break;
            case 'PUT':
                $put = json_decode(file_get_contents("php://input"), true);

                $kras = $put['kras'];
                
                foreach($kras as $val){
                    $kra = array(
                        'details' => $val['details'],
                    );

                    $this->hr_model->updateKras($val['id'], $kra);
                }

        
                $response = array(
                    "message" => 'Successfully update kras',
                );
            
                header('content-type: application/json');
                echo json_encode($response);
                break;
            case 'POST':
                $_POST = json_decode(file_get_contents("php://input"), true);

                $action_item_id = $this->input->post('action_item_id');
                $kra_1= $this->input->post('kra_1');
                $kra_2= $this->input->post('kra_2');
                $kra_3= $this->input->post('kra_3');

                $appraisal_kras_or_kpi_array = [
                    array(

                        "user_id" => $this->session->hr['user_id'],
                        'details' => $kra_1,
                        'action_item_id' => $action_item_id,
                    ),
                    array(
                        "user_id" => $this->session->hr['user_id'],
                        'details' => $kra_2,
                        'action_item_id' => $action_item_id,
                    ),
                    array(
                        "user_id" => $this->session->hr['user_id'],
                        'details' => $kra_3,
                        'action_item_id' => $action_item_id,
                    ),
                ];
    
                $this->hr_model->insertKraOrKpi($appraisal_kras_or_kpi_array);
                
                $response = array(
                    "message" => 'Successfully save kra or kpi',
                );
                
                header('content-type: application/json');
                echo json_encode($response);
            break;
        }
    }

    public function submit_hr_appraisal(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
            $_POST = json_decode(file_get_contents("php://input"), true);

            $generated_hash = substr(md5(uniqid(mt_rand(), true)), 0, 20);
            $user_id = $this->session->hr['user_id'];

            $kra_kpi_grade = $this->input->post('kra_kpi_grade');
            $core_competency_grade = $this->input->post('core_competency_grade');
            $functional_competency_and_punctuality_grade = $this->input->post('functional_competency_and_punctuality_grade');
            $attendance_and_punctuality = $this->input->post('attendance_and_punctuality');
            $comments = $this->input->post('comments');

            $appraisal_response = array(
                "user_id " => $user_id,
                'status' => 0,
                'hash' => $generated_hash,
            );


            $appraisal_response_id = $this->hr_model->insertAppraisalResponses($appraisal_response);


            $appraisal_response_kra_or_kpi_grade = array(
                "appraisal_response_id  " => $appraisal_response_id,
            );

            $appraisal_response_kra_or_kpi_grade_id = $this->hr_model->insertAppraisalKraOrKpiGrades($appraisal_response_kra_or_kpi_grade);

            foreach($kra_kpi_grade as $val){
                    
                $appraisal_response_kra_or_kpi_grade_answer = array(
                    "appraisal_response_kra_or_kpi_grade_id" => $appraisal_response_kra_or_kpi_grade_id,
                    'kra_kpi_grade_id' => $val['id'],
                    'key_result_areas_or_key_performance_indicators' => $val['key_result_areas_or_key_performance_indiciators'],
                    'result_achieved_or_not_achieved' => isset($val['result_achieved_or_not_achieved']) ? $val['result_achieved_or_not_achieved'] : "",
                    'rating' => $val['rating'],
                );

                $this->hr_model->insertAppraisalKraOrKpiGradeAnswers($appraisal_response_kra_or_kpi_grade_answer);
            }

            $appraisal_response_core_competency_grade = array(
                "appraisal_response_id  " => $appraisal_response_id,
            );

            $appraisal_response_core_competency_grade_id = $this->hr_model->insertAppraisalCoreCompetencyGrades($appraisal_response_core_competency_grade);

            
            foreach($core_competency_grade as $val){
                $appraisal_response_core_competency_grade_answer = array(
                    "appraisal_response_core_competency_grade_id" => $appraisal_response_core_competency_grade_id,
                    'core_competency_grade_id' => $val['id'],
                    'rating' => $val['rating'],
                    'critical_incidents_or_comments' => isset($val['critical_incidents_or_comments']) ? $val['critical_incidents_or_comments'] : "",
                );

                $this->hr_model->insertAppraisalCoreCompetencyGradeAnswers($appraisal_response_core_competency_grade_answer);
            }

            
            $appraisal_response_functional_competency_and_punctuality_grade = array(
                "appraisal_response_id" => $appraisal_response_id,
                "absences" =>$attendance_and_punctuality['absences'],
                "tardiness" =>$attendance_and_punctuality['tardiness'],
            );

            $appraisal_response_functional_competency_and_punctuality_grade_id = $this->hr_model->insertAppraisalFunctionalGrades($appraisal_response_functional_competency_and_punctuality_grade);


            foreach($functional_competency_and_punctuality_grade as $val){
                $appraisal_functional_competency_and_punctuality_grade_answer = array(
                    "appraisal_response_functional_competency_and_punctuality_grade_i" => $appraisal_response_functional_competency_and_punctuality_grade_id,
                    'functional_competency_and_punctuality_grade_id' => $val['id'],
                    'rating' => $val['rating'],
                    'critical_incidents_or_comments' => isset($val['critical_incidents_or_comments']) ? $val['critical_incidents_or_comments'] : "",
                );

                $this->hr_model->insertAppraisalFunctionalGradeAnswers($appraisal_functional_competency_and_punctuality_grade_answer);
            }

            $appraisal_response_comments = array(
                "appraisal_response_id" => $appraisal_response_id,
                "key_strengths" =>isset($comments['key_strengths']) ? $comments['key_strengths'] : "",
                "areas_for_development" =>isset($comments['areas_for_development']) ? $comments['areas_for_development'] : "",
                "major_development_plans_for_next_year" =>isset($comments['major_development_plans_for_next_year']) ? $comments['major_development_plans_for_next_year'] : "",
                "comments_on_your_overall_performance_and_development_plan" =>isset($comments['comments_on_your_overall_performance_and_development_plan']) ? $comments['comments_on_your_overall_performance_and_development_plan'] : "",
            );

            $this->hr_model->insertAppraisalComments($appraisal_response_comments);

            
            $action_item = $this->hr_model->getActionItemSubmitKra($user_id);

            if(isset($action_item)){
                $this->hr_model->updateActionItemStatus($action_item->id, 2);
                $user_details = $this->hr_model->getUser($user_id);

                $direct_user_latest_action_item_for_approve_assessment = $this->hr_model->getDirectUserLatestActionItem($user_details->direct_user_id, 4);

                if(empty($direct_user_latest_action_item_for_approve_assessment)){

                    $new_action_item = array(
                        'user_id' => $user_details->direct_user_id,
                        'module_id' => 1,
                        'item_id' => 4,
                        'status' => 1,
                        'dateupdated' => date('Y-m-d H:i:s',time() + 1)
                    );
    
                    $this->hr_model->insertActionItem($new_action_item);
                }
            }

            


            $response = array(
                "message" => 'Successfully submit assessment',
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }


    public function performance_criteria(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $performance_criteria = $this->hr_model->getPerformanceCriteria();

            $data = array(
                "performance_criteria" => $performance_criteria, 
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

    public function rating_scale(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $rating_scale = $this->hr_model->getRatingScale();

            $data = array(
                "rating_scale" => $rating_scale, 
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

    public function kra_kpi_grade(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
            $user_id = $this->session->hr['user_id'];

            $kra_kpi_grade = $this->hr_model->getKraKpiGrade();

            $kras = $this->hr_model->getKras($user_id);

            $kras_with_answer = $this->hr_model->getterKraKpiGrade($user_id);

            $index = 0;

            foreach($kras as $val){
                $kra_kpi_grade[$index]->key_result_areas_or_key_performance_indiciators = $val->details;

                if($index == 2){
                    break;
                }
                $index++;
            }

            $index = 0;
            foreach($kras_with_answer as $val){
                $kra_kpi_grade[$index]->key_result_areas_or_key_performance_indiciators = $val->key_result_areas_or_key_performance_indicators;
                $kra_kpi_grade[$index]->result_achieved_or_not_achieved = $val->result_achieved_or_not_achieved;
                $kra_kpi_grade[$index]->rating = $val->rating;
                if($index == 2){
                    break;
                }
                $index++;
            }


            $data = array(
                "kra_kpi_grade" => $kra_kpi_grade, 
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

    public function core_competency_grade(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $user_id = $this->session->hr['user_id'];
            $core_competency_grade = $this->hr_model->getCoreCompetencyGrade();
            $core_compe_with_answer = $this->hr_model->getterCoreCompetencyGrade($user_id);

            $index = 0;

            foreach($core_compe_with_answer as $val){
                $core_competency_grade[$index]->critical_incidents_or_comments = $val->critical_incidents_or_comments;
                $core_competency_grade[$index]->rating = $val->rating;
                if($index == 6){
                    break;
                }
                $index++;
            }


            $data = array(
                "core_competency_grade" => $core_competency_grade, 
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

    public function functional_competency_and_punctuality_grade(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':


            $user_id = $this->session->hr['user_id'];
            $functional_competency_and_punctuality_grade = $this->hr_model->getFunctionalCompetencyAndPunctualityGrade();
            $func_compe_with_answer = $this->hr_model->getterFunctionalCompetencyAndPunctualityGrade($user_id);

            $index = 0;

            foreach($func_compe_with_answer as $val){
                $functional_competency_and_punctuality_grade[$index]->critical_incidents_or_comments = $val->critical_incidents_or_comments;
                $functional_competency_and_punctuality_grade[$index]->rating = $val->rating;
                if($index == 7){
                    break;
                }
                $index++;
            }
            
            $attendance_and_punctuality_with_answer = $this->hr_model->getterAttendanceAndPunctualityGrade($user_id);



            $data = array(
                "functional_competency_and_punctuality_grade" => $functional_competency_and_punctuality_grade, 
                "attendance_and_punctuality_grade" => $attendance_and_punctuality_with_answer,
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

    public function comments(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $user_id = $this->session->hr['user_id'];
            
            $comments = $this->hr_model->getterComments($user_id);


            $data = array(
                "comments" => $comments, 
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

    public function appraisal_response(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $user_id = $this->session->hr['user_id'];
            
            $appraisal_response = $this->hr_model->getAppraisalResponse($user_id);


            $data = array(
                "appraisal_response" => $appraisal_response, 
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

    public function attendance_and_punctuality(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $attendance_and_punctuality = $this->hr_model->getAttendanceAndPunctuality();

            $data = array(
                "attendance_and_punctuality" => $attendance_and_punctuality, 
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }

	public function session(){
		switch($this->input->server('REQUEST_METHOD')){
		  case 'GET':

			$user_profile =  $this->hr_model->getUserProfile($this->session->hr['user_id']);
	
			$data = array(
				"hr" => array(
					"identity" => $this->session->hr['identity'],
					"email" => $this->session->hr['email'],
					"user_id" => $this->session->hr['user_id'],
					"old_last_login" => $this->session->hr['old_last_login'],
					"last_check" => $this->session->hr['last_check'],
					"is_admin" => $this->hr_auth->in_group(1),
					"is_manager" => $this->hr_auth->in_group(2),
					"is_employee" => $this->hr_auth->in_group(3),
					"user_status_id" => $user_profile->user_status_id,
				)
			);
			
			$data["hr"]['user_details'] = $this->hr_model->getUser($this->session->hr['user_id']);

			$response = array(
			  "message" => 'Successfully fetch hr session',
			  "data" => $data,
			);
	  
			header('content-type: application/json');
			echo json_encode($response);
			return;
		}
	}

    public function getter($user_id){
        /*$kra_kpi_grade = $this->hr_model->getterKraKpiGrade($user_id);
        $core_competency_grade = $this->hr_model->getterCoreCompetencyGrade($user_id);
        $functional_competency_and_punctuality_grade = $this->hr_model->getterFunctionalCompetencyAndPunctualityGrade($user_id);
        $comments = $this->hr_model->getterComments($user_id);
        
        $data = array(
            "kra_kpi_grade" => $kra_kpi_grade,
            "core_competency_grade" => $core_competency_grade,
            "functional_competency_and_punctuality_grade" => $functional_competency_and_punctuality_grade,
            "comments" => $comments,
        );
        print_r($data);
        exit();*/
        
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $kra_kpi_grade = $this->hr_model->getterKraKpiGrade($user_id);
            $core_competency_grade = $this->hr_model->getterCoreCompetencyGrade($user_id);
            $functional_competency_and_punctuality_grade = $this->hr_model->getterFunctionalCompetencyAndPunctualityGrade($user_id);
            $comments = $this->hr_model->getterComments($user_id);

            $data = array(
                "kra_kpi_grade" => $kra_kpi_grade,
                "core_competency_grade" => $core_competency_grade,
                "functional_competency_and_punctuality_grade" => $functional_competency_and_punctuality_grade,
                "comments" => $comments,
            );

            $response = array(
                "message" => '',
                "data"    => $data,
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }
}