<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// POPCLUB 
$route['popclub/platform'] = 'popclub/platform';
$route['popclub/category'] = 'popclub/category';

$route['popclub/redeem_deal'] = 'popclub/redeem_deal';
$route['popclub/popclub_data'] = 'popclub/popclub_data';
$route['popclub/session'] = 'popclub/session';
$route['popclub/check_product_variant_deals'] = 'popclub/check_product_variant_deals';
$route['popclub/clear_all_session'] = 'popclub/clear_all_session';

$route['popclub/deal/(:any)'] = 'popclub/deal/$1';
$route['popclub/(:any)'] = 'popclub/deals/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['facebook/login'] = 'user_authentication';
$route['facebook/login_point'] = 'user_authentication/fb_login_point';
$route['facebook/login/success'] = 'user_authentication/success_login';
$route['facebook/logout'] = 'user_authentication/logout';


$route['store'] = 'store';
