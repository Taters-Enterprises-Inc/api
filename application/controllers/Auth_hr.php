<?php defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Auth_hr extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->db = $this->load->database('hr', TRUE, TRUE);

        $this->load->library('form_validation');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->hr_auth->set_message_delimiters('', '');
        $this->hr_auth->set_error_delimiters('', '');

		$this->load->model('hr_model');
        $this->lang->load('auth');
    }
	
	public function create_group(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);
				$this->data['title'] = $this->lang->line('create_group_title');

				if (!$this->hr_auth->logged_in() || !$this->hr_auth->is_admin()) {
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => 'Unauthorized user'));
					return;
				}

				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');

				if ($this->form_validation->run() === TRUE) {
					$new_group_id = $this->hr_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
					if ($new_group_id) {
						
						header('content-type: application/json');
						echo json_encode(array("message" =>  $this->hr_auth->messages()));
						return;
					} else {

						$this->output->set_status_header(401);
						header('content-type: application/json');
						echo json_encode(array("message" => validation_errors()));
						return;
					}
				}else{
					// display the create group form
					// set the flash data error message if there is one
					$this->data['message'] = (validation_errors() ? validation_errors() : ($this->hr_auth->errors() ? $this->hr_auth->errors() : $this->session->flashdata('message')));

					$this->data['group_name'] = [
						'name'  => 'group_name',
						'id'    => 'group_name',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('group_name'),
					];
					$this->data['description'] = [
						'name'  => 'description',
						'id'    => 'description',
						'type'  => 'text',
						'value' => $this->form_validation->set_value('description'),
					];

					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => validation_errors()));
					return;
				}
				break;
		}
		
	}
    
    public function login(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);

		        $this->data['title'] = $this->lang->line('login_heading');
                $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
                $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
                
		        if ($this->form_validation->run() === TRUE) {
                    $remember = (bool) $this->input->post('remember');

                    if ($this->hr_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->hr_auth->messages()));
                        return;
                    } else {
				        $this->output->set_status_header(401);
                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->hr_auth->errors()));
                        return;
                    }
                }else{ 
                    $this->output->set_status_header(401);
                    header('content-type: application/json');
                    echo json_encode(array("message" =>  validation_errors()));
                    return;
                }

                break;
        }
    }
	
	public function edit_user(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST = json_decode(file_get_contents("php://input"), true);
				$this->data['title'] = $this->lang->line('edit_user_heading');

				$userId = $this->input->post('userId');

				$user = $this->hr_auth->user($userId)->row();
				$groups = $this->hr_auth->groups()->result_array();
				$currentGroups = $this->hr_auth->get_users_groups($userId)->result();

				// validate form input
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('firstName', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
				$this->form_validation->set_rules('lastName', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
				$this->form_validation->set_rules('designation', $this->lang->line('edit_user_validation_company_label'), 'trim|required');
				$this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'trim|required');
				$this->form_validation->set_rules('store', 'Store', 'trim|required');
				$this->form_validation->set_rules('email', 'Email', 'trim|required');
				$this->form_validation->set_rules('phoneNumber', $this->lang->line('edit_user_validation_phone_label'), 'trim|required');
				
				if ($this->input->post('password')) {
					$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'hr_auth') . ']|matches[confirmPassword]');
					$this->form_validation->set_rules('confirmPassword', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
				}

				if ($this->form_validation->run() === TRUE) {
					$data = [
						// 'username' => $this->input->post('email'),
						'email' => $this->input->post('email'),
					];

					// update the password if it was posted
					if ($this->input->post('password')) {
						$data['password'] = $this->input->post('password');
					}

					// Only allow updating groups if user is admin
					if ($this->hr_auth->is_admin()) {
						// Update the groups user belongs to
						$this->hr_auth->remove_from_group('', $userId);

						$groupData = $this->input->post('groups');
						if (isset($groupData) && !empty($groupData)) {
							foreach ($groupData as $grp) {
								$this->hr_auth->add_to_group($grp, $userId);
							}
						}
					}


					// check to see if we are updating the user
					if ($this->hr_auth->update($user->id, $data)) {
						$userProfile = array(
							'first_name' => $this->input->post('firstName'),
							'last_name' => $this->input->post('lastName'),
							'designation' => $this->input->post('designation'),
							'email' => $this->input->post('email'),
							'phone_number' => $this->input->post('phoneNumber'),
						);

						$this->hr_model->updateUser($user->id, $userProfile);
						$this->hr_model->updateStore($user->id, $this->input->post('store'));
						$this->hr_model->updateCompany($user->id, $this->input->post('company'));

						header('content-type: application/json');
						echo json_encode(array("message" =>  $this->hr_auth->messages()));
						return;
					} else {

						$this->output->set_status_header('401');
						header('content-type: application/json');
						echo json_encode(array("message" => validation_errors()));
						return;
					}
				}else{

				// 	// set the flash data error message if there is one
				// 	$this->data['message'] = (validation_errors() ? validation_errors() : ($this->hr_auth->errors() ? $this->hr_auth->errors() : $this->session->flashdata('message')));

				// 	$this->data['email'] = [
				// 		'name'  => 'email',
				// 		'id'    => 'email',
				// 		'type'  => 'text',
				// 		'value' => $this->form_validation->set_value('email', $user->first_name),
				// 	];

				// 	$this->data['password'] = [
				// 		'name' => 'password',
				// 		'id'   => 'password',
				// 		'type' => 'password'
				// 	];
				// 	$this->data['confirmPassword'] = [
				// 		'name' => 'confirmPassword',
				// 		'id'   => 'confirmPassword',
				// 		'type' => 'password'
				// 	];

				// 	$this->output->set_status_header('401');
				// 	header('content-type: application/json');
				// 	echo json_encode(array("message" => validation_errors()));
				// 	return;
				}
		}

	}
    
	public function create_user(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$_POST =  json_decode(file_get_contents("php://input"), true);
				$this->data['title'] = $this->lang->line('create_user_heading');
		
				// validate form input
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[users.email]');
				$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'hr_auth') . ']|matches[confirmPassword]');
				$this->form_validation->set_rules('confirmPassword', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

				$this->form_validation->set_rules('firstName', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
				$this->form_validation->set_rules('lastName', $this->lang->line('create_user_validation_lname_label'), 'trim|required');;
				$this->form_validation->set_rules('designation', 'Designation', 'trim');
				$this->form_validation->set_rules('store', 'Store ', 'trim');
				$this->form_validation->set_rules('phoneNumber', $this->lang->line('create_user_validation_phone_label'), 'trim');
				$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
	
				
				if ($this->form_validation->run() === TRUE) {
					
					$email = strtolower($this->input->post('email'));
					$password = $this->input->post('password');

					$user_id = $this->hr_auth->register($email, $password, $email);

					if($user_id !== FALSE){
						header('content-type: application/json');
						echo json_encode(array("message" =>  $this->hr_auth->messages()));
						return;
					}else{
				        $this->output->set_status_header(401);
                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->hr_auth->errors()));
                        return;
					}
				} else {
					// display the create user form
					// set the flash data error message if there is one
					$this->data['message'] = (validation_errors() ? validation_errors() : ($this->hr_auth->errors() ? $this->hr_auth->errors() : $this->session->flashdata('message')));
		
					$this->data['email'] = [
						'name' => 'email',
						'id' => 'email',
						'type' => 'text',
						'value' => $this->form_validation->set_value('email'),
					];

					$this->data['password'] = [
						'name' => 'password',
						'id' => 'password',
						'type' => 'password',
						'value' => $this->form_validation->set_value('password'),
					];
					
					$this->data['confirmPassword'] = [
						'name' => 'confirmPassword',
						'id' => 'confirmPassword',
						'type' => 'password',
						'value' => $this->form_validation->set_value('confirmPassword'),
					];

					$this->data['firstName'] = [
						'name' => 'firstName',
						'id' => 'firstName',
						'type' => 'text',
						'value' => $this->form_validation->set_value('firstName'),
					];

					$this->data['lastName'] = [
						'name' => 'lastName',
						'id' => 'lastName',
						'type' => 'text',
						'value' => $this->form_validation->set_value('lastName'),
					];

					$this->data['designation'] = [
						'name' => 'designation',
						'id' => 'designation',
						'type' => 'text',
						'value' => $this->form_validation->set_value('designation'),
					];

					$this->data['company'] = [
						'name' => 'company',
						'id' => 'company',
						'type' => 'text',
						'value' => $this->form_validation->set_value('company'),
					];

					$this->data['store'] = [
						'name' => 'store',
						'id' => 'store',
						'type' => 'text',
						'value' => $this->form_validation->set_value('store'),
					];

					$this->data['phoneNumber'] = [
						'name' => 'phoneNumber',
						'id' => 'phoneNumber',
						'type' => 'text',
						'value' => $this->form_validation->set_value('phoneNumber'),
					];
		
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => validation_errors()));
				}
				return;
			
		}
	}
	
	public function _get_csrf_nonce(){
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return [$key => $value];
	}

	public function _valid_csrf_nonce(){
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
			return TRUE;
		}
		return FALSE;
	}
    
	public function logout(){
		$this->data['title'] = "Logout";
		$this->hr_auth->logout();
        
        header('content-type: application/json');
        echo json_encode(array("message" => 'Successfully logout user'));
        return;
	}
}