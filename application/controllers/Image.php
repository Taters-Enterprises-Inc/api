<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function load_image($file_name)
	{	
		$file_extension = strtolower(substr(strrchr($file_name,"."),1));

		switch( $file_extension ) {
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpeg"; break;
			case "svg": $ctype="image/svg+xml"; break;
			default:
		}

		header('Content-type: ' . $ctype);
		// $url = img_url().$file_name;
		// $url = upload_url().'payment_proof/'.$file_name;
		$url = 'https://site.test/staging/api/assets/upload/proof_payment/'.$file_name;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$res = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		curl_close($ch) ;
		echo $res;
	}

	public function load_catering_image($file_name)
	{	
		$file_extension = strtolower(substr(strrchr($file_name,"."),1));

		switch( $file_extension ) {
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpeg"; break;
			case "svg": $ctype="image/svg+xml"; break;
			default:
		}

		header('Content-type: ' . $ctype);
		// $url = img_url().$file_name;
		// $url = upload_url().'payment_proof/'.$file_name;
		$url = upload_url().'catering_proof_payment/'.$file_name;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$res = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		curl_close($ch) ;
		echo $res;
	}

	public function load_catering_image_contract($file_name)
	{	
		$file_extension = strtolower(substr(strrchr($file_name,"."),1));

		switch( $file_extension ) {
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpeg"; break;
			case "svg": $ctype="image/svg+xml"; break;
			default:
		}

		header('Content-type: ' . $ctype);
		// $url = img_url().$file_name;
		// $url = upload_url().'payment_proof/'.$file_name;
		$url = upload_url().'catering_upload_contract/'.$file_name;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$res = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		curl_close($ch) ;
		echo $res;
	}

}

/* End of file Image.php */
/* Location: ./application/controllers/Image.php */