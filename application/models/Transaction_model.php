<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaction_model extends CI_Model {
    
    public function insert_client_orders($data)
    {
        $this->db->trans_start();
        $this->db->insert_batch('order_items', $data);
        $this->db->trans_complete();
        return  $this->db->trans_status();
    }
	
    public function insert_client_orders_deal($data)
    {
        $this->db->trans_start();
        $this->db->insert_batch('deals_order_items', $data);
        $this->db->trans_complete();
        return  $this->db->trans_status();
    }

    public function insert_transaction_details($data)
    {   
        $this->db->trans_start();
		$this->db->insert('transaction_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        $id = ($this->db->trans_status() === FALSE) ? 0 : $insert_id;
        return  json_decode(json_encode(array('status'=>$this->db->trans_status(),'id'=>$id)), FALSE);
    }
    
    public function insert_client_details($post)
    {  
        if (isset($_SESSION['userData']['oauth_uid'])) {

            $this->db->trans_start();
            // $address = (empty($this->input->post('checkout_address'))) ? $this->session->customer_address : $this->input->post('checkout_address');
            $data = array(
                'fb_user_id'        => $this->get_facebook_client_id($_SESSION['userData']['oauth_uid']),
                'email'             => $post['eMail'],
                'address'           => $post['address'],
                'contact_number'    => $post['phoneNumber'],
                'moh'               => 2,
                'payops'            => $post['payops'],
                'add_name'          => $post['firstName'].' '.$post['lastName'],
                'add_contact'       => $post['phoneNumber'],
                'add_address'       => $post['address']
            );
            $this->db->insert('client_tb', $data);
            $insert_id = $this->db->insert_id();
            $this->db->trans_complete();

        } elseif(isset($_SESSION['userData']) && $_SESSION['userData']['login_type'] == 'mobile'){
                $this->db->trans_start();
                // $address = (empty($this->input->post('checkout_address'))) ? $this->session->customer_address : $this->input->post('checkout_address');
                $data = array(
                    'mobile_user_id'    => $this->get_mobile_client_id($_SESSION['userData']['mobile_user_id']),
                    'fname'             => $post['firstName'],
                    'lname'             => $post['lastName'],
                    'email'             => $post['eMail'],
                    'address'           => $post['address'],
                    'contact_number'    => $post['phoneNumber'],
                    'moh'               => 2,
                    'payops'            => $post['payops'],
                    'add_name'          => $post['firstName'].' '.$post['lastName'],
                    'add_contact'       => $post['phoneNumber'],
                    'add_address'       => $post['address']
                );
                $this->db->insert('client_tb', $data);
                $insert_id = $this->db->insert_id();
            $this->db->trans_complete();
        } else {
            $this->db->trans_start();
                // $address = (empty($this->input->post('checkout_address'))) ? $this->session->customer_address : $this->input->post('checkout_address');
                $data = array(
                    'fname'             => $post['firstName'],
                    'lname'             => $post['lastName'],
                    'email'             => $post['eMail'],
                    'address'           => $post['address'],
                    'contact_number'    => $post['phoneNumber'],
                    'moh'               => 2,
                    'payops'            => $post['payops'],
                    'add_name'          => $post['firstName'].' '.$post['lastName'],
                    'add_contact'       => $post['phoneNumber'],
                    'add_address'       => $post['address']
                );
                $this->db->insert('client_tb', $data);
                $insert_id = $this->db->insert_id();
            $this->db->trans_complete();
        }
        
        require FCPATH . 'vendor/autoload.php';
        
        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );

        $pusher = new Pusher\Pusher(
            '8a62b17c8a9baa690edb',
            '0e16bc6f7b22f371826b',
            '1188170',
            $options
        );

        $data['message'] = ''; //put any message here
        $data['store_name'] = substr($_SESSION['cache_data']['region_name'],strpos($_SESSION['cache_data']['region_name'], "-") + 1);
        $pusher->trigger('dashboard_notifs', 'my-event', $data);

        $id = ($this->db->trans_status() === FALSE) ? 0 : $insert_id;
        return  json_decode(json_encode(array('status'=>$this->db->trans_status(),'id'=>$id)), FALSE);
    }
    
    //jepoy get facebook client id
    public function get_facebook_client_id($oauth_id){
        $this->db->select('id');
        $this->db->where('oauth_uid', $oauth_id);
        $query = $this->db->get('fb_users');
        $data = $query->result_array();
        return $data[0]['id'];
    }
    
    //jepoy get mobile client id
    public function get_mobile_client_id($id){
        $this->db->select('id');
        $this->db->where('id', $id);
        $query = $this->db->get('mobile_users');
        $data = $query->result_array();
        return $data[0]['id'];
    }
}