<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Logs_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function insertTransactionLogs($user_id , $action, $reference_id, $details = '')
    {   
		$values = array(
            'user_id'       => $user_id,
            'action'        => $action,
            'details'       => $details,
            'reference_id'  => $reference_id,
            'dateadded'     => date('Y-m-d H:i:s')
        );

        $this->db->insert('transaction_logs_tb', $values);
    }

    public function getTransactionLogs($reference_id)
    {
        $this->db->select('
            A.id, 
            A.dateadded, 
            A.action,
            CONCAT(B.first_name , " " , B.last_name) as user,
            A.details
        ');
        
        $this->db->from('transaction_logs_tb A');
        $this->db->join('users B', 'B.id = A.user_id');

        $this->db->where('A.reference_id', $reference_id);

        $query = $this->db->get();
        return $query->result();
    }

}

/* End of file Logs_model.php */
/* Location: ./application/models/Logs_model.php */