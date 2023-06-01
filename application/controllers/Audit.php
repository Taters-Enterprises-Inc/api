<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Audit extends CI_Controller
{
	public function __construct(){
		parent::__construct();

        $this->load->library('form_validation');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->bsc_auth->set_message_delimiters('', '');
        $this->bsc_auth->set_error_delimiters('', '');

        $this->lang->load('auth');
		$this->load->model('audit_model');

		
	}


	public function login(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);
		        $this->data['title'] = $this->lang->line('login_heading');
                $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
                $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
                
		        if ($this->form_validation->run() === TRUE) {
                    $remember = (bool) $this->input->post('remember');

                    if ($this->ion_auth->audit_login($this->input->post('identity'), $this->input->post('password'), $remember)) {

                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->ion_auth->messages()));
                        return;
                    } else {

				        $this->output->set_status_header(401);
                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->ion_auth->errors()));
                        return;
                    }

                }else{ 
                    $this->output->set_status_header(401);
                    header('content-type: application/json');
                    echo json_encode(array("message" =>  validation_errors()));
                    return;
                }

                break;
        }
    }


    public function logout(){
		$this->data['title'] = "Logout";
		$this->bsc_auth->logout();
        
        header('content-type: application/json');
        echo json_encode(array("message" => 'Successfully logout user'));
        return;
	}

    public function getAuditFormData(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':
                $type = $this->input->get('type') ?? null;


                $questions_data = $this->audit_model->getAuditEvaluationData($type);
                $default_weight_date = $this->audit_model->getDefaultWeight($type);
                $response = array(
                    "message" => 'Successfully fetch Form questions',
                    "data" => array(
                     'default_weight' => $default_weight_date,
                     'question_data' =>  $questions_data,
                        
                    ),
                    );
            
                    header('content-type: application/json');
                    echo json_encode($response);
                    return;
            break;

            case 'POST': 

                $_POST = json_decode(file_get_contents("php://input"), true);


                $store_id = $this->input->post('selectedStoreId');
                $type_id = $this->input->post('selectedTypeId');
                $attention = $this->input->post('attention');
                $period = $this->input->post('period');
                $date = $this->input->post('date');
                $answers = $this->input->post('answers');
                $results = $this->input->post('result');
                $generated_hash = substr(md5(uniqid(mt_rand(), true)), 0, 20);
                $message = "";

                $audit_information = array(
                    "attention" => $attention,
                    "audit_type_id"   => $type_id,
                    "store_id"  => $store_id,
                    "audit_period" => $period,
                    'dateadded' => $date,
                    'user_id'   => $this->session->admin['user_id'],
                    "hash"      => $generated_hash,
                );

                $audit_response_id = $this->audit_model->insertAuditResponse($audit_information);

                if(isset($answers)){
                    foreach($answers as $answer){
                        $rating_id = $answer['form_rating_id'] == 0 ? 4 : $answer['form_rating_id'];
                        $remarks = $answer['remarks'] ?? 'N/A';
    
                        $audit_response_rating = array(
                            "response_id"   => $audit_response_id,
                            "question_id"   => $answer['question_id'] ?? 4,
                            'rating_id'     => $rating_id,
                            'remarks'       => $remarks,
                            'urgency_rating' => $answer['level'],
                            'equivalent_point' => $answer['equivalent_point'],
                        );
                        
                        $this->audit_model->insertAuditAnswer($audit_response_rating);
                    
                    }
                }else {
                    $message = "No Answer data";
                }

                if(isset($results)){
                    foreach($results as $result){
    
                        $audit_response_result = array(
                            "response_id"   => $audit_response_id,
                            "category_id"   => $result['category'],
                            'grade'         => $result['grade'],
                            'weight'        => $result['weight'],
                            'final_score'   => $result['final'],
                        );
                        
                        $this->audit_model->insertAuditResult($audit_response_result);
                    
                    }
                }else {
                    $message = "No Answer data";
                }
                

                $response_info = $this->audit_model->getAuditFormInformation($generated_hash);


                $response = array(
					'message' => $message,
					"data" => array(
						"hash" => $generated_hash,
					),
					"response_data" => $response_info ?? null,
				);

				header('content-type: application/json');
				echo json_encode($response);

            break;

        }
    }


    public function getAuditResponse($hash){

        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                if(!isset($hash)){
					$this->output->set_status_header('401');
					echo json_encode(array( "message" => 'Missing queries!'));
					break;
				}


                $response_info = $this->audit_model->getAuditFormInformation($hash);

                $response = array(
                    "message" => 'Successfully fetch Response Data',
                    "data" => 
                        $response_info
                    );
            
                    header('content-type: application/json');
                    echo json_encode($response);
                    return;
                
                break;
           
        }


    }

    public function getAuditResponseInformation(){

        switch($this->input->server('REQUEST_METHOD')){
            case 'GET': 

            $per_page = $this->input->get('per_page') ?? 25;
            $page_no = $this->input->get('page_no') ?? 0;
            $order = $this->input->get('order') ?? 'asc';
            $order_by = $this->input->get('order_by') ?? 'id';
            $search = $this->input->get('search');

            if($page_no != 0){
                $page_no = ($page_no - 1) * $per_page;
              }

            $response_count = $this->audit_model->getAuditResponseInformationCount($search);
            $responses = $this->audit_model->getAuditResponseInformation($page_no, $per_page, $order_by, $order, $search);
            
            $pagination = array(
                "total_rows" => $response_count,
                "per_page" => $per_page,
              );

            $response = array(
            "message" => 'Successfully fetch Form questions',
            "data" => array(
                "pagination" => $pagination,
                "responses" => $responses
            ),
            );
    
            header('content-type: application/json');
            echo json_encode($response);
            return;

            break;
        }

    }


    public function getAuditFormQuestions(){

        switch($this->input->server('REQUEST_METHOD')){
            case 'GET': 

            $per_page = $this->input->get('per_page') ?? 25;
            $page_no = $this->input->get('page_no') ?? 0;
            $order = $this->input->get('order') ?? 'asc';
            $order_by = $this->input->get('order_by') ?? 'id';
            $search = $this->input->get('search');

            if($page_no != 0){
                $page_no = ($page_no - 1) * $per_page;
              }

            $questions_count = $this->audit_model->getFormQuestionsCount($search);
            $questions = $this->audit_model->getFormQuestions($page_no, $per_page, $order_by, $order, $search);
            
            $pagination = array(
                "total_rows" => $questions_count,
                "per_page" => $per_page,
              );

            $response = array(
            "message" => 'Successfully fetch Form questions',
            "data" => array(
                "pagination" => $pagination,
                "questions" => $questions
            ),
            );
    
            header('content-type: application/json');
            echo json_encode($response);
            return;



            case 'PUT':

                $put = json_decode(file_get_contents("php://input"), true);
                
                $this->audit_model->updateQuestionStatus($put['id'], $put['status'], $put['type']);

                $response = array(
                    "message" => 'Successfully update questions active status',
                  );
            
                  header('content-type: application/json');
                  echo json_encode($response);
                  break;
          }

    }   



    public function stores(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

                $store = $this->audit_model->getStore();

                $data = array(
                    "stores" => $store,
                );
                
                $response = array(
                    "message" => 'Successfully fetch all stores',
                    "data"    => $data, 

                  );
            
                  header('content-type: application/json');
                  echo json_encode($response);
                  break;
            break;
        }
    }

    

	
}