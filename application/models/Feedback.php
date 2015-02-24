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

use system\Model;
use application\controllers\Feedback;

/**
 * Manipulação de dados referente aos feedbacks
 *
 * @author Ednei Leite da Silva
 */
class Feedback extends Model {
	
	/**
	 * Cadastra tipo de feedback
	 * @param string $nome Nome do feedback
	 * @param string $abrev Abreviatura
	 * @param boolean $descontar Descontar tempo
	 * @param string $descricao Descrição do tipo de feedback
	 * @return boolean
	 */
	public function cadastrar($nome, $abrev, $descontar, $descricao) {
		$dados = array (
				'nome' => $nome,
				'abreviatura' => $abrev,
				'descontar' => $descontar,
				'descricao' => $descricao 
		);
		
		return $this->insert ( 'tipo_feedback', $dados );
	}
	
	/**
	 * Busca os tipos de feedback a partir do nome
	 * @param string $nome Nome de tipo de feedback
	 * @return Array Retorna array com os tipos de feedback
	 */
	public function getNomeFeedback($nome) {
		$sql = "SELECT nome AS value FROM tipo_feedback WHERE nome LIKE :nome";
		
		return $this->select ( $sql, array (
				'nome' => '%' . $nome . '%' 
		) );
	}
	
	/**
	 * Busca de dados do tipo de feedback
	 * @param string $nome Nome do tipo de feedback
	 * @return array Retorna Array com dados do tipo de Feedback
	 */
	public function getDadosTipoFeedback($nome) {
		$sql = "SELECT * FROM tipo_feedback WHERE nome = :nome";
		return $this->select ( $sql, array (
				'nome' => $nome 
		), false );
	}
	
	/**
	 * Atualiza o tipo de feedback
	 * @param int $id Código do tipo de feedback
	 * @param string $nome Nome do tipo de feedback
	 * @param string $abrev Abreviatura do tipo de feedback
	 * @param boolean $descontar Descontar do tempo de solução
	 * @param string $descricao Descrição do tipo de feedback
	 * @return boolean Retorna true se sucesso, false caso contrario
	 */
	public function alterar($id, $nome, $abrev, $descontar, $descricao) {
		$dados = array (
				'nome' => $nome,
				'abreviatura' => $abrev,
				'descontar' => $descontar,
				'descricao' => $descricao 
		);
		
		return $this->update ( 'tipo_feedback', $dados, "id = {$id}" );
	}
	
	/**
	 * Remove tipo de feedback
	 * @param int $id Código do tipo de feedback
	 * @return boolean Retorna true se sucesso, false caso contrario.
	 */
	public function excluir ($id){
		return $this->delete('tipo_feedback', "id = {$id}");
	}
}