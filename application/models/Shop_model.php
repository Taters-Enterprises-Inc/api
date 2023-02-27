<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shop_model extends CI_Model 
{
    
    public function __construct(){
		$this->db =  $this->load->database('default', TRUE, TRUE);
        $this->bscDB = $this->load->database('bsc', TRUE, TRUE);
    }
    
    public function getUserInboxHistoryCount($type, $id, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('notifications A');
        $this->db->join('notification_events B', 'B.id = A.notification_event_id');
        $this->db->join($this->bscDB->database.'.customer_survey_responses C', 'C.id = B.customer_survey_response_id', 'left');
        $this->db->join('transaction_tb D', 'D.id = C.transaction_id', 'left');
        $this->db->join('catering_transaction_tb E', 'E.id = C.catering_transaction_id', 'left');
        $this->db->or_where('B.notification_event_type_id', 4);
        $this->db->or_where('B.notification_event_type_id', 5);
        $this->db->or_where('B.notification_event_type_id', 6);

        
        if ($type == 'mobile') {
            $this->db->where('A.mobile_user_to_notify', $id);

        } else if($type == 'facebook') {
            $this->db->where('A.fb_user_to_notify', $id);
        }

            
        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.text', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getUserInboxHistory($type, $id, $row_no, $row_per_page, $order_by,  $order, $search){

        $this->db->select('
            A.id,
            A.dateadded,
            B.notification_event_type_id,
            B.text,
            C.invoice_no,
            C.hash as survey_hash,
            D.hash_key as transaction_hash,
            E.hash_key as catering_transaction_hash,

            F.title,
            F.body,
            F.closing,
            F.closing_salutation,
            F.image_title,
            F.image_url,
            F.internal_link_title,
            F.internal_link_url,
            F.message_from,
            F.email,
            F.contact_number,
        ');

        $this->db->from('notifications A');
        $this->db->join('notification_events B', 'B.id = A.notification_event_id');
        $this->db->join($this->bscDB->database.'.customer_survey_responses C', 'C.id = B.customer_survey_response_id', 'left');
        $this->db->join('transaction_tb D', 'D.id = B.transaction_tb_id', 'left');
        $this->db->join('catering_transaction_tb E', 'E.id = B.catering_transaction_tb_id', 'left');

        $this->db->join('notification_messages F', 'F.id = B.notification_message_id', 'left');

        $this->db->or_where('B.notification_event_type_id', 4);

        if ($type == 'mobile') {
            $this->db->where('A.mobile_user_to_notify', $id);

        } else if($type == 'facebook') {
            $this->db->where('A.fb_user_to_notify', $id);
        }
        
        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.text', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_store_id_by_hash_key($hash_key){
        $this->db->select('store');
        $this->db->where('hash_key', $hash_key);
        $query = $this->db->get('transaction_tb');
        $data = $query->row();
        return $data;
    }


    public function get_logon_type($hash_key){
        $this->db->select('logon_type');
        $this->db->from('transaction_tb');
        $this->db->where('hash_key', $hash_key);
        $query_logon_type = $this->db->get();
        return $query_logon_type->row();
    }
    
    public function getUserOrderHistoryCount($type, $id, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'A.client_id = B.id' ,'left');
        $this->db->join('raffle_ss_registration_tb C', 'A.id = C.trans_id','left');
        $this->db->join('raffle_coupon_tb D', 'C.raffle_coupon_id = D.id' ,'left');
        $this->db->join($this->bscDB->database.'.customer_survey_responses E', 'E.transaction_id = A.id', 'left');
    
        
        if ($type == 'mobile') {
            $this->db->where('B.mobile_user_id', $id);

        } else if($type == 'facebook') {
            $this->db->where('B.fb_user_id', $id);
        }

            
        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getUserOrderHistory($type, $id, $row_no, $row_per_page, $order_by,  $order, $search){

        $this->db->select('
            A.id,
            A.dateadded,
            A.status,
            A.tracking_no,
            A.purchase_amount,
            A.distance_price,
            A.cod_fee,
            C.generated_raffle_code,
            C.application_status,
            A.hash_key,
            E.hash as survey_hash,
        ');

        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'A.client_id = B.id' ,'left');
        $this->db->join('raffle_ss_registration_tb C', 'A.id = C.trans_id','left');
        $this->db->join('raffle_coupon_tb D', 'C.raffle_coupon_id = D.id' ,'left');
        $this->db->join($this->bscDB->database.'.customer_survey_responses E', 'E.transaction_id = A.id', 'left');


        if ($type == 'mobile') {
            $this->db->where('B.mobile_user_id', $id);

        } else if($type == 'facebook') {
            $this->db->where('B.fb_user_id', $id);
        }
        
        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.fname', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    function upload_payment($data,$file_name,$tracking_no,$transaction_id)
    { 
        date_default_timezone_set('Asia/Manila');
        $upload_time = date('Y-m-d H:i:s');
        $file_name = $data['file_name'];

        $this->db->set('payment_proof', $file_name);
        $this->db->set('status', 2);
        $this->db->set('upload_date', $upload_time);
        $this->db->where("tracking_no", $tracking_no);
        $this->db->update("transaction_tb");
        // return ($this->db->affected_rows() != 1) ? false : true;
        $return_data['upload_status'] = ($this->db->affected_rows() != 1) ? false : true;

        if($return_data['upload_status'] > 0){
            
            $client_query = $this->db->select('client_id')
                ->get_where('transaction_tb', array('id' => $transaction_id))
                ->result();
            $return_data['client_data'] = $client_query[0];
        }
        return $return_data;
    }

   
    public function get_facebook_details($id){
        $this->db->select("*");
        $this->db->from('fb_users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function get_mobile_details($id){
        $this->db->select("*");
        $this->db->from('mobile_users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function view_order($hash_key)
    {   
        $this->db->select('hash_key');
        $this->db->from('transaction_tb');
        $this->db->where('hash_key', $hash_key);
        $check_hash = $this->db->get();
        $result = $check_hash->result();

        if(!empty($result)){
            $table = "client_tb A";
            $select_column = array(
                "A.fb_user_id", 
                "A.mobile_user_id",
                "A.fname", 
                "A.lname", 
                "A.email","A.address",
                "A.contact_number",
                "B.id", 
                "B.tracking_no",
                "B.purchase_amount",
                "B.distance_price",
                "B.cod_fee",
                "A.moh",
                "A.payops",
                "B.remarks", 
                "B.status",
                "B.dateadded",
                "B.hash_key",
                "B.store",
                "B.invoice_num",
                "B.reseller_id",
                "B.reseller_discount",
                "B.discount",
                "B.voucher_id",
                "B.table_number",
                "Z.name AS store_name",
                "Z.address AS store_address",
                "Z.contact_number AS store_contact",
                "Z.contact_person AS store_person",
                "Z.email AS store_email",
                "A.add_name",
                "A.add_contact",
                "A.add_address",
                "V.discount_value",
                "V.voucher_code",
                "B.giftcard_discount",
                "B.giftcard_number",
                "E.name AS discount_name",
                "E.percentage AS discount_percentage"
            );
            $join_A = "A.id = B.client_id";
            $this->db->select($select_column);  
            $this->db->from($table);
            $this->db->join('transaction_tb B', $join_A ,'left');
            $this->db->join('store_tb Z', 'Z.store_id = B.store' ,'left');
            $this->db->join('voucher_logs_tb V', 'V.transaction_id = B.id' ,'left');
            $this->db->join('discount_users D', 'D.id = B.discount_user_id' ,'left');
            $this->db->join('discount E', 'E.id = D.discount_id' ,'left');
            $this->db->where('B.hash_key', $hash_key);
            $query_info = $this->db->get();
            $info = $query_info->result();

            // $fb_table = "fb_users F";
            // $select_column_F = array("F.first_name","F.last_name","F.email","C.address", "C.contact_number","B.logon_type","B.id", "B.tracking_no","B.purchase_amount","B.distance_price","B.cod_fee","C.moh","C.payops","B.remarks", "B.status","B.dateadded","B.hash_key","B.store", "B.invoice_num","B.reseller_id","B.reseller_discount","B.discount","B.voucher_id","Z.name AS store_name","Z.address AS store_address","Z.contact_number AS store_contact","Z.contact_person AS store_person","Z.email AS store_email","C.add_address","V.discount_value","V.voucher_code");
            // $join_F = "F.id = B.client_id";
            // $this->db->select($select_column_F);  
            // $this->db->from($fb_table);
            // $this->db->join('transaction_tb B', $join_F ,'left');
            // $this->db->join('store_tb Z', 'Z.store_id = B.store' ,'left');
            // $this->db->join('fb_client_tb C', 'C.hash_key = B.hash_key' ,'left');
            // $this->db->join('voucher_logs_tb V', 'V.transaction_id = B.id' ,'left');
            // $this->db->where('B.hash_key', $hash_key);
            // $query_fb_info = $this->db->get();
            // $fb_info = $query_fb_info->result();

            //jepoy addon for tag on view cart
            $select_col = array("D.name AS deal_name","D.description AS deal_description","D.promo_discount_percentage","O.product_id","O.combination_id","O.type","O.quantity","O.status","O.remarks","O.promo_id","O.promo_price","O.sku","O.sku_id","O.price AS calc_price","O.product_price","P.product_image","P.name","P.description","P.delivery_details","P.uom","P.add_details","P.add_remarks","P.product_hash","P.note","P.product_code","O.product_label","O.addon_drink","O.addon_flav","O.addon_butter","O.addon_base_product","U.name AS freebie_prod_name");
            $this->db->from('products_tb P');
            $this->db->select($select_col);
            $this->db->join('order_items O', 'P.id = O.product_id' ,'left');
            $this->db->join('transaction_tb T', 'O.transaction_id = T.id' ,'left');
            $this->db->join('freebie_products_tb U', 'U.id = O.product_id' ,'left');
            $this->db->join('dotcom_deals_tb D', 'D.id = O.deal_id' ,'left');
            $this->db->where('T.hash_key', $hash_key);
            $this->db->order_by('type','DESC');
            $query_orders = $this->db->get();
            $orders = $query_orders->result();

            $select_col = array("D.id" ,"D.name","D.promo_discount_percentage", 'D.product_image', 'O.quantity', 'O.remarks', "O.price");
			$this->db->from('dotcom_deals_tb D');
            $this->db->select($select_col);
            $this->db->join('deals_order_items O', 'D.id = O.deal_id' ,'left');
            $this->db->join('transaction_tb T', 'O.transaction_id = T.id' ,'left');
            $this->db->where('T.hash_key', $hash_key);
            $query_deals = $this->db->get();
            $deals = $query_deals->result();
			

            $this->db->from('personnel_tb');
            $this->db->select('name,contact_number');
            // $this->db->where('reference_code', ($result[0]->logon_type == 'facebook') ? $fb_info[0]->moh : $info[0]->moh);
            // $this->db->where('assigned_store', ($result[0]->logon_type == 'facebook') ? $fb_info[0]->store: $info[0]->store);
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
            // $join_data['fb_info'] = $fb_info[0];
            $join_data['order_details'] = $orders;
			$join_data['deals_details'] = $deals;
            $join_data['personnel'] = $personnel[0];
            $join_data['bank'] = $bank[0];
            
            // print_r($join_data);
            return $join_data;
        }else{
            return $join_data = array();
        }
    }

    function fetch_product_name($id)
    {
        $this->db->select('name');
        // $this->db->where('product_id', $id);
        $this->db->where('id', $id);
        $query = $this->db->get('products_tb');
        return $query->result();
    }
	
    function insert_giftcard_user($data){
        $this->db->insert('giftcard_users',$data);
        return $this->db->affected_rows() ? 1 : 0;
    }
    
    function get_suggested_product($prod_id)
    {
        if(isset($_SESSION['cache_data'])){
            $region = $this->db
                    ->select('product_id')
                    ->get_where('region_da_log', array('region_id' => $_SESSION['cache_data']['region_id'],
                        'status' => 1))
                    ->result();
            foreach ($region as $row) {
                $disable_region_items[] = $row->product_id;
            }
        }else{
            $disable_region_items = array();
        }
        $disable_products = empty($disable_region_items) ? 0 : $disable_region_items;

        $product_ids = $this->db->select('paired_products_id')
            ->get_where('suggestive_selling_tb', array('product_id' => $prod_id))
            ->result();

        foreach ($product_ids as $val) {
            $paired_ids[] = $val->paired_products_id;
        }
        $suggested_product_ids = empty($paired_ids) ? 0 : $paired_ids;

        $this->db->select('id,product_image AS image,name,description,price,uom,product_hash AS hash');
        $this->db->where('status', 1);
        $this->db->where_in('id', $suggested_product_ids);
        $this->db->where_not_in('id', $disable_products);

        $query = $this->db->get('products_tb');
        return $query->result();
    }

    function fetch_variants_details($id)
    {
        $this->db->select('name');
        $this->db->where('id', $id);
        $query = $this->db->get('product_variant_options_tb');
        return $query->row();
    }

    function fetch_product_sku($variants)
    {
        $this->db->select("MAX(B.price) AS price,A.sku_id,MAX(B.sku) AS sku,MAX(B.product_id) AS product_id");
        $this->db->from('product_variant_option_combinations_tb A');
        $this->db->join('product_skus_tb B', 'A.sku_id = B.id');
        if(count($variants) === 1){
            $this->db->where('A.product_variant_option_id', $variants[0]);
        }else{
            $this->db->where_in('A.product_variant_option_id', $variants);
            $this->db->having('COUNT(A.sku_id) > 1');
        }
        $this->db->group_by('A.sku_id');
        $query = $this->db->get();
        return $query->row();
    }

    function youtube_video_ads($prod_id){
        $this->db->select('*');
        $this->db->from('youtube_video_ads');
        $this->db->where('product_id',$prod_id);
        $query = $this->db->get();
        return $query->result();
    }
	
    function get_product_addons_join($prod_id){
        $this->db->select('*');
        $this->db->from('product_with_addons');
        $this->db->where('product_id',$prod_id);
        $this->db->join('products_tb','products_tb.id = product_with_addons.addon_product_id');
        $query = $this->db->get();
        return $query->result();
    }

    function get_product_addons($prod_id){
        $this->db->select('*');
        $this->db->where('product_id',$prod_id);
        $query = $this->db->get('product_with_addons');
        return $query->result();
    }
	
    function getProductVariantsSize($product_id){
        $this->db->select("B.id,B.name");
        $this->db->from('product_variants_tb A');
        $this->db->join('product_variant_options_tb B', 'B.product_variant_id = A.id','left');
        $this->db->where('A.product_id', $product_id);
        $this->db->where('A.name', 'size');
        $this->db->where('B.status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    function getProductVariantsFlavor($product_id){
        $this->db->select("
            B.id, 
            B.name, 
            B.product_variant_id,
            A.name as parent_name
        ");
        $this->db->from('product_variants_tb A');
        $this->db->join('product_variant_options_tb B', 'B.product_variant_id = A.id','left');
        $this->db->where('A.product_id', $product_id);
        $this->db->where('A.name !=', 'size');
        $this->db->where('B.status', 1);
        $query = $this->db->get();
        return $query->result();
    }
    
    
		
	public function get_product($hash)
    {
        $this->db->select('
			id, 
            product_hash,
			product_image, 
			name, 
			description, 
			add_details, 
			delivery_details, 
			price, 
			category, 
			num_flavor, 
			add_remarks, 
			note, 
			to_gc_value
		');
        $this->db->from('products_tb');
		$this->db->where('product_hash', $hash);

        $query = $this->db->get();
        return $query->row();
    }

    public function get_product_by_id($id)
    {
        $this->db->select('
			id, 
			product_image, 
			name, 
			description, 
			add_details, 
			delivery_details, 
			price, 
			category, 
			num_flavor, 
			add_remarks, 
			note, 
			to_gc_value
		');
        $this->db->from('products_tb');
		$this->db->where('id', $id);

        $query = $this->db->get();
        return $query->row();
    }
	
    function get_details($id)
    {

        $this->db->select("*");
        $this->db->from('products_tb');
        $this->db->where('id', $id);

        $query = $this->db->get();
        return $query->result();
    }

    function fetch_category_products($region,$category,$sort_id,$min,$max,$name)
    {  
        if($region!=0){
            $region = $this->db
                ->select('product_id')
                ->get_where('region_da_log', array('region_id' => $region,'status' => 1))
                ->result();
            foreach ($region as $row) {
                $disable_region_items[] = $row->product_id;
            }
            $to_disable = empty($disable_region_items) ? 0 : $disable_region_items;
        }

        $store_menu_type = (isset($_SESSION['cache_data']['store_menu_type'])) ? $_SESSION['cache_data']
        ['store_menu_type'] : '';

        // use if store_type of some stores shown is popcorn
        if(isset($store_menu_type)){
            $category_by_menu_type = $this->db->select('category')
                ->get_where('store_menu_type_tb ', array('menu_type' => $store_menu_type))
                ->result();

            foreach ($category_by_menu_type as $val) {
                $product_categories[] = $val->category;
            }
            $shown_categories = empty($product_categories) ? 0 : $product_categories;
        }
		
        $product_category_tb = ($store_menu_type == 2) ? 'C.product_id,C.category_id' : '';

        $this->db->select("
            A.sequence,
            A.id AS category_id,
            A.category_name,
            A.description AS category_details,
            A.img AS category_image,
            A.card_bgcolor AS category_background,
            A.visibility AS visibility,
            B.id AS product_id,
            B.name AS product_name,
            B.product_image,
            B.description AS product_description,
            B.price AS product_price,
            B.product_hash,
            ".$product_category_tb."
        ");

        $this->db->from('category_tb A');
        if($store_menu_type != '2') {
            $this->db->join('products_tb B', 'B.category = A.id','left');
            $this->db->join('product_category_tb C', 'C.product_id = B.id');
            $this->db->group_by('C.product_id'); 
        } else {
            $this->db->join('product_category_tb C', 'C.category_id = A.id');
            $this->db->join('products_tb B', 'B.id = C.product_id');
        }

        $this->db->where('A.status', 1);
        $this->db->where('B.status', 1);

        if($store_menu_type == '2') {
            $this->db->where('C.category_id =', 14);
        }

        if($region != 0 && $to_disable != 0){
            $this->db->where_in('B.id', $to_disable);
            
            if(!empty($store_menu_type)){
                $this->db->where_in('A.id', $shown_categories);
            }

        }
        
        $this->db->order_by("A.sequence", "asc");
        $this->db->order_by("B.name", "asc"); 

        $query = $this->db->get();
        $query_data = $query->result();

        $return_data = array();

        if($store_menu_type != '2'){
            if($category != 14 || $category == 'prod_category_page') {
                foreach ($query_data as $key => $val) {
                    
                    $redeem_data = $this->session->redeem_data;
                    $deal_products_promo_exclude = $redeem_data['deal_products_promo_exclude'];
                    $deal_products_promo_include = $redeem_data['deal_products_promo_include'];
                    $promo_discount_percentage = null;

                    if($deal_products_promo_exclude){
                        $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];

                        foreach($deal_products_promo_exclude as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = null;
                                break;
                            }
                        }
                    }
                    
                    if($deal_products_promo_include){
                        foreach($deal_products_promo_include as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];
                                break;
                            }
                        }
                    }


                    $return_data[$val->sequence]['category_id']          = $val->category_id;
                    $return_data[$val->sequence]['category_name']        = $val->category_name;
                    $return_data[$val->sequence]['category_details']     = $val->category_details;
                    $return_data[$val->sequence]['category_image']       = $val->category_image;
                    $return_data[$val->sequence]['category_background']  = $val->category_background;
                    $return_data[$val->sequence]['visibility']  = $val->visibility;
                    $return_data[$val->sequence]['category_products'][] = array(
                        'id' => $val->product_id,
                        'name' => $val->product_name,
                        'image' => $val->product_image,
                        'description' => $val->product_description,
                        'price' => $val->product_price,
                        'promo_discount_percentage' => $promo_discount_percentage,
                        'hash' => $val->product_hash,
                    );
                }
            }

            //use if store_type of some stores shown is popcorn
            else if(!empty($store_menu_type)) {
                foreach ($query_data as $key => $val) {
                    
                    $redeem_data = $this->session->redeem_data;
                    $deal_products_promo_exclude = $redeem_data['deal_products_promo_exclude'];
                    $deal_products_promo_include = $redeem_data['deal_products_promo_include'];
                    $promo_discount_percentage = null;

                    if($deal_products_promo_exclude){
                        $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];

                        foreach($deal_products_promo_exclude as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = null;
                                break;
                            }
                        }

                    }
                    
                    if($deal_products_promo_include){
                        foreach($deal_products_promo_include as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];
                                break;
                            }
                        }
                    }


                    $return_data[$val->sequence]['category_id']          = $val->category_id;
                    $return_data[$val->sequence]['category_name']        = $val->category_name;
                    $return_data[$val->sequence]['category_details']     = $val->category_details;
                    $return_data[$val->sequence]['category_image']       = $val->category_image;
                    $return_data[$val->sequence]['category_background']  = $val->category_background;
                    $return_data[$val->sequence]['visibility']  = $val->visibility;
                    $return_data[$val->sequence]['category_products'][] = array(
                        'id' => $val->product_id,
                        'name' => $val->product_name,
                        'image' => $val->product_image,
                        'description' => $val->product_description,
                        'price' => $val->product_price,
                        'promo_discount_percentage' => $promo_discount_percentage,
                        'hash' => $val->product_hash,
                    );
                }
            }

            //for showing all products (shop grid)
            else if ($category == 14){
                foreach ($query_data as $key => $value) {

                    $redeem_data = $this->session->redeem_data;
                    $deal_products_promo_exclude = $redeem_data['deal_products_promo_exclude'];
                    $deal_products_promo_include = $redeem_data['deal_products_promo_include'];
                    $promo_discount_percentage = null;

                    if($deal_products_promo_exclude){
                        $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];

                        foreach($deal_products_promo_exclude as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = null;
                                break;
                            }
                        }

                    }

                    if($deal_products_promo_include){
                        foreach($deal_products_promo_include as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];
                                break;
                            }
                        }
                    }


                    $return_data[]['category_products'][] = array(
                        'category_id'      => $value->category_id,
                        'category_name'    => $value->category_name,
                        'category_details' => $value->category_details,
                        'category_image'   => $value->category_image,
                        'id'     => $value->product_id,
                        'name'   => $value->product_name,
                        'image'  => $value->product_image,
                        'description'  => $value->product_description,
                        'price'  => $value->product_price,
                        'promo_discount_percentage' => $promo_discount_percentage,
                        'hash'  => $value->product_hash,
                    );

                }
            }
        }
        else {

            if($category != 14 || $category == 'prod_category_page') {
                foreach ($query_data as $key => $val) {

                    $redeem_data = $this->session->redeem_data;
                    $deal_products_promo_exclude = $redeem_data['deal_products_promo_exclude'];
                    $deal_products_promo_include = $redeem_data['deal_products_promo_include'];
                    $promo_discount_percentage = null;

                    if($deal_products_promo_exclude){
                        $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];

                        foreach($deal_products_promo_exclude as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = null;
                                break;
                            }
                        }
                    }
                    
                    if($deal_products_promo_include){
                        foreach($deal_products_promo_include as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];
                                break;
                            }
                        }
                    }

                    $return_data[0]['category_id'] = $val->category_id;
                    $return_data[0]['category_name'] = $val->category_name;
                    $return_data[0]['category_details'] = $val->category_details;
                    $return_data[0]['category_image'] = $val->category_image;
                    $return_data[0]['category_background'] = $val->category_background;
                    $return_data[0]['visibility'] = $val->visibility;
                    $return_data[0]['category_products'][] = array(
                        'id' => $val->product_id,
                        'name' => $val->product_name,
                        'image' => $val->product_image,
                        'description' => $val->product_description,
                        'promo_discount_percentage' => $promo_discount_percentage,
                        'price' => $val->product_price,
                        'hash' => $val->product_hash,
                    );
                }
            }
            //for showing all products (shop grid)
            else if ($category == 14){
                foreach ($query_data as $key => $value) {
                    
                    $redeem_data = $this->session->redeem_data;
                    $deal_products_promo_exclude = $redeem_data['deal_products_promo_exclude'];
                    $deal_products_promo_include = $redeem_data['deal_products_promo_include'];
                    $promo_discount_percentage = null;

                    if($deal_products_promo_exclude){
                        $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];

                        foreach($deal_products_promo_exclude as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = null;
                                break;
                            }
                        }
                    }
                    
                    if($deal_products_promo_include){
                        foreach($deal_products_promo_include as $value){
                            if($value->product_id === $val->product_id){
                                $promo_discount_percentage = (float)$redeem_data['promo_discount_percentage'];
                                break;
                            }
                        }
                    }

                    $return_data[]['category_products'][] = array(
                        'category_id'      => $value->category_id,
                        'category_name'    => $value->category_name,
                        'category_details' => $value->category_details,
                        'category_image'   => $value->category_image,
                        'id'     => $value->product_id,
                        'name'   => $value->product_name,
                        'image'  => $value->product_image,
                        'description'  => $value->product_description,
                        'price'  => $value->product_price,
                        'promo_discount_percentage' => $promo_discount_percentage,
                        'hash'  => $value->product_hash,
                    );

                }
            }
        }

        // return re-arrange key values of array and convert to object
        $reindex = array_values($return_data);
        return $reindex;
    }
}