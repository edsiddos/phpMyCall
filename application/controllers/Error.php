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
namespace application\controllers;

/**
 * Classe que exibe mensagens de erros
 *
 * @author Ednei Leite da Silva
 */
class Error extends \system\Controller {
	public function __construct() {
		parent::__construct ();
		if (! Login::verifica_login ()) {
			$this->redir ( "Login/index" );
		}
	}
	
	/**
	 * Método que mostra a página de erro 404
	 */
	public function erro_404() {
		$vars = array (
				'title' => 'Página não encontrada.' 
		);
		
		$this->load_view ( 'default/header', $vars );
		$this->load_view ( 'errors/erro_404' );
		$this->load_view ( 'default/footer' );
	}
}
