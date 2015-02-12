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
namespace libs;

use system\Model, DateTime;

/**
 * Grava log de operações realizada no sistema
 *
 * @author Ednei Leite da Silva
 */
class Log {
	
	/**
	 * Grava log no banco de dados
	 *
	 * @param array $dados
	 *        	Array com dados gerais do log
	 * @param integer $id_usuario
	 *        	ID do usuário que executou a operação
	 */
	public static function gravar(array $dados, $id_usuario) {
		$model = new Model ();
		
		$ip = $_SERVER ['REMOTE_ADDR'];
		
		$hoje = new DateTime ();
		
		$dados_json = json_encode ( $dados );
		
		$insert = array (
				'ip' => $ip,
				'data_hora' => $hoje->format ( 'Y-m-d H:i:s' ),
				'dados' => $dados_json,
				'usuario' => $id_usuario 
		);
		
		$model->insert ( 'log', $insert );
	}
}