<?php defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Auth_bsc extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->bsc_auth->set_message_delimiters('', '');
        $this->bsc_auth->set_error_delimiters('', '');

        $this->lang->load('auth');
    }
	
	public function create_group(){
		$this->data['title'] = $this->lang->line('create_group_title');

		if (!$this->bsc_auth->logged_in() || !$this->bsc_auth->is_admin()) {
			$this->output->set_status_header('401');
			header('content-type: application/json');
			echo json_encode(array("message" => 'Unauthorized user'));
			return;
		}

		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');

		if ($this->form_validation->run() === TRUE) {
			$new_group_id = $this->bsc_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
			if ($new_group_id) {
				
				header('content-type: application/json');
				echo json_encode(array("message" =>  $this->bsc_auth->messages()));
				return;
			} else {

				$this->output->set_status_header(401);
				header('content-type: application/json');
				echo json_encode(array("message" => validation_errors()));
				return;
			}
		}

		// display the create group form
		// set the flash data error message if there is one
		$this->data['message'] = (validation_errors() ? validation_errors() : ($this->bsc_auth->errors() ? $this->bsc_auth->errors() : $this->session->flashdata('message')));

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
    
    public function login(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
		        $this->data['title'] = $this->lang->line('login_heading');
                $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
                $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
                
		        if ($this->form_validation->run() === TRUE) {
                    $remember = (bool) $this->input->post('remember');

                    if ($this->bsc_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {

                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->bsc_auth->messages()));
                        return;
                    } else {

				        $this->output->set_status_header(401);
                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->bsc_auth->errors()));
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
	
	public function edit_user($id){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$post = json_decode(file_get_contents("php://input"), true);
				$this->data['title'] = $this->lang->line('edit_user_heading');

				if (!$this->bsc_auth->logged_in() || (!$this->bsc_auth->is_admin() && !($this->bsc_auth->user()->row()->id == $id))) {
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => 'Unauthorized user'));
					return;
				}
				$user = $this->bsc_auth->user($id)->row();
				$groups = $this->bsc_auth->groups()->result_array();
				$currentGroups = $this->bsc_auth->get_users_groups($id)->result();
				

				// validate form input
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
				$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
				$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'trim');
				$this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'trim');
				

				if (isset($_POST) && !empty($_POST)) {
					// update the password if it was posted
					if ($this->input->post('password')) {
						$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'bsc_auth') . ']|matches[password_confirm]');
						$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
					}

					if ($this->form_validation->run() === TRUE) {
						$data = [
							'first_name' => $this->input->post('first_name'),
							'last_name' => $this->input->post('last_name'),
							'company' => $this->input->post('company'),
							'phone' => $this->input->post('phone'),
						];

						// update the password if it was posted
						if ($this->input->post('password')) {
							$data['password'] = $this->input->post('password');
						}

						// Only allow updating groups if user is admin
						if ($this->bsc_auth->is_admin()) {
							// Update the groups user belongs to
							$this->bsc_auth->remove_from_group('', $id);

							$groupData = $this->input->post('groups');
							if (isset($groupData) && !empty($groupData)) {
								foreach ($groupData as $grp) {
									$this->bsc_auth->add_to_group($grp, $id);
								}
							}
						}

						// check to see if we are updating the user
						if ($this->bsc_auth->update($user->id, $data)) {
							
							header('content-type: application/json');
							echo json_encode(array("message" =>  $this->bsc_auth->messages()));
							return;
						} else {

							$this->output->set_status_header('401');
							header('content-type: application/json');
							echo json_encode(array("message" => validation_errors()));
							return;
						}
					}
				}
				

				// display the edit user form
				$this->data['csrf'] = $this->_get_csrf_nonce();

				// set the flash data error message if there is one
				$this->data['message'] = (validation_errors() ? validation_errors() : ($this->bsc_auth->errors() ? $this->bsc_auth->errors() : $this->session->flashdata('message')));

				// pass the user to the view
				$this->data['user'] = $user;
				$this->data['groups'] = $groups;
				$this->data['currentGroups'] = $currentGroups;

				$this->data['first_name'] = [
					'name'  => 'first_name',
					'id'    => 'first_name',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('first_name', $user->first_name),
				];
				$this->data['last_name'] = [
					'name'  => 'last_name',
					'id'    => 'last_name',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('last_name', $user->last_name),
				];
				$this->data['company'] = [
					'name'  => 'company',
					'id'    => 'company',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('company', $user->company),
				];
				$this->data['phone'] = [
					'name'  => 'phone',
					'id'    => 'phone',
					'type'  => 'text',
					'value' => $this->form_validation->set_value('phone', $user->phone),
				];
				$this->data['password'] = [
					'name' => 'password',
					'id'   => 'password',
					'type' => 'password'
				];
				$this->data['password_confirm'] = [
					'name' => 'password_confirm',
					'id'   => 'password_confirm',
					'type' => 'password'
				];
				$this->output->set_status_header('401');
				header('content-type: application/json');
				echo json_encode(array("message" => validation_errors()));
				return;
		}

	}
    
	public function create_user(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
				$this->data['title'] = $this->lang->line('create_user_heading');

				if (!$this->bsc_auth->logged_in() || !$this->bsc_auth->is_admin()) {
					$this->output->set_status_header('401');
					header('content-type: application/json');
					echo json_encode(array("message" => 'Unauthorized user'));
					return;
				}
		
		
				// validate form input
				$this->form_validation->set_error_delimiters('', '');
				$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
				$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
				$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[users.email]');
				$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
				$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
				$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'bsc_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
		
				if ($this->form_validation->run() === TRUE) {
					$email = strtolower($this->input->post('email'));
					$password = $this->input->post('password');
		
					$additional_data = [
						'first_name' => $this->input->post('first_name'),
						'last_name' => $this->input->post('last_name'),
						'company' => $this->input->post('company'),
						'phone' => $this->input->post('phone'),
					];
				}
				if ($this->form_validation->run() === TRUE && $this->bsc_auth->register($email, $password, $email, $additional_data)) {
					// check to see if we are creating the user
					// redirect them back to the admin page
					header('content-type: application/json');
					echo json_encode(array("message" =>  $this->bsc_auth->messages()));
					redirect("auth", 'refresh');
				} else {
					// display the create user form
					// set the flash data error message if there is one
					$this->data['message'] = (validation_errors() ? validation_errors() : ($this->bsc_auth->errors() ? $this->bsc_auth->errors() : $this->session->flashdata('message')));
		
					$this->data['first_name'] = [
						'name' => 'first_name',
						'id' => 'first_name',
						'type' => 'text',
						'value' => $this->form_validation->set_value('first_name'),
					];
					$this->data['last_name'] = [
						'name' => 'last_name',
						'id' => 'last_name',
						'type' => 'text',
						'value' => $this->form_validation->set_value('last_name'),
					];
					$this->data['identity'] = [
						'name' => 'identity',
						'id' => 'identity',
						'type' => 'text',
						'value' => $this->form_validation->set_value('identity'),
					];
					$this->data['email'] = [
						'name' => 'email',
						'id' => 'email',
						'type' => 'text',
						'value' => $this->form_validation->set_value('email'),
					];
					$this->data['company'] = [
						'name' => 'company',
						'id' => 'company',
						'type' => 'text',
						'value' => $this->form_validation->set_value('company'),
					];
					$this->data['phone'] = [
						'name' => 'phone',
						'id' => 'phone',
						'type' => 'text',
						'value' => $this->form_validation->set_value('phone'),
					];
					$this->data['password'] = [
						'name' => 'password',
						'id' => 'password',
						'type' => 'password',
						'value' => $this->form_validation->set_value('password'),
					];
					$this->data['password_confirm'] = [
						'name' => 'password_confirm',
						'id' => 'password_confirm',
						'type' => 'password',
						'value' => $this->form_validation->set_value('password_confirm'),
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
		$this->bsc_auth->logout();
        
        header('content-type: application/json');
        echo json_encode(array("message" => 'Successfully logout user'));
        return;
	}
}