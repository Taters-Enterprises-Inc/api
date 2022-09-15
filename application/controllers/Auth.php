<?php defined('BASEPATH') or exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
 * @property CI_Form_validation      $form_validation The form validation library
 */
class Auth extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('auth');
    }
    
    public function login(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
		        $this->data['title'] = $this->lang->line('login_heading');
                $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
                $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');
                
		        if ($this->form_validation->run() === TRUE) {
                    $remember = (bool) $this->input->post('remember');

                    if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {

                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->ion_auth->messages()));
                        return;
                    } else {

				        $this->output->set_status_header(401);
                        header('content-type: application/json');
                        echo json_encode(array("message" =>  $this->ion_auth->errors()));
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
    
	public function logout()
	{
		$this->data['title'] = "Logout";
		$this->ion_auth->logout();
        
        header('content-type: application/json');
        echo json_encode(array("message" => 'Successfully logout user'));
        return;
	}

}