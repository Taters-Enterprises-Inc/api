<?php defined('BASEPATH') OR exit('No direct script access allowed');

class stock_ordering_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('stock-ordering', TRUE, TRUE);
        $this->newteishop = $this->load->database('default', TRUE, TRUE);

    }


    function getStore(){
        $this->newteishop->select('
            A.store_id,
            A.name,
        ');

        $this->newteishop->from('store_tb A');

        $query = $this->newteishop->get();
        return $query->result();
    }
    
    
}