<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Notification_model extends CI_Model {

    public function __construct(){
        $this->bsc_db = $this->load->database('bsc', TRUE, TRUE);
    }
    
    public function insertNotificationMessageAndGetId(
        $notification_message_data
    ){
        $this->db->insert('notification_messages',$notification_message_data);
        return $this->db->insert_id();
    }
    public function insertAndGetNotificationEvent(
        $notification_event_data
    ){
        $this->db->insert('notification_events',$notification_event_data);
        return $this->db->insert_id();
    }

    public function insertNotification(
        $notifications_data
    ){
        $this->db->insert('notifications', $notifications_data);
    }

    public function seenNotification($notification_id, $date_now){
		$this->db->set('dateseen', $date_now);
        $this->db->where("id", $notification_id);
        $this->db->update("notifications");
    }

    public function getNotifications(
        $user_id,
        $notification_event_type_id,
        $is_unseen,
        $type
    ){
        $this->db->select('
            A.id,
            A.dateadded, 
            A.dateseen,
            B.text,
            B.notification_event_type_id,
            B.customer_survey_response_id,
            
            C.id as transaction_tb_id,
            D.id as catering_transaction_tb_id,
            E.id as deals_redeems_tb_id,
            
            C.tracking_no,
            D.tracking_no as catering_tracking_no,
            E.redeem_code as redeem_code,
            
        ');

        $this->db->from('notifications A');
        $this->db->join('notification_events B', 'B.id = A.notification_event_id');
        
        $this->db->join('transaction_tb C', 'C.id = B.transaction_tb_id','left');
        $this->db->join('catering_transaction_tb D', 'D.id = B.catering_transaction_tb_id','left');
        $this->db->join('deals_redeems_tb E', 'E.id = B.deals_redeems_tb_id','left');

        if($is_unseen){
            $this->db->where('A.dateseen', NULL);
        }

        if(isset($notification_event_type_id)){
            $this->db->where('B.notification_event_type_id',$notification_event_type_id);
        }

        if($type =='mobile'){
            $this->db->where('mobile_user_to_notify', $user_id);
        }else if($type == 'facebook'){
            $this->db->where('fb_user_to_notify', $user_id);
        }else if($type == 'admin'){
            if(isset($user_id)){
                $this->db->where('user_to_notify', $user_id);
            }
            
        }

        $this->db->order_by('A.id','DESC');
        
        $query = $this->db->get();
        
        return $query->result();
    }

    public function getUnseenNotificationsCount(
        $user_id,
        $notification_event_type_id,
        $type
    ){
        $this->db->select('count(*) as all_count');

        $this->db->from('notifications A');
        $this->db->join('notification_events B', 'B.id = A.notification_event_id');
        $this->db->where('A.dateseen', NULL);
        
        if(isset($notification_event_type_id)){
            $this->db->where('B.notification_event_type_id',$notification_event_type_id);
        }

        if($type =='mobile'){
            $this->db->where('mobile_user_to_notify', $user_id);
        }else if($type == 'facebook'){
            $this->db->where('fb_user_to_notify', $user_id);
        }else if($type == 'admin'){
            if(isset($user_id)){
                $this->db->where('user_to_notify', $user_id);
            }
            
        }
   
        $query = $this->db->get();
        return $query->row()->all_count;
    }

}