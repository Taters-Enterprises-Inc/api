<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
$dotenv->load();

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => $_ENV['DATABASE_USERNAME'],
	'password' => $_ENV['DATABASE_PASSWORD'],
	'database' => $_ENV['DATABASE_NAME'],
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);


$db['bsc'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => $_ENV['BSC_DATABASE_USERNAME'],
	'password' => $_ENV['BSC_DATABASE_PASSWORD'],
	'database' => $_ENV['BSC_DATABASE_NAME'],
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['audit'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => $_ENV['DATABASE_USERNAME'],
	'password' => $_ENV['DATABASE_PASSWORD'],
	'database' => $_ENV['AUDIT_DATABASE_NAME'],
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);


$db['stock-ordering'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => $_ENV['DATABASE_USERNAME'],
	'password' => $_ENV['DATABASE_PASSWORD'],
	'database' => $_ENV['STOCK_ORDERING_DATABASE_NAME'],
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);


$db['sales'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => $_ENV['DATABASE_USERNAME'],
	'password' => $_ENV['DATABASE_PASSWORD'],
	'database' => $_ENV['SALES_DATABASE_NAME'],
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);


