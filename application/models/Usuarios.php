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
	public function get_perfil($nome) {
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
	public function inserir_usuario($dados) {
		return $this->insert ( 'usuario', $dados );
	}
	
	/**
	 * Verifica se usuário existe
	 *
	 * @param string $user
	 *        	Usuário
	 * @return Array
	 */
	public function get_usuario($user, $id) {
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
	public function get_email($email, $id) {
		$sql = "SELECT EXISTS(SELECT * FROM usuario WHERE email = :email AND id <> :id) AS exist";
		
		return $this->select ( $sql, array (
				'email' => $email,
				'id' => $id 
		), false );
	}
	
	/**
	 * Dados necessários para alterar perfil de usuários
	 *
	 * @param string $nome
	 *        	Nome do perfil do usuário
	 */
	public function get_id_usuarios($nome) {
		$sql = "SELECT usuario.id, usuario.nome, perfil.perfil AS perfil FROM usuario
				INNER JOIN perfil ON usuario.perfil = perfil.id
				WHERE usuario.perfil < (SELECT id FROM perfil WHERE perfil = :nome)
				ORDER BY usuario.nome";
		
		return $this->select ( $sql, array (
				'nome' => $nome 
		) );
	}
	
	/**
	 * Busca dados do usuário a partir do ID
	 *
	 * @param int $id
	 *        	ID do usuário
	 * @return Array Retorna array com os dados do usuário
	 */
	public function get_dados_usuarios($id) {
		$sql = "SELECT id, usuario, nome, email, perfil FROM usuario WHERE id = :id";
		
		return $this->select ( $sql, array (
				'id' => $id 
		), false );
	}
}