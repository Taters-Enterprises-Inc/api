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
		$this->load->model('user_model');
		$this->load->model('notification_model');
		$this->load->model('store_model');
	}

	public function index(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				$raw_survey_details = $this->survey_model->getSurveyQuestions();
				$survey_details = array();

				foreach($raw_survey_details as $raw_survey_detail){
					$survey_details[$raw_survey_detail->survey_section_id]['section_name'] = $raw_survey_detail->section_name;
					$survey_details[$raw_survey_detail->survey_section_id]['surveys'][] = $raw_survey_detail;
				}

				$survey_details = array_values($survey_details);

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
				$invoice_no = $this->input->post('invoiceNo');
				$order_date = $this->input->post('orderedDate');
				$store_id = $this->input->post('storeId');
				$order_hash = $this->input->post('orderHash');
				$service = $this->input->post('service');
				$fb_user_id = $this->session->userData['fb_user_id'] ?? null;
				$mobile_user_id = $this->session->userData['mobile_user_id'] ?? null;
                $generated_hash = substr(md5(uniqid(mt_rand(), true)), 0, 20);
				$admin_and_csr_notification_event_message = '';

				switch($service){
					case 'snackshop':
						$order_details = $this->shop_model->view_order($order_hash);
						$store_id = $order_details['clients_info']->store;
						$invoice_no = $order_details['clients_info']->invoice_num;
						$admin_and_csr_notification_event_message = $this->session->userData['first_name'] . " " . $this->session->userData['last_name'] ." feedbacks in snackshop!";
						
						$customer_survey = array(
							"transaction_id" => $order_details['clients_info']->id,
							"invoice_no" => $order_details['clients_info']->invoice_num,
							"order_date" =>  $order_details['clients_info']->dateadded,
							"store_id" =>  $order_details['clients_info']->store,
							'customer_survey_response_order_type_id' => 2,
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"status" => 2,
							"hash" => $generated_hash,
						);
								
						$mobile_and_fb_survey_notification_event_data = array(
							"notification_event_type_id" => 4,
							"transaction_tb_id" => $order_details['clients_info']->id,
							"text" => 'Claim Your Gift!'
						);
						
						$admin_and_csr_notification_event_data = array(
							"notification_event_type_id" => 5,
							"transaction_tb_id" => $order_details['clients_info']->id,
							"text" => $admin_and_csr_notification_event_message,
						);
						
						$mobile_and_fb_survey_notification_message_data = array(
							"title" => "Claim Your Gift!",
							"body" => "Here are the steps to claim your prize:
							
									1. Send an email to stacey@rafflepress.com within 7 days to claim your prize
									2. Please confirm that it’s OK for us to publish your name on our social media channels and website
									3. This is optional, but if you’re as excited as we are about your win, take a selfie and share it with us!

									If you have any questions, just hit reply on this email and I’ll be happy to help!
									",
							"message_from" => "Taters Enterprises Inc.",
							"contact_number" => "(+64) 977-275-5595",
							"email" => "tei.csr@tatersgroup.com",
							"image_title" => $invoice_no,
							"image_url" => "https://www.ilovetaters.com/api/assets/images/home/cards/taters_branches.jpg",
						);
						break;
					case 'catering':
						$order_details = $this->catering_model->view_order($order_hash);
						$store_id = $order_details['clients_info']->store;
						$invoice_no = $order_details['clients_info']->invoice_num;
						$admin_and_csr_notification_event_message = $this->session->userData['first_name'] . " " . $this->session->userData['last_name'] ." feedbacks in catering!";

						$customer_survey = array(
							"catering_transaction_id" => $order_details['clients_info']->id,
							"invoice_no" => $order_details['clients_info']->invoice_num,
							"order_date" =>  $order_details['clients_info']->dateadded,
							"store_id" =>  $order_details['clients_info']->store,
							'customer_survey_response_order_type_id' => 3,
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"status" => 2,
							"hash" => $generated_hash,
						);
						
						$mobile_and_fb_survey_notification_event_data = array(
							"notification_event_type_id" => 4,
							"catering_transaction_tb_id" => $order_details['clients_info']->id,
							"text" => 'Claim Your Gift!'
						);

						$admin_and_csr_notification_event_data = array(
							"notification_event_type_id" => 5,
							"catering_transaction_tb_id" => $order_details['clients_info']->id,
							"text" => $admin_and_csr_notification_event_message,
						);

						
						$mobile_and_fb_survey_notification_message_data = array(
							"title" => "Claim your gift!",
							"body" => "Here are the steps to claim your gift:
							
									1. Send an email to stacey@rafflepress.com within 7 days to claim your prize
									2. Please confirm that it’s OK for us to publish your name on our social media channels and website
									3. This is optional, but if you’re as excited as we are about your win, take a selfie and share it with us!

									If you have any questions, just hit reply on this email and I’ll be happy to help!
									",
							"message_from" => "Taters Enterprises Inc.",
							"contact_number" => "(+64) 977-275-5595",
							"email" => "tei.csr@tatersgroup.com",
							"image_title" => $invoice_no,
							"image_url" => "https://www.ilovetaters.com/api/assets/images/home/cards/taters_branches.jpg",
						);

						break;
					default:  // WALK IN
						$admin_and_csr_notification_event_message =  $this->session->userData['first_name'] . " " . $this->session->userData['last_name'] ." feedbacks in walk-in!";

						$customer_survey = array(
							"invoice_no	" => $invoice_no,
							"order_date" => $order_date,
							"store_id" => $store_id,
							'customer_survey_response_order_type_id' => 1,
							"fb_user_id" => $fb_user_id,
							"mobile_user_id" => $mobile_user_id,
							"status" => 1,
							"hash" => $generated_hash,
						);
						
						$mobile_and_fb_survey_notification_event_data = array(
							"notification_event_type_id" => 4,
							"text" => "Congratulations and Thank you for completing the survey!",
						);
						
						$admin_and_csr_notification_event_data = array(
							"notification_event_type_id" => 5,
							"text" => $admin_and_csr_notification_event_message,
						);

						$mobile_and_fb_survey_notification_message_data = array(
							"title" => "Congratulations and Thank you for completing the survey!",
							"body" => "Your feedback is really important as it will helps us to constantly improve our service to give a POPTASTIC customer experience.",
							"closing" => "Don't hesitate to reach out if you have any more questions, comments, or concerns.",
							"closing_salutation" => "Best wishes,",
							"message_from" => "Taters Enterprises Inc.",
							"contact_number" => "(+64) 977-275-5595",
							"email" => "tei.csr@tatersgroup.com",
							"internal_link_title" => "Survey Answer",
							"internal_link_url" => "/feedback/complete/".$generated_hash,
						);
						
						break;
				}


				$customer_survey_id = $this->survey_model->insertCustomerSurveyResponse($customer_survey);

				foreach($answers as $answer){
					if(isset($answer['surveyQuestionRatingId'])){
						$customer_survey_rating = array(
							"customer_survey_response_id" => $customer_survey_id,
							"survey_question_id" => $answer['surveyQuestionId'],
							'survey_question_rating_id' => $answer['surveyQuestionRatingId'],
							'rate' => $answer['rate'],
							'others' => $answer['others'] ?? null,
						);

	
						$this->survey_model->insertCustomerSurveyResponseRating($customer_survey_rating);
					}else{
						$survey_question_answer_id = $answer['surveyQuestionAnswerId'] ?? null;
						$customer_survey_answer = array(
							"customer_survey_response_id" => $customer_survey_id,
							"survey_question_id" => $answer['surveyQuestionId'],
							'survey_question_answer_id' => $survey_question_answer_id != 'others' ?  $survey_question_answer_id : null,
							'text' => $answer['text'] ?? null,
							'others' => $answer['others'] ?? null,
						);
	
						$this->survey_model->insertCustomerSurveyResponseAnswer($customer_survey_answer);
					}
				}

				$mobile_and_fb_survey_notification_message_id = $this->notification_model->insertNotificationMessageAndGetId($mobile_and_fb_survey_notification_message_data);

				$mobile_and_fb_survey_notification_event_data['customer_survey_response_id'] = $customer_survey_id;
				$mobile_and_fb_survey_notification_event_data['notification_message_id'] = $mobile_and_fb_survey_notification_message_id;
				
				$mobile_and_fb_survey_notification_event_id = $this->notification_model->insertAndGetNotificationEvent($mobile_and_fb_survey_notification_event_data);
				

				//mobile or fb             
				$mobile_and_fb_survey_notification_data = array(
					"fb_user_to_notify" => $this->session->userData['fb_user_id'] ?? null,
					"mobile_user_to_notify" => $this->session->userData['mobile_user_id'] ?? null,
					"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
					"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
					'notification_event_id' => $mobile_and_fb_survey_notification_event_id,
					"dateadded" => date('Y-m-d H:i:s'),
				);
				
				$this->notification_model->insertNotification($mobile_and_fb_survey_notification_data);   

				$admin_and_csr_notification_event_data['customer_survey_response_id'] = $customer_survey_id;
				
				$admin_and_csr_notification_event_id = $this->notification_model->insertAndGetNotificationEvent($admin_and_csr_notification_event_data);
				
				//store group
				$users = $this->store_model->getUsersStoreGroupsByStoreId($store_id);
				foreach($users as $user){
					$admin_and_csr_notification_event_data = array(
						"user_to_notify" => $user->user_id,
						"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
						"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
						'notification_event_id' => $admin_and_csr_notification_event_id,
						"dateadded" => date('Y-m-d H:i:s'),
					);

					$this->notification_model->insertNotification($admin_and_csr_notification_event_data);   
				}
                        
				//admin
				$admin_users = $this->user_model->getUsersByGroupId(1);
				foreach($admin_users as $user){
					$admin_and_csr_notification_event_data = array(
						"user_to_notify" => $user->user_id,
						"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
						"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
						'notification_event_id' => $admin_and_csr_notification_event_id,
						"dateadded" => date('Y-m-d H:i:s'),
					);
					$this->notification_model->insertNotification($admin_and_csr_notification_event_data);   
				}
				
				//csr admin
				$csr_admin_users = $this->user_model->getUsersByGroupId(10);
				foreach($csr_admin_users as $user){
					$admin_and_csr_notification_event_data = array(
						"user_to_notify" => $user->user_id,
						"fb_user_who_fired_event" => $this->session->userData['fb_user_id'] ?? null,
						"mobile_user_who_fired_event" => $this->session->userData['mobile_user_id'] ?? null,
						'notification_event_id' => $admin_and_csr_notification_event_id,
						"dateadded" => date('Y-m-d H:i:s'),
					);
					$this->notification_model->insertNotification($admin_and_csr_notification_event_data);   
				}
				
				$realtime_notification = array(
					"store_id" => $store_id,
					"message" => $admin_and_csr_notification_event_message,
				);

				notify('admin-survey-verification','new-survey', $realtime_notification);
				

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

				$survey_answer = $this->survey_model->getCustomerSurveyAnswers($hash);

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
