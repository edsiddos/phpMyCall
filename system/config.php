<?php

/*
 * Copyright (C) 2015 - Ednei Leite da Silva
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


// Mostra todos os erros
define ( 'DEBUG', false );

if (DEBUG === true) {
	error_reporting ( E_ALL );
	ini_set ( "display_errors", 1 );
}

// ################################################
// Define os parâmetros de acesso ao banco de dados
// Endereço do banco de dados
define ( 'DB_HOST', 'localhost' );

// Nome do banco de dados
define ( 'DB_NOME', 'myphphelpdesk' );

// Usuário do banco de dados
define ( 'DB_USER', 'dev' );

// Senha do usuário do banco de dados
define ( 'DB_PASS', 'dev' );

// ################################################
// caminho relativo para a pasta do projeto
define ( 'PATH', '/var/www/html/myphphelpdesk' );

// Caminha da pasta application
define ( 'APPLICATION', PATH . '/application' );

// Caminho para a pasta dos controladores
define ( 'CONTROLLER', APPLICATION . '/controllers' );

// Caminho para a pasta das models
define ( 'MODELS', APPLICATION . '/models' );

// Caminho para a pasta das views
define ( 'VIEWS', APPLICATION . '/views' );

// Caminho para a pasta system
define ( 'SYSTEM', PATH . '/system' );

// Caminho para a pasta class
define ( 'CLASSES', PATH . '/class' );

// ################################################
// Endereço do site
define ( 'HTTP', 'http://localhost/myphphelpdesk' );

// Pasta dos arquivos css
define ( 'HTTP_CSS', HTTP . '/static/css' );

// Pasta para imagens
define ( 'HTTP_IMG', HTTP . '/static/img' );

// Pasta para os arquivos javascript
define ( 'HTTP_JS', HTTP . '/static/js' );