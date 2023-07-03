<?php defined('BASEPATH') OR exit('No direct script access allowed');

class stock_ordering_model extends CI_Model {

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
            B.category_id,
            A.order_qty,
            A.commited_qty,
            A.delivered_qty,
            A.total_cost,
            A.order_information_id,
        ');

        //to be implemented cost and current stock
        $this->db->from('order_item_tb A');
        $this->db->join('product_tb B', 'B.product_id = A.product_id', 'left');
        $this->db->where('A.order_information_id', $order_id);

        $product_query = $this->db->get();
        return $product_query->result_array();
    }


    public function getProduct($category, $store_id){
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

    public function getOrderData($id){
        $this->db->select('
            B.name as store_name,
            A.id,
            E.category_name,
            A.order_placement_date,
            A.requested_delivery_date,
            A.commited_delivery_date,
            A.order_confirmation_date,
            A.actual_delivery_date,
            C.description,
            D.billing_id,
            D.billing_amount,
            F.short_name,
            A.reviewed_date,
            A.dispatch_date,
            A.enroute_date,
            A.payment_confirmation_date,
            A.delivery_receipt,
            A.updated_delivery_receipt,
            A.payment_detail_image,
        ');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->join('order_status C', 'C.id = A.status_id', 'left');
        $this->db->join('billing_information_tb D', 'D.id = A.billing_information_id', 'left');
        $this->db->join('category_tb E', 'E.category_id = A.order_type_id', 'left');
        $this->db->join('payment_status_tb F', 'F.id = A.payment_status_id', 'left');
        $this->db->where('A.id', $id);

        $order_query = $this->db->get();
        return $order_query->row();
    }

    public function getOrders($row_no, $row_per_page, $order_by,  $order, $search, $status){

        $this->db->select('
            A.id,
            B.name as store_name,
            E.category_name,
            A.order_placement_date,
            A.requested_delivery_date,
            A.commited_delivery_date,
            A.order_confirmation_date,
            A.actual_delivery_date,
            C.description,
            D.billing_id,
            D.billing_amount,
            F.short_name
        ');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->join('order_status C', 'C.id = A.status_id', 'left');
        $this->db->join('billing_information_tb D', 'D.id = A.billing_information_id', 'left');
        $this->db->join('category_tb E', 'E.category_id = A.order_type_id', 'left');
        $this->db->join('payment_status_tb F', 'F.id = A.payment_status_id', 'left');
        $this->db->where('A.status_id', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('A.id', $search);
            $this->db->or_like("B.store_name", $search);
            $this->db->or_like('C.description', $search);
            $this->db->or_like('F.short_name', $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $order_query = $this->db->get();
        return $order_query->result();
        
    }

    public function getOrdersCount($search, $status){

        $this->db->select('count(*) as all_count');
        $this->db->from('order_information_tb A');
        $this->db->join($this->newteishop->database.'.store_tb B', 'B.store_id = A.store_id', 'left');
        $this->db->join('order_status C', 'C.id = A.status_id', 'left');
        $this->db->join('billing_information_tb D', 'D.id = A.billing_information_id', 'left');
        $this->db->join('category_tb E', 'E.category_id = A.order_type_id', 'left');
        $this->db->join('payment_status_tb F', 'F.id = A.payment_status_id', 'left');
        $this->db->where('A.status_id', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('A.id', $search);
            $this->db->or_like("B.store_name", $search);
            $this->db->or_like('C.description', $search);
            $this->db->or_like('F.short_name', $search);
            $this->db->group_end();
        }

        $order_query = $this->db->get();
        return $order_query->row()->all_count;
        
    }

    public function getStore(){
        $this->newteishop->select('
            A.store_id,
            A.name,
        ');

        $this->newteishop->from('store_tb A');

        $query = $this->newteishop->get();
        return $query->result();
    }

    public function updateOrderInfo($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }

     public function updateOrderItem($id ,$id_product, $data){
        $this->db->where('order_information_id', $id);
        $this->db->where('product_id', $id_product);
        $this->db->update('order_item_tb', $data);
    }

    public function reviewOrder($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }

    public function confirmOrder($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }

    public function dispatchOrder($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }

    public function orderEnRoute($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }

    public function updateActualDeliveryDate($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }

    public function updateDeliveredQty($id,$id_product, $data){
        $this->db->where('order_information_id', $id);
        $this->db->where('product_id', $id_product);
        $this->db->update('order_item_tb', $data);
    }

    public function insertBllingInfo($data){
        $this->db->insert('billing_information_tb', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function updateBillingInformationId($id, $data){
        $this->db->where('id', $id);
        $this->db->update('order_information_tb', $data);
    }


}