<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
  1 - New
  4 - Declined
  5 - Forfeited
  6 - Completed
*/

class Survey_model extends CI_Model 
{
	
    public function __construct()
    {
		$this->newteishopDB =  $this->load->database('default', TRUE, TRUE);
        $this->db = $this->load->database('bsc', TRUE, TRUE);
    }

	public function getSurvey()
    {   
		$this->db->select('
			A.id,
			A.description,
		
		');

		$this->db->from('survey A');

		$query_survey_details = $this->db->get();
		$survey_details = $query_survey_details->result();

		foreach($survey_details as $key => $survey){
			$this->db->select('
				A.offered_answer_id,
				B.text,
			
			');
			$this->db->from('survey_question_answer A');
			$this->db->join('offered_answer B','B.id = A.offered_answer_id','left');
			$this->db->where('A.survey_id',$survey->id);

			$query_survey_answers= $this->db->get();
			$survey_answers = $query_survey_answers->result();
			$survey_details[$key]->answers = $survey_answers;

		}

		return $survey_details;

    }

}
