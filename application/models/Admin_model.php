<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model 
{
    public function getSnackshopOrders($row_no, $row_per_page, $status){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.invoice_num,
            concat(B.fname,' ',B.lname) as client_name,
            B.payops,
            C.name as store_name
        ");
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        
        if($status)
            $this->db->where('A.status', $status);
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function getSnackshopOrdersCount($status){
        $this->db->select('count(*) as all_count');
        if($status)
            $this->db->where('status', $status);
        $this->db->from('transaction_tb');
        $query = $this->db->get();
        return $query->row()->all_count;
    }
}