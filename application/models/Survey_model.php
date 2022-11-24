<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
  1 - New
  4 - Declined
  5 - Forfeited
  6 - Completed
*/

class Survey_model extends CI_Model 
{
    public function __construct(){
		$this->newteishopDB =  $this->load->database('default', TRUE, TRUE);
        $this->db = $this->load->database('bsc', TRUE, TRUE);
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
			A.is_comment,
		');

		$this->db->from('survey_questions A');

		$query_survey_details = $this->db->get();
		$survey_details = $query_survey_details->result();

		foreach($survey_details as $key => $survey){
			$this->db->select('
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
