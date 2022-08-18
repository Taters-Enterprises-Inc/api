<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model 
{
	
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
	
    function fetch_product_variants($product_id,$name)
    {
        $this->db->select("B.id,B.name");
        $this->db->from('product_variants_tb A');
        $this->db->join('product_variant_options_tb B', 'B.product_variant_id = A.id','left');
        $this->db->where('A.product_id', $product_id);
        $this->db->where('A.name', $name);
        $this->db->where('B.status', 1);
        $query = $this->db->get();
        return $query->result();
    }
		
	public function get_product($hash)
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
		$this->db->where('product_hash', $hash);

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
    {   // select disable products
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
		
        //use if store_type of some stores shown is popcorn
        $product_category_tb = ($store_menu_type == 2) ? 'C.product_id,C.category_id' : '';
        // select all the category products
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
           //use if store_type of some stores shown is popcorn
            $this->db->join('product_category_tb C', 'C.product_id = B.id');
            $this->db->group_by('C.product_id'); //added to prevent showing duplicated products
           //use if store_type of some stores shown is popcorn
        }
        else {
            $this->db->join('product_category_tb C', 'C.category_id = A.id');
            $this->db->join('products_tb B', 'B.id = C.product_id');
        }

        $this->db->where('A.status', 1);
        $this->db->where('B.status', 1);

        if($store_menu_type == '2') {
            $this->db->where('C.category_id =', 14);
        }

        if($region != 0 && $to_disable != 0){
            $this->db->where_not_in('B.id', $to_disable);
            //use if store_type of some stores shown is popcorn
            if(!empty($store_menu_type)){
                $this->db->where_in('A.id', $shown_categories);
            }
            //use if store_type of some stores shown is popcorn
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
        $this->db->order_by("B.name", "asc"); //added to make products on category & products page in fixed position
        $query = $this->db->get();
        $query_data = $query->result();
        // arrangement of return array data
        $return_data = array();

        if($store_menu_type != '2'){
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
            }

            //use if store_type of some stores shown is popcorn
            else if(!empty($store_menu_type)) {
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
            }

            //for showing all products (shop grid)
            else if ($category == 14){
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
        }
        else {

            if($category != 14 || $category == 'prod_category_page') {
                foreach ($query_data as $key => $val) {

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
                        'price' => $val->product_price,
                        'hash' => $val->product_hash,
                    );
                }
            }
            //for showing all products (shop grid)
            else if ($category == 14){
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
        }

        // return re-arrange key values of array and convert to object
        $reindex = array_values($return_data);
        return $reindex;
    }
}
