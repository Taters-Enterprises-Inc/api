<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Report_model extends CI_Model{

    public function __construct()
    {
        $this->load->database();
    }
    
    public function getReportTransaction($startDate, $endDate){
        $this->db->select('
            A.tracking_no as TRACKING NO,
            B.fname as FIRSTNAME,
            B.lname as SURNAME,
            A.dateadded as ORDER DATE,
            B.add_contact as CONTACT NUMBER,
            B.add_address as DELIVERY ADDRESS,
            B.email as EMAIL,
            A.purchase_amount as AMOUNT,
            A.distance_price as DELIVERY FEE,
            (A.purchase_amount + A.distance_price) as " ",
            A.status as STATUS,
            C.name as STORE,
            A.invoice_num as INVOICE NUMBER,
            A.payops as PAYMENT OPTION,
            B.moh as MODE OF HANDLING,
            A.distance as DISTANCE,
            D.voucher_code as VOUCHER CODE,
            D.discount_value as DISCOUNT VALUE,
            E.redeem_code as POPCLUB CODE,
            F.alias as POPCLUB DEAL NUMBER,
            H.name as POPCLUB DEAL CATEGORY,
            (F.original_price - F.promo_price) as "POPCLUB DISCOUNT",
        ');

        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B','B.id = A.client_id');
        $this->db->join('store_tb C','C.store_id = A.store');
        $this->db->join('voucher_logs_tb D', 'D.transaction_id = A.id', 'left');
        $this->db->join('deals_redeems_tb E', 'E.id = A.deals_redeems_id', 'left');
        $this->db->join('dotcom_deals_tb F', 'F.id = E.deal_id', 'left');
        $this->db->join('dotcom_deals_platform_combination G', 'G.deal_id = F.id', 'left');
        $this->db->join('dotcom_deals_category H', 'H.id = G.platform_category_id', 'left');

        
        $this->db->where('A.dateadded >=', $startDate);
        $this->db->where('A.dateadded <=', $endDate);
        $this->db->where('A.status', 6);
        $this->db->order_by('A.dateadded', 'ASC');
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
