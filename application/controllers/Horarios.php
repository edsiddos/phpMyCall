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

use application\models\Horarios as ModelHorarios;
use libs\Menu;
use libs\Log;
use system\Controller;

/**
 * Mantem Horarios
 *
 * @author Ednei Leite da Silva
 */
class Horarios extends Controller {
	
	/**
	 * Manipula dados referentes a horarios
	 *
	 * @var ModelHorarios
	 */
	private $model;
	
	/**
	 * Método construtor verifica se usuário esta logado
	 * e instancia objeto de conexão com banco de dados
	 */
	public function __construct() {
		parent::__construct ();
		if (! Login::verificaLogin ()) {
			$this->redir ( 'Login/index' );
		} else {
			$this->model = new ModelHorarios ();
		}
	}
	public function manterFeriados() {
		$permissao = 'Horarios/manterFeriados';
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					"title" => "Feriados" 
			);
			
			$this->loadView ( "default/header", $title );
			$this->loadView ( "horarios/feriados" );
			$this->loadView ( "default/footer" );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	public function mostrarCalendario() {
		$this->loadView ( "horarios/calendario" );
	}
}