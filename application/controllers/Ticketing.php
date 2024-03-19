<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Ticketing extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('excel');
        $this->load->helper(['url', 'language']);

        $this->form_validation->set_error_delimiters('', '');
        $this->bsc_auth->set_message_delimiters('', '');
        $this->bsc_auth->set_error_delimiters('', '');

        $this->lang->load('auth');
        $this->load->model('ticketing_model');
    }

    public function tickets()
    {
        // USE THIS CODE FOR TESTING
        // echo "This is working!";
        // $ticket = $this->ticketing_model->getTickets();
        // print_r($ticket);
        // die();

        switch ($this->input->server('REQUEST_METHOD')) {
            case 'GET':
                $per_page = $this->input->get('per_page') ?? 25;
                $page_no = $this->input->get('page_no') ?? 0;
                $status = $this->input->get('status') ?? null;
                $order = $this->input->get('order') ?? 'desc';
                $order_by = $this->input->get('order_by') ?? 'id'; // 👈 change 'id' to change the default ordering
                $search = $this->input->get('search');

                if ($page_no != 0) {
                    $page_no = ($page_no - 1) * $per_page;
                }

                $tickets_count = $this->ticketing_model->getAllTicketsCount($status, $search);
                $tickets = $this->ticketing_model->getAllTickets($page_no, $per_page, $status, $order_by, $order, $search);

                $pagination = array(
                    "total_rows" => $tickets_count,
                    "per_page" => $per_page,
                );

                $response = array(
                    "message" => 'Successfully fetch all tickets',
                    "data"    => array(
                        "pagination" => $pagination,
                        "tickets" => $tickets,
                    ),
                );

                header('content-type: application/json');
                echo json_encode($response);
                return;
        }
    }

    public function my_tickets()
    {
        switch ($this->input->server('REQUEST_METHOD')) {
            case 'GET':
                $per_page = $this->input->get('per_page') ?? 25;
                $page_no = $this->input->get('page_no') ?? 0;
                $status = $this->input->get('status') ?? null;
                $order = $this->input->get('order') ?? 'desc';
                $order_by = $this->input->get('order_by') ?? 'id'; // 👈 change 'id' to change the default ordering
                $search = $this->input->get('search');

                if ($page_no != 0) {
                    $page_no = ($page_no - 1) * $per_page;
                }

                $tickets_count = $this->ticketing_model->getMyTicketsCount($status, $search);
                $tickets = $this->ticketing_model->getMyTickets($page_no, $per_page, $status, $order_by, $order, $search);

                $pagination = array(
                    "total_rows" => $tickets_count,
                    "per_page" => $per_page,
                );

                $response = array(
                    "message" => 'Successfully fetch my tickets',
                    "data"    => array(
                        "pagination" => $pagination,
                        "tickets" => $tickets,
                    ),
                );

                header('content-type: application/json');
                echo json_encode($response);
                return;
        }
    }

    public function submit_ticket()
    {
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST': 
                $_POST =  json_decode(file_get_contents("php://input"), true);
                
                $department_id = $this->input->post('departmentId');
                if ($department_id === null) {
                    $department_id = 0;
                }

                $ticket_data = array(
                    'department_id' => $department_id,
                    'status'        => 1,
                );

                $ticket_id = $this->ticketing_model->insertTicket($ticket_data);

                $ticket_information = array(
                    'ticket_id'    => $ticket_id,
                    'ticket_title'  => $this->input->post('ticketTitle'),
                    'ticket_details' => $this->input->post('ticketDetails'),
                );

                $this->ticketing_model->insertTicketInformation($ticket_information);

                $response = array(
                    "message" => 'Successfully added a new ticket',
                );

                header('content-type: application/json');
                echo json_encode($response);

            break;
        }
    }
}
