<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticketing_model extends CI_Model {
  
  public function __construct() {
    $this->db = $this->load->database('ticketing', TRUE, TRUE);
    $this->newteishop = $this->load->database('default', TRUE, TRUE);
  }  

  public function getTicket($ticket_id) {
    $this->db->select('
      T.id, 
      T.status,
      T.department_id,
      TI.ticket_title
    ');
    $this->db->from('tickets T');
    $this->db->join('
      ticket_information TI', 
      'TI.ticket_id = T.id', 
      'left'
    );
    $this->db->where('T.id', $ticket_id);
    
    $query = $this->db->get();
    return $query->row();
  }

  public function getAllTickets($row_no, $row_per_page, $status, $order_by,  $order, $search) {
    $this->db->select('
      T.id, 
      T.status,
      T.department_id,
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
    
    if($status)
      $this->db->where('T.status', $status);  
      
    if($search) {
      $this->db->group_start();
      $this->db->like('T.id', $search);
      $this->db->or_like('TI.ticket_title', $search);
      $this->db->group_end();
    }  
    
    $this->db->limit($row_per_page, $row_no);
    $this->db->order_by($order_by, $order);  
    
    $query = $this->db->get();
    return $query->result();
  }  
  
  public function getAllTicketsCount($status, $search) {
    $this->db->select('count(*) as all_count');
    $this->db->from('tickets T');
    $this->db->join('
      ticket_information TI', 
      'TI.ticket_id = T.id', 
      'left'
    );  
    
    if($status)
      $this->db->where('T.status', $status);  

    if($search) {
      $this->db->group_start();
      $this->db->like('T.id', $search);
      $this->db->or_like('TI.ticket_title', $search);
      $this->db->group_end();
    }  

    $query = $this->db->get();
    return $query->row()->all_count;
  }

  public function getMyTickets($row_no, $row_per_page, $status, $order_by,  $order, $search) {
    $this->db->select('
      T.id, 
      T.status,
      TI.ticket_title
    ');
    $this->db->from('tickets T');
    $this->db->join('
      ticket_information TI', 
      'TI.ticket_id = T.id', 
      'left'
    );
    $this->db->where('T.department_id', 5); // 👈 change 2nd parameter to the department id of the logged in user
    
    if($status)
      $this->db->where('T.status', $status);  
      
    if($search) {
      $this->db->group_start();
      $this->db->like('T.id', $search);
      $this->db->or_like('TI.ticket_title', $search);
      $this->db->group_end();
    }  
    
    $this->db->limit($row_per_page, $row_no);
    $this->db->order_by($order_by, $order);  
    
    $query = $this->db->get();
    return $query->result();
  }

  public function getMyTicketsCount($status, $search) {
    $this->db->select('count(*) as all_count');
    $this->db->from('tickets T');
    $this->db->join('
      ticket_information TI', 
      'TI.ticket_id = T.id', 
      'left'
    );
    $this->db->where('T.department_id', 5); // 👈 change 2nd parameter to the department id of the logged in user
    
    if($status)
      $this->db->where('T.status', $status);  
  
    if($search) {
      $this->db->group_start();
      $this->db->like('T.id', $search);
      $this->db->or_like('TI.ticket_title', $search);
      $this->db->group_end();
    }  
  
    $query = $this->db->get();
    return $query->row()->all_count;
  }

  public function insertTicket($ticket_data)
	{
		$this->db->insert('tickets', $ticket_data);
		$ticket_id = $this->db->insert_id();

		return $ticket_id;
	}

  public function insertTicketInformation($ticket_information)
	{
		$this->db->trans_start();
		$this->db->insert('ticket_information', $ticket_information);
		$this->db->trans_complete();
	}

  public function triageTicket($ticket_id, $data)
	{
		$this->db->where('id', $ticket_id);
    $this->db->update('tickets', $data);
	}
}
