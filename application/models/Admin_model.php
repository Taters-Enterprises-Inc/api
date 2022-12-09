<?php defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
{
	public function __construct()
	{

		$this->bsc_db = $this->load->database('bsc', TRUE, TRUE);
	}

	function getSurvey($survey_id)
	{
		$this->bsc_db->select('
            A.id,
            A.dateadded,
            A.order_date,
            A.status,
            B.name as store_name,
            C.first_name,
            C.last_name,
            A.order_no,
            D.tracking_no as snackshop_tracking_no,
            E.tracking_no as catering_tracking_no,
            F.redeem_code as popclub_redeem_code,
            G.name as order_type
        ');
		$this->bsc_db->from('customer_survey_responses A');
		$this->bsc_db->join($this->db->database . '.store_tb B', 'B.store_id = A.store_id');
		$this->bsc_db->join('user_profile C', 'C.user_id = A.user_id', 'left');
		$this->bsc_db->join($this->db->database . '.transaction_tb D', 'D.id = A.transaction_id', 'left');
		$this->bsc_db->join($this->db->database . '.catering_transaction_tb E', 'E.id = A.catering_transaction_id', 'left');
		$this->bsc_db->join($this->db->database . '.deals_redeems_tb F', 'F.id = A.deals_redeem_id', 'left');
		$this->bsc_db->join('customer_survey_response_order_types G', 'G.id = A.customer_survey_response_order_type_id');

		$this->bsc_db->where('A.id', $survey_id);

		$query_customer_survey_response = $this->bsc_db->get();
		$customer_survey_response = $query_customer_survey_response->row();

		$this->bsc_db->select('
            A.id, 
            A.other_text,
            A.customer_survey_response_id,
            B.description as question,
            C.text as answer,
        ');

		$this->bsc_db->from('customer_survey_response_answers A');
		$this->bsc_db->join('survey_questions B', 'B.id = A.survey_question_id', 'left');
		$this->bsc_db->join('survey_question_offered_answers C', 'C.id = A.survey_question_offered_answer_id', 'left');

		$this->bsc_db->where('A.customer_survey_response_id', $customer_survey_response->id);

		$query_customer_survey_response_answers = $this->bsc_db->get();
		$customer_survey_response_answers = $query_customer_survey_response_answers->result();

		$data = $customer_survey_response;

		$data->answers = $customer_survey_response_answers;

		return $data;
	}

	public function getSurveysCount($status, $search)
	{
		$this->bsc_db->select('count(*) as all_count');

		$this->bsc_db->from('customer_survey_responses A');
		$this->bsc_db->join($this->db->database . '.store_tb B', 'B.store_id = A.store_id');
		$this->bsc_db->join('user_profile C', 'C.user_id = A.user_id', 'left');
		$this->bsc_db->join($this->db->database . '.transaction_tb D', 'D.id = A.transaction_id', 'left');
		$this->bsc_db->join($this->db->database . '.catering_transaction_tb E', 'E.id = A.catering_transaction_id', 'left');
		$this->bsc_db->join($this->db->database . '.deals_redeems_tb F', 'F.id = A.deals_redeem_id', 'left');
		$this->bsc_db->join('customer_survey_response_order_types G', 'G.id = A.customer_survey_response_order_type_id');

		if ($status)
			$this->bsc_db->where('A.status', $status);

		if ($search) {
			$this->bsc_db->group_start();
			$this->bsc_db->or_like('A.order_no', $search);
			$this->bsc_db->or_like('B.name', $search);
			$this->bsc_db->or_like('C.first_name', $search);
			$this->bsc_db->or_like('C.last_name', $search);
			$this->bsc_db->or_like('D.tracking_no', $search);
			$this->bsc_db->or_like('E.tracking_no', $search);
			$this->bsc_db->or_like('F.redeem_code', $search);
			$this->bsc_db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
			$this->bsc_db->group_end();
		}

		$query = $this->bsc_db->get();
		return $query->row()->all_count;
	}

	public function getSurveys($row_no, $row_per_page, $status, $order_by,  $order, $search)
	{

		$this->bsc_db->select("
            A.id,
            A.dateadded,
            A.order_date,
            A.status,
            B.name as store_name,
            C.first_name,
            C.last_name,
            A.order_no,
            D.tracking_no as snackshop_tracking_no,
            E.tracking_no as catering_tracking_no,
            F.redeem_code as popclub_redeem_code,
            G.name as order_type
        ");

		$this->bsc_db->from('customer_survey_responses A');
		$this->bsc_db->join($this->db->database . '.store_tb B', 'B.store_id = A.store_id');
		$this->bsc_db->join('user_profile C', 'C.user_id = A.user_id', 'left');
		$this->bsc_db->join($this->db->database . '.transaction_tb D', 'D.id = A.transaction_id', 'left');
		$this->bsc_db->join($this->db->database . '.catering_transaction_tb E', 'E.id = A.catering_transaction_id', 'left');
		$this->bsc_db->join($this->db->database . '.deals_redeems_tb F', 'F.id = A.deals_redeem_id', 'left');
		$this->bsc_db->join('customer_survey_response_order_types G', 'G.id = A.customer_survey_response_order_type_id');

		if ($status)
			$this->bsc_db->where('A.status', $status);

		if ($search) {
			$this->bsc_db->group_start();
			$this->bsc_db->or_like('A.order_no', $search);
			$this->bsc_db->or_like('B.name', $search);
			$this->bsc_db->or_like('C.first_name', $search);
			$this->bsc_db->or_like('C.last_name', $search);
			$this->bsc_db->or_like('D.tracking_no', $search);
			$this->bsc_db->or_like('E.tracking_no', $search);
			$this->bsc_db->or_like('F.redeem_code', $search);
			$this->bsc_db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
			$this->bsc_db->group_end();
		}

		$this->bsc_db->limit($row_per_page, $row_no);
		$this->bsc_db->order_by($order_by, $order);

		$query = $this->bsc_db->get();
		return $query->result();
	}

	public function changeStatusSurveyVerification($survey_verification_id, $status)
	{
		$this->bsc_db->set('status', (int) $status);
		$this->bsc_db->where('id', $survey_verification_id);
		$this->bsc_db->update("customer_survey_responses");
	}

	function updateSettingStoreOperatingHours(
		$store_id,
		$available_start_time,
		$available_end_time
	) {
		$this->db->set('available_start_time', $available_start_time);
		$this->db->set('available_end_time', $available_end_time);
		$this->db->where("store_id", $store_id);
		$this->db->update("store_tb");
	}

	function getStore($store_id)
	{
		$this->db->select('
            A.store_id,
            A.name,
            A.available_start_time,
            A.available_end_time,
            B.name as menu_name,
        ');

		$this->db->from('store_tb A');
		$this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
		$this->db->where('A.store_id', $store_id);

		$query = $this->db->get();
		return $query->row();
	}

	function updateSettingStore($store_id, $name_of_field_status, $status)
	{
		switch ($name_of_field_status) {
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
			case 'branch_status':
				$this->db->set('branch_status', $status);
				break;
		}
		$this->db->where("store_id", $store_id);
		$this->db->update("store_tb");
	}

	function getSettingStoresCount($search, $store)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('store_tb A');
		$this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');

		if (!empty($store))
			$this->db->where_in('A.store_id', $store);

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.name', $search);
			$this->db->or_like('B.name', $search);
			$this->db->group_end();
		}


		$query = $this->db->get();
		return $query->row()->all_count;
	}

	function getSettingStores($row_no, $row_per_page, $order_by, $order, $search, $store)
	{
		$this->db->select('
            A.store_id,
            A.name,
            A.status,
            A.catering_status,
            A.popclub_walk_in_status,
            A.popclub_online_delivery_status,
            A.branch_status,
            A.available_start_time,
            A.available_end_time,
            B.name as menu_name,
        ');

		$this->db->from('store_tb A');
		$this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');

		if (!empty($store))
			$this->db->where_in('A.store_id', $store);

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.name', $search);
			$this->db->or_like('B.name', $search);
			$this->db->group_end();
		}

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		return $this->db->get()->result();
	}

	public function get_order_summary($id)
	{
		$table = "client_tb A";
		$select_column = array("A.fname", "A.lname", "A.email", "A.address", "A.contact_number", "A.moh", "A.payops", "B.id", "B.tracking_no", "B.purchase_amount", "B.distance_price", "B.cod_fee", "B.table_number", "A.moh", "A.payops", "B.remarks", "B.status", "B.dateadded", "B.hash_key", "B.store", "B.invoice_num", "B.reseller_id", "B.reseller_discount", "B.discount", "Z.name AS store_name", "Z.address AS store_address", "Z.contact_number AS store_contact", "Z.contact_person AS store_person", "Z.email AS store_email", "Z.delivery_rate AS delivery_rate", "Z.moh_notes AS moh_notes", "Z.moh_setup AS moh_setup", "B.payment_proof", "A.add_name", "A.add_contact", "A.add_address", "V.discount_value", "V.voucher_code");
		$join_A = "A.id = B.client_id";
		$this->db->select($select_column);
		$this->db->from($table);
		$this->db->join('transaction_tb B', $join_A, 'left');
		$this->db->join('store_tb Z', 'Z.store_id = B.store', 'left');
		$this->db->join('voucher_logs_tb V', 'V.transaction_id = B.id', 'left');
		$this->db->where('B.id', $id);
		$query_info = $this->db->get();
		$info = $query_info->result();

		$this->db->from('products_tb P');
		$this->db->select('
            P.id,
            P.name,
            P.price,
            P.add_details,
            P.add_remarks,
            P.dateadded,
            O.transaction_id,
            O.quantity,
            O.remarks,
            O.promo_id,
            O.promo_price,
            O.product_price,
            O.product_label,
            O.addon_base_product,
            O.addon_base_product_name,
            D.alias,
            D.promo_discount_percentage
        ');
		$this->db->join('order_items O', 'P.id = O.product_id', 'left');
		$this->db->join('dotcom_deals_tb D', 'D.id = O.deal_id', 'left');
		$this->db->where('O.transaction_id', $id);
		$query_orders = $this->db->get();
		$orders = $query_orders->result();

		$this->db->select("
            A.product_price,
            A.quantity,
            A.remarks,
            B.name,
            B.alias,
            B.description,
        ");
		$this->db->from('deals_order_items A');
		$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
		$this->db->where('A.transaction_id', $id);
		$deals_query = $this->db->get();
		$deals = $deals_query->result();

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
		$join_data['order_details'] = array_merge($orders, $deals);
		$join_data['personnel'] = $personnel[0];
		$join_data['bank'] = $bank[0];

		// print_r($join_data);
		return $join_data;
	}

	public function get_deal_categories()
	{
		$this->db->select("id, name");
		$this->db->from("dotcom_deals_category");
		return $this->db->get()->result();
	}


	public function get_caters_package_categories()
	{
		$this->db->select("id, category_name as name");
		$this->db->from("catering_category_tb");
		return $this->db->get()->result();
	}

	public function get_product_categories()
	{
		$this->db->select("id, category_name as name");
		$this->db->from("category_tb");
		return $this->db->get()->result();
	}

	function getStoreProductCount($store_id, $category_id, $status, $search)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('region_da_log A');
		$this->db->join('products_tb B', 'B.id = A.product_id');
		$this->db->join('category_tb C', 'C.id = B.category');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->or_like('C.category_name', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);
		$this->db->where('A.status', $status);

		if (isset($category_id)) $this->db->where('C.id', $category_id);

		if ($status)
			$this->db->where('A.status', $status);

		$query = $this->db->get();
		return $query->row()->all_count;
	}



	function getAllCatersPackage()
	{
		$query = $this->db->get('catering_packages_tb');
		return $query->result();
	}


	function getStoreCatersPackageCount($store_id, $category_id, $status, $search)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('catering_region_da_log A');
		$this->db->join('catering_packages_tb B', 'B.id = A.product_id');
		$this->db->join('catering_category_tb C', 'C.id = B.category');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->or_like('C.category_name', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);
		$this->db->where('A.status', $status);

		if (isset($category_id)) $this->db->where('C.id', $category_id);

		if ($status)
			$this->db->where('A.status', $status);

		$query = $this->db->get();
		return $query->row()->all_count;
	}

	function getStoreCatersPackageAddonsCount($store_id, $status, $search)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('catering_package_addons_tb	 A');
		$this->db->join('catering_packages_tb B', 'B.id = A.product_id');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);
		$this->db->where('A.status', $status);

		if (isset($category_id)) $this->db->where('C.id', $category_id);

		if ($status)
			$this->db->where('A.status', $status);

		$query = $this->db->get();
		return $query->row()->all_count;
	}


	function getStoreCatersProductAddonsCount($store_id, $status, $search)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('catering_product_addons_tb	 A');
		$this->db->join('products_tb B', 'B.id = A.product_id');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);
		$this->db->where('A.status', $status);

		if (isset($category_id)) $this->db->where('C.id', $category_id);

		if ($status)
			$this->db->where('A.status', $status);

		$query = $this->db->get();
		return $query->row()->all_count;
	}

	function getStoreCatersProductAddons($row_no, $row_per_page, $store_id,  $status, $order_by, $order, $search)
	{
		$this->db->select('A.id, B.name, A.store_id, B.add_details');

		$this->db->from('catering_product_addons_tb	 A');
		$this->db->join('products_tb B', 'B.id = A.product_id');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);
		$this->db->where('A.status', $status);

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		return $this->db->get()->result();
	}

	function getStoreCatersPackageAddons($row_no, $row_per_page, $store_id,  $status, $order_by, $order, $search)
	{
		$this->db->select('A.id, B.name, A.store_id, B.add_details');
		$this->db->from('catering_package_addons_tb	 A');
		$this->db->join('catering_packages_tb B', 'B.id = A.product_id');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);
		$this->db->where('A.status', $status);

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		return $this->db->get()->result();
	}
	//Todo  get Catering
	function getStoreCatersPackages($row_no, $row_per_page, $store_id, $category_id,  $status, $order_by, $order, $search)
	{
		$this->db->select('A.id, B.name, A.store_id, B.add_details, C.category_name');
		$this->db->from('catering_region_da_log A');
		$this->db->join('catering_packages_tb B', 'B.id = A.product_id');
		$this->db->join('catering_category_tb C', 'C.id = B.category');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->or_like('C.category_name', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);
		$this->db->where('A.status', $status);

		if (isset($category_id)) $this->db->where('C.id', $category_id);

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		return $this->db->get()->result();
	}

	function getStoreProducts($row_no, $row_per_page, $store_id, $category_id,  $status, $order_by, $order, $search)
	{
		$this->db->select('A.id, B.name, A.store_id, B.add_details, C.category_name');
		$this->db->from('region_da_log A');
		$this->db->join('products_tb B', 'B.id = A.product_id');
		$this->db->join('category_tb C', 'C.id = B.category');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->or_like('C.category_name', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);
		$this->db->where('A.status', $status);

		if (isset($category_id)) $this->db->where('C.id', $category_id);

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		return $this->db->get()->result();
	}

	function getStoreDealsCount($store_id, $category_id, $status, $search)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('deals_region_da_log A');
		$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
		$this->db->join('store_tb C', 'C.store_id = A.store_id');
		$this->db->join('dotcom_deals_store_menu_tb D', 'D.store_menu_tb_id = C.store_menu_type_id AND D.deal_id = B.id');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->or_like('B.alias', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);

		if (isset($category_id)) $this->db->where('A.platform_category_id', $category_id);

		$this->db->where('A.status', $status);

		$query = $this->db->get();
		return $query->row()->all_count;
	}

	function updateStoreDeal($id, $status)
	{
		$this->db->set('status', $status);
		$this->db->where("id", $id);
		$this->db->update("deals_region_da_log");
	}
	// TODO Catering
	function updateStoreCatersPackage($id, $status)
	{
		$this->db->set('status', $status);
		$this->db->where("id", $id);
		$this->db->update("catering_region_da_log");
	}

	function updateStoreCatersProductAddon($id, $status)
	{
		$this->db->set('status', $status);
		$this->db->where("id", $id);
		$this->db->update("catering_product_addons_tb");
	}

	function updateStoreCatersPackageAddon($id, $status)
	{
		$this->db->set('status', $status);
		$this->db->where("id", $id);
		$this->db->update("catering_package_addons_tb");
	}

	function updateStoreProduct($id, $status)
	{
		$this->db->set('status', $status);
		$this->db->where("id", $id);
		$this->db->update("region_da_log");
	}

	function getStoreDeals($row_no, $row_per_page, $store_id, $category_id, $status, $order_by, $order, $search)
	{
		$this->db->select('A.id, B.alias, B.name, A.store_id');
		$this->db->from('deals_region_da_log A');
		$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
		$this->db->join('store_tb C', 'C.store_id = A.store_id');
		$this->db->join('dotcom_deals_store_menu_tb D', 'D.store_menu_tb_id = C.store_menu_type_id AND D.deal_id = B.id');

		if ($search) {
			$this->db->group_start();
			$this->db->like('B.name', $search);
			$this->db->or_like('B.alias', $search);
			$this->db->group_end();
		}

		$this->db->where('B.status', 1);
		$this->db->where('A.store_id', $store_id);

		if (isset($category_id)) $this->db->where('A.platform_category_id', $category_id);

		$this->db->where('A.status', $status);

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		return $this->db->get()->result();
	}


	function get_fname_lname_email($id)
	{
		$this->db->select('first_name,last_name,email');
		$this->db->from('fb_users');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	function get_fname_lname_email_mobile($id)
	{
		$this->db->select('first_name,last_name,email');
		$this->db->from('mobile_users');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	function updateStoreOrStatusCateringTransaction($request, $password, $transaction_id, $to_store_id, $to_status_id)
	{
		$this->db->select("password");
		$this->db->from('users');
		$this->db->where('id', 1);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			$row = $query->row();

			if (password_verify($password, $row->password)) {

				if ($request == 'change_status') {

					$this->db->set('status', $to_status_id);

					switch ($to_status_id) {
						case 1:
							$this->db->set('uploaded_contract', '');
							$this->db->set('initial_payment_proof', '');
							$this->db->set('final_payment_proof', '');
							break;
						case 4:
							$this->db->set('initial_payment_proof', '');
							$this->db->set('final_payment_proof', '');
							break;
						case 6:
							$this->db->set('final_payment_proof', '');
							break;
					}
					$this->db->where('id', $transaction_id);
					$this->db->update('catering_transaction_tb');
					return true;
				} else if ($request == 'store_transfer') {

					$this->db->set('store', $to_store_id);
					$this->db->where('id', $transaction_id);
					$this->db->update('catering_transaction_tb');
					return true;
				}
			} else {
				return "Wrong Password";
			}
		} else {
			return false;
		}
	}

	function updateStoreOrStatusSnackshopTransaction($request, $password, $transaction_id, $to_store_id, $to_status_id)
	{
		$this->db->select("password");
		$this->db->from('users');
		$this->db->where('id', 1);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			$row = $query->row();

			if (password_verify($password, $row->password)) {

				if ($request == 'change_status') {

					$this->db->set('status', $to_status_id);
					if ($to_status_id == 1) {
						$this->db->set('payment_proof', '');
					}
					$this->db->where('id', $transaction_id);
					$this->db->update('transaction_tb');
					return true;
				} else if ($request == 'store_transfer') {

					$this->db->set('store', $to_store_id);
					$this->db->where('id', $transaction_id);
					$this->db->update('transaction_tb');
					return true;
				}
			} else {
				return "Wrong Password";
			}
		} else {
			return false;
		}
	}

	function generate_shop_invoice_num($transaction_id)
	{
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
		if ($return_data['status'] == TRUE) {
			$gen = '%06d';
			$inv = sprintf($gen, $insert_id);
			$invoice_num = date("y") . '-' . $inv;
			$this->db->set('invoice_num', $invoice_num);
			$this->db->where('id', $transaction_id);
			$this->db->update('transaction_tb');
			return ($this->db->affected_rows()) ? 1 : 0;
		}
	}

	function update_shop_on_click($transaction_id, $trans_action)
	{

		$this->db->set('on_click', $trans_action);
		$this->db->where('id', $transaction_id);
		$this->db->update('transaction_tb');

		return $this->db->affected_rows() ? 1 : 0;
	}

	function generate_catering_invoice_num($id)
	{
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
		if ($return_data['status'] == TRUE) {
			$gen = '%06d';
			$inv = sprintf($gen, $insert_id);
			$invoice_num = date("y") . '-' . $inv;
			$this->db->set('invoice_num', $invoice_num);
			$this->db->where('id', $id);
			$this->db->update('catering_transaction_tb');
			return ($this->db->affected_rows()) ? 1 : 0;
		}
	}

	function update_catering_on_click($id, $trans_action)
	{

		$this->db->set('on_click', $trans_action);
		$this->db->where('id', $id);
		$this->db->update('catering_transaction_tb');

		return $this->db->affected_rows() ? 1 : 0;
		// return $form_data;  
	}

	function update_catering_status($id, $action)
	{
		$this->db->set('status', $action);
		$this->db->where('id', $id);
		$this->db->update('catering_transaction_tb');
		return ($this->db->affected_rows()) ? 1 : 0;
	}

	function update_shop_status($transaction_id, $status)
	{
		if ($status == 3) {
			$raffle_code = "RC" . substr(md5(uniqid(mt_rand(), true)), 0, 6);
			$this->db->set('application_status', 1);
			$this->db->set('generated_raffle_code', $raffle_code);
			$this->db->where('trans_id', $transaction_id);
			$this->db->update('raffle_ss_registration_tb');
		}

		if ($status == 6) {
			$this->db->select('*');
			$this->db->from('giftcard_users');
			$this->db->where('trans_id', $transaction_id);
			$result = $this->db->get()->result();

			if (!empty($result)) {
				foreach ($result as $key => $res) {
					$giftcard_number = "GC" . substr(md5(uniqid(mt_rand(), true)), 0, 6);
					$this->db->set('status', 1);
					$this->db->set('giftcard_number', $giftcard_number);
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

		if ($query->num_rows() > 0) {
			return "Invalid Reference number";
		} else {
			$this->db->set('reference_num', $ref_num);
			$this->db->where('id', $transaction_id);
			$this->db->update('transaction_tb');
			return ($this->db->affected_rows()) ? 1 : 0;
		}
	}

	function uploadPayment($id, $data, $file_name)
	{
		$file_name = $data['file_name'];
		$this->db->set('payment_proof', $file_name);
		$this->db->set('status', 2);
		$this->db->where("id", $id);
		$this->db->update("transaction_tb");
		return ($this->db->affected_rows()) ? 1 : 0;
	}

	public function getGroups()
	{
		$this->db->select("
            id,
            name,
            description,
        ");

		$this->db->from('groups');

		$query = $this->db->get();
		return $query->result();
	}

	public function getUser($user_id)
	{
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

	public function getUsersCount($search)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('users A');

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.first_name', $search);
			$this->db->or_like('A.last_name', $search);
			$this->db->or_like('A.email', $search);
			$this->db->group_end();
		}

		$query = $this->db->get();
		return $query->row()->all_count;
	}

	public function getUserGroups($user_id)
	{

		$this->db->select("
            B.id,
            B.name,
            B.description,
        ");

		$this->db->from('users_groups A');
		$this->db->join('groups B', 'B.id = A.group_id');
		$this->db->where('A.user_id', $user_id);


		$query = $this->db->get();
		return $query->result();
	}

	public function getUsers($row_no, $row_per_page, $order_by,  $order, $search)
	{
		$this->db->select("
            A.id,
            A.active,
            A.first_name,
            A.last_name,
            A.email
        ");

		$this->db->from('users A');

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.first_name', $search);
			$this->db->or_like('A.last_name', $search);
			$this->db->or_like('A.email', $search);
			$this->db->group_end();
		}

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		$query = $this->db->get();

		return $query->result();
	}

	public function changeStatusUserDiscount($discount_users_id, $status)
	{
		$this->db->set('status', (int) $status);
		$this->db->where('id', $discount_users_id);
		$this->db->update("discount_users");
	}

	public function completeRedeem($redeem_id)
	{
		$this->db->set('status', 6);
		$this->db->where('id', $redeem_id);
		$this->db->update("deals_redeems_tb");
	}


	public function declineRedeem($redeem_id)
	{
		$this->db->set('status', 4);
		$this->db->where('id', $redeem_id);
		$this->db->update("deals_redeems_tb");
	}

	public function getPopclubRedeemItems($redeem_id)
	{
		$this->db->select("
            A.price,
            A.quantity,
            A.remarks,
            B.alias,
            B.description
        ");
		$this->db->from('deals_order_items A');
		$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
		$this->db->where('A.redeems_id', $redeem_id);

		$query = $this->db->get();
		return $query->result();
	}

	public function getPopclubRedeem($redeem_code)
	{
		$this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.redeem_code,
            A.expiration,
            A.purchase_amount,
            A.invoice_num,
            B.add_name as client_name,
            B.fb_user_id,
            B.mobile_user_id,
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

	public function getDiscount($discount_user_id)
	{
		$this->db->select("
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.dateadded,
            A.id_front,
            A.id_back,
            A.discount_id,
            A.status,
            B.name as discount_name,

            C.first_name as fb_first_name,
            C.last_name as fb_last_name,

            D.first_name as mobile_first_name,
            D.last_name as mobile_last_name,
        ");
		$this->db->from('discount_users A');
		$this->db->join('discount B', 'B.id = A.discount_id');
		$this->db->join('fb_users C', 'C.id = A.fb_user_id', 'left');
		$this->db->join('mobile_users D', 'D.id = A.mobile_user_id', 'left');

		$this->db->where('A.id', $discount_user_id);

		$query = $this->db->get();
		return $query->row();
	}

	public function getDiscountsCount($status, $search)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('discount_users A');
		$this->db->join('discount B', 'B.id = A.discount_id');

		if ($status)
			$this->db->where('A.status', $status);


		if ($search) {
			$this->db->group_start();
			$this->db->like('A.id_number', $search);
			$this->db->or_like('A.first_name', $search);
			$this->db->or_like('A.middle_name', $search);
			$this->db->or_like('A.last_name', $search);
			$this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
			$this->db->group_end();
		}

		$query = $this->db->get();
		return $query->row()->all_count;
	}

	public function getPopclubRedeemsCount($status, $search, $store)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('deals_redeems_tb A');
		$this->db->join('deals_client_tb B', 'B.id = A.client_id');
		$this->db->join('store_tb C', 'C.store_id = A.store');
		$this->db->where('A.platform_id', 1);

		if ($status)
			$this->db->where('A.status', $status);

		if (!empty($store))
			$this->db->where_in('A.store', $store);

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.redeem_code', $search);
			$this->db->or_like('B.add_name', $search);
			$this->db->or_like('C.name', $search);
			$this->db->or_like('A.purchase_amount', $search);
			$this->db->or_like('A.invoice_num', $search);
			$this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
			$this->db->group_end();
		}

		$query = $this->db->get();
		return $query->row()->all_count;
	}

	public function getDiscounts($row_no, $row_per_page, $status, $order_by,  $order, $search)
	{

		$this->db->select("
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.dateadded,
            A.discount_id,
            A.status,
            B.name as discount_name,
            B.percentage,
        ");
		$this->db->from('discount_users A');
		$this->db->join('discount B', 'B.id = A.discount_id');


		if ($status)
			$this->db->where('A.status', $status);


		if ($search) {
			$this->db->group_start();
			$this->db->like('A.id_number', $search);
			$this->db->or_like('A.first_name', $search);
			$this->db->or_like('A.middle_name', $search);
			$this->db->or_like('A.last_name', $search);
			$this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
			$this->db->group_end();
		}

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		$query = $this->db->get();
		return $query->result();
	}

	public function getPopclubRedeems($row_no, $row_per_page, $status, $order_by,  $order, $search, $store)
	{
		$this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.redeem_code,
            A.expiration,
            A.purchase_amount,
            B.mobile_user_id,
            B.fb_user_id,
            B.add_name as client_name,
            B.payops,
            C.name as store_name
        ");
		$this->db->from('deals_redeems_tb A');
		$this->db->join('deals_client_tb B', 'B.id = A.client_id');
		$this->db->join('store_tb C', 'C.store_id = A.store');
		$this->db->where('A.platform_id', 1);


		if ($status)
			$this->db->where('A.status', $status);

		if (!empty($store))
			$this->db->where_in('A.store', $store);

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.redeem_code', $search);
			$this->db->or_like('B.add_name', $search);
			$this->db->or_like('C.name', $search);
			$this->db->or_like('A.purchase_amount', $search);
			$this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
			$this->db->group_end();
		}

		$this->db->limit($row_per_page, $row_no);
		$this->db->order_by($order_by, $order);

		$query = $this->db->get();
		return $query->result();
	}

	public function getSnackshopOrderItems($transaction_id)
	{
		$this->db->select("
            A.price,
            A.product_price,
            A.quantity,
            A.remarks,
            A.product_label,
            B.name,
            B.description,
            B.add_details,
            C.name as deal_name,
            C.description as deal_description,
            C.promo_discount_percentage,
        ");
		$this->db->from('order_items A');
		$this->db->join('products_tb B', 'B.id = A.product_id');
		$this->db->join('dotcom_deals_tb C', 'C.id = A.deal_id', 'left');
		$this->db->where('A.transaction_id', $transaction_id);
		$products_query = $this->db->get();
		$products = $products_query->result();

		$this->db->select("
            A.price,
            A.product_price,
            A.quantity,
            A.remarks,
            B.name,
            B.alias,
            B.description,
        ");
		$this->db->from('deals_order_items A');
		$this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
		$this->db->where('A.transaction_id', $transaction_id);
		$deals_query = $this->db->get();
		$deals = $deals_query->result();

		return array_merge($products, $deals);
	}

	public function getSnackshopOrder($tracking_no)
	{
		$this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.invoice_num,

            A.discount,
            A.discount_user_id,
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
            B.fb_user_id,
            B.mobile_user_id,
            
            C.name as store_name,
            E.name AS discount_name,
            E.percentage AS discount_percentage,
        ");
		$this->db->from('transaction_tb A');
		$this->db->join('client_tb B', 'B.id = A.client_id');
		$this->db->join('store_tb C', 'C.store_id = A.store');
		$this->db->join('discount_users D', 'D.id = A.discount_user_id', 'left');
		$this->db->join('discount E', 'E.id = D.discount_id', 'left');
		$this->db->where('A.tracking_no', $tracking_no);

		$query = $this->db->get();
		return $query->row();
	}

	public function getCateringBookingItems($transaction_id)
	{
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
		$this->db->where('A.type', 'main');
		$query_catering_packages = $this->db->get();
		$catering_packages = $query_catering_packages->result();

		$this->db->select("
            B.product_price,
            B.quantity,
            B.remarks,
            B.product_label,
            A.name,
            A.description,
            A.add_details,
        ");
		$this->db->from('products_tb A');
		$this->db->join('catering_order_items B', 'B.product_id = A.id', 'left');
		$this->db->where('B.transaction_id', $transaction_id);
		$this->db->where('B.type', 'addon');
		$query_catering_add_ons = $this->db->get();
		$catering_add_ons = $query_catering_add_ons->result();

		return array_merge($catering_packages, $catering_add_ons);
	}

	public function getCateringBooking($tracking_no)
	{
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
            A.remarks,
            A.hash_key,

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
            A.discount,
            A.discount_user_id,
            B.fb_user_id,
            B.mobile_user_id,

            B.add_name as client_name,
            B.fname,
            B.lname,
            B.add_address,
            B.payops,
            B.email,
            B.add_contact,
            B.contact_number,

            C.name as store_name,
            C.address as store_address,
            C.contact_person as store_person,
            C.contact_number as store_contact,
            C.email as store_email,

            E.name AS discount_name,
            E.percentage AS discount_percentage,
        ");
		$this->db->from('catering_transaction_tb A');
		$this->db->join('catering_client_tb B', 'B.id = A.client_id');
		$this->db->join('store_tb C', 'C.store_id = A.store');
		$this->db->join('discount_users D', 'D.id = A.discount_user_id', 'left');
		$this->db->join('discount E', 'E.id = D.discount_id', 'left');
		$this->db->where('A.tracking_no', $tracking_no);

		$query = $this->db->get();
		return $query->row();
	}



	public function getCateringBookingsCount($status, $search, $store)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('catering_transaction_tb A');
		$this->db->join('catering_client_tb B', 'B.id = A.client_id');
		$this->db->join('store_tb C', 'C.store_id = A.store');


		if ($search) {
			$this->db->group_start();
			$this->db->like('A.tracking_no', $search);
			$this->db->or_like('B.add_name', $search);
			$this->db->or_like('C.name', $search);
			$this->db->or_like('A.purchase_amount', $search);
			$this->db->or_like('A.invoice_num', $search);
			$this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
			$this->db->group_end();
		}

		if ($status)
			$this->db->where('A.status', $status);

		if (!empty($store))
			$this->db->where_in('A.store', $store);

		$query = $this->db->get();
		return $query->row()->all_count;
	}

	public function getCateringBookings($row_no, $row_per_page, $status, $order_by,  $order, $search, $store)
	{
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
            A.discount,

            B.add_name as client_name,
            B.payops,
            C.name as store_name
        ");

		$this->db->from('catering_transaction_tb A');
		$this->db->join('catering_client_tb B', 'B.id = A.client_id');
		$this->db->join('store_tb C', 'C.store_id = A.store');

		if ($status)
			$this->db->where('A.status', $status);

		if (!empty($store))
			$this->db->where_in('A.store', $store);

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.tracking_no', $search);
			$this->db->or_like('B.add_name', $search);
			$this->db->or_like('C.name', $search);
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

	public function getSnackshopOrders($row_no, $row_per_page, $status, $order_by,  $order, $search, $store)
	{
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


		if ($status)
			$this->db->where('A.status', $status);

		if (!empty($store))
			$this->db->where_in('A.store', $store);

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.tracking_no', $search);
			$this->db->or_like('B.fname', $search);
			$this->db->or_like('C.name', $search);
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

	public function getSnackshopOrdersCount($status, $search, $store)
	{
		$this->db->select('count(*) as all_count');

		$this->db->from('transaction_tb A');
		$this->db->join('client_tb B', 'B.id = A.client_id');
		$this->db->join('store_tb C', 'C.store_id = A.store');

		if ($status)
			$this->db->where('A.status', $status);

		if (!empty($store))
			$this->db->where_in('A.store', $store);

		if ($search) {
			$this->db->group_start();
			$this->db->like('A.tracking_no', $search);
			$this->db->or_like('B.fname', $search);
			$this->db->or_like('C.name', $search);
			$this->db->or_like('A.purchase_amount', $search);
			$this->db->or_like('A.invoice_num', $search);
			$this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
			$this->db->group_end();
		}

		$query = $this->db->get();
		return $query->row()->all_count;
	}
}