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

		$this->load->model('survey_model');
		$this->load->model('shop_model');
		$this->load->model('catering_model');
		$this->load->model('deals_model');
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
				$order_no = $this->input->post('orderedNo');
				$order_date = $this->input->post('orderedDate');
				$store_id = $this->input->post('storeId');
				$order_hash = $this->input->post('orderHash');
				$service = $this->input->post('service');

				switch($service){
					case 'SNACKSHOP':
						$order_details = $this->shop_model->view_order($order_hash);
						$customer_survey = array(
							"transaction_id" => $order_details['clients_info']->id,
							"order_date" =>  $order_details['clients_info']->dateadded,
							"store_id" =>  $order_details['clients_info']->store,
							'customer_survey_response_order_type_id' => 2,
							"status" => 2,
						);
						break;
					case 'CATERING':
						$order_details = $this->catering_model->view_order($order_hash);

						$customer_survey = array(
							"catering_transaction_id" => $order_details['clients_info']->id,
							"order_date" =>  $order_details['clients_info']->dateadded,
							"store_id" =>  $order_details['clients_info']->store,
							'customer_survey_response_order_type_id' => 3,
							"status" => 2,
						);
						break;
						
					case 'POPCLUB-STORE-VISIT':
						$order_details = $this->deals_model->getUserDeals($order_hash);

						$customer_survey = array(
							"deals_redeem_id" => $order_details['clients_info']->id,
							"order_date" =>  $order_details['clients_info']->dateadded,
							"store_id" =>  $order_details['clients_info']->store,
							'customer_survey_response_order_type_id' => 4,
							"status" => 2,
						);
						break;
					default:  // WALK IN
						$customer_survey = array(
							"order_no" => $order_no,
							"order_date" => $order_date,
							"store_id" => $store_id,
							'customer_survey_response_order_type_id' => 1,
							"status" => 1,
						);
						
						break;
				}


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
