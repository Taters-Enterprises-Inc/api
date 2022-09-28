<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model 
{
    
    function updateSettingStore($store_id, $name_of_field_status, $status){
        switch($name_of_field_status){
            case 'status':
                $this->db->set('status', $status);
                break;
            case 'catering_status':
                $this->db->set('catering_status', $status);
                break;
            case 'popclub_walk_in_status':
                $this->db->set('popclub_walk_in_status', $status);
                break;
            case 'popclub_online_delivery_status':
                $this->db->set('popclub_online_delivery_status', $status);
                break;
        }
        $this->db->where("store_id", $store_id);
        $this->db->update("store_tb");
    }
    
    function getSettingStoresCount($search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');

        if($search){
            $this->db->like('A.name', $search);
            $this->db->or_like('B.name', $search);
        }
            
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function getSettingStores($row_no, $row_per_page, $order_by, $order, $search) {
        $this->db->select('
            A.store_id,
            A.name,
            A.status,
            A.catering_status,
            A.popclub_walk_in_status,
            A.popclub_online_delivery_status,
            B.name as menu_name,
        ');

        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');

        if($search){
            $this->db->like('A.name', $search);
            $this->db->or_like('B.name', $search);
        }
            

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }
    
    public function get_order_summary($id)
    { 
        $table = "client_tb A";
        $select_column = array("A.fname", "A.lname", "A.email","A.address", "A.contact_number","A.moh","A.payops","B.id", "B.tracking_no","B.purchase_amount","B.distance_price","B.cod_fee","B.table_number","A.moh","A.payops","B.remarks", "B.status","B.dateadded","B.hash_key","B.store", "B.invoice_num","B.reseller_id","B.reseller_discount","B.discount","Z.name AS store_name","Z.address AS store_address","Z.contact_number AS store_contact","Z.contact_person AS store_person","Z.email AS store_email","Z.delivery_rate AS delivery_rate","Z.moh_notes AS moh_notes","Z.moh_setup AS moh_setup","B.payment_proof","A.add_name","A.add_contact","A.add_address","V.discount_value","V.voucher_code");
        $join_A = "A.id = B.client_id";
        $this->db->select($select_column);  
        $this->db->from($table);
        $this->db->join('transaction_tb B', $join_A ,'left');
        $this->db->join('store_tb Z', 'Z.store_id = B.store' ,'left');
        $this->db->join('voucher_logs_tb V', 'V.transaction_id = B.id' ,'left');
        $this->db->where('B.id', $id);
        $query_info = $this->db->get();
        $info = $query_info->result();

        $this->db->from('products_tb P');
        $this->db->select('*');
        $this->db->join('order_items O', 'P.id = O.product_id' ,'left');
        $this->db->where('O.transaction_id', $id);
        $query_orders = $this->db->get();
        $orders = $query_orders->result();

        $this->db->from('personnel_tb');
        $this->db->select('name,contact_number');
        $this->db->where('reference_code', $info[0]->moh);
        $this->db->where('assigned_store', $info[0]->store);
        $query_orders = $this->db->get();
        $personnel = $query_orders->result();

        $this->db->from('bank_account_tb');
        $this->db->select('*');
        $this->db->where('store_id', $info[0]->store);
        $this->db->where('indicator', $info[0]->payops);
        $query_orders = $this->db->get();
        $bank = $query_orders->result();

        $join_data['clients_info'] = $info[0];
        $join_data['order_details'] = $orders;
        $join_data['personnel'] = $personnel[0];
        $join_data['bank'] = $bank[0];

        // print_r($join_data);
        return $join_data;
    }

    
    public function get_categories() {
        $this->db->select("id, category_name name");
        $this->db->from("category_tb");
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result();
    }

    function getStoreProductCount($store_id, $category_id, $status, $search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('region_da_log A');
        $this->db->join('products_tb B', 'B.id = A.product_id');
        $this->db->join('category_tb C', 'C.id = B.category');

        if($search){
            $this->db->like('B.name', $search);
            $this->db->or_like('C.category_name', $search);
        }

        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);

        if($category_id) $this->db->where('C.id', $category_id);
        
        if($status)
            $this->db->where('A.status', $status);
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function getStoreProducts($row_no, $row_per_page, $store_id, $category_id,  $status, $order_by, $order, $search) {
        $this->db->select('A.id, B.name, A.store_id, B.add_details, C.category_name');
        $this->db->from('region_da_log A');
        $this->db->join('products_tb B', 'B.id = A.product_id');
        $this->db->join('category_tb C', 'C.id = B.category');

        if($search){
            $this->db->like('B.name', $search);
            $this->db->or_like('C.category_name', $search);
        }
            
        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if($category_id !== "6") $this->db->where('C.id', $category_id);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }

    function getStoreDealsCount( $store_id, $status, $search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('deals_region_da_log A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');

        if($search){
            $this->db->like('B.name', $search);
            $this->db->or_like('B.alias', $search);
        }

        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        
        if($status)
            $this->db->where('A.status', $status);
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function updateStoreDeal($id, $status){
		$this->db->set('status', $status);
        $this->db->where("id", $id);
        $this->db->update("deals_region_da_log");
    }
    
    function updateStoreProduct($id, $status){
		$this->db->set('status', $status);
        $this->db->where("id", $id);
        $this->db->update("region_da_log");
    }

    function getStoreDeals($row_no, $row_per_page, $store_id, $status, $order_by, $order, $search) {
        $this->db->select('A.id, B.alias, B.name, A.store_id');
        $this->db->from('deals_region_da_log A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');

        if($search){
            $this->db->like('B.name', $search);
            $this->db->or_like('B.alias', $search);
        }
            
        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        
        $this->db->where('A.status', $status);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }

    
    function get_fname_lname_email($id){
        $this->db->select('first_name,last_name,email');
        $this->db->from('fb_users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row(); 
    }

    function get_fname_lname_email_mobile($id){
        $this->db->select('first_name,last_name,email');
        $this->db->from('mobile_users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row(); 
    }

    function check_admin_password($request,$password,$transaction_id,$store_id,$status){
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

    function generate_shop_invoice_num($transaction_id){
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

    function update_shop_on_click($transaction_id,$trans_action){

        $this->db->set('on_click',$trans_action);
        $this->db->where('id', $transaction_id);
        $this->db->update('transaction_tb');

        return $this->db->affected_rows() ? 1 : 0;
    }

    function generate_catering_invoice_num($id){
        $curr_year = date("yy");
        $data = array(
            'year' => $curr_year,
            'dateadded'         => date('Y-m-d H:i:s'),
            'transaction_id'    => $id
        );
        $this->db->insert('catering_invoice_tb', $data);
        $insert_id = $this->db->insert_id();
        $return_data['id'] = $insert_id;
        $return_data['status'] = ($this->db->affected_rows()) ? TRUE : FALSE;
        if($return_data['status'] == TRUE){
            $gen = '%06d';
            $inv = sprintf($gen, $insert_id);
            $invoice_num = date("y").'-'.$inv;
            $this->db->set('invoice_num', $invoice_num);
            $this->db->where('id', $id);
            $this->db->update('catering_transaction_tb');
            return ($this->db->affected_rows()) ? 1 : 0;
        }
    }

    function update_catering_on_click($id,$trans_action){

        $this->db->set('on_click',$trans_action);
        $this->db->where('id', $id);
        $this->db->update('catering_transaction_tb');

        return $this->db->affected_rows() ? 1 : 0;
        // return $form_data;  
    }

    function update_catering_status($id,$action){           
        $this->db->set('status', $action);
        $this->db->where('id', $id);
        $this->db->update('catering_transaction_tb');
        return ($this->db->affected_rows()) ? 1 : 0;
    }

    function update_shop_status($transaction_id,$status){   
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
    
    function validate_ref_num($transaction_id, $ref_num){   
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

    function uploadPayment($id,$data,$file_name){
        $file_name = $data['file_name'];
        $this->db->set('payment_proof', $file_name);
        $this->db->set('status', 2);
        $this->db->where("id", $id);
        $this->db->update("transaction_tb");
        return ($this->db->affected_rows()) ? 1 : 0;
    }

    function getStores(){
        $this->db->select('
            A.store_id,
            A.name,
            B.name as menu_name,
        ');
        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
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

    
    public function declineRedeem($redeem_code){
		$this->db->set('status', 4);
        $this->db->where('redeem_code', $redeem_code);
        $this->db->update("deals_redeems_tb");
    }
    
    public function getPopclubRedeemItems($redeem_id){
        $this->db->select("
            A.price,
            A.quantity,
            A.remarks,
            B.alias,
        ");
        $this->db->from('deals_order_items A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
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
    
    public function getPopclubRedeemsCount($status, $search, $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
            
        if($search){
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
        }

        if($status)
            $this->db->where('A.status', $status);

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->row()->all_count;
    }
    
    public function getPopclubRedeems($row_no, $row_per_page, $status, $order_by,  $order, $search, $store){
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
        

        if($search){
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
        }
            
        if($status)
            $this->db->where('A.status', $status);

        if(!empty($store))
            $this->db->where_in('A.store', $store);

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
        $products_query = $this->db->get();
        $products = $products_query->result();




        $this->db->select("
            A.product_price,
            A.quantity,
            A.remarks,
            A.product_label,
            B.name,
            B.description,
            B.add_details,
        ");
        $this->db->from('deals_order_items A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
        $this->db->where('A.redeems_id', $transaction_id);
        $deals_query = $this->db->get();
        $deals = $deals_query->result();
        
		return array_merge($products, $deals);
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

            concat(B.fname,' ',B.lname) as client_name,
            B.add_name,
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

    public function getCateringBookingItems($transaction_id){
        $this->db->select("
            A.product_price,
            A.quantity,
            A.remarks,
            A.product_label,
            B.name,
            B.description,
            B.add_details,
        ");
        $this->db->from('catering_order_items A');
        $this->db->join('catering_packages_tb B', 'B.id = A.product_id');
        $this->db->where('A.transaction_id', $transaction_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getCateringBooking($tracking_no){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.serving_time,
            A.tracking_no,
            A.invoice_num,
            A.logon_type,
            A.serving_time,
            A.start_datetime,
            A.end_datetime,
            A.message,
            A.event_class,
            A.company_name,

            A.purchase_amount,
            A.service_fee,
            A.night_diff_fee,
            A.additional_hour_charge,
            A.cod_fee,
            A.distance_price,

            A.payment_plan,

            A.uploaded_contract,
            
            A.initial_payment,
            A.initial_payment_proof,

            A.final_payment,
            A.final_payment_proof,
            

            
            A.reference_num,
            A.store,

            B.fb_user_id,
            B.mobile_user_id,

            B.add_name as client_name,
            B.add_address,
            B.payops,
            B.email,
            B.add_contact,
            B.contact_number,
            C.name as store_name
        ");
        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        $this->db->where('A.tracking_no', $tracking_no);

        $query = $this->db->get();
        return $query->row();
    }
    
    public function getCateringBookingsCount($status, $search, $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        
            
        if($search){
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
        }
        
        if($status)
            $this->db->where('A.status', $status);

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getCateringBookings($row_no, $row_per_page, $status, $order_by,  $order, $search, $store){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.serving_time,
            A.tracking_no,
            A.invoice_num,

            A.purchase_amount,
            A.service_fee,
            A.night_diff_fee,
            A.additional_hour_charge,
            A.cod_fee,
            A.distance_price,
            
            A.reference_num,
            A.store,

            B.add_name as client_name,
            B.payops,
            C.name as store_name
        ");

        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        

        if($search){
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
        }
        
        if($status)
            $this->db->where('A.status', $status);
            
        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getSnackshopOrders($row_no, $row_per_page, $status, $order_by,  $order, $search, $store){
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
            B.add_name,
            B.add_address,
            C.name as store_name
        ");
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        
        

        if($search){
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
        }

        if($status)
            $this->db->where('A.status', $status);
            
        if(!empty($store))
            $this->db->where_in('A.store', $store);
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

    public function getSnackshopOrdersCount($status, $search, $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');

        if($search){
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
        }

        if($status)
            $this->db->where('A.status', $status);

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->row()->all_count;
    }
}