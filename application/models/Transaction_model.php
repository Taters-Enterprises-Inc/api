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
    
	public function insertPopClubTransactionDetails($data){   
		$this->db->trans_start();
		$this->db->insert('deals_redeems_tb', $data);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		
		$id = ($this->db->trans_status() === FALSE) ? 0 : $insert_id;
		return  json_decode(json_encode(array('status'=>$this->db->trans_status(),'id'=>$id)), FALSE);
	}

	public function insertPopClubClientOrder($data){
		$this->db->trans_start();
		$this->db->insert('deals_order_items', $data);
		$this->db->trans_complete();
		return  $this->db->trans_status();
	}
    
    

}