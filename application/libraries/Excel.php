<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('PHPExcel.php');

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

date_default_timezone_set('Asia/Manila');

class Excel extends PHPExcel
{
	public function __construct(){
		parent::__construct();
    }
}