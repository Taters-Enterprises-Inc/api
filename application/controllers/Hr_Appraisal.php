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
}