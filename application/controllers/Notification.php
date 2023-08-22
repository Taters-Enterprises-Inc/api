<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
date_default_timezone_set('Asia/Manila');

class Notification extends CI_Controller {
	
    public function __construct(){
		parent::__construct();

		$this->load->model('notification_model');
	}

	public function index(){
		switch($this->input->server('REQUEST_METHOD')){
			case 'GET':
				if(!isset($_SESSION['userData'])){
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message"=>"Unauthorized user"));
				}

				$user_id = isset($_SESSION['userData']['oauth_uid']) ? $_SESSION['userData']['fb_user_id'] : $_SESSION['userData']['mobile_user_id'];
				$type = isset($_SESSION['userData']['oauth_uid']) ? 'facebook' : 'mobile';

				$response = array(
					"data" => array(
						"all" => array(
							'notifications'=> $this->notification_model->getNotifications($user_id, null, false, $type),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, null, true, $type),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, null, $type),
						),
						"snackshop_order" => array(
							'notifications'=> $this->notification_model->getNotifications($user_id, 1, false, $type),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, 1, true, $type),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, 1, $type),
						),
						"catering_booking" => array(
							'notifications'=> $this->notification_model->getNotifications($user_id, 2, false, $type),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, 2, true, $type),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, 2, $type),
						),
						"popclub_redeem" => array(
							'notifications'=> $this->notification_model->getNotifications($user_id, 3, false, $type),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, 3, true, $type),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, 3, $type),
						),
						"inbox" => array(
							'notifications'=> $this->notification_model->getNotifications($user_id, 4, false, $type),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, 4, true, $type),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, 4, $type),
						),
						"user_discount" => array(
							'notifications'=> $this->notification_model->getNotifications($user_id, 6, false, $type),
							"unseen_notifications" => $this->notification_model->getNotifications($user_id, 6, true, $type),
							'unseen_notifications_count' => $this->notification_model->getUnseenNotificationsCount($user_id, 6, $type),
						),
					),
					"message" => "Successfully fetch notification"
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
     	}
  	}

	
	public function seen($notification_id){
		switch($this->input->server('REQUEST_METHOD')){
			case 'PUT':
				$date_now = date('Y-m-d H:i:s');
				$this->notification_model->seenNotification($notification_id, $date_now);
				
				$response = array(
					"message" => "Successfully seen notification"
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
		}
	}

}