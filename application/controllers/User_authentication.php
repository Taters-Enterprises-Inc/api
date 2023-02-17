<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class User_Authentication extends CI_Controller {
    function __construct() {
		parent::__construct();
		
		// Load facebook oauth library
		$this->load->library('facebook');
		
		// Load user model
		$this->load->model('user');
    }
    
    public function index(){
		$userData = array();
		
		// Authenticate user with facebook
		if($this->facebook->is_authenticated()){
			// Get user info from facebook
			$fbUser = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,link,gender,picture');
            $login_point = $_SESSION['fb_login_point'];
            
            if(isset($fbUser['error'])){
                $result = false;
                redirect($login_point);	
                return;
            }

            // Preparing data for database insertion
            $userData['oauth_provider'] = 'facebook';
            $userData['oauth_uid']	= !empty($fbUser['id'])?$fbUser['id']:'';;
            $userData['first_name']	= !empty($fbUser['first_name'])?$fbUser['first_name']:'';
            $userData['last_name']	= !empty($fbUser['last_name'])?$fbUser['last_name']:'';
            $userData['email']		= !empty($fbUser['email'])?$fbUser['email']:'';
            $userData['gender']		= !empty($fbUser['gender'])?$fbUser['gender']:'';
			$userData['picture']	= !empty($fbUser['picture']['data']['url'])?$fbUser['picture']['data']['url']:'';
            $userData['link']		= !empty($fbUser['link'])?$fbUser['link']:'https://www.facebook.com/';
            
            // Insert or update user data to the database
            $userID = $this->user->checkUser($userData);
			
			// Check user data insert or update status
            if(!empty($userID)){
                $userData['fb_user_id'] = $userID;
                $data['userData'] = $userData;
				
				// Store the user profile info into session
                $this->session->set_userdata('userData', $userData);
            }else{
               $data['userData'] = array();
            }
			
			// Facebook logout URL
			$data['logoutURL'] = $this->facebook->logout_url();
            $result = true;
			redirect($login_point);	
            return;
		}else{
			// Facebook authentication url
            $data['authURL'] =  $this->facebook->login_url();
            $result = false;
        }
        header('content-type: application/json');
        echo json_encode(array('result' => $result, 'url' => $data['authURL']));
    }

	public function logout() {
		// Remove local Facebook session
		$this->facebook->destroy_session();
		// Remove user data from session
		$this->session->unset_userdata('userData');
        $this->session->unset_userdata('orders');

        $response = array(
            'message' => "Successfully logout user"
        );

        header('content-type: application/json');
        echo json_encode($response);
    }
    
    public function success_login() {
        $this->load->view('pages/shop_v2/header');
        $this->load->view('pages/shop_v2/success_login');
        $this->load->view('pages/shop_v2/footer');
    }

    public function fb_login_point(){
		$post = json_decode(file_get_contents("php://input"), true);

        $this->session->set_userdata('fb_login_point', $post['fb_login_point']);
        // $this->session->unset_userdata('fb_login_point');
        $status = true;
        
        header('content-type: application/json');
        echo json_encode(array('result' => $status));
    }
}
