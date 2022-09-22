<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model 
{
    function check_admin_password($request,$password,$transaction_id,$store_id,$status)
    {
        $this->db->select("password");
        $this->db->from('users');
        $this->db->where('id', 1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $row = $query->row();

            if (password_verify($password, $row->password)) {

                if ($request == 'change_status') {
                    
                    $this->db->set('status',$status);
                    if ($status == 1) {
                        $this->db->set('payment_proof','');
                    }
                    $this->db->where('id', $transaction_id);
                    $this->db->update('transaction_tb');
                    return true;
                }
                else if ($request == 'store_transfer') {
                    
                    $this->db->set('store', $store_id);
                    $this->db->where('id', $transaction_id);
                    $this->db->update('transaction_tb');
                    return true;
                }
            }
            else {
                return "Wrong Password";
            } 
        } 
        else {
            return false;
        }
    }

    function generate_invoice_num($transaction_id){
        $curr_year = date("yy");
        $data = array(
            'year' => $curr_year,
            'dateadded'         => date('Y-m-d H:i:s'),
            'transaction_id'    => $transaction_id
        );
        $this->db->insert('invoice_tb', $data);
        $insert_id = $this->db->insert_id();
        $return_data['id'] = $insert_id;
        $return_data['status'] = ($this->db->affected_rows()) ? TRUE : FALSE;
        if($return_data['status'] == TRUE){
            $gen = '%06d';
            $inv = sprintf($gen, $insert_id);
            $invoice_num = date("y").'-'.$inv;
            $this->db->set('invoice_num', $invoice_num);
            $this->db->where('id', $transaction_id);
            $this->db->update('transaction_tb');
            return ($this->db->affected_rows()) ? 1 : 0;
        }
    }
    function update_on_click($transaction_id,$trans_action){

        $this->db->set('on_click',$trans_action);
        $this->db->where('id', $transaction_id);
        $this->db->update('transaction_tb');

        return $this->db->affected_rows() ? 1 : 0;
    }

    function update_status($transaction_id,$status)
    {   
        if ($status == 3) {
            $raffle_code = "RC".substr(md5(uniqid(mt_rand(), true)), 0, 6);
            $this->db->set('application_status',1);
            $this->db->set('generated_raffle_code',$raffle_code);
            $this->db->where('trans_id', $transaction_id);
            $this->db->update('raffle_ss_registration_tb');
        }

        if ($status == 6) {
            $this->db->select('*');
            $this->db->from('giftcard_users');
            $this->db->where('trans_id',$transaction_id);
            $result = $this->db->get()->result();

            if (!empty($result)) {
                foreach ($result as $key => $res) {
                    $giftcard_number = "GC".substr(md5(uniqid(mt_rand(), true)), 0, 6);
                    $this->db->set('status',1);
                    $this->db->set('giftcard_number',$giftcard_number);
                    $this->db->where('trans_id', $res->trans_id);
                    $this->db->where('id', $res->id);
                    $this->db->update('giftcard_users');
                }
            }
        }
        
        $this->db->set('status', $status);
        $this->db->where('id', $transaction_id);
        $this->db->update('transaction_tb');
        return ($this->db->affected_rows()) ? 1 : 0;

    }
    
    function validate_ref_num($transaction_id, $ref_num)
    {   
        $this->db->select('id');
        $this->db->from('transaction_tb');
        $this->db->where('reference_num', $ref_num);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {   
            return "Invalid Reference number";
        }
        else{
            $this->db->set('reference_num', $ref_num);
            $this->db->where('id', $transaction_id);
            $this->db->update('transaction_tb');
            return ($this->db->affected_rows()) ? 1 : 0;
        }   
    }

    function uploadPayment($id,$data,$file_name)
    {
        $file_name = $data['file_name'];
        $this->db->set('payment_proof', $file_name);
        $this->db->set('status', 2);
        $this->db->where("id", $id);
        $this->db->update("transaction_tb");
        return ($this->db->affected_rows()) ? 1 : 0;
    }

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

            A.payment_proof,
            A.reference_num,
            A.store,

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
            
            A.payment_proof,
            A.reference_num,
            A.store,

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