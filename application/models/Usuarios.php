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
 * Manipula usuários
 *
 * @author Ednei Leite da Silva
 */
class Usuarios extends \system\Model {
	
	/**
	 * Obtem os perfils que o usuário novo podera ter a partir
	 * do usuário que esta criando (um gerente não poderá criar outro gerente)
	 *
	 * @param string $nome
	 *        	Nome do perfil.
	 * @return Array Array com os perfils disponiveis.
	 */
	public function getPerfil($nome) {
		$sql = "SELECT * FROM perfil
		WHERE id < (SELECT id FROM perfil WHERE perfil = :nome)";
		
		return $this->select ( $sql, array (
				'nome' => $nome 
		) );
	}
	
	/**
	 * Grava novo usuário.
	 *
	 * @param Array $dados
	 *        	Array com os dados necessários para criação de novo usuário.
	 * @return boolean TRUE se inserido.
	 */
	public function inserirUsuario($dados) {
		return $this->insert ( 'usuario', $dados );
	}
	
	/**
	 * Verifica se usuário existe
	 *
	 * @param string $user
	 *        	Usuário
	 * @return Array
	 */
	public function getUsuario($user, $id) {
		$sql = "SELECT EXISTS(SELECT * FROM usuario WHERE usuario = :user AND id <> :id) AS exist";
		
		return $this->select ( $sql, array (
				'user' => $user,
				'id' => $id 
		), false );
	}
	
	/**
	 * Verifica se email existe
	 *
	 * @param stirng $email        	
	 * @return Array
	 */
	public function getEmail($email, $id) {
		$sql = "SELECT EXISTS(SELECT * FROM usuario WHERE email = :email AND id <> :id) AS exist";
		
		return $this->select ( $sql, array (
				'email' => $email,
				'id' => $id 
		), false );
	}
	
	/**
	 * Busca usuários a partir de um nome informado.
	 *
	 * @param string $nome
	 *        	Nome do usuário.
	 * @param string $perfil
	 *        	Perfil do usuário (nível de acesso).
	 * @return Array Retorna relação de nomes semelhantes.
	 */
	public function getUsuarioNome($usuario, $perfil) {
		$sql = "SELECT nome, usuario FROM usuario WHERE usuario LIKE :usuario
				AND perfil < (SELECT id FROM perfil WHERE perfil = :perfil)";
		
		$result = $this->select ( $sql, array (
				'usuario' => "%{$usuario}%",
				'perfil' => $perfil 
		) );
		
		foreach ( $result as $key => $values ) {
			$return [$key] ['label'] = $values ['usuario'] . ' (' . $values ['nome'] . ')';
			$return [$key] ['value'] = $values ['usuario'];
		}
		
		return $return;
	}
	
	/**
	 * Busca dados do usuário a partir do ID
	 *
	 * @param int $id
	 *        	ID do usuário
	 * @return Array Retorna array com os dados do usuário
	 */
	public function getDadosUsuarios($usuario) {
		$sql = "SELECT id, usuario, nome, email, perfil FROM usuario WHERE usuario = :usuario";
		
		return $this->select ( $sql, array (
				'usuario' => $usuario 
		), false );
	}
	
	/**
	 * Atualiza dados dos usuários (Alterar).
	 *
	 * @param Array $dados
	 *        	Array com os dados a ser alterado.
	 * @param int $id
	 *        	Id do usuário.
	 * @return boolean True alteração com sucesso.
	 */
	public function atualizaUsuario($dados, $id) {
		return $this->update ( 'usuario', $dados, "id = {$id}" );
	}
	
	/**
	 * Realiza a exclusão de usuários.
	 *
	 * @param int $id
	 *        	Id do usuário.
	 * @param string $usuario        	
	 * @param string $email        	
	 * @param string $perfil
	 *        	Perfil do usuário solicitante de exclusão.
	 * @return boolean True se excluido com sucesso, False se falha.
	 */
	public function excluirUsuario($id, $usuario, $email, $perfil) {
		$where = "id = {$id} AND usuario = '{$usuario}' AND email = '{$email}' AND perfil < ";
		$where .= "(SELECT id FROM perfil WHERE perfil = '{$perfil}')";
		
		return $this->delete ( 'usuario', $where );
	}
}