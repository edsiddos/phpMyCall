<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$route = array(
    'default_controller' => 'login',
    '404_override' => '',
    'translate_uri_dashes' => TRUE,
    'main' => 'admin/main/index',
    'main/(.+)' => 'admin/main/$1'
);
