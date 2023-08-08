<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//Shop
$route['shop/product'] = 'shop/product';
$route['shop/product/view-log'] = 'shop/product_view_log';
$route['shop/products'] = 'shop/products';
$route['shop/orders'] = 'shop/orders';
$route['shop/deals'] = 'shop/deals';
$route['shop/get_product_sku'] = 'shop/get_product_sku';
$route['shop/influencer-promo'] = 'shop/influencer_promo';
$route['shop/initial-checkout-log'] = 'shop/initial_checkout_log';


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
$route['shared/survey/(:any)/(:hash)'] = 'shared/survey/$1/$2';

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
$route['profile/inbox'] = 'profile/inbox';
$route['profile/influencer'] = 'profile/influencer';
$route['profile/influencer/cashout'] = 'profile/influencer_cashout';
$route['profile/update-influencer'] = 'profile/update_influencer';
$route['profile/influencer-referee'] = 'profile/influencer_referee';
$route['profile/influencer-upload-contract'] = 'profile/influencer_upload_contract';
$route['profile/influencer-cashouts'] = 'profile/influencer_cashouts';

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
$route['download/influencer-contract'] = 'download/influencer_contract/';

//Image
$route['load-image/(:any)'] = 'image/load_image/$1';	
$route['load-image-catering/(:any)'] = 'image/load_catering_image/$1';	
$route['load-image-catering-contract/(:any)'] = 'image/load_catering_image_contract/$1';
$route['load-image-user-discount/(:any)'] = 'image/load_image_user_discount/$1';
$route['load-image-product/(:any)'] = 'image/load_image_product/$1';
$route['load-image-influencer/(:any)'] = 'image/load_image_influencer/$1';
$route['load-image-influencer-contract/(:any)'] = 'image/load_influencer_image_contract/$1';

//Audit
$route['audit/settings/auditformquestions'] = 'audit/getAuditFormQuestions';
$route['audit/login'] = 'audit/login';
$route['audit/logout'] = 'audit/logout';
$route['audit/stores'] = 'audit/stores';

// $route['audit/response'] = 'audit/getAuditResponse';
$route['audit/evaluation'] = 'audit/getAuditFormData';
$route['audit/response/answer/(:any)'] = 'audit/getAuditResponse/$1';
$route['audit/response/quality/audit/information'] = 'audit/getAuditResponseInformation';
$route['audit/result'] = 'audit/getAuditResults';


//Stock Ordering
$route['stock/order/stores'] = 'Stock_Ordering/stores';
$route['stock/order/products'] = 'Stock_Ordering/products';
$route['stock/new/order'] = 'Stock_Ordering/new_order';
$route['stock/orders'] = 'Stock_Ordering/getOrders';
$route['stock/update-order'] = 'Stock_Ordering/update_order';
$route['stock/review-order'] = 'Stock_Ordering/review_order';
$route['stock/confirm-order'] = 'Stock_Ordering/confirm_order';
$route['stock/dispatch-order'] = 'Stock_Ordering/dispatch_order';
$route['stock/order-en-route'] = 'Stock_Ordering/order_en_route';
$route['stock/receive-order-delivery'] = 'Stock_Ordering/receive_order_delivery';
$route['stock/update-billing'] = 'Stock_Ordering/update_billing';
$route['stock/pay-billing'] = 'Stock_Ordering/pay_billing';
$route['stock/confirm-payment'] = 'Stock_Ordering/confirm_payment';
$route['stock/orders'] = 'Stock_Ordering/getOrders'; 
$route['stock/ordered/products'] = 'Stock_Ordering/getProductData';
$route['stock/order/delivery-receive-approval'] = 'Stock_Ordering/delivery_receive_approval';
$route['stock/cancelled'] = 'Stock_Ordering/cancelled_order'; 
$route['stock/get-window-time'] = 'Stock_Ordering/getWindowTime'; 


$route['stock/get-product-list'] = 'Stock_Ordering/get_product_list';
$route['stock/get-product-info'] = 'Stock_Ordering/get_product_info';
$route['stock/get-product-availability'] = 'Stock_Ordering/get_product_availability';
$route['stock/add-product-availability'] = 'Stock_Ordering/add_product_availability';

$route['stock/generate-si-pdf/(:any)'] = 'download/theoretical_sales_invoice/$1';
$route['stock/ordered/download-payment/(:any)'] = 'download/stock_order_download_payment_information/$1';

$route['stock/most-ordered-product/(:any)/(:any)'] = 'Stock_Ordering/most_ordered_product/$1/$2';

$route['stock/import-view'] = 'Stock_Ordering/import_view';
$route['stock/import-si'] = 'Stock_Ordering/import_si';



//Admin
$route['auth/login'] = 'auth/login';
$route['auth/logout'] = 'auth/logout';
$route['auth/create-user'] = 'auth/create_user';
$route['auth/edit-user/(:any)'] = 'auth/edit_user/$1';
$route['auth/create-group'] = 'auth/create_group';

$route['admin/stores'] = 'admin/stores';
$route['admin/stores/popclub'] = 'admin/popclub_stores';
$route['admin/stores/snackshop'] = 'admin/snackshop_stores';
$route['admin/stores/catering'] = 'admin/catering_stores';
$route['admin/setting-user-stores'] = 'admin/setting_user_stores';
$route['admin/service-stores'] = 'admin/service_stores';
$route['admin/products'] = 'admin/products';
$route['admin/store'] = 'admin/store';
$route['admin/store-operating-hours'] = 'admin/store_operating_hours';
$route['admin/store-menu'] = 'admin/store_menu';
$route['admin/session'] = 'admin/session';
$route['admin/payment'] = 'admin/payment';
$route['admin/reference-num'] = 'admin/reference_num';
$route['admin/admin-privilege'] = 'admin/admin_privilege';
$route['admin/admin-catering-privilege'] = 'admin/admin_catering_privilege';
$route['admin/partner-company-employee-id-number'] = 'admin/partner_company_employee_id_number';

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
$route['admin/popclub/categories'] = 'admin/popclub_categories';
$route['admin/popclub/(:any)'] = 'admin/popclub_redeem/$1';
$route['admin/popclub'] = 'admin/popclub';


$route['admin/discount/user-discount-change-status'] = 'admin/user_discount_change_status';
$route['admin/discount/(:num)'] = 'admin/discount/$1';
$route['admin/discounts'] = 'admin/discounts';


$route['admin/availability/deal'] = 'admin/deal_availability';
$route['admin/availability/product'] = 'admin/product_availability';
$route['admin/availability/caters-product'] = 'admin/caters_product_availability';
$route['admin/availability/caters-package'] = 'admin/caters_package_availability';
$route['admin/availability/caters-package-addon'] = 'admin/caters_package_addon_availability';
$route['admin/availability/caters-product-addon'] = 'admin/caters_product_addon_availability';

$route['admin/deal-categories'] = 'admin/deal_categories';
$route['admin/product-categories'] = 'admin/product_categories';
$route['admin/package-categories'] = 'admin/package_categories';
$route['admin/caters-package-categories'] = 'admin/caters_package_categories';

$route['admin/snackshop-transaction-logs/(:num)'] = 'admin/snackshop_transaction_logs/$1';
$route['admin/catering-transaction-logs/(:num)'] = 'admin/catering_transaction_logs/$1';
$route['admin/customer-survey-response-logs/(:num)'] = 'admin/customer_survey_response_logs/$1';

$route['admin/notifications'] = 'admin/notifications';
$route['admin/notification/(:num)/seen'] = 'admin/notification_seen/$1';

$route['admin/report-pmix/(:any)/(:any)'] = 'admin/report_pmix/$1/$2';
$route['admin/report-transaction/(:any)/(:any)'] = 'admin/report_transaction/$1/$2';
$route['admin/report-transaction-catering/(:any)/(:any)'] = 'admin/report_transaction_catering/$1/$2';
$route['admin/report-popclub-store-visit/(:any)/(:any)'] = 'admin/report_popclub_store_visit/$1/$2';
$route['admin/report-popclub-snacks-delivered/(:any)/(:any)'] = 'admin/report_popclub_snacks_delivered/$1/$2';
$route['admin/report-customer-feedback/(:any)/(:any)'] = 'admin/report_customer_feedback/$1/$2';

$route['admin/survey-verification/survey-verification-change-status'] = 'admin/survey_verification_change_status';
$route['admin/survey-verification/(:any)']= 'admin/survey_verification/$1';
$route['admin/survey-verifications'] = 'admin/survey_verifications';

$route['admin/setting/users'] = 'admin/setting_users';
$route['admin/setting/user/(:any)'] = 'admin/setting_user/$1';
$route['admin/setting/groups'] = 'admin/setting_groups';
$route['admin/setting/shop-products'] = 'admin/setting_shop_products';
$route['admin/setting/shop-product'] = 'admin/setting_shop_product';
$route['admin/setting/catering-packages'] = 'admin/setting_catering_packages';
$route['admin/setting/catering-package'] = 'admin/setting_catering_package';
$route['admin/setting/edit-catering-package'] = 'admin/setting_edit_catering_package';
$route['admin/setting/copy-catering-package'] = 'admin/setting_copy_catering_package';
$route['admin/setting/shop-product/stores'] = 'admin/setting_product_stores';
$route['admin/setting/edit-shop-product'] = 'admin/setting_edit_shop_product';
$route['admin/setting/copy-shop-product'] = 'admin/setting_copy_shop_product';
$route['admin/setting/delete-shop-product'] = 'admin/setting_delete_shop_product';
$route['admin/setting/stores'] = 'admin/setting_stores';
$route['admin/setting/store'] = 'admin/setting_store';
$route['admin/setting/edit-store'] = 'admin/setting_edit_store';
$route['admin/setting/popclub-deals'] = 'admin/setting_popclub_deals';
$route['admin/setting/deal-shop-products'] = 'admin/setting_deal_shop_products';
$route['admin/setting/popclub-deal'] = 'admin/setting_popclub_deal';
$route['admin/setting/edit-popclub-deal'] = 'admin/setting_edit_popclub_deal';


$route['admin/influencer/applications'] = 'admin/influencer_applications';
$route['admin/influencer/application/change-status'] = 'admin/influencer_change_status';
$route['admin/influencer/application/(:num)'] = 'admin/influencer_application/$1';
$route['admin/influencer/promos'] = 'admin/influencer_promos';
$route['admin/influencers'] = 'admin/influencers';
$route['admin/influencer'] = 'admin/influencer';
$route['admin/influencer/cashouts'] = 'admin/influencer_cashouts';
$route['admin/influencer/cashout/change-status'] = 'admin/influencer_cashout_change_status';
$route['admin/influencer/cashout/(:num)'] = 'admin/influencer_cashout/$1';

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

$route['admin/dashboard/shop/sales-history'] = 'admin/snackshop_dashboard_sales_history';
$route['admin/dashboard/shop/total-transaction'] = 'admin/snackshop_dashboard_transaction_total';
$route['admin/dashboard/shop/total-completed-transaction'] = 'admin/snackshop_dashboard_completed_transaction_total';
$route['admin/dashboard/shop/add-to-cart-logs'] = 'admin/snackshop_add_to_cart_logs';
$route['admin/dashboard/shop/product-view-logs'] = 'admin/snackshop_product_view_logs';
$route['admin/dashboard/shop/initial-checkout-logs'] = 'admin/snackshop_initial_checkout_logs';
$route['admin/dashboard/shop/users-total'] = 'admin/snackshop_users_total';
$route['admin/dashboard/shop/featured-products'] = 'admin/snackshop_featured_products';
$route['admin/dashboard/catering/sales-history'] = 'admin/catering_dashboard_sales_history';
$route['admin/dashboard/popclub/sales-history'] = 'admin/popclub_dashboard_sales_history';
$route['admin/dashboard/customer-feedback/ratings'] = 'admin/customer_feedback_ratings';

$route['admin/region-store-combination'] = 'admin/region_store_combination';
$route['admin/locales'] = 'admin/locales';

//Notification
$route['notification'] = 'notification';
$route['notification/(:num)/seen'] = 'notification/seen/$1';

//Survey
$route['survey'] = 'survey';
$route['survey/answer/(:any)'] = 'survey/answer/$1';

//Others
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
