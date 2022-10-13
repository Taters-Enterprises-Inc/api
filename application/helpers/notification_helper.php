<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('notify'))
{
    function notify($subscribe_to, $channel, $data){
        $CI = get_instance();

        require FCPATH . 'vendor/autoload.php';
        
        $dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
        $dotenv->load();
        
        $options = array(
            'cluster' => $_ENV['PUSHER_CLUSTERR'],
            'useTLS' => true
        );
        $pusher = new Pusher\Pusher(
            $_ENV['PUSHER_KEY'],
            $_ENV['PUSHER_SECRET'],
            $_ENV['PUSHER_APP_ID'],
            $options
        );
        $pusher->trigger($subscribe_to, $channel, $data);
    }   
}