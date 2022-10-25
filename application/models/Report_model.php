<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model{

    public function __construct()
    {
        $this->load->database();
    }
    
    public function getReportTransaction($startDate, $endDate){
        $columns = array("a.id AS TRANSACTION_ID", "a.tracking_no AS TRACKING_NO", "c.fname AS FIRSTNAME", "c.lname AS SURNAME", " b.dateadded AS 'COMPLETE_DATE'", "a.dateadded AS ORDER_DATE", "c.contact_number AS CONTACT_NUMBER", "c.address AS DELIVERY_ADDRESS", "c.email AS EMAIL", "a.purchase_amount AS AMOUNT", "a.distance_price AS DELIVERY FEE", "a.status AS STATUS", "d.name AS STORE", "a.invoice_num AS INVOICE NUMBER", "c.payops AS PAYMENT OPTION", "c.moh AS MODE OF HANDLING", "a.distance AS DISTANCE", "e.voucher_code AS VOUCHER CODE", "e.discount_value AS VOUCHER DISCOUNT", "CASE WHEN a.reseller_id = 0 THEN 'REG-CUS' ELSE 'RESELLER' END AS 'CUSTOMER_TYPE'");

        $this->db->select($columns);
        $this->db->from('transaction_tb a');
        $this->db->join('transaction_logs_tb b', " b.reference_id = a.id AND b.details = 'Complete Order Success'", 'left');
        $this->db->join('client_tb c', 'a.client_id = c.id', 'left');
        $this->db->join('store_tb d', 'a.store = d.store_id', 'left');
        $this->db->join('voucher_logs_tb e', ' e.transaction_id = a.id', 'left');
        $this->db->join('order_items f', 'f.transaction_id = a.id', 'left');

        
        $this->db->where('a.dateadded >=', $startDate);
        $this->db->where('a.dateadded <=', $endDate);
        $this->db->order_by('a.dateadded', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function getReportPmix($startDate, $endDate){
        $this->db->select("
            f.transaction_id AS 'oi.transaction_id',
            f.combination_id AS 'oi.combination_id',
            f.product_id AS 'oi.product_id',
            p.name AS 'p.product_name',
            p.product_code AS 'p.product_code',
            f.quantity AS 'oi.quantity',
            f.status AS 'oi.status',
            f.store AS 'oi.store',
            f.remarks AS 'oi.remarks',
            f.type AS 'oi.type',
            f.promo_id AS 'oi.promo_id',
            f.promo_price AS 'oi.promo_price',
            f.sku AS 'oi.sku',
            f.sku_id AS 'oi.sku_id',
            f.price AS 'oi.price',
            f.product_price AS 'oi.product_price',
            f.product_label AS 'oi.product_label',
            f.product_discount AS 'oi.product_discount',
            f.addon_drink AS 'oi.addon_drink',
            a.tracking_no AS 'ttb.tracking_no',
            a.status AS 'ttb.status',
            a.store AS 'ttb.store',
            a.invoice_num AS 'ttb.invoice_num',
            b.user_id AS 'tltb.user_id',
            b.action AS 'tltb.action',
            b.details AS 'tltb.details',
            b.dateadded AS 'tltb.dateadded',
            s.name AS 'stre.name',
            a.distance AS 'ttb.distance',
            a.distance_id AS 'ttb.distance_id',
            a.distance_price AS 'ttb.distance_price',
            c.address AS 'ttb.address',
            d.category_name AS 'd.category_name'
        ");

        $this->db->from('order_items f');
        $this->db->join('transaction_tb a', 'a.id = f.transaction_id');
        $this->db->join('transaction_logs_tb b', "b.reference_id = f.transaction_id AND b.dateadded LIKE '%2022%'");
        $this->db->join('products_tb p', 'p.id = f.product_id');
        $this->db->join('store_tb s', 's.store_id  = a.store');
        $this->db->join('client_tb c', 'c.id = a.client_id');
        $this->db->join('category_tb d', 'd.id = p.category');

        $this->db->where("a.status = 6 AND b.details LIKE '%Complete Order Success%'");
        $this->db->where('a.dateadded >=', $startDate);
        $this->db->where('a.dateadded <=', $endDate);
        $this->db->order_by('a.dateadded', 'ASC');

        return $this->db->get()->result();
    }
}
