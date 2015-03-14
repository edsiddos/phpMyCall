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
use \libs\Log;

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
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao )) {
			$title = array (
					'title' => 'Cadastrar projetos' 
			);
			
			$existe_usuario = $this->model->existeUsuarios ( $perfil );
			
			$pagina = array (
					'link' => HTTP . '/ProjetosProblemas/novoProjetoProblema',
					'botao' => array (
							'value' => ($existe_usuario ? 'Próximo' : 'Cadastrar Projeto'),
							'type' => ($existe_usuario ? 'button' : 'submit') 
					) 
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
		$permissao_1 = "ProjetosProblemas/cadastrar";
		$permissao_2 = "ProjetosProblemas/alterar";
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao_1 ) || Menu::possuePermissao ( $perfil, $permissao_2 )) {
			$nome = $_POST ['term'];
			
			echo json_encode ( $this->model->getProjetos ( $nome ) );
		}
	}
	
	/**
	 * Busca os tipos de problemas existentes
	 */
	public function getProblemas() {
		$permissao_1 = "ProjetosProblemas/cadastrar";
		$permissao_2 = "ProjetosProblemas/alterar";
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao_1 ) || Menu::possuePermissao ( $perfil, $permissao_2 )) {
			$nome = $_POST ['term'];
			
			echo json_encode ( $this->model->getProblemas ( $nome ) );
		}
	}
	
	/**
	 * Busca ID de um projeto
	 */
	public function getIdProjeto() {
		$permissao_1 = "ProjetosProblemas/cadastrar";
		$permissao_2 = "ProjetosProblemas/alterar";
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao_1 ) || Menu::possuePermissao ( $perfil, $permissao_2 )) {
			$nome = $_POST ['nome'];
			
			echo json_encode ( $this->model->getIdProjeto ( $nome ) );
		}
	}
	
	/**
	 * Retorna relação de usuários com id
	 */
	public function relacaoUsuarios() {
		$permissao_1 = "ProjetosProblemas/cadastrar";
		$permissao_2 = "ProjetosProblemas/alterar";
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao_1 ) || Menu::possuePermissao ( $perfil, $permissao_2 )) {
			$id = $_POST ['id'];
			
			$usuarios = $this->model->relacaoUsuarios ( $perfil );
			$participantes = $this->model->getRelacaoParticipantes ( $id );
			$vars = $this->model->getDescricaoProjeto ( $id );
			
			foreach ( $usuarios as $values ) {
				if (in_array ( $values ['id'], $participantes )) {
					$vars ['participantes'] [] = $values;
				} else {
					$vars ['usuarios'] [] = $values;
				}
			}
			
			$this->loadView ( 'projetos_problemas/usuarios', $vars );
		}
	}
	
	/**
	 * Insere um novo projeto com os respectivos participantes
	 */
	public function novoProjetoProblema() {
		$permissao = 'ProjetosProblemas/cadastrar';
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao )) {
			$id = $_POST ['inputID'];
			$participantes = $_POST ['relacaoParticipantes'];
			$projeto = $_POST ['inputProjeto'];
			$problema = $_POST ['inputProblema'];
			$resposta = $_POST ['inputResposta'];
			$solucao = $_POST ['inputSolucao'];
			$descricao = $_POST ['textDescricao'];
			$descricao_projeto = $_POST ['descricaoProjeto'];
			
			if (! $this->model->existeProjetoProblema ( $projeto, $problema )) {
				if (empty ( $id )) {
					$id = $this->model->insertProjeto ( $projeto, $descricao_projeto );
					
					if (empty ( $id )) {
						$_SESSION ['msg_erro'] = "Erro ao criar projeto";
					} else if (! empty ( $participantes )) {
						$this->model->adicionaPartcipantesProjeto ( $participantes, $id );
					}
				}
				
				if (! empty ( $id )) {
					$id_problema = $this->model->getIdProblema ( $problema );
					
					if (empty ( $id_problema )) {
						$id_problema = $this->model->insertTipoProblema ( $problema );
						
						if (empty ( $id_problema )) {
							$_SESSION ['msg_erro'] = "Erro ao criar tipo de problema";
						}
					}
					
					if (! empty ( $id_problema )) {
						$this->model->criaProjetoProblemas ( $id, $id_problema, $resposta, $solucao, $descricao );
						$_SESSION ['msg_sucesso'] = 'Projeto criado com sucesso';
					}
				}
				
				$dados = array (
						'dados' => array (
								'operacao' => empty ( $id ) ? 'Criação projeto e problema' : 'Adição de tipo de problema',
								'id_projeto' => $id,
								'nome_projeto' => $projeto,
								'descricao_projeto' => $descricao_projeto,
								'id_tipo_problema' => $id_problema,
								'nome_tipo_problema' => $problema,
								'tempo_resposta' => $resposta,
								'tempo_solucao' => $solucao,
								'projeto_tipo_problema' => $descricao,
								'novos_usuarios' => $participantes 
						) 
				);
			} else {
				$_SESSION ['msg_erro'] = "Já existe projeto com este tipo de problema.";
			}
			
			$dados ['msg'] = empty ( $_SESSION ['msg_erro'] ) ? $_SESSION ['msg_sucesso'] : $_SESSION ['msg_erro'];
			$dados ['aplicacao'] = $permissao;
			
			Log::gravar ( $dados, $_SESSION ['id'] );
			
			$this->redir ( 'ProjetosProblemas/cadastrar' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Tela de alteração dos projetos
	 */
	public function alterar() {
		$permissao = "ProjetosProblemas/alterar";
		$perfil = $_SESSION ['perfil'];
		
		if (Menu::possuePermissao ( $perfil, $permissao )) {
			$title = array (
					'title' => 'Alterar projetos' 
			);
			
			$existe_usuario = $this->model->existeUsuarios ( $perfil );
			
			$pagina = array (
					'link' => HTTP . '/ProjetosProblemas/atualizarProjetoProblema',
					'botao' => array (
							'value' => ($existe_usuario ? 'Próximo' : 'Alterar Projeto'),
							'type' => ($existe_usuario ? 'button' : 'submit') 
					) 
			);
			
			$listaProjetos ['listaProjeto'] = $this->model->listaProjetoProblemas ();
			
			$this->loadView ( 'default/header', $title );
			$this->loadView ( 'projetos_problemas/alterar', $listaProjetos );
			$this->loadView ( 'projetos_problemas/index', $pagina );
			$this->loadView ( 'default/footer' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Busca informações sobre o projeto.
	 */
	public function getDadosProjetosProblemas() {
		$id = $_POST ['id'];
		
		echo json_encode ( $this->model->getDadosProjetoProblema ( $id ) );
	}
	
	/**
	 * Realiza a operação de atualização do projeto (alterar)
	 */
	public function atualizarProjetoProblema() {
		$id_projeto = $_POST ['inputID'];
		$participantes = $_POST ['relacaoParticipantes'];
		$descricao_projeto = $_POST ['descricaoProjeto'];
		$id_projeto_old = $_POST ['inputProjetoOld'];
		$id_projeto_problema = $_POST ['inputProjetoProblema'];
		$projeto = $_POST ['inputProjeto'];
		$problema = $_POST ['inputProblema'];
		$resposta = $_POST ['inputResposta'];
		$solucao = $_POST ['inputSolucao'];
		$descricao_problema_projeto = $_POST ['textDescricao'];
		$permissao = "ProjetosProblemas/alterar";
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			
			$update_projeto = array (
					'nome' => $projeto,
					'descricao' => $descricao_projeto 
			);
			
			if ($this->model->alteraProjeto ( $update_projeto, $id_projeto_old )) {
				$participantes_old = $this->model->getRelacaoParticipantes ( $id_projeto_old );
				$insert = explode ( ',', $participantes );
				$delete = array ();
				
				foreach ( $participantes_old as $value ) {
					if (! in_array ( $value, $insert )) {
						$this->model->deleteParticipantesProjeto ( $value, $id_projeto_old );
						$delete [] = $value;
					}
					
					$key = array_search ( $value, $insert );
					if ($key !== false) {
						unset ( $insert [$key] );
					}
				}
				
				$delete = implode ( ',', $delete );
				$insert = implode ( ',', $insert );
				
				$this->model->adicionaPartcipantesProjeto ( $insert, $id_projeto_old );
				
				$id_problema = $this->model->getIdProblema ( $problema );
				
				if (empty ( $id_problema )) {
					$id_problema = $this->model->insertTipoProblema ( $problema );
					
					if (empty ( $id_problema )) {
						$_SESSION ['msg_erro'] = "Erro ao criar tipo de problema";
					}
				}
				
				if (! empty ( $id_problema )) {
					$this->model->atualizaProjetoProblemas ( $id_projeto_problema, $id_projeto_old, $id_problema, $resposta, $solucao, $descricao_problema_projeto );
					$_SESSION ['msg_sucesso'] = "Atualização de projeto, problema realizada com sucesso";
				}
				
				$dados = array (
						'dados' => array (
								'altera_nome_projeto' => $id_projeto == 0 ? 'sim' : 'não',
								'id_projeto' => $id_projeto_old,
								'nome_projeto' => $projeto,
								'descricao_projeto' => $descricao_projeto,
								'id_tipo_problema' => $id_problema,
								'nome_tipo_problema' => $problema,
								'id_projeto_tipo_problema' => $id_projeto_problema,
								'tempo_resposta' => $resposta,
								'tempo_solucao' => $solucao,
								'projeto_tipo_problema' => $descricao_problema_projeto,
								'novos_usuarios' => $insert,
								'excluir_usuarios' => $delete 
						),
						'msg' => empty ( $_SESSION ['msg_erro'] ) ? $_SESSION ['msg_sucesso'] : $_SESSION ['msg_erro'],
						'aplicacao' => $permissao 
				);
				
				Log::gravar ( $dados, $_SESSION ['id'] );
			}
			
			$this->redir ( 'ProjetosProblemas/alterar' );
		} else {
			$this->main ( 'Main/index' );
		}
	}
	
	/**
	 * Tela de exclusão de projeto
	 */
	public function excluir() {
		$permissao = "ProjetosProblemas/excluir";
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$title = array (
					'title' => 'Excluir projetos' 
			);
			
			$pagina = array (
					'link' => HTTP . '/ProjetosProblemas/excluirProjetoProblema',
					'botao' => array (
							'value' => 'Excluir',
							'type' => 'button' 
					) 
			);
			
			$listaProjetos ['listaProjeto'] = $this->model->listaProjetoProblemas ();
			
			$this->loadView ( 'default/header', $title );
			$this->loadView ( 'projetos_problemas/excluir', $listaProjetos );
			$this->loadView ( 'projetos_problemas/index', $pagina );
			$this->loadView ( 'default/footer' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
	
	/**
	 * Realiza a exclusão do projeto tipo de problema selecionado
	 */
	public function excluirProjetoProblema() {
		$permissao = "ProjetosProblemas/excluir";
		
		if (Menu::possuePermissao ( $_SESSION ['perfil'], $permissao )) {
			$id_projeto = $_POST ['inputID'];
			$id_projeto_problema = $_POST ['inputProjetoProblema'];
			
			$projeto = $_POST ['inputProjeto'];
			$problema = $_POST ['inputProblema'];
			
			if ($this->model->excluirProjetoProblemas ( $id_projeto, $id_projeto_problema )) {
				$_SESSION ['msg_sucesso'] = 'Sucesso ao excluir projeto tipo de problema';
			} else {
				$_SESSION ['msg_erro'] = 'Erro ao excluir projeto tipo de problema';
			}
			
			$dados = array (
					'dados' => array (
							'id_projeto' => $id_projeto,
							'id_projeto_problema' => $id_projeto_problema,
							'projeto' => $projeto,
							'problema' => $problema 
					),
					'aplicacao' => $permissao,
					'msg' => empty ( $_SESSION ['msg_erro'] ) ? $_SESSION ['msg_sucesso'] : $_SESSION ['msg_erro'] 
			);
			
			Log::gravar ( $dados, $_SESSION ['id'] );
			
			$this->redir ( 'ProjetosProblemas/excluir' );
		} else {
			$this->redir ( 'Main/index' );
		}
	}
}