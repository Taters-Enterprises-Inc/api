<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Singapore');

class Survey extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->model('survey_model');;
	}

	public function index(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$survey_details = $this->survey_model->getSurveyQuestions();
				
				$response = array(
					'data' => $survey_details,
					'message' => 'Successfully fetch surveys'
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);

				$answers = $this->input->post('answers');

				$customer_survey = array(
					"receipt_no" => '5123',
					"status" => 1,
				);

				$customer_survey_id = $this->survey_model->insertCustomerSurveyResponse($customer_survey);

				foreach($answers as $answer){
					$customer_survey_answer = array(
						"customer_survey_response_id" => $customer_survey_id,
						"survey_question_id" => $answer['surveyQuestionId'],
						'survey_question_offered_answer_id' => $answer['surveyQuestionOfferedAnswerId'] ?? null,
						'other_text' => $answer['otherText'] ?? null,
					);

					$this->survey_model->insertCustomerSurveyResponseAnswer($customer_survey_answer);
				}

				$response = array(
					'message' => $answers[1]
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}
	
}
