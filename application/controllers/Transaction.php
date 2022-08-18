<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Transaction extends CI_Controller {
    public function shop(){
        switch($this->input->server('REQUEST_METHOD')){
            case 'POST':
                $hash_key = substr(md5(uniqid(mt_rand(), true)), 0, 20);
                $tracking_no = substr(md5(uniqid(mt_rand(), true)), 0, 6);
                // $query_result = $this->transaction_model->insert_client_details($hash_key);
                

                break;
        }
    }
}