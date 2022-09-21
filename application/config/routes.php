<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//Shop
$route['shop/product'] = 'shop/product';
$route['shop/products'] = 'shop/products';
$route['shop/orders'] = 'shop/orders';
$route['shop/get_product_sku'] = 'shop/get_product_sku';

//Catering
$route['catering/products'] = 'catering/products';
$route['catering/product'] = 'catering/product';
$route['catering/orders'] = 'catering/orders';
$route['catering/upload_contract'] = 'catering/upload_contract';

// POPCLUB 
$route['popclub/platform'] = 'popclub/platform';
$route['popclub/category'] = 'popclub/category';

$route['popclub/redeem'] = 'popclub/redeem';
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
$route['shared/upload_payment'] = 'shared/upload_payment';
$route['shared/catering_upload_payment'] = 'shared/catering_upload_payment';
$route['shared/contacts'] = 'shared/contacts';

// Facebook
$route['facebook/login'] = 'user_authentication';
$route['facebook/login_point'] = 'user_authentication/fb_login_point';
$route['facebook/login/success'] = 'user_authentication/success_login';
$route['facebook/logout'] = 'user_authentication/logout';

// Mobile Users
$route['mobile_users/login_mobile_user'] = 'mobile_users/login_mobile_user';
$route['mobile_users/registration'] = 'mobile_users/registration';
$route['mobile_users/mobile_generate_forgot_pass_code'] = 'mobile_users/mobile_generate_forgot_pass_code';
$route['mobile_users/mobile_resend_forgot_pass_code'] = 'mobile_users/mobile_resend_forgot_pass_code';
$route['mobile_users/validate_otp_code'] = 'mobile_users/validate_otp_code';
$route['mobile_users/change_password'] = 'mobile_users/change_password';

// Profile
$route['profile/snackshop-orders'] = 'profile/snackshop_orders';
$route['profile/catering-bookings'] = 'profile/catering_bookings';
$route['profile/contact/(:num)'] = 'profile/contact/$1';

// Store
$route['store'] = 'store';
$route['store/reset'] = 'store/reset';

// Branches
$route['branches'] = 'branches';

//Cart
$route['cart/shop'] = 'cart/shop';
$route['cart/catering'] = 'cart/catering';

//Transactions
$route['transaction/shop'] = 'transaction/shop';
$route['transaction/catering'] = 'transaction/catering';

//Download
$route['download/contract/(:any)'] = 'download/contract/$1';

//Image
$route['load-image/(:any)'] = 'image/load_image/$1';	

//Auth
$route['auth/login'] = 'auth/login';
$route['auth/logout'] = 'auth/logout';
$route['auth/create-user'] = 'auth/create_user';
$route['auth/edit-user/(:any)'] = 'auth/edit_user/$1';
$route['auth/create-group'] = 'auth/create_group';

//Admin
$route['admin/stores'] = 'admin/stores';
$route['admin/session'] = 'admin/session';
$route['admin/shop/(:any)'] = 'admin/shop_order/$1';
$route['admin/shop'] = 'admin/shop';
$route['admin/popclub/(:any)/complete'] = 'admin/popclub_complete_redeem/$1';
$route['admin/popclub/(:any)'] = 'admin/popclub_redeem/$1';
$route['admin/popclub'] = 'admin/popclub';
$route['admin/users'] = 'admin/users';
$route['admin/user/(:any)'] = 'admin/user/$1';
$route['admin/groups'] = 'admin/groups';

//Others
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
