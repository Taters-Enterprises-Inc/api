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

    public function getRatingScale(){
        $this->db->select('name, description');
        $this->db->from('rating_scale');

        $query = $this->db->get();
        return $query->result();
    }

    public function getKraKpiGrade(){
        $this->db->select('*');
        $this->db->from('kra_kpi_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getCoreCompetencyGrade(){
        $this->db->select('*');
        $this->db->from('core_competency_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getFunctionalCompetencyAndPunctualityGrade(){
        $this->db->select('*');
        $this->db->from('functional_competency_and_punctuality_grade');

        $query = $this->db->get();
        return $query->result();
    }

    public function getAttendanceAndPunctuality(){
        $this->db->select('name, absences, tardiness');
        $this->db->from('attendance_and_punctuality');

        $query = $this->db->get();
        return $query->result();
    }
}