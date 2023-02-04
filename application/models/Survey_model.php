<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Survey_model extends CI_Model{

    public function __construct(){
		$this->newteishopDB =  $this->load->database('default', TRUE, TRUE);
        $this->db = $this->load->database('bsc', TRUE, TRUE);
    }

	public function getCustomerSurveyResponseInOrderService($service, $hash){
		$this->db->select('A.id, A.hash');
		
		$this->db->from('customer_survey_responses A');

		switch($service){
			case 'snackshop':
				$this->db->join($this->newteishopDB->database.'.transaction_tb B', 'B.id = A.transaction_id');
				$this->db->where('B.hash_key', $hash);
				break;
			case 'catering':
				$this->db->join($this->newteishopDB->database.'.catering_transaction_tb B', 'B.id = A.catering_transaction_id');
				$this->db->where('B.hash_key', $hash);
				break;
		}

		$query = $this->db->get();
		return $query->row();
	}

	public function getCustomerSurveyAnswer($hash){
		$this->db->select('
			A.id,
			A.order_date,
		');

		$this->db->from('customer_survey_responses A');

		$this->db->where('A.hash', $hash);

		$query_customer_survey_response = $this->db->get();
		$customer_survey_response = $query_customer_survey_response->row();

		$this->db->select('
			A.id, 
			A.other_text,
			A.customer_survey_response_id,
			B.description as question,
			D.text as answer,
		');

		$this->db->from('customer_survey_response_answers A');
		$this->db->join('survey_questions B', 'B.id = A.survey_question_id');
		$this->db->join('survey_question_answers C', 'C.id = A.survey_question_answer_id', 'left');
		$this->db->join('survey_question_offered_answers D', 'D.id = C.survey_question_offered_answer_id', 'left');

		$this->db->where('A.customer_survey_response_id', $customer_survey_response->id);

		$query_customer_survey_response_answers = $this->db->get();
		$customer_survey_response_answers = $query_customer_survey_response_answers->result();
		
		$data = $customer_survey_response;
		$data->answers = $customer_survey_response_answers;

		return $data;
	}

	public function insertCustomerSurveyResponse($data){
        $this->db->trans_start();
		$this->db->insert('customer_survey_responses', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
		return $insert_id;
	}

	public function insertCustomerSurveyResponseAnswer($data){
        $this->db->trans_start();
		$this->db->insert('customer_survey_response_answers', $data);
        $this->db->trans_complete();
	}

	public function getSurveyQuestions(){   
		$this->db->select('
			A.id,
			A.description,
			A.is_text_field,
			A.is_text_area,
		');

		$this->db->from('survey_questions A');

		$query_survey_details = $this->db->get();
		$survey_details = $query_survey_details->result();

		foreach($survey_details as $key => $survey){
			$this->db->select('
				A.id,
				A.survey_question_offered_answer_id,
				B.text,
			');

			$this->db->from('survey_question_answers A');
			$this->db->join('survey_question_offered_answers B','B.id = A.survey_question_offered_answer_id','left');
			$this->db->where('A.survey_question_id',$survey->id);

			$query_survey_answers= $this->db->get();
			$survey_answers = $query_survey_answers->result();
			$survey_details[$key]->answers = $survey_answers;

		}

		return $survey_details;

    }

}
