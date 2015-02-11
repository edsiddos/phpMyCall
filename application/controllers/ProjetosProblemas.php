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

use \application\models\ProjetosProblemas as ModelProjetosProblemas;
use \libs\Menu;

/**
 * Manipulas os projetos e tipos de problemas
 *
 * @author Ednei Leite da Silva
 */
class ProjetosProblemas extends \system\Controller {
	
	/**
	 * Objeto de conexão com banco de dados
	 * que manipula dados de projetos e problemas
	 *
	 * @var ProjetosProblemas
	 */
	private $model;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		if (! Login::verifica_login ()) {
			$this->redir ( "Login/index" );
		}
		
		$this->model = new ModelProjetosProblemas ();
	}
	
	/**
	 * Tela de cadastro de projetos e problemas
	 */
	public function cadastrar_projeto_problema() {
		$permissao = str_replace ( (__NAMESPACE__ . '\\'), '', (__CLASS__ . "/" . __FUNCTION__) );
		
		if (Menu::possue_permissao ( $_SESSION ['perfil'], $permissao )) {
			$this->load_view ( 'default/header', array (
					'title' => 'Cadastrar projetos' 
			) );
			$this->load_view ( 'projetos_problemas/cadastrar' );
			$this->load_view ( 'default/footer' );
		}
	}
	
	/**
	 * Busca os tipos de projetos
	 */
	public function getProjetos() {
		$nome = $_POST ['term'];
		
		echo json_encode ( $this->model->getProjetos ( $nome ) );
	}
	
	/**
	 * Busca os tipos de problemas existentes
	 */
	public function getProblemas() {
		$nome = $_POST ['term'];
		
		echo json_encode ( $this->model->getProblemas ( $nome ) );
	}
}