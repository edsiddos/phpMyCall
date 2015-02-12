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
		if (! Login::verifica_login ()) {
			$this->redir ( 'Login/index' );
		} else {
			$this->model = new ModelUsuarios ();
		}
	}
	
	/**
	 * Gera tela com formulário para inserção de novo usuário
	 */
	public function cadastrar_usuario() {
		$permissao = 'Usuarios/cadastrar_usuario';
		
		if (Menu::possue_permissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					"title" => "Cadastro de usuário" 
			);
			
			$vars = array (
					'perfil' => $this->model->get_perfil ( $_SESSION ['perfil'] ),
					'link' => HTTP . '/Usuarios/novo_usuario',
					'title_botao' => "Cadastrar Usuário" 
			);
			
			$this->load_view ( "default/header", $title );
			$this->load_view ( "usuarios/usuario", $vars );
			$this->load_view ( "default/footer" );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Realiza a inserção de um novo usuário no sistema
	 */
	public function novo_usuario() {
		$permissao = 'Usuarios/cadastrar_usuario';
		
		if (Menu::possue_permissao ( $_SESSION ['perfil'], $permissao )) {
			$dados = $this->get_dados_post_usuario ();
			
			if ($this->model->inserir_usuario ( $dados )) {
				$_SESSION ['msg_sucesso'] = "Usuário inserido com sucesso.";
				$dados ['status'] = $permissao . ' - ok';
			} else {
				$_SESSION ['msg_erro'] = "Erro ao inserir novo usuário. Verifique dados e tente novamente.";
				$dados ['status'] = $permissao . ' - falha';
			}
			
			Log::gravar ( $dados, $_SESSION ['id'] );
			
			$this->redir ( 'Usuarios/cadastrar_usuario' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Processa dados para atualização ou inserção de um usuário.
	 *
	 * @return Array Retorna um array com os dados do usuário.
	 */
	private function get_dados_post_usuario() {
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
	public function valida_usuario() {
		$permissao_1 = 'Usuarios/cadastrar_usuario';
		$permissao_2 = 'Usuarios/alterar_usuario';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possue_permissao ( $perfil, $permissao_1 ) || Menu::possue_permissao ( $perfil, $permissao_2 )) {
			$user = $_POST ['user'];
			$id = $_POST ['id'];
			
			echo json_encode ( $this->model->get_usuario ( $user, $id ) );
		}
	}
	
	/**
	 * Verifica se existe email para algum usuário
	 */
	public function valida_email() {
		$permissao_1 = 'Usuarios/cadastrar_usuario';
		$permissao_2 = 'Usuarios/alterar_usuario';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possue_permissao ( $perfil, $permissao_1 ) || Menu::possue_permissao ( $perfil, $permissao_2 )) {
			$email = $_POST ['email'];
			$id = $_POST ['id'];
			
			echo json_encode ( $this->model->get_email ( $email, $id ) );
		}
	}
	
	/**
	 * Busca usuário para realizar alteração
	 */
	public function alterar_usuario() {
		$permissao = 'Usuarios/alterar_usuario';
		
		if (Menu::possue_permissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					"title" => "Alterar usuário" 
			);
			
			$vars = array (
					'perfil' => $this->model->get_perfil ( $_SESSION ['perfil'] ),
					'link' => HTTP . '/Usuarios/atualiza_usuario',
					'title_botao' => "Alterar Usuário" 
			);
			
			$this->load_view ( "default/header", $title );
			$this->load_view ( "usuarios/relacao_usuarios" );
			$this->load_view ( "usuarios/usuario", $vars );
			$this->load_view ( "default/footer" );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Busca os nomes de usuários
	 */
	public function get_usuario_nome() {
		$permissao_1 = 'Usuarios/alterar_usuario';
		$permissao_2 = 'Usuarios/excluir_usuario';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possue_permissao ( $perfil, $permissao_1 ) || Menu::possue_permissao ( $perfil, $permissao_2 )) {
			$usuario = $_POST ['term'];
			
			echo json_encode ( $this->model->get_usuario_nome ( $usuario, $perfil ) );
		}
	}
	
	/**
	 * Busca dados do usuario selecionado para alteração
	 */
	public function get_dados_usuarios() {
		$permissao_1 = 'Usuarios/alterar_usuario';
		$permissao_2 = 'Usuarios/excluir_usuario';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possue_permissao ( $perfil, $permissao_1 ) || Menu::possue_permissao ( $perfil, $permissao_2 )) {
			$usuario = $_POST ['usuario'];
			
			echo json_encode ( $this->model->get_dados_usuarios ( $usuario ) );
		}
	}
	
	/**
	 * Realiza a atualização do usuário
	 */
	public function atualiza_usuario() {
		$permissao = 'Usuarios/alterar_usuario';
		
		if (Menu::possue_permissao ( $_SESSION ['perfil'], $permissao )) {
			$dados = $this->get_dados_post_usuario ();
			
			$id = $_POST ['inputID'];
			
			if ($this->model->atualiza_usuario ( $dados, $id )) {
				$_SESSION ['msg_sucesso'] = "Usuário alterado com sucesso.";
				$dados ['status'] = $permissao . ' - ok';
			} else {
				$_SESSION ['msg_erro'] = "Erro ao alterar usuário. Verifique dados e tente novamente.";
				$dados ['status'] = $permissao . ' - falha';
			}
			
			Log::gravar ( $dados, $_SESSION ['id'] );
			
			$this->redir ( 'Usuarios/alterar_usuario' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Exibe tela de exclusão de usuários
	 */
	public function excluir_usuario() {
		$permissao = 'Usuarios/excluir_usuario';
		
		if (Menu::possue_permissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					"title" => "Excluir usuário" 
			);
			
			$vars = array (
					'perfil' => $this->model->get_perfil ( $_SESSION ['perfil'] ),
					'link' => HTTP . '/Usuarios/remove_usuario',
					'title_botao' => "Excluir Usuário" 
			);
			
			$this->load_view ( "default/header", $title );
			$this->load_view ( "usuarios/delete" );
			$this->load_view ( "usuarios/usuario", $vars );
			$this->load_view ( "default/footer" );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Remove usuário selecionado
	 */
	public function remove_usuario() {
		$permissao = 'Usuarios/excluir_usuario';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possue_permissao ( $perfil, $permissao )) {
			$id = $_POST ['inputID'];
			$usuario = $_POST ['inputUsuario'];
			$email = $_POST ['inputEMail'];
			
			$dados = array (
					'id' => $id,
					'usuario' => $usuario,
					'email' => $email,
					'perfil' => $perfil 
			);
			
			if ($this->model->exclui_usuario ( $id, $usuario, $email, $perfil )) {
				$_SESSION ['msg_sucesso'] = "Usuário excluido com sucesso.";
				$dados ['status'] = $permissao . ' - ok';
			} else {
				$_SESSION ['msg_erro'] = "Erro ao excluir usuário. Verifique dados e tente novamente.";
				$dados ['status'] = $permissao . ' - falha';
			}
			
			Log::gravar ( $dados, $_SESSION ['id'] );
			
			$this->redir ( 'Usuarios/excluir_usuario' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
}