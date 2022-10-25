<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bsc_model extends CI_Model {
	public function __construct(){
        $this->db = $this->load->database('bsc', TRUE, TRUE);
    }

    public function getAllCompanies(){
        $this->db->select('
            id,
            name,
        ');

        $this->db->from('companies');
        $query = $this->db->get();
        return $query->result();
    }

}