<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$route = array(
    'default_controller' => 'login',
    '404_override' => '',
    'translate_uri_dashes' => TRUE,
    'empresas' => 'admin/empresas/index',
    'empresas/(.+)' => 'admin/empresas/$1',
    'feedback' => 'admin/feedback/index',
    'feedback/(.+)' => 'admin/feedback/$1',
    'horarios' => 'admin/horarios/index',
    'horarios/(.+)' => 'admin/horarios/$1',
    'main' => 'admin/main/index',
    'main/(.+)' => 'admin/main/$1',
    'usuarios' => 'admin/usuarios/index',
    'usuarios/(.+)' => 'admin/usuarios/$1',
);
