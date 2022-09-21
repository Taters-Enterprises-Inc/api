<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model 
{
    function getStores()
    {
        $this->db->select('
            store_id,
            name,
        ');
        $this->db->from('store_tb');
        $query = $this->db->get();
        return $query->result();
    }

    public function getGroups(){
        $this->db->select("
            id,
            name,
            description,
        ");

        $this->db->from('groups');
        
        $query = $this->db->get();
        return $query->result();

    }

    public function getUser($user_id){
        $this->db->select('
            A.id,
            A.first_name,
            A.last_name,
            A.phone,
            A.company,
        ');

        $this->db->from('users A');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getUsersCount($search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('users A');

        if($search){
            $this->db->like('A.first_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like('A.email', $search);
        }
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getUserGroups($user_id){
        
        $this->db->select("
            B.id,
            B.name,
            B.description,
        ");

        $this->db->from('users_groups A');
        $this->db->join('groups B', 'B.id = A.group_id');
        $this->db->where('A.user_id',$user_id);
        

        $query = $this->db->get();
        return $query->result();
    }

    public function getUsers($row_no, $row_per_page, $order_by,  $order, $search){
        $this->db->select("
            A.id,
            A.active,
            A.first_name,
            A.last_name,
            A.email
        ");

        $this->db->from('users A');

        if($search){
            $this->db->like('A.first_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like('A.email', $search);
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();

        return $query->result();
    }

    public function completeRedeem($redeem_code){
		$this->db->set('status', 6);
        $this->db->where('redeem_code', $redeem_code);
        $this->db->update("deals_redeems_tb");
    }
    
    public function getPopclubRedeemItems($redeem_id){
        $this->db->select("
            A.price,
            A.quantity,
            A.remarks,
        ");
        $this->db->from('deals_order_items A');
        $this->db->where('A.redeems_id', $redeem_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getPopclubRedeem($redeem_code){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.redeem_code,
            A.expiration,
            A.purchase_amount,
            A.invoice_num,
            B.add_name as client_name,
            B.payops,
            B.contact_number,
            B.email,
            B.address,
            B.add_address,
            C.name as store_name
        ");
        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        $this->db->where('A.redeem_code', $redeem_code);

        $query = $this->db->get();
        return $query->row();
    }
    
    public function getPopclubRedeemsCount($status, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');

        if($status)
            $this->db->where('A.status', $status);
            
        if($search){
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
        }
        $query = $this->db->get();
        return $query->row()->all_count;
    }
    
    public function getPopclubRedeems($row_no, $row_per_page, $status, $order_by,  $order, $search){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.redeem_code,
            A.expiration,
            A.purchase_amount,
            B.add_name as client_name,
            B.payops,
            C.name as store_name
        ");
        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        
        if($status)
            $this->db->where('A.status', $status);

        if($search){
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

    public function getSnackshopOrderItems($transaction_id){
        $this->db->select("
            A.product_price,
            A.quantity,
            A.remarks,
            A.product_label,
            B.name,
            B.description,
            B.add_details,
        ");
        $this->db->from('order_items A');
        $this->db->join('products_tb B', 'B.id = A.product_id');
        $this->db->where('A.transaction_id', $transaction_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getSnackshopOrder($tracking_no){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.invoice_num,

            A.discount,
            A.reseller_discount,
            A.giftcard_discount,
            A.distance_price,
            A.cod_fee,

            B.add_name as client_name,
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
            A.distance_price,
            A.invoice_num,
            
            A.discount,
            A.reseller_discount,
            A.giftcard_discount,
            A.distance_price,
            A.cod_fee,

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