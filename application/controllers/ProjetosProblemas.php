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
		if (! Login::verificaLogin ()) {
			$this->redir ( "Login/index" );
		}
		
		$this->model = new ModelProjetosProblemas ();
	}
	
	/**
	 * Tela de cadastro de projetos e problemas
	 */
	public function cadastrar() {
		$permissao = "ProjetosProblemas/cadastrar";
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					'title' => 'Cadastrar projetos' 
			);
			
			$pagina = array (
					'link' => 'novoProjetoProblema',
					'botao' => 'Cadastrar Projeto' 
			);
			
			$this->loadView ( 'default/header', $title );
			$this->loadView ( 'projetos_problemas/cadastrar' );
			$this->loadView ( 'projetos_problemas/index', $pagina );
			$this->loadView ( 'default/footer' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Busca os tipos de projetos
	 */
	public function getProjetos() {
		$permissao = "ProjetosProblemas/cadastrar";
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$nome = $_POST ['term'];
			
			echo json_encode ( $this->model->getProjetos ( $nome ) );
		}
	}
	
	/**
	 * Busca os tipos de problemas existentes
	 */
	public function getProblemas() {
		$permissao = "ProjetosProblemas/cadastrar";
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$nome = $_POST ['term'];
			
			echo json_encode ( $this->model->getProblemas ( $nome ) );
		}
	}
	
	/**
	 * Busca ID de um projeto
	 */
	public function getIdProjeto() {
		$permissao = "ProjetosProblemas/cadastrar";
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao )) {
			$nome = $_POST ['nome'];
			
			echo json_encode ( $this->model->getIdProjeto ( $nome ) );
		}
	}
	public function relacaoUsuarios() {
		$permissao = "ProjetosProblemas/cadastrar";
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao )) {
			$nome = $_POST ['nome'];
			
			$vars ['usuarios'] = $this->model->relacaoUsuarios ( $perfil );
			
			$this->loadView ( 'projetos_problemas/usuarios', $vars );
		}
	}
}