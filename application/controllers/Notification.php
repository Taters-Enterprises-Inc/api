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
				$user_id = isset($_SESSION['userData']['oauth_uid']) ? $_SESSION['userData']['fb_user_id'] : $_SESSION['userData']['mobile_user_id'];
				$type = isset($_SESSION['userData']['oauth_uid']) ? 'facebook' : 'mobile';


				$response = array(
					"data" => array(
						"snackshop" => array(
							"notifications" => $this->notification_model->getNotifications($user_id, 1, true, $type),
							"count" => $this->notification_model->getUnseenNotificationsCount($user_id, 1, $type),
						),
						"catering" => array(
							"notifications" => $this->notification_model->getNotifications($user_id, 2, true, $type),
							"count" => $this->notification_model->getUnseenNotificationsCount($user_id, 2, $type),
						)
					),
					"message" => "Succesfully fetch notification"
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
			case 'PUT':
				$post = json_decode(file_get_contents("php://input"), true);
				$date_now = date('Y-m-d H:i:s');

				$this->notification_model->seenNotification($post['notificationId'], $date_now);

				$response = array(
					"message" => "Succesfully seen notification"
				);
				
				header('content-type: application/json');
				echo json_encode($response);
				break;
     	}
  }

}