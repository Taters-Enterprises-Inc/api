<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_ordering_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('stock-ordering', TRUE, TRUE);
        $this->newteishop = $this->load->database('default', TRUE, TRUE);

    }

    public function getProductData($order_id){
        $this->db->select('
            A.id,
            B.product_id,
            B.product_name,
            B.uom,
            B.cost,
            B.category_id,
            A.order_qty,
            A.commited_qty,
            A.delivered_qty,
            A.total_cost,
            A.order_information_id,
            A.dispatched_qty,
            A.out_of_stock
        ');

        //to be implemented cost and current stock
        $this->db->from('order_item_tb A');
        $this->db->join('product_tb B', 'B.product_id = A.product_id', 'left');
        $this->db->where('A.order_information_id', $order_id);

        $product_query = $this->db->get();
        return $product_query->result_array();
    }

    public function getProduct($category, $store_id){

        $this->db->select('B.product_id, B.product_name, B.uom, B.category_id, B.cost');
        $this->db->from('product_availability_tb A');
        $this->db->join('product_tb B', 'B.product_id = A.product_id', 'left');
        $this->db->where('A.store_id', $store_id);
        $this->db->where('B.category_id', $category);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function getProducts($row_no, $row_per_page, $order_by, $order, $search){

        $this->db->select('*');
        $this->db->from('product_tb');


        if($search){
            $this->db->group_start();
            $this->db->like('product_id', $search);
            $this->db->or_like("product_name", $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function getProductsCount($row_no, $row_per_page, $order_by, $order, $search){

        $this->db->select('count(*) as all_count');
        $this->db->from('product_tb');

        if($search){
            $this->db->group_start();
            $this->db->like('product_id', $search);
            $this->db->or_like("product_name", $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        $query = $this->db->get();
        $result = $query->row()->all_count;

        return $result;
    }

    public function getProductDataById($id){

        $this->db->select('*');
        $this->db->from('product_tb');
        $this->db->where('id', $id);
       
        
        $query = $this->db->get();
        $result = $query->row();

        return $result;
    }

    public function getProductStore($product_id){

        $this->db->select('C.name, C.store_id');
        $this->db->from('product_tb A');
        $this->db->join('product_availability_tb B', 'B.product_id = A.product_id', 'left');
        $this->db->join($this->newteishop->database.'.store_tb C', 'C.store_id = B.store_id', 'left');
        $this->db->where('A.product_id', $product_id);

        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }


    


    public function getSchedule($category,$store_id){

        $this->db->select('
            A.leadtime,
            A.cutoff,
            A.is_mwf,
            A.is_tths,
        ');
        $this->db->from('order_schedule_logic_tb A');
        $this->db->join('store_region_combination B', 'B.region_id = A.region_id', 'left');
        $this->db->join($this->newteishop->database.'.store_tb C', 'C.store_id = B.store_id', 'left');
        $this->db->where('A.category_id', $category);
        $this->db->where('C.store_id', $store_id);

        $query = $this->db->get();
        $sched = $query->row();

        if ($sched) {
            $sched->is_mwf = (bool) $sched->is_mwf;
            $sched->is_tths = (bool) $sched->is_tths;
        }

        return $sched;
    }

    public function insertNewOrders($data){
        $this->db->trans_start();
		$this->db->insert('order_information_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
		return $insert_id;
	}

    public function insertNewOrdersProducts($data){
        $this->db->trans_start();
		$this->db->insert('order_item_tb', $data);
        $this->db->trans_complete();    
	}

    public function insertNewProducts($data){
        $this->db->trans_start();
		$this->db->insert('product_tb', $data);
        $this->db->trans_complete();    
	}

    public function insertNewProductsStore($data){
        $this->db->trans_start();
		$this->db->insert('product_availability_tb', $data);
        $this->db->trans_complete();    
	}

    public function getOrderData($id){
        $this->db->select('
            B.name as store_name,
            B.store_id,
            B.franchise_type_id,
            A.id,
            A.ship_to_address,
            E.category_id,
            E.category_name,
            A.order_placement_date,
            A.requested_delivery_date,
            A.commited_delivery_date,
            A.actual_delivery_date,
            C.description,
            F.short_name,
            A.reviewed_date,
            A.dispatch_date,
            A.payment_confirmation_date,
            A.delivery_receipt,
            A.updated_delivery_receipt,
            A.updated_delivery_goods_receipt,
            A.updated_delivery_region_receipt,
            A.franchisee_payment_detail_image,
            A.payment_detail_image,
            G.label as transport_route,
            H.region_id,
            I.region_name,
            A.status_id,
            J.id as logistic_id,
            J.type as logistic_type, 

        ');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->join('order_status C', 'C.id = A.status_id', 'left');
        $this->db->join('category_tb E', 'E.category_id = A.order_type_id', 'left');
        $this->db->join('payment_status_tb F', 'F.id = A.payment_status_id', 'left');
        $this->db->join('transportation_tb G', 'G.id = A.transportation_id', 'left');
        $this->db->join('store_region_combination H', "H.store_id = B.store_id", 'left');
        $this->db->join('region_tb I', "I.id = H.region_id", "left");
        $this->db->join('logistic_type J', 'J.id = A.logistic_type_id', 'left');



        $this->db->where('A.id', $id);

        $order_query = $this->db->get();
        $orders = $order_query->row();

        $this->db->select('
            A.remarks, 
            A.date,
            B.first_name,
            B.last_name
        ');
        $this->db->from('remarks A'); 
        $this->db->join($this->newteishop->database.'.users B', 'B.id = A.user_id', 'left');
        $this->db->where('order_information_id', $orders->id);
        $remarks_query = $this->db->get();
        $remarks = $remarks_query->result();
      
        $orders->remarks = $remarks;

        $this->db->select('
            A.datetime,
            B.first_name,
            B.last_name,
            C.name
        ');
        $this->db->from('tracking_logs A');
        $this->db->join($this->newteishop->database.'.users B', 'B.id = A.user_id', 'left');
        $this->db->join('tracking_type C', 'C.id = A.tracking_type_id', 'left');
        $this->db->where('A.order_id', $orders->id);
        $tracking_query = $this->db->get();
        $tracking = $tracking_query->result();
      
        $orders->tracking = $tracking;


        return $orders;
    }

    public function getOrders($row_no, $row_per_page, $order_by,  $order, $search, $status, $store_id, $filter_by_store_name, $date_type, $start_date, $end_date){
        $this->db->select('
            A.id,
            B.name as store_name,
            B.franchise_type_id,
            E.category_name,
            A.order_placement_date,
            A.requested_delivery_date,
            A.commited_delivery_date,
            A.order_confirmation_date,
            A.actual_delivery_date,
            C.description,
            F.short_name,
            G.id as logistic_id,
            G.type as logistic_type, 

        ');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->join('order_status C', 'C.id = A.status_id', 'left');
        $this->db->join('category_tb E', 'E.category_id = A.order_type_id', 'left');
        $this->db->join('payment_status_tb F', 'F.id = A.payment_status_id', 'left');
        $this->db->join('logistic_type G', 'G.id = A.logistic_type_id', 'left');

        if($store_id){
            $this->db->where_in('A.store_id', $store_id);
        }

        $this->db->where('A.status_id', $status);

        if(isset($filter_by_store_name)){
           
            $this->db->where('B.name', $filter_by_store_name);
        }

        if((isset($date_type) && isset($start_date) && isset($end_date))){
            $this->db->where("A.$date_type BETWEEN '$start_date' AND '$end_date'");
          
        }
        
        if($search){
            $this->db->group_start();
            $this->db->like('A.id', $search);
            $this->db->or_like("B.name", $search);
            $this->db->or_like('C.description', $search);
            $this->db->or_like('F.short_name', $search);
            $this->db->group_end();
        }
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $order_query = $this->db->get();
        return $order_query->result();
        
    }

    public function getOrdersCount($search, $status, $store_id, $filter_by_store_name, $date_type, $start_date, $end_date){

        $this->db->select('count(*) as all_count');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->join('order_status C', 'C.id = A.status_id', 'left');
        $this->db->join('category_tb E', 'E.category_id = A.order_type_id', 'left');
        $this->db->join('payment_status_tb F', 'F.id = A.payment_status_id', 'left');
        $this->db->join('logistic_type G', 'G.id = A.logistic_type_id', 'left');

        if($store_id){
            $this->db->where_in('A.store_id', $store_id);
        }
        $this->db->where('A.status_id', $status);

        if(isset($filter_by_store_name)){
           
            $this->db->where('B.name', $filter_by_store_name);
        }

        if((isset($date_type) && isset($start_date) && isset($end_date))){
            $this->db->where("A.$date_type BETWEEN '$start_date' AND '$end_date'");
          
        }


        if($search){
            $this->db->group_start();
            $this->db->like('A.id', $search);
            $this->db->or_like("B.name", $search);
            $this->db->or_like('C.description', $search);
            $this->db->or_like('F.short_name', $search);
            $this->db->group_end();
        }

        $order_query = $this->db->get();
        return $order_query->row()->all_count;
        
    }


    public function getOrdersBadge($status, $store_id){

        $this->db->select('count(*) as all_count');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->join('order_status C', 'C.id = A.status_id', 'left');
        $this->db->join('category_tb E', 'E.category_id = A.order_type_id', 'left');
        $this->db->join('payment_status_tb F', 'F.id = A.payment_status_id', 'left');
        $this->db->join('logistic_type G', 'G.id = A.logistic_type_id', 'left');

        if($store_id){
            $this->db->where_in('A.store_id', $store_id);
        }

        $this->db->where('A.status_id', $status);
        

        $order_query = $this->db->get();
        return $order_query->row()->all_count;
        
    }



    public function getStore($user_id, $isAdmin){

        if($isAdmin){
            $this->newteishop->select('store_id, name, franchise_type_id');
            $this->newteishop->from('store_tb');
            $this->newteishop->where('branch_status', 1);
        }else{

            $this->newteishop->select('
                A.store_id,
                A.name,
                A.franchise_type_id
            ');

            $this->newteishop->from('store_tb A');
            $this->newteishop->join('users_store_groups B', 'B.store_id = A.store_id', 'left');
            $this->newteishop->where('A.branch_status', 1);
            $this->newteishop->where('B.user_id', $user_id);

        }

        $query = $this->newteishop->get();
        return $query->result_array();
    }

  

    public function getShipToAddress($id){
        $this->db->select('
            store_id,
            ship_to_address,
            
        ');

        $this->db->from('ship_to_tb');

        if(isset($id)){
            $this->db->where('store_id', $id);
        }

        $ship_query = $this->db->get();
        $ship_to = $ship_query->result();


        $this->newteishop->select('
        
            store_id,
            address as ship_to_address,
        ');
        $this->newteishop->from('store_tb');
        
        if(isset($id)){
            $this->newteishop->where('store_id', $id);
        }

        $address_query = $this->newteishop->get();
        $store_address = $address_query->result();

        $data = array_merge(
            $ship_to,
            $store_address
        );


        $unique_ship_to = array_reduce($ship_to, function ($carry, $item) {
            $address = $item->ship_to_address;
            if (!isset($carry[$address])) {
                $carry[$address] = $item;
            }
            return $carry;
        }, array());

        $data = array_values($unique_ship_to);


        return $data;
    }

    public function updateOrderInfo($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }

    public function updateProductData($id, $data){
        $this->db->where('id', $id);
        $this->db->update('product_tb', $data);

    }

    public function removeProductDataAvailability($data){
        $this->db->delete('product_availability_tb', $data);
    }

    public function updateOrderItem($id ,$id_product, $data){
        $this->db->where('order_information_id', $id);
        $this->db->where('product_id', $id_product);
        $this->db->update('order_item_tb', $data);
    }

    public function insertNewOrderitem($data){
        $this->db->trans_start();
		$this->db->insert_batch('order_item_tb', $data);
        $this->db->trans_complete();


        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insertRemarks($data){
        $this->db->trans_start();
		$this->db->insert('remarks', $data);
        $this->db->trans_complete();
    }

    public function insertTracking($data){
        $this->db->trans_start();
		$this->db->insert('tracking_logs', $data);
        $this->db->trans_complete();
    }

    //-----Flag for remove-----

    //========================

    public function getProductCost($product_id){
        $this->db->select('cost');
        $this->db->from('product_tb');
        $this->db->where('product_id', $product_id);


        $query = $this->db->get();
        return $query->row();
    }

    public function getAllStore(){
        $this->newteishop->select('name, store_id');
        $this->newteishop->from('store_tb');
        $query = $this->newteishop->get();
        return $query->result();
    }

    public function getStoreId($order_id){
        $this->db->select('store_id, order_type_id');
        $this->db->from('order_information_tb');
        $this->db->where('id', $order_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getStoreIdByUserId($user_id, $isAdmin){

        if($isAdmin){
            $this->newteishop->select('store_id');
            $this->newteishop->from('store_tb');
            $query = $this->newteishop->get();
            return $query->result();
        }

        $this->newteishop->select('A.store_id');
        $this->newteishop->from('store_tb A');
        $this->newteishop->join('users_store_groups B', 'B.store_id = A.store_id', 'left');
        $this->newteishop->join('users C', 'C.id = B.user_id', 'left');
        $this->newteishop->where('C.id', $user_id);

        $query = $this->newteishop->get();
        return $query->result();
    }

    public function getProductMultiplier($store_id, $category_id){
        $this->db->select('product_multiplier');
        $this->db->from('product_cost_per_store_tb');
        $this->db->where('store_id', $store_id);
        $this->db->where('category_id', $category_id);
        
        $query = $this->db->get();
        return $query->row();
    }

    public function getUserGroup(){
        $this->db->select("
            id,
            short_name as name,
            description,
        ");

        $this->db->from('order_status');
        
        $query = $this->db->get();
        return $query->result();
    }

    


       public function getUserGroups($user_id){

        if($user_id == 1) {
            $this->db->select('*');
            $this->db->from('order_status');

            $query = $this->db->get();
            return $query->result();
        }
        
        $this->db->select("
            B.id,
            B.short_name,
            B.description,
        ");

        $this->db->from('user_tab_combination A');
        $this->db->join('order_status B', 'B.id = A.order_status_id	');
        $this->db->where('A.user_id',$user_id);
        

        $query = $this->db->get();
        return $query->result();
    }

    public function insertTransactionLog($data){
        $this->db->trans_start();
        $this->db->insert('transaction_logs_tb', $data);
        $this->db->trans_complete();
    }

    public function getStoreDetailsForPdf($order_id){
        $this->db->select('
            A.store_id,
            B.name,
            B.address,
            A.ship_to_address,
            A.id,
            A.requested_delivery_date
        ');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->where('A.id', $order_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getProductDataForPdf($order_id){
        $this->db->select('
            A.id,
            B.product_id,
            B.product_name,
            B.uom,
            B.category_id,
            A.order_qty,
            A.commited_qty,
            A.delivered_qty,
            A.total_cost,
            A.order_information_id,
            A.dispatched_qty,
            A.product_rate,
            B.cost
        ');

        $this->db->from('order_item_tb A');
        $this->db->join('product_tb B', 'B.product_id = A.product_id', 'left');
        $this->db->where('A.order_information_id', $order_id);

        $product_query = $this->db->get();
        return $product_query->result_array();
    }

    public function getRemarkDetailsForPdf($order_id){
        $this->db->select('
            A.remarks,
            A.date,
            B.first_name,
            B.last_name
        ');
        $this->db->from('remarks A');
        $this->db->join($this->newteishop->database.'.users B', 'B.id = A.user_id', 'left');
        $this->db->where('A.order_information_id', $order_id);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getSumForSiPdf($order_id){
        $this->db->select('SUM(delivered_qty) AS `sum_dqty`, SUM(total_cost) AS `total_cost`');
        $this->db->from('order_item_tb');
        $this->db->where('order_information_id', $order_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function updateforSI($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }

    public function getSiOtherDetails($order_id){
        $this->db->select('total_sales, vatable_sales, vat_exempt_sales, zero_rated_sales, vat_amount,  less_vat, vat_ex_amount, less_sc_pwd, amount_due, add_vat, total_amount_due');
        $this->db->from('order_information_tb');
        $this->db->where('id', $order_id);

        $query = $this->db->get();
        return $query->row();
    }

    /* All codes that are related to the Product Availability feature */

    public function getProductList(){

        $this->db->select('*');
        $this->db->from('product_tb');
        $this->db->where('active_status', 1);

        $order_query = $this->db->get();
        return $order_query->result();
        
    }

    public function getProductInfo($product_id){
        $this->db->select('*');
        $this->db->from('product_tb');
        $this->db->where('product_id', $product_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getProductAvailablity($product_id){
        $this->db->select('
            A.store_id,
            B.name
        ');
        $this->db->from('product_availability_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->where('A.product_id', $product_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function insertProductAvailability($data){
        $this->db->trans_start();
        $this->db->insert('product_availability_tb', $data);
        $this->db->trans_complete();
    }

    public function getCommitedDate($id){
        $this->db->select('commited_delivery_date');
        $this->db->from('order_information_tb');
        $this->db->where('id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getWindowTime($store_id){
        $this->db->select('start_time, end_Time');
        $this->db->from('store_windows_time');
        $this->db->where('store_id', $store_id);

        $query = $this->db->get();
        return $query->row();
    }

    /* End */

    public function insertSiTb($data, $table_name){
        $this->db->insert_batch($table_name, $data);
    }

    public function getOrderMSI($search){
        $this->db->select('
            A.si,
            A.order_id,
            A.store,
            B.order_placement_date,
            B.requested_delivery_date,
            B.commited_delivery_date,

        ');
        $this->db->from('multim_si_tb A');
        $this->db->join('order_information_tb B', 'B.id = A.order_id', 'inner');
        $this->db->where('B.payment_status_id', 1); // Payment Status 1 for unpaid
        $this->db->where('B.status_id', 8);

        if($search){
            $this->db->where('A.si', $search);
        }

        $this->db->group_by('A.si');

        $query = $this->db->get();
        return $query->result();
    }

    public function insertPayBillPaymentTb($data){
        $this->db->insert_batch('pay_bill_payment_tb', $data);
    }

    public function getMultiMSiPdf($order_id){
        $this->db->select('*');
        $this->db->from('multim_si_tb');
        $this->db->where('order_id', $order_id);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function getMultiMSiDetailsForPdf($order_id){
        $this->db->select('order_id, store');
        $this->db->from('multim_si_tb');
        $this->db->where('order_id', $order_id);

        $query = $this->db->get();
        return $query->row();
    }
    
    public function filename_factory_prefix($order_id, $si_type){

        $this->db->select('C.company_code, B.category_name')
                 ->from('order_information_tb A')
                 ->join('category_tb B', 'B.category_id = A.order_type_id', 'left')
                 ->join($this->newteishop->database.'.store_tb C', 'C.store_id = A.store_id', 'left')
                 ->where('A.id', $order_id);

        $query = $this->db->get();
        $prefix = $query->row();



        $filename_prefix = trim($prefix->company_code).'_'.$prefix->category_name;

        $filename_prefix = $si_type ? strtoupper($si_type).'-SI_'.$filename_prefix : $filename_prefix;

        return $filename_prefix;
    }

    public function get_delivery_schedule($store_id){

        $this->db->select('*');
        $this->db->from('store_schedule');
        $this->db->where_in('store_id', $store_id);
        $query = $this->db->get();
        return $query->result();
    }


    public function getFranchiseType($order_id){
        $this->db->select('B.franchise_type_id');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->where('A.id', $order_id);

        $query = $this->db->get();
        return $query->row();

    }

    public function getFranchiseTypeByStoreId($store_id){
        $this->newteishop->select('franchise_type_id');
        $this->newteishop->from('store_tb');
        $this->newteishop->where_in('store_id', $store_id);
        $this->newteishop->where('franchise_type_id', '2');



        $query = $this->newteishop->get();
        return $query->result();
    }

}
