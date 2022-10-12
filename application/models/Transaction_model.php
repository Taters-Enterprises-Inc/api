<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaction_model extends CI_Model {

    public function insertSnackShopTransactionDetails($data)
    {   
        $this->db->trans_start();
		$this->db->insert('transaction_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return array(
            'id'=>$insert_id,
            'status' =>$this->db->trans_status(),
        );
    }
    
    public function insertSnackshopClientOrders($data)
    {
        $this->db->trans_start();
        $this->db->insert_batch('order_items', $data);
        $this->db->trans_complete();
        return  $this->db->trans_status();
    }
    
    public function insertCateringTransactionDetails($data)
    {   
        $this->db->trans_start();
        $this->db->insert('catering_transaction_tb', $data);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return array(
            'id'=>$insert_id,
            'status' =>$this->db->trans_status(),
        );
    }

    public function insertCateringClientOrders($data)
    {
        $this->db->trans_start();
        $this->db->insert_batch('catering_order_items', $data);
        $this->db->trans_complete();
        return  $this->db->trans_status();
    }
    
	public function insertPopClubClientOrders($data){
		$this->db->trans_start();
		$this->db->insert_batch('deals_order_items', $data);
		$this->db->trans_complete();
		return  $this->db->trans_status();
	}
    

}