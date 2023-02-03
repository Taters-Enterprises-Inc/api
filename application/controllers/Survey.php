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
		$this->load->model('notification_model');
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
				$fb_user_id = $this->session->userData['fb_user_id'] ?? null;
				$mobile_user_id = $this->session->userData['mobile_user_id'] ?? null;
                $generated_hash = substr(md5(uniqid(mt_rand(), true)), 0, 20);

				$message = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore";

				switch($service){
					case 'snackshop':
						$order_details = $this->shop_model->view_order($order_hash);
						$customer_survey = array(
							"transaction_id" => $order_details['clients_info']->id,
							"order_date" =>  $order_details['clients_info']->dateadded,
							"store_id" =>  $order_details['clients_info']->store,
							'customer_survey_response_order_type_id' => 2,
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"status" => 2,
							"hash" => $generated_hash,
						);
								
						$notification_event_data = array(
							"notification_event_type_id" => 4,
							"transaction_tb_id" => $order_details['clients_info']->id,
							"text" => $message
						);
						break;
					case 'catering':
						$order_details = $this->catering_model->view_order($order_hash);
						$customer_survey = array(
							"catering_transaction_id" => $order_details['clients_info']->id,
							"order_date" =>  $order_details['clients_info']->dateadded,
							"store_id" =>  $order_details['clients_info']->store,
							'customer_survey_response_order_type_id' => 3,
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"status" => 2,
							"hash" => $generated_hash,
						);
						
						$notification_event_data = array(
							"notification_event_type_id" => 4,
							"transaction_tb_id" => $order_details['clients_info']->id,
							"text" => $message
						);
						break;
					default:  // WALK IN
						$customer_survey = array(
							"order_no" => $order_no,
							"order_date" => $order_date,
							"store_id" => $store_id,
							'customer_survey_response_order_type_id' => 1,
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"status" => 1,
							"hash" => $generated_hash,
						);
						
						$notification_event_data = array(
							"notification_event_type_id" => 4,
							"text" => $message
						);
						
						break;
				}


				$customer_survey_id = $this->survey_model->insertCustomerSurveyResponse($customer_survey);

				foreach($answers as $answer){
					$customer_survey_answer = array(
						"customer_survey_response_id" => $customer_survey_id,
						"survey_question_id" => $answer['surveyQuestionId'],
						'survey_question_answer_id' => $answer['surveyQuestionAnswerId'] ?? null,
						'other_text' => $answer['otherText'] ?? null,
					);

					$this->survey_model->insertCustomerSurveyResponseAnswer($customer_survey_answer);
				}
				
				$notification_event_id = $this->notification_model->insertAndGetNotificationEvent($notification_event_data);

				//mobile or fb             
				$notifications_data = array(
					"fb_user_to_notify" => $this->session->userData['fb_user_id'] ?? null,
					"mobile_user_to_notify" => $this->session->userData['mobile_user_id'] ?? null,
					"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
					"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
					'notification_event_id' => $notification_event_id,
					"dateadded" => date('Y-m-d H:i:s'),
				);
				
				$this->notification_model->insertNotification($notifications_data);   
				

				$response = array(
					'message' => 'Survey submitted!',
					"data" => array(
						"hash" => $generated_hash,
					),
					"test" => $customer_survey,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

	public function answer($hash){
		switch($this->input->server('REQUEST_METHOD')){
		case 'GET':

				if(!isset($hash)){
					$this->output->set_status_header('401');
					echo json_encode(array( "message" => 'Missing queries!'));
					break;
				}

				$survey_answer = $this->survey_model->getCustomerSurveyAnswer($hash);

				$response = array(
					"message" => 'Successfully fetch survey answer',
					"data" => $survey_answer,
				);

				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}
	
}
