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
namespace system;

/**
 * Classe base para os controladores da aplicação
 *
 * @author Ednei Leite da Silva
 */
class Controller {
	private $session = array ();
	public function __construct() {
	}
	
	/**
	 * Carrega as views das aplicações
	 *
	 * @param string $path
	 *        	Caminho a partir da pasta view.
	 * @param Array $vars
	 *        	Array com as variaveis.
	 */
	protected function loadView($path, $vars = NULL) {
		if (file_exists ( VIEWS . '/' . $path . '.phtml' )) {
			if (count ( $vars ) > 0 && is_array ( $vars )) {
				extract ( $vars, EXTR_PREFIX_SAME, 'data' );
			}
			require_once VIEWS . '/' . $path . '.phtml';
		} else {
			$this->error ( "Página não encontrada" );
		}
	}
	
	/**
	 * Exibe erro ao acessar a página
	 *
	 * @param string $mensagem
	 *        	Mensagem de erro
	 */
	private function error($mensagem) {
		echo $mensagem;
	}
	
	/**
	 * Redireciona página
	 *
	 * @param string $url
	 *        	Caminho relativo (link interno)
	 */
	public function redir($url) {
		header ( "Location: " . HTTP . "/{$url}" );
	}
}