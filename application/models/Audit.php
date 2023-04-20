<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bsc_model extends CI_Model {

	public function __construct(){
        $this->newteishopDB =  $this->load->database('default', TRUE, TRUE);
        $this->db = $this->load->database('audit', TRUE, TRUE);
    }

    

    
}