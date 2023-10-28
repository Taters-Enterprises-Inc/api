<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Hr_appraisal extends CI_Controller
{
	public function __construct(){
		parent::__construct();

        $this->load->library('form_validation');
        //$this->load->library('excel');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->bsc_auth->set_message_delimiters('', '');
        $this->bsc_auth->set_error_delimiters('', '');

        $this->lang->load('auth');
		$this->load->model('hr_appraisal_model');
        //$this->load->model('report_model');
	}

    public function performance_criteria(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $performance_criteria = $this->hr_appraisal_model->getPerformanceCriteria();

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

            $rating_scale = $this->hr_appraisal_model->getRatingScale();

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

            $kra_kpi_grade = $this->hr_appraisal_model->getKraKpiGrade();

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

            $core_competency_grade = $this->hr_appraisal_model->getCoreCompetencyGrade();

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

            $functional_competency_and_punctuality_grade = $this->hr_appraisal_model->getFunctionalCompetencyAndPunctualityGrade();

            $data = array(
                "functional_competency_and_punctuality_grade" => $functional_competency_and_punctuality_grade, 
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

            $attendance_and_punctuality = $this->hr_appraisal_model->getAttendanceAndPunctuality();

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
}