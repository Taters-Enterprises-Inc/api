<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//Shop
$route['shop/product'] = 'shop/product';
$route['shop/products'] = 'shop/products';
$route['shop/orders'] = 'shop/orders';
$route['shop/deals'] = 'shop/deals';
$route['shop/get_product_sku'] = 'shop/get_product_sku';

//Catering
$route['catering/packages'] = 'catering/packages';
$route['catering/package'] = 'catering/package';
$route['catering/products'] = 'catering/products';
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

$route['popclub/redeem-validators'] = 'popclub/redeem_validators';
$route['popclub/orders'] = 'popclub/orders';

$route['popclub/deal/(:any)'] = 'popclub/deal/$1';
$route['popclub/(:any)'] = 'popclub/deals/$1';

//Shared
$route['shared/session'] = 'shared/session';
$route['shared/clear_redeems'] = 'shared/clear_redeems';
$route['shared/clear_all_session'] = 'shared/clear_all_session';
$route['shared/upload_payment'] = 'shared/upload_payment';
$route['shared/catering_upload_payment'] = 'shared/catering_upload_payment';
$route['shared/contacts'] = 'shared/contacts';
$route['shared/available-user-discount'] = 'shared/available_user_discount';
$route['shared/stores'] = 'shared/stores';
$route['shared/companies'] = 'shared/companies';

// Facebook
$route['facebook/login'] = 'user_authentication';
$route['facebook/login_point'] = 'user_authentication/fb_login_point';
$route['facebook/login/success'] = 'user_authentication/success_login';
$route['facebook/logout'] = 'user_authentication/logout';

// Mobile Users
$route['mobile_users/login_mobile_user'] = 'mobile_users/login_mobile_user';
$route['mobile_users/registration'] = 'mobile_users/registration';
$route['mobile_users/mobile_generate_forgot_pass_code'] = 'mobile_users/mobile_generate_forgot_pass_code';
$route['mobile_users/validate_otp_code'] = 'mobile_users/validate_otp_code';
$route['mobile_users/change_password'] = 'mobile_users/change_password';

// Profile
$route['profile/popclub-redeems'] = 'profile/popclub_redeems';
$route['profile/snackshop-orders'] = 'profile/snackshop_orders';
$route['profile/catering-bookings'] = 'profile/catering_bookings';
$route['profile/user-discount'] = 'profile/user_discount';
$route['profile/update-user-discount'] = 'profile/update_user_discount';
$route['profile/contact/(:num)'] = 'profile/contact/$1';

// Store
$route['store'] = 'store';
$route['store/product'] = 'store/product';
$route['store/reset'] = 'store/reset';

// Branches
$route['branches'] = 'branches';

//Cart
$route['cart/shop'] = 'cart/shop';
$route['cart/catering'] = 'cart/catering';
$route['cart/catering-product'] = 'cart/catering_product';

//Transactions
$route['transaction/shop'] = 'transaction/shop';
$route['transaction/catering'] = 'transaction/catering';

//Download
$route['download/contract/(:any)'] = 'download/contract/$1';

//Image
$route['load-image/(:any)'] = 'image/load_image/$1';	
$route['load-image-catering/(:any)'] = 'image/load_catering_image/$1';	
$route['load-image-catering-contract/(:any)'] = 'image/load_catering_image_contract/$1';
$route['load-image-user-discount/(:any)'] = 'image/load_image_user_discount/$1';
$route['load-image-product/(:any)'] = 'image/load_image_product/$1';


//Admin
$route['auth/login'] = 'auth/login';
$route['auth/logout'] = 'auth/logout';
$route['auth/create-user'] = 'auth/create_user';
$route['auth/edit-user/(:any)'] = 'auth/edit_user/$1';
$route['auth/create-group'] = 'auth/create_group';

$route['admin/stores'] = 'admin/stores';
$route['admin/products'] = 'admin/products';
$route['admin/store'] = 'admin/store';
$route['admin/store-operating-hours'] = 'admin/store_operating_hours';
$route['admin/session'] = 'admin/session';
$route['admin/payment'] = 'admin/payment';
$route['admin/reference-num'] = 'admin/reference_num';
$route['admin/admin-privilege'] = 'admin/admin_privilege';
$route['admin/admin-catering-privilege'] = 'admin/admin_catering_privilege';

$route['admin/catering-update-status'] = 'admin/catering_update_status';
$route['admin/catering'] = 'admin/catering';
$route['admin/catering/(:any)'] = 'admin/catering_order/$1';
$route['admin/catering-update-order-item-remarks'] = 'admin/catering_update_order_item_remarks';
$route['admin/catering-package-flavors/(:num)'] = 'admin/catering_package_flavors/$1';

$route['admin/print_view/(:any)'] = 'admin/print_view/$1';
$route['admin/print_asdoc/(:any)'] = 'admin/print_asdoc/$1';
$route['admin/shop-update-status'] = 'admin/shop_update_status';
$route['admin/shop/(:any)'] = 'admin/shop_order/$1';
$route['admin/shop'] = 'admin/shop';

$route['admin/popclub/complete-redeem'] = 'admin/popclub_complete_redeem';
$route['admin/popclub/decline-redeem'] = 'admin/popclub_decline_redeem';
$route['admin/popclub/(:any)'] = 'admin/popclub_redeem/$1';
$route['admin/popclub'] = 'admin/popclub';

$route['admin/discount/user-discount-change-status'] = 'admin/user_discount_change_status';
$route['admin/discount/(:any)'] = 'admin/discount/$1';
$route['admin/discounts'] = 'admin/discounts';

$route['admin/availability/deal'] = 'admin/deal_availability';
$route['admin/availability/product'] = 'admin/product_availability';
$route['admin/availability/caters-product'] = 'admin/caters_product_availability';
$route['admin/availability/caters-package'] = 'admin/caters_package_availability';
$route['admin/availability/caters-package-addon'] = 'admin/caters_package_addon_availability';
$route['admin/availability/caters-product-addon'] = 'admin/caters_product_addon_availability';

$route['admin/deal-categories'] = 'admin/deal_categories';
$route['admin/product-categories'] = 'admin/product_categories';
$route['admin/caters-package-categories'] = 'admin/caters_package_categories';

$route['admin/snackshop-transaction-logs/(:num)'] = 'admin/snackshop_transaction_logs/$1';
$route['admin/catering-transaction-logs/(:num)'] = 'admin/catering_transaction_logs/$1';

$route['admin/notifications'] = 'admin/notifications';
$route['admin/notification/(:num)/seen'] = 'admin/notification_seen/$1';

$route['admin/report-pmix/(:any)/(:any)'] = 'admin/report_pmix/$1/$2';
$route['admin/report-transaction/(:any)/(:any)'] = 'admin/report_transaction/$1/$2';

$route['admin/survey-verification/survey-verification-change-status'] = 'admin/survey_verification_change_status';
$route['admin/survey-verification/(:any)']= 'admin/survey_verification/$1';
$route['admin/survey-verifications'] = 'admin/survey_verifications';

$route['admin/setting/stores'] = 'admin/setting_stores';
$route['admin/setting/users'] = 'admin/setting_users';
$route['admin/setting/user/(:any)'] = 'admin/setting_user/$1';
$route['admin/setting/groups'] = 'admin/setting_groups';
$route['admin/setting/shop-products'] = 'admin/setting_shop_products';
$route['admin/setting/shop-product'] = 'admin/setting_shop_product';
$route['admin/setting/shop-product/type'] = 'admin/setting_shop_product_type';
$route['admin/setting/edit-shop-product'] = 'admin/setting_edit_shop_product';
$route['admin/setting/delete-shop-product'] = 'admin/setting_delete_shop_product';

// BSC
$route['auth-bsc/login'] = 'auth_bsc/login';
$route['auth-bsc/logout'] = 'auth_bsc/logout';
$route['auth-bsc/create-user'] = 'auth_bsc/create_user';
$route['auth-bsc/edit-user'] = 'auth_bsc/edit_user';
$route['auth-bsc/create-group'] = 'auth_bsc/create_group';

$route['bsc/session'] = 'bsc/session';
$route['bsc/users'] = 'bsc/users';
$route['bsc/stores'] = 'bsc/stores';
$route['bsc/user/(:num)'] = 'bsc/user/$1';
$route['bsc/user/status'] = 'bsc/user_status';

$route['admin/sales/(:any)'] = 'admin/sales/$1';
$route['admin/sales/(:any)/sum'] = 'admin/total_sales/$1';

//Notification
$route['notification'] = 'notification';
$route['notification/(:num)/seen'] = 'notification/seen/$1';

//Survey
$route['survey'] = 'survey';
$route['survey/answer'] = 'survey/answer';

//Others
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
