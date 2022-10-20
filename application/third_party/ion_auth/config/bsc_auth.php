<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
$dotenv->load();

$config['database_group_name'] = 'bsc';

$config['tables']['users']           = 'users';
$config['tables']['groups']          = 'groups';
$config['tables']['users_groups']    = 'users_groups';
$config['tables']['login_attempts']  = 'login_attempts';

$config['join']['users']  = 'user_id';
$config['join']['groups'] = 'group_id';

$config['hash_method']				= 'bcrypt';	
$config['bcrypt_default_cost']		= 10;
$config['bcrypt_admin_cost']		= 12;
$config['argon2_default_params']	= [
	'memory_cost'	=> 1 << 12,
	'time_cost'		=> 2,
	'threads'		=> 2
];
$config['argon2_admin_params']		= [
	'memory_cost'	=> 1 << 14,	
	'time_cost'		=> 4,
	'threads'		=> 2
];

$config['site_title']                 = "Example.com";
$config['admin_email']                = "admin@example.com";
$config['default_group']              = 'members';
$config['admin_group']                = 'admin';
$config['identity']                   = 'username';

$config['min_password_length']        = 8;
$config['email_activation']           = FALSE;
$config['manual_activation']          = FALSE;
$config['remember_users']             = TRUE;
$config['user_expire']                = 86500;
$config['user_extend_on_login']       = FALSE;
$config['track_login_attempts']       = TRUE;
$config['track_login_ip_address']     = TRUE;
$config['maximum_login_attempts']     = 3;
$config['lockout_time']               = 600;

$config['forgot_password_expiration'] = 1800;
                   										
$config['recheck_timer']              = 0;


$config['session_hash'] = '6583d6c4f205998ecacc9f51b68a2a2e44ea0006';

$config['remember_cookie_name'] = 'remember_code';

$config['use_ci_email'] = FALSE;
$config['email_config'] = [
	'mailtype' => 'html',
];

$config['email_templates'] = 'auth/email/';

$config['email_activate'] = 'activate.tpl.php';

$config['email_forgot_password'] = 'forgot_password.tpl.php';

$config['delimiters_source']       = 'config';
$config['message_start_delimiter'] = '<p>';
$config['message_end_delimiter']   = '</p>';
$config['error_start_delimiter']   = '<p>';
$config['error_end_delimiter']     = '</p>';
