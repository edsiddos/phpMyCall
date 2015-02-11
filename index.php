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
require_once 'system/config.php';

/**
 *
 * @param string $class
 *        	Classe a ser carregado
 */
function __autoload($class) {
	$file = str_replace ( '\\', DIRECTORY_SEPARATOR, $class . '.php' );
	
	/*
	 * Verifica se arquivo existe
	 */
	if (file_exists ( $file )) {
		require_once ($file);
	}
}

$system = new \system\System ( $_GET ['url'] );
$system->run ();
