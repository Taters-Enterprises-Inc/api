<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Logs_model extends CI_Model {

    var $table = 'transaction_logs_tb';

    public function __construct()
    {
        $this->load->database();
    }

    public function insert_log($data)
    {   

        if (!$this->db->table_exists($this->table) )
        {
            return array('Error' => 'Table-logs does not exist!');
            exit();
        }

        date_default_timezone_set('Asia/Manila');
        
		$values = array(
            'user_id'       => $data['user'],
            'action'        => $data['action'],
            'details'        => $data['details'],
            'reference_id'  => $data['reference_id'],
            'dateadded'     => date('Y-m-d H:i:s')
        );

        $this->db->insert($this->table, $values);

        if(($this->db->affected_rows() != 1))
        {
            $ret['id']      = null;
            $ret['status']  = FALSE;
        }else{
            $ret['id']      = $this->db->insert_id();
            $ret['status']  = TRUE;
        }

        return $ret;
    }

    public function fetch_logs($conditions)
    {
        if (!$this->db->table_exists($this->table) )
        {
            return array('Error' => 'Table-logs does not exist!');
            exit();
        }

        $this->db->select('*, a.dateadded AS addeddate');
        // $this->db->join('users b', 'b.id = a.user_id','left');
        
        $this->db->from($this->table.' a');
        $this->db->join('users b', 'b.id = a.user_id','left');
        $this->db->join('transaction_tb t', 't.id = a.reference_id','left');
        $this->db->join('client_tb c', 'c.id = a.user_id','left');

        if(!empty($conditions)){
            
            if(array_key_exists('date_start', $conditions)){
                $this->db->where('dateadded >=', $conditions['date_start']);
                $this->db->where('dateadded <=', $conditions['date_end']);
                unset($conditions['date_start']);
                unset($conditions['date_end']);
            }

            $this->db->where($conditions);
        }

        $query = $this->db->get();
        return $query->result();
    }

}

/* End of file Logs_model.php */
/* Location: ./application/models/Logs_model.php */