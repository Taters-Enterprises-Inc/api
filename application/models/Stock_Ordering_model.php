<?php defined('BASEPATH') OR exit('No direct script access allowed');

class stock_ordering_model extends CI_Model {

	public function __construct(){
        $this->db = $this->load->database('stock-ordering', TRUE, TRUE);
        $this->newteishop = $this->load->database('default', TRUE, TRUE);

    }


    function getProduct($category, $store_id){
        //to updated with $store_id
        $this->db->select('p.product_id, p.product_name, p.uom, p.category_id, pc.cost');
        $this->db->from('product_tb p');
        $this->db->join('product_cost_tb pc', 'p.product_id = pc.product_id', 'left');
        $this->db->where('p.category_id', $category);

        $query = $this->db->get();
        $result = $query->result_array();


        // $data = array(
        //     array("frozen" => array()),
        //     array("dry" => array())
        // );

        // foreach ($result as $row) {
        //     $product = array(
        //         'productId' => $row['product_id'],
        //         'productName' => $row['product_name'],
        //         'uom' => $row['uom'],
        //         'cost' => $row['cost']
        //     );

        //     if ($row["category_id"] == 1) {
        //         array_push($data[0]["frozen"], $product);
        //     } elseif ($row["category_id"] == 2) {
        //         array_push($data[1]["dry"], $product);
        //     }
        // }

        return $result;
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


    function getStore(){
        $this->newteishop->select('
            A.store_id,
            A.name,
        ');

        $this->newteishop->from('store_tb A');

        $query = $this->newteishop->get();
        return $query->result();
    }


}