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
		}

		$query = $this->db->get();
		return $query->row();
	}
	
	public function getCustomerSurveyAnswers($hash){
		$this->db->select('
			A.id,
			A.order_date,
			A.invoice_no,
			B.name as store_name,
		');

		$this->db->from('customer_survey_responses A');
		$this->db->join($this->newteishopDB->database.'.store_tb B', 'B.store_id = A.store_id');

		$this->db->where('A.hash', $hash);

		$query_customer_survey_response = $this->db->get();
		$customer_survey_response = $query_customer_survey_response->row();

		$this->db->select('
			A.id, 
			A.text,
			A.others,
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
		
		$this->db->select('
			A.id, 
			A.others,
			A.customer_survey_response_id,
			A.rate,
			B.description as question,
			D.name,
			D.lowest_rate_text,
			D.lowest_rate,
			D.highest_rate_text,
			D.highest_rate,
		');

		$this->db->from('customer_survey_response_ratings A');
		$this->db->join('survey_questions B', 'B.id = A.survey_question_id');
		$this->db->join('survey_question_ratings C', 'C.id = A.survey_question_rating_id', 'left');
		$this->db->join('survey_question_offered_ratings D', 'D.id = C.survey_question_offered_rating_id', 'left');

		$this->db->where('A.customer_survey_response_id', $customer_survey_response->id);

		$query_customer_survey_response_ratings = $this->db->get();
		$customer_survey_response_ratings = $query_customer_survey_response_ratings->result();
		
		$data = $customer_survey_response;
		$data->answers = $customer_survey_response_answers;
		$data->ratings = $customer_survey_response_ratings;

		return $data;
	}

	public function insertCustomerSurveyResponse($data){
        $this->db->trans_start();
		$this->db->insert('customer_survey_responses', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
		return $insert_id;
	}

	public function insertCustomerSurveyResponseRating($data){
        $this->db->trans_start();
		$this->db->insert('customer_survey_response_ratings', $data);
        $this->db->trans_complete();
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
			A.is_email,
			A.is_required,
			A.others,
			A.survey_section_id,
			B.name as section_name,
		');

		$this->db->from('survey_questions A');
		$this->db->join('survey_question_sections B', 'B.id = A.survey_section_id', 'left');

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
		
		foreach($survey_details as $key => $survey){
			$this->db->select('
				A.id,
				A.survey_question_offered_rating_id,
				B.name,
				B.description,
				B.lowest_rate_text,
				B.lowest_rate,
				B.highest_rate_text,
				B.highest_rate,
			');

			$this->db->from('survey_question_ratings A');
			$this->db->join('survey_question_offered_ratings B','B.id = A.survey_question_offered_rating_id','left');
			$this->db->where('A.survey_question_id',$survey->id);

			$query_survey_ratings= $this->db->get();
			$survey_ratings = $query_survey_ratings->result();
			$survey_details[$key]->ratings = $survey_ratings;
		}


		return $survey_details;

    }

}
