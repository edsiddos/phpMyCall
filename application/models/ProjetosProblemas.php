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
namespace application\models;

/**
 * Manipula inserção, atualização e consultas de projetos e tipos de problemas.
 *
 * @author Ednei Leite da Silva
 */
class ProjetosProblemas extends \system\Model {
	
	/**
	 * Busca os nome dos projetos existentes
	 *
	 * @param string $nome
	 *        	Nome do projeto.
	 * @return Array Com os nome dos projetos.
	 */
	public function getProjetos($nome) {
		$sql = "SELECT nome FROM projeto WHERE nome LIKE :nome";
		
		$result = $this->select ( $sql, array (
				'nome' => "%{$nome}%" 
		) );
		
		$return = array ();
		foreach ( $result as $key => $value ) {
			$return [$key] ['label'] = $value ['nome'];
			$return [$key] ['value'] = $value ['nome'];
		}
		
		return $return;
	}
	
	/**
	 * Busca os tipos de problemas cadastrados
	 *
	 * @param string $nome
	 *        	Nome do tipo de problema
	 * @return Array Retorna array com os nome dos projetos
	 */
	public function getProblemas($nome) {
		$sql = "SELECT nome FROM tipo_problema WHERE nome LIKE :nome";
		
		$result = $this->select ( $sql, array (
				'nome' => "%{$nome}%" 
		) );
		
		$return = array ();
		foreach ( $result as $key => $value ) {
			$return [$key] ['label'] = $value ['nome'];
			$return [$key] ['value'] = $value ['nome'];
		}
		
		return $return;
	}
	
	/**
	 * Busca o ID do projeto selecionado
	 *
	 * @param string $nome
	 *        	Nome do projeto
	 * @return array Resultado da pesquisa
	 */
	public function getIdProjeto($nome) {
		$sql = "SELECT id FROM projeto WHERE nome = :nome";
		
		return $this->select ( $sql, array (
				'nome' => $nome 
		), false );
	}
	
	/**
	 * Busca todos os usuários com menor permissão
	 *
	 * @param string $perfil
	 *        	Nome do perfil
	 */
	public function relacaoUsuarios($perfil) {
		$sql = "SELECT usuario.id, usuario.nome, perfil.perfil FROM usuario
				INNER JOIN perfil ON usuario.perfil = perfil.id
				WHERE usuario.perfil <= (SELECT id FROM perfil WHERE BINARY perfil = :perfil)";
		
		return $this->select ( $sql, array (
				'perfil' => $perfil 
		) );
	}
	
	/**
	 * Verifica se já existe Projeto com determinado tipo de problema
	 *
	 * @param string $projeto
	 *        	Nome do projeto
	 * @param string $problema
	 *        	Nome do problema
	 * @return boolean True se existir, False caso contrario.
	 */
	public function existeProjetoProblema($projeto, $problema) {
		$sql = "SELECT projeto_tipo_problema.id FROM projeto_tipo_problema
				INNER JOIN projeto ON projeto_tipo_problema.projeto = projeto.id
				INNER JOIN tipo_problema ON projeto_tipo_problema.problema = tipo_problema.id
				WHERE projeto.nome = :projeto
					AND tipo_problema.nome = :problema";
		
		$return = $this->select ( $sql, array (
				'projeto' => $projeto,
				'problema' => $problema 
		), false );
		
		return (empty ( $return ['id'] ) ? false : true);
	}
	
	/**
	 * Cria novo projeto e retorna id
	 *
	 * @param string $nome
	 *        	Nome do projeto
	 * @param string $descricao
	 *        	Descrição do projeto
	 * @return mixed Retorna id (int) do projeto ou false caso de erro.
	 */
	public function insertProjeto($nome, $descricao) {
		$array = array (
				'nome' => $nome,
				'descricao' => $descricao 
		);
		
		if ($this->insert ( 'projeto', $array )) {
			$sql = "SELECT id FROM projeto WHERE nome = :nome AND descricao = :descricao";
			
			$return = $this->select ( $sql, $array, false );
			return $return ['id'];
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Retorna o ID do problema
	 *
	 * @param string $nome
	 *        	Nome do problema
	 * @return integer ID do problema
	 */
	public function getIdProblema($nome) {
		$sql = "SELECT id FROM tipo_problema WHERE nome = :nome";
		
		$array = array (
				'nome' => $nome 
		);
		
		$id = $this->select ( $sql, $array, false );
		
		return $id ['id'];
	}
	
	/**
	 * Cria novo tipo de problema e retorna id do problema
	 *
	 * @param string $nome
	 *        	Nome do problema
	 * @return mixed Retorna código do problema caso sucesso, false caso erro.
	 */
	public function insertTipoProblema($nome) {
		$array = array (
				'nome' => $nome 
		);
		
		if ($this->insert ( 'tipo_problema', $array )) {
			$sql = "SELECT id FROM tipo_problema WHERE nome = :nome";
			
			$id = $this->select ( $sql, $array, false );
			return $id ['id'];
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Adiciona participantes do projeto
	 *
	 * @param string $participantes
	 *        	String com os participantes
	 * @param int $projeto
	 *        	Código do projeto
	 */
	public function adicionaPartcipantesProjeto($participantes, $projeto) {
		$usuarios = explode ( ',', $participantes );
		
		foreach ( $usuarios as $values ) {
			$this->insert ( 'projeto_responsaveis', array (
					'usuario' => $values,
					'projeto' => $projeto 
			) );
		}
	}
	
	/**
	 * Cria relação do projeto com problemas
	 *
	 * @param int $projeto
	 *        	Código do projeto
	 * @param int $problema
	 *        	Código do problema
	 * @param string $resposta
	 *        	tempo de resposta
	 * @param string $solucao
	 *        	tempo de solução
	 * @param string $descricao
	 *        	Descrição do tipo de problema
	 */
	public function criaProjetoProblemas($projeto, $problema, $resposta, $solucao, $descricao) {
		$array = array (
				'projeto' => $projeto,
				'problema' => $problema,
				'resposta' => $resposta,
				'solucao' => $solucao,
				'descricao' => $descricao 
		);
		
		$this->insert ( 'projeto_tipo_problema', $array );
	}
	
	/**
	 * Lista todos os tipo de projetos e problemas
	 *
	 * @return array Retorna lista
	 */
	public function listaProjetoProblemas() {
		$sql = "SELECT projeto_tipo_problema.id, projeto.nome AS projeto, tipo_problema.nome AS problema
				FROM projeto_tipo_problema
				INNER JOIN projeto ON projeto_tipo_problema.projeto = projeto.id
				INNER JOIN tipo_problema ON projeto_tipo_problema.problema = tipo_problema.id";
		
		$result = $this->select ( $sql );
		
		foreach ( $result as $values ) {
			$return [$values ['projeto']] [$values ['id']] = $values ['problema'];
		}
		
		return $return;
	}
	
	/**
	 * Busca dados do projeto
	 *
	 * @param int $id
	 *        	Código do projeto
	 * @return array
	 */
	public function getDadosProjetoProblema($id) {
		$sql = "SELECT projeto.id,
					projeto.nome AS projeto,
					tipo_problema.nome AS problema,
					DATE_FORMAT(projeto_tipo_problema.resposta, '%H:%i') AS resposta,
					DATE_FORMAT(projeto_tipo_problema.solucao, '%H:%i') AS solucao,
					projeto_tipo_problema.descricao AS descricao
				FROM projeto_tipo_problema
				INNER JOIN projeto ON projeto_tipo_problema.projeto = projeto.id
				INNER JOIN tipo_problema ON projeto_tipo_problema.problema = tipo_problema.id
				WHERE projeto_tipo_problema.id = :id";
		
		return $this->select ( $sql, array (
				'id' => $id 
		), false );
	}
	
	/**
	 * Relação de usuários participantes de determinado projeto.
	 *
	 * @param int $id
	 *        	Código do projeto
	 * @return Array
	 */
	public function getRelacaoParticipantes($id) {
		$sql = "SELECT usuario FROM projeto_responsaveis WHERE projeto = :id";
		
		$result = $this->select ( $sql, array (
				'id' => $id 
		) );
		
		foreach ( $result as $values ) {
			$retorno [] = $values ['usuario'];
		}
		
		return $retorno;
	}
	
	/**
	 * Descrição do projeto
	 *
	 * @param int $id
	 *        	Código do projeto
	 * @return Array
	 */
	public function getDescricaoProjeto($id) {
		$sql = "SELECT descricao AS descricao_projeto FROM projeto WHERE id = :id";
		
		return $this->select ( $sql, array (
				'id' => $id 
		), false );
	}
	
	/**
	 * Altera projeto
	 *
	 * @param Array $dados
	 *        	Array com os dados
	 * @param int $id
	 *        	Código do projeto
	 * @return boolean True caso sucesso.
	 */
	public function alteraProjeto($dados, $id) {
		return $this->update ( 'projeto', $dados, "id = {$id}" );
	}
	
	/**
	 * Remove participantes de um projeto
	 *
	 * @param int $usuario
	 *        	Código do usuário
	 * @param int $projeto
	 *        	Código do projeto
	 */
	public function deleteParticipantesProjeto($usuario, $projeto) {
		$this->delete ( 'projeto_responsaveis', "usuario = {$usuario} AND projeto = {$projeto}" );
	}
	
	/**
	 * Atualiza relação do projeto com problemas
	 *
	 * @param int $id
	 *        	Código do projeto tipo problema
	 * @param int $projeto
	 *        	Código do projeto
	 * @param int $problema
	 *        	Código do problema
	 * @param string $resposta
	 *        	tempo de resposta
	 * @param string $solucao
	 *        	tempo de solução
	 * @param string $descricao
	 *        	Descrição do tipo de problema
	 */
	public function atualizaProjetoProblemas($id, $projeto, $problema, $resposta, $solucao, $descricao) {
		$array = array (
				'projeto' => $projeto,
				'problema' => $problema,
				'resposta' => $resposta,
				'solucao' => $solucao,
				'descricao' => $descricao 
		);
		
		$this->update ( 'projeto_tipo_problema', $array, "id = {$id}" );
	}
	
	/**
	 * Exclui um projeto tipo de problema
	 *
	 * @param int $id_projeto
	 *        	Código do projeto
	 * @param int $id_projeto_problema
	 *        	Código do tipo projeto problema
	 * @return bool True se operação realizada com sucesso.
	 */
	public function excluirProjetoProblemas($id_projeto, $id_projeto_problema) {
		$sql = "SELECT COUNT(id) AS cont FROM projeto_tipo_problema WHERE projeto = :projeto";
		$result = $this->select ( $sql, array (
				'projeto' => $id_projeto 
		), false );
		
		$return = true;
		
		$return &= (! empty ( $this->delete ( 'projeto_tipo_problema', "id = {$id_projeto_problema}" ) ));
		
		/*
		 * Caso seja o ultimo tipo de problema
		 * exclui o projeto
		 */
		if ($result ['cont'] == 1) {
			$this->delete ( 'projeto_responsaveis', "projeto = {$id_projeto}" );
			$return &= (! empty ( $this->delete ( 'projeto', "id = {$id_projeto}" ) ));
		}
		
		return $return;
	}
}