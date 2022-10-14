<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');

class Notification_model extends CI_Model {

    public function __construct(){
        $this->load->database();
    }

    public function insertNotification($data,$notification_event_type_id ,$text){
        $this->db->insert('notification_events', array(
            "notification_event_type_id" => $notification_event_type_id,
            "text" => $text
        ));

        $data['event_id'] = $this->db->insert_id();
        
        $this->db->insert('notifications', $data);
    }

}