<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model{

    public function __construct()
    {
		$this->db =  $this->load->database('sales', TRUE, TRUE);
    }
    
    
}
