<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// POPCLUB 
$route['popclub/platform'] = 'popclub/platform';
$route['popclub/category'] = 'popclub/category';

$route['popclub/popclub_data'] = 'popclub/popclub_data';
$route['popclub/session'] = 'popclub/session';
$route['popclub/clear_all_session'] = 'popclub/clear_all_session';


$route['popclub/deal/(:any)'] = 'popclub/deal/$1';
$route['popclub/(:any)'] = 'popclub/deals/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['store'] = 'store';
