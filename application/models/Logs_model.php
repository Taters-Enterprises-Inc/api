<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Logs_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
        $this->bsc_db = $this->load->database('bsc', TRUE, TRUE);
    }

    public function getCustomerSurveyResponseLogs($customer_survey_response_id){
        $this->bsc_db->select('
            A.id,
            A.customer_survey_response_id,
            A.details,
            A.dateadded,
            B.name as action_name,
            B.color as action_color,
            CONCAT(C.first_name , " " , C.last_name) as user,
        ');

        $this->bsc_db->from('customer_survey_response_logs A');
        $this->bsc_db->join('customer_survey_response_log_actions B', 'B.id = A.customer_survey_response_log_action_id');
        $this->bsc_db->join($this->db->database.".users C", 'C.id = A.user_id');

        $this->bsc_db->where('A.customer_survey_response_id', $customer_survey_response_id);

        $query = $this->bsc_db->get();

        return $query->result();
    }
    

    public function insertCustomerSurveyResponseLog($user_id, $action,  $customer_survey_response_id, $details){
		$values = array(
            'customer_survey_response_id' => $customer_survey_response_id,
            'user_id' => $user_id,
            'details' => $details,
        );

        $this->bsc_db->insert('customer_survey_response_logs', $values);
    }
    

    public function insertCateringTransactionLogs($user_id , $action, $reference_id, $details = '')
    {   
		$values = array(
            'user_id'       => $user_id,
            'action'        => $action,
            'details'       => $details,
            'reference_id'  => $reference_id,
            'dateadded'     => date('Y-m-d H:i:s')
        );

        $this->db->insert('catering_transaction_logs_tb', $values);
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

    public function getCateringTransactionLogs($reference_id){
        $this->db->select('
            A.id, 
            A.dateadded, 
            A.action,
            CONCAT(B.first_name , " " , B.last_name) as user,
            A.details
        ');
        
        $this->db->from('catering_transaction_logs_tb A');
        $this->db->join('users B', 'B.id = A.user_id');

        $this->db->where('A.reference_id', $reference_id);

        $query = $this->db->get();
        return $query->result();
    }
    public function getTransactionLogs($reference_id){
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