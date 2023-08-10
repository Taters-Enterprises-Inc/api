<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inbox_model extends CI_Model 
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

}