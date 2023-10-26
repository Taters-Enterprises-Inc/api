<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hr_appraisal_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('hr-appraisal', TRUE, TRUE);
        //$this->newteishop = $this->load->database('default', TRUE, TRUE);
    }

    public function getPerformanceCriteria(){
        $this->db->select('name, minimum_score, maximum_score');
        $this->db->from('performance_criteria');

        $query = $this->db->get();
        return $query->result();
    }
}
