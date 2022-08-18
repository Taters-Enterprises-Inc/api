<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Google
{
    
    public static function get_distance($s_address, $s_customer_address)
    {
        $apikey = 'AIzaSyAi3QDkRTVGFyD4vuUS0lEx080Nm6GNsI8';
        // $distance = urlencode($venue['address']);
        $distance = urlencode($s_address);
        // $customer_location = urlencode($venue['customer_address']);
        $customer_location = urlencode($s_customer_address);
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $distance . '&destinations=' . $customer_location . '&key=' . $apikey;
        // New Code for getting content
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        $decodedText = html_entity_decode($response);
        $myArray = json_decode($decodedText, true);
        $xmlValue   = $myArray['rows'][0]['elements'][0]['distance']['text'];
        $xmlStatus  = $myArray['rows'][0]['elements'][0]['status'];


        if (explode(' ', $xmlValue)[1] == 'm') {
            $disValue = preg_replace('/[^0-9\.]/', '', $xmlValue);
            $newXmlValue = ((int) $disValue) * 0.001;
        } else {
            $newXmlValue = preg_replace('/[^0-9\.]/', '', $xmlValue);
        }
        // if($newXmlValue >= 30){
        //     continue;
        return ($newXmlValue);
    }
    
    public function geolocator($address){
        // Google API
        // cURL Method
        // execute query distance computation
        // return distance and status

        $api_key = "AIzaSyAi3QDkRTVGFyD4vuUS0lEx080Nm6GNsI8";

        //decode address from ajax javascript encoding
        $decode_address         = urldecode($address);

        //decode raw url address to pass on session variable (format that PHP/codeigniter can understand)
        $decode_raw_url_address = rawurldecode($decode_address);

        //convert encoding to UTF-8
        $covert_endcoding       = mb_convert_encoding($decode_raw_url_address, 'UTF-8');
		
        //encode address to pass as parameter to geocode address=
        $encoded_address        = urlencode($covert_endcoding);

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$encoded_address.'&key='.$api_key;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        $response_json = html_entity_decode($response);
        $geo = json_decode($response_json,true);

        $location_result = $geo["results"][0]["geometry"]["location"] ;
        return $location_result;
    }

}
