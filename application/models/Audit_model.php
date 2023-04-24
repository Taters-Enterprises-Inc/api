<?php defined('BASEPATH') OR exit('No direct script access allowed');

class audit_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('audit', TRUE, TRUE);
    }
    
}