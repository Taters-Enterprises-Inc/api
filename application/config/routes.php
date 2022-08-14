<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//Shop
$route['shop/products'] = 'shop/products';

// POPCLUB 
$route['popclub/platform'] = 'popclub/platform';
$route['popclub/category'] = 'popclub/category';

$route['popclub/redeem'] = 'popclub/redeem';
$route['popclub/delete_redeem'] = 'popclub/delete_redeem';
$route['popclub/redeems'] = 'popclub/redeems';
$route['popclub/redeem_deal'] = 'popclub/redeem_deal';
$route['popclub/popclub_data'] = 'popclub/popclub_data';
$route['popclub/check_product_variant_deals'] = 'popclub/check_product_variant_deals';

$route['popclub/deal/(:any)'] = 'popclub/deal/$1';
$route['popclub/(:any)'] = 'popclub/deals/$1';

//Shared
$route['shared/session'] = 'shared/session';
$route['shared/clear_redeems'] = 'shared/clear_redeems';
$route['shared/clear_all_session'] = 'shared/clear_all_session';

// Facebook
$route['facebook/login'] = 'user_authentication';
$route['facebook/login_point'] = 'user_authentication/fb_login_point';
$route['facebook/login/success'] = 'user_authentication/success_login';
$route['facebook/logout'] = 'user_authentication/logout';

// Store
$route['store'] = 'store';

//Others
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
