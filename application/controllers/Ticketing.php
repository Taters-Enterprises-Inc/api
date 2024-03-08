<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Ticketing extends CI_Controller
{
	public function __construct(){
		parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('excel');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->bsc_auth->set_message_delimiters('', '');
        $this->bsc_auth->set_error_delimiters('', '');

        $this->lang->load('auth');
		//$this->load->model('stock_ordering_model');
        $this->load->model('ticketing_model');
	}

    public function tickets(){

        // FOR TESTING PURPOSES ONLY
        // echo "This is working!";
        // $ticket = $this->ticketing_model->getTickets();
        // print_r($ticket);
        // die();

        switch($this->input->server('REQUEST_METHOD')){
            case 'GET':

            $ticket = $this->ticketing_model->getTickets();

            $data = array(
                "tickets" => $ticket,
            );

            $response = array(
                "message" => 'Successfully fetch all tickets',
                "data"    => $data, 
            );
            
            header('content-type: application/json');
            echo json_encode($response);
            break;
        }
    }
}