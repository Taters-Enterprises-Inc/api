<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transaction_model extends CI_Model {
    
    public function insert_client_details($hash_key)
    {  
        if($_SESSION['moh'] == 2){
            $payops = $this->input->post('delivery_checkout_payops');
        }else{
            $payops = $this->input->post('checkout_payops');
        }
        if (isset($_SESSION['userData']['oauth_uid'])) {
            $this->db->trans_start();
                // $address = (empty($this->input->post('checkout_address'))) ? $this->session->customer_address : $this->input->post('checkout_address');
                $data = array(
                    'fb_user_id'        => $this->shop_model->get_facebook_client_id($_SESSION['userData']['oauth_uid']),
                    'email'             => $this->input->post('checkout_email'),
                    'address'           => $this->input->post('checkout_address'),
                    'contact_number'    => $this->input->post('checkout_phone'),
                    'moh'               => $this->session->moh,
                    'payops'            => (isset($_SESSION['userData']['store_user_id'])) ? 0 : $payops,
                    'add_name'          => $this->input->post('checkout_fname').' '.$this->input->post('checkout_lname'),
                    'add_contact'       => $this->input->post('checkout_phone'),
                    'add_address'       => $this->input->post('checkout_address')
                );
                $this->db->insert('client_tb', $data);
                $insert_id = $this->db->insert_id();
            $this->db->trans_complete();
        } elseif(isset($_SESSION['userData']) && $_SESSION['userData']['login_type'] == 'mobile'){
            $this->db->trans_start();
                // $address = (empty($this->input->post('checkout_address'))) ? $this->session->customer_address : $this->input->post('checkout_address');
                $data = array(
                    'mobile_user_id'    => $this->shop_model->get_mobile_client_id($_SESSION['userData']['mobile_user_id']),
                    'fname'             => $this->input->post('checkout_fname'),
                    'lname'             => $this->input->post('checkout_lname'),
                    'email'             => $this->input->post('checkout_email'),
                    'address'           => $this->input->post('checkout_address'),
                    'contact_number'    => $this->input->post('checkout_phone'),
                    'moh'               => $this->session->moh,
                    'payops'            => (isset($_SESSION['userData']['store_user_id'])) ? 0 : $payops,
                    'add_name'          => $this->input->post('checkout_fname').' '.$this->input->post('checkout_lname'),
                    'add_contact'       => $this->input->post('checkout_phone'),
                    'add_address'       => $this->input->post('checkout_address')
                );
                $this->db->insert('client_tb', $data);
                $insert_id = $this->db->insert_id();
            $this->db->trans_complete();
        } else {
            $this->db->trans_start();
                // $address = (empty($this->input->post('checkout_address'))) ? $this->session->customer_address : $this->input->post('checkout_address');
                $data = array(
                    'fname'             => $this->input->post('checkout_fname'),
                    'lname'             => $this->input->post('checkout_lname'),
                    'email'             => $this->input->post('checkout_email'),
                    'address'           => $this->input->post('checkout_address'),
                    'contact_number'    => $this->input->post('checkout_phone'),
                    'moh'               => $this->session->moh,
                    'payops'            => $payops,
                    'add_name'          => $this->input->post('checkout_fname').' '.$this->input->post('checkout_lname'),
                    'add_contact'       => $this->input->post('checkout_phone'),
                    'add_address'       => $this->input->post('checkout_address')
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
}