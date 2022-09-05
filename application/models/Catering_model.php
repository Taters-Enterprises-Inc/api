<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Catering_model extends CI_Model 
{
    function get_product_prices($id){
        $this->db->select('*');
        $this->db->from('catering_package_prices_tb');
        $this->db->where('package_id',$id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_catering_product_addons($region_id){
        $this->db->select('*');
        $this->db->from('catering_product_addons_tb a');
        $this->db->join('products_tb b','b.id = a.product_id');
        $this->db->where('a.region_id',$region_id);
        $this->db->where('a.status','0');
        $query = $this->db->get();
        return $query->result();
    }

    function get_catering_addons($region_id){
        $this->db->select('*');
        $this->db->from('catering_package_addons_tb a');
        $this->db->join('catering_packages_tb b','b.id = a.product_id');
        $this->db->where('a.region_id',$region_id);
        $this->db->where('a.status','0');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_product_variants($product_id)
    {
        $this->db->select("B.id,B.name,B.product_variant_id, A.name as parent_name");
        $this->db->from('catering_package_variants_tb A');
        $this->db->join('catering_package_variant_options_tb B', 'B.product_variant_id = A.id','left');
        $this->db->where('A.product_id', $product_id);
        $this->db->where('B.status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    function fetch_variants_details($id)
    {
        $this->db->select('name');
        $this->db->where('id', $id);
        $query = $this->db->get('catering_package_variant_options_tb');
        return $query->row();
    }
    

    function get_package_prices($id){
        $this->db->select('*');
        $this->db->from('catering_package_prices_tb');
        $this->db->where('package_id',$id);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function get_details($id){
        $this->db->select("*");
        $this->db->from('catering_packages_tb');
        $this->db->where('id', $id);

        $query = $this->db->get();
        return $query->row();
    }

    public function get_product($hash){
        
        $this->db->select('
            A.id, 
            A.product_image, 
            A.name, 
            A.description, 
            A.add_details, 
            A.delivery_details, 
            A.price, A.category, 
            A.num_flavor, 
            A.add_remarks,
            A.note, 
            B.category_name, 
            A.to_gc_value
        ');
        $this->db->from('catering_packages_tb A');
        $this->db->join('catering_category_tb B', 'B.id= A.category');
        $this->db->where('A.product_hash', $hash);

        $query = $this->db->get();

        return $query->row();    
    } 
    
    function fetch_category_products($region,$category,$sort_id,$min,$max,$name){
        if($region!=0){
          $region = $this->db
              ->select('product_id')
              ->get_where('catering_region_da_log', array('region_id' => $region,'status' => 1))
              ->result();
          foreach ($region as $row) {
              $disable_region_items[] = $row->product_id;
          }
          $to_disable = empty($disable_region_items) ? 0 : $disable_region_items;
        }
  
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
        ");
  
        $this->db->from('catering_category_tb A');
        $this->db->join('catering_packages_tb B', 'B.category = A.id','left');
        $this->db->join('catering_package_category_tb C', 'C.product_id = B.id');
        $this->db->group_by('C.product_id');
  
        $this->db->where('A.status', 1);
        $this->db->where('B.status', 1);
  
  
        if($region != 0 && $to_disable != 0){
            $this->db->where_not_in('B.id', $to_disable);
        }
        if ($name != null) {
            $this->db->like('B.name', $name, 'both');
        }
        if ( ($min == 0 || $min != 0) && $max != 0) {
            $this->db->where("price BETWEEN '$min' AND '$max'");
        }
        if($category != 0 && $category != 14) {
            $this->db->where('A.id', $category);
        }
        if ($sort_id == 'low_high') {
            $this->db->order_by('B.price', 'ASC');
        }
        if ($sort_id == 'high_low') {
            $this->db->order_by('B.price', 'DESC');
        }
        if ($sort_id == 'a_z') {
            $this->db->order_by('B.name', 'ASC');
        }
        if ($sort_id == 'z_a') {
            $this->db->order_by('B.name', 'DESC');
        }
        $this->db->order_by("A.sequence", "asc");
        $this->db->order_by("B.name", "asc"); 
        $query = $this->db->get();
        $query_data = $query->result();
  
  
        $return_data = array();
        if($category != 14 || $category == 'prod_category_page') {
          foreach ($query_data as $key => $val) {
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
                  'hash' => $val->product_hash,
              );
          }
      }else if ($category == 14){
          foreach ($query_data as $key => $value) {
  
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
                  'hash'  => $value->product_hash,
              );
  
          }
      }
  
        // return re-arrange key values of array and convert to object
        $reindex = array_values($return_data);
        return $reindex;
    }
    
    public function get_user_booking_history($id,$type){
        $this->db->select('
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.status,
            A.hash_key,
        ');
        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'A.client_id = B.id' ,'left');
        // $this->db->join('raffle_coupon_code_tb D', 'A.raffle_coupon_code = D.raffle_coupon_code' ,'left');
        if ($type == 'mobile') {
            $this->db->where('B.mobile_user_id', $id);
        } else {
            $this->db->where('B.fb_user_id', $id);
        }
        $this->db->order_by('A.dateadded','DESC');
        $query = $this->db->get();
        return $query->result();
    }
}