<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ticketing_model extends CI_Model {
	public function __construct(){
        $this->db = $this->load->database('ticketing', TRUE, TRUE);
        $this->newteishop = $this->load->database('default', TRUE, TRUE);
    }

    public function getTickets(){
        $this->db->select('
            T.id, 
            TI.ticket_title
        ');
        $this->db->from('tickets T');
        $this->db->join('
            ticket_information TI', 
            'TI.ticket_id = T.id', 
            'left'
        );
        //$this->db->where('T.status', 1);
        //$this->db->where('T.department_id IS NULL', null, false);

        $query = $this->db->get();
        return $query->result_array();
    }
}
