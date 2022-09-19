<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model 
{
    public function getSnackshopOrder($tracking_no){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.invoice_num,
            concat(B.fname,' ',B.lname) as client_name,
            B.payops,
            B.contact_number,
            B.email,
            B.address,
            B.add_address,
            C.name as store_name
        ");
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        $this->db->where('A.tracking_no', $tracking_no);

        $query = $this->db->get();
        return $query->row();
    }
    public function getSnackshopOrders($row_no, $row_per_page, $status, $order_by,  $order, $search){
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

        if($search){
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like('A.invoice_num', $search);
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

    public function getSnackshopOrdersCount($status, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');

        if($status)
            $this->db->where('A.status', $status);
            
        if($search){
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like('A.invoice_num', $search);
        }
        $query = $this->db->get();
        return $query->row()->all_count;
    }
}