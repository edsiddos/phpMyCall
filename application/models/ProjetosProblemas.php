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
		$sql = "SELECT projeto.nome FROM projeto WHERE projeto.nome LIKE :nome";
		
		$result = $this->select ( $sql, array (
				'nome' => "%{$nome}%" 
		) );
		
		$return = array ();
		foreach ( $result as $values ) {
			$return [] = $values ['nome'];
		}
		
		return $return;
	}
	public function getProblemas($nome) {
		$sql = "SELECT tipo_problema.nome FROM tipo_problema WHERE tipo_problema.nome LIKE :nome";
		
		$result = $this->select ( $sql, array (
				'nome' => "%{$nome}%" 
		) );
		
		$return = array ();
		foreach ( $result as $values ) {
			$return [] = $values ['nome'];
		}
		
		return $return;
	}
	public function getIdProjeto($nome) {
		$sql = "SELECT id FROM projeto WHERE nome = :nome";
		
		return $this->select ( $sql, array (
				'nome' => $nome 
		), false );
	}
	public function relacaoUsuarios($perfil) {
		$sql = "SELECT usuario.id, usuario.nome, perfil.perfil FROM usuario
				INNER JOIN perfil ON usuario.perfil = perfil.id
				WHERE usuario.perfil <= (SELECT id FROM perfil WHERE perfil = :perfil)";
		
		return $this->select ( $sql, array (
				'perfil' => $perfil 
		) );
	}
}