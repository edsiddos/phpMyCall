<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$config_db = parse_ini_file('database.ini', TRUE);

$active_group = 'default';
$query_builder = TRUE;

$dsn = $config_db['default']['driver'] . ':';
$dsn .= 'host=' . $config_db['default']['hostname'] . ';';
$dsn .= 'port=' . $config_db['default']['port'] . ';';
$dsn .= 'dbname=' . $config_db['default']['database'] . ';';
$dsn .= 'user=' . $config_db['default']['username'] . ';';
$dsn .= 'password=' . $config_db['default']['password'];

$db['default'] = array(
    'dsn' => $dsn,
    'hostname' => $config_db['default']['hostname'],
    'username' => $config_db['default']['username'],
    'password' => $config_db['default']['password'],
    'database' => $config_db['default']['database'],
    'dbdriver' => 'pdo',
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
