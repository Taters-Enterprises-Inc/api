<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('snap'))
{
    function snap($user,$reference_id,$action,$details = '')
    {
        // Get a reference to the controller object
        $CI = get_instance();

        // You may need to load the model if it hasn't been pre-loaded
        $CI->load->model('logs_model');

        $set_values = array(
            'user'          => $user,
            'reference_id'  => $reference_id,
            'action'        => $action,
            'details'       => $details
        );

        // Call a function of the model
        return $CI->logs_model->insert_log($set_values);
    }   
}

if ( ! function_exists('snap_view'))
{
    function snap_view($conditons= array())
    {
        // Get a reference to the controller object
        $CI = get_instance();

        // You may need to load the model if it hasn't been pre-loaded
        $CI->load->model('logs_model');

        // Call a function of the model
        return $CI->logs_model->fetch_logs($conditons);
    }   
}