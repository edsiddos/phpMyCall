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

use \application\models\Usuarios as ModelUsuarios;
use \libs\Menu;
use \libs\Log;

/**
 * Mantem usuários
 *
 * @author Ednei Leite da Silva
 */
class Usuarios extends \system\Controller {
	
	/**
	 * Objeto para obtenção de dados dos usuários.
	 *
	 * @var ModelUsuarios
	 */
	private $model;
	
	/**
	 * Verifica se usuários esta logado antes de executar operação
	 */
	public function __construct() {
		parent::__construct ();
		if (! Login::verificaLogin ()) {
			$this->redir ( 'Login/index' );
		} else {
			$this->model = new ModelUsuarios ();
		}
	}
	
	/**
	 * Gera tela com formulário para inserção de novo usuário
	 */
	public function cadastrar() {
		$permissao = 'Usuarios/cadastrar';
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					"title" => "Cadastro de usuário" 
			);
			
			$vars = array (
					'perfil' => $this->model->getPerfil ( $_SESSION ['perfil'] ),
					'link' => HTTP . '/Usuarios/novoUsuario',
					'title_botao' => "Cadastrar Usuário" 
			);
			
			$this->loadView ( "default/header", $title );
			$this->loadView ( "usuarios/usuario", $vars );
			$this->loadView ( "default/footer" );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Realiza a inserção de um novo usuário no sistema
	 */
	public function novoUsuario() {
		$permissao = 'Usuarios/cadastrar';
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$dados = $this->getDadosPostUsuario ();
			
			if ($this->model->inserirUsuario ( $dados )) {
				$_SESSION ['msg_sucesso'] = "Usuário inserido com sucesso.";
				$dados ['status'] = $permissao . ' - ok';
			} else {
				$_SESSION ['msg_erro'] = "Erro ao inserir novo usuário. Verifique dados e tente novamente.";
				$dados ['status'] = $permissao . ' - falha';
			}
			
			Log::gravar ( $dados, $_SESSION ['id'] );
			
			$this->redir ( 'Usuarios/cadastrar' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Processa dados para atualização ou inserção de um usuário.
	 *
	 * @return Array Retorna um array com os dados do usuário.
	 */
	private function getDadosPostUsuario() {
		$nome = $_POST ['inputNome'];
		$usuario = $_POST ['inputUsuario'];
		$senha = (isset ( $_POST ['inputSenha'] ) ? $_POST ['inputSenha'] : NULL);
		$changeme = (isset ( $_POST ['inputChangeme'] [0] ) ? TRUE : FALSE);
		$email = $_POST ['inputEMail'];
		$perfil = $_POST ['selectPerfil'];
		
		/* Verifica se todos os dados necessários foram informados */
		$datetime = NULL;
		
		// caso o usuário tenha selecionado "Senha temporária"
		// seta data de troca para "HOJE"
		if ($changeme) {
			$datetime = new \DateTime ();
		} else {
			$datetime = new \DateTime ();
			$datetime->add ( new \DateInterval ( 'P30D' ) );
		}
		
		$dados = array (
				'usuario' => $usuario,
				'nome' => $nome,
				'email' => $email,
				'perfil' => $perfil,
				'dt_troca' => $datetime->format ( 'Y-m-d' ) 
		);
		
		if (! empty ( $senha )) {
			$dados ['senha'] = sha1 ( md5 ( $senha ) );
		}
		
		return $dados;
	}
	
	/**
	 * Verifica se o usuário existe
	 */
	public function validaUsuario() {
		$permissao_1 = 'Usuarios/cadastrar';
		$permissao_2 = 'Usuarios/alterar';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao_1 ) || Menu::possuePermissao ( $perfil, $permissao_2 )) {
			$user = $_POST ['user'];
			$id = $_POST ['id'];
			
			echo json_encode ( $this->model->getUsuario ( $user, $id ) );
		}
	}
	
	/**
	 * Verifica se existe email para algum usuário
	 */
	public function validaEmail() {
		$permissao_1 = 'Usuarios/cadastrar';
		$permissao_2 = 'Usuarios/alterar';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao_1 ) || Menu::possuePermissao ( $perfil, $permissao_2 )) {
			$email = $_POST ['email'];
			$id = $_POST ['id'];
			
			echo json_encode ( $this->model->getEmail ( $email, $id ) );
		}
	}
	
	/**
	 * Busca usuário para realizar alteração
	 */
	public function alterar() {
		$permissao = 'Usuarios/alterar';
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					"title" => "Alterar usuário" 
			);
			
			$vars = array (
					'perfil' => $this->model->getPerfil ( $_SESSION ['perfil'] ),
					'link' => HTTP . '/Usuarios/atualizaUsuario',
					'title_botao' => "Alterar Usuário" 
			);
			
			$this->loadView ( "default/header", $title );
			$this->loadView ( "usuarios/relacao_usuarios" );
			$this->loadView ( "usuarios/usuario", $vars );
			$this->loadView ( "default/footer" );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Busca os nomes de usuários
	 */
	public function getUsuarioNome() {
		$permissao_1 = 'Usuarios/alterar';
		$permissao_2 = 'Usuarios/excluir';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao_1 ) || Menu::possuePermissao ( $perfil, $permissao_2 )) {
			$usuario = $_POST ['term'];
			
			echo json_encode ( $this->model->getUsuarioNome ( $usuario, $perfil ) );
		}
	}
	
	/**
	 * Busca dados do usuario selecionado para alteração
	 */
	public function getDadosUsuarios() {
		$permissao_1 = 'Usuarios/alterar';
		$permissao_2 = 'Usuarios/excluir';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao_1 ) || Menu::possuePermissao ( $perfil, $permissao_2 )) {
			$usuario = $_POST ['usuario'];
			
			echo json_encode ( $this->model->getDadosUsuarios ( $usuario ) );
		}
	}
	
	/**
	 * Realiza a atualização do usuário
	 */
	public function atualizaUsuario() {
		$permissao = 'Usuarios/alterar';
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$dados = $this->getDadosPostUsuario ();
			
			$id = $_POST ['inputID'];
			
			if ($this->model->atualizaUsuario ( $dados, $id )) {
				$_SESSION ['msg_sucesso'] = "Usuário alterado com sucesso.";
				$dados ['status'] = $permissao . ' - ok';
			} else {
				$_SESSION ['msg_erro'] = "Erro ao alterar usuário. Verifique dados e tente novamente.";
				$dados ['status'] = $permissao . ' - falha';
			}
			
			Log::gravar ( $dados, $_SESSION ['id'] );
			
			$this->redir ( 'Usuarios/alterar' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Exibe tela de exclusão de usuários
	 */
	public function excluir() {
		$permissao = 'Usuarios/excluir';
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					"title" => "Excluir usuário" 
			);
			
			$vars = array (
					'perfil' => $this->model->getPerfil ( $_SESSION ['perfil'] ),
					'link' => HTTP . '/Usuarios/removeUsuario',
					'title_botao' => "Excluir Usuário" 
			);
			
			$this->loadView ( "default/header", $title );
			$this->loadView ( "usuarios/delete" );
			$this->loadView ( "usuarios/usuario", $vars );
			$this->loadView ( "default/footer" );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Remove usuário selecionado
	 */
	public function removeUsuario() {
		$permissao = 'Usuarios/excluir';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao )) {
			$id = $_POST ['inputID'];
			$usuario = $_POST ['inputUsuario'];
			$email = $_POST ['inputEMail'];
			
			$dados = array (
					'id' => $id,
					'usuario' => $usuario,
					'email' => $email,
					'perfil' => $perfil 
			);
			
			if ($this->model->excluirUsuario ( $id, $usuario, $email, $perfil )) {
				$_SESSION ['msg_sucesso'] = "Usuário excluido com sucesso.";
				$dados ['status'] = $permissao . ' - ok';
			} else {
				$_SESSION ['msg_erro'] = "Erro ao excluir usuário. Verifique dados e tente novamente.";
				$dados ['status'] = $permissao . ' - falha';
			}
			
			Log::gravar ( $dados, $_SESSION ['id'] );
			
			$this->redir ( 'Usuarios/excluir' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
}