<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('notify'))
{
    function notify($subscribe_to, $channel, $data){
        $CI = get_instance();

        require FCPATH . 'vendor/autoload.php';
        
        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );
        $pusher = new Pusher\Pusher(
            '8a62b17c8a9baa690edb',
            '0e16bc6f7b22f371826b',
            '1188170',
            $options
        );
        $pusher->trigger($subscribe_to, $channel, $data);
    }   
}