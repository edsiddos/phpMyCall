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

use \system\Model;

/**
 * Consulta permissões dos usuários
 *
 * @author Ednei Leite da Silva
 */
class Menu {
	
	/**
	 * Consulta os menus por perfils
	 *
	 * @return Array Retorna os menus e links por perfil
	 */
	private static function consulta_menu_perfil() {
		$sql = "SELECT menu.nome AS menu, submenu.nome AS submenu, opcoes_menu.nome AS opcao, opcoes_menu.link, perfil.perfil
                FROM opcoes_menu
                INNER JOIN permissao_perfil ON opcoes_menu.id = permissao_perfil.menu
                INNER JOIN perfil ON permissao_perfil.perfil = perfil.id
                LEFT JOIN opcoes_menu AS submenu ON opcoes_menu.menu_pai = submenu.id
                LEFT JOIN opcoes_menu AS menu ON submenu.menu_pai = menu.id
                ORDER BY perfil.perfil, opcoes_menu.menu_pai, opcoes_menu.id";
		
		$model = new Model ();
		
		return $model->select ( $sql );
	}
	
	/**
	 * Menus por perfil de usuários.
	 * exemplo:
	 * <pre>
	 * Usuário array(
	 * Chat => chat/index,
	 * Solicitação => array(
	 * Finalizadas => Solicitacao/finalizada,
	 * Em atendimento => Solicitacao/atendimento,
	 * Abertas => Solicitacao/aberta
	 * ))
	 * </pre>
	 *
	 * @return Array Menus em uma array separados por perfil.
	 */
	public static function gera_menu_por_perfil() {
		$dados = Menu::consulta_menu_perfil ();
		
		$menu = array ();
		
		foreach ( $dados as $values ) {
			if (empty ( $values ['menu'] ) && empty ( $values ['submenu'] )) {
				$menu [$values ['perfil']] [$values ['opcao']] = $values ['link'];
			} else if (empty ( $values ['menu'] )) {
				$menu [$values ['perfil']] [$values ['submenu']] [$values ['opcao']] = $values ['link'];
			} else {
				$menu [$values ['perfil']] [$values ['menu']] [$values ['submenu']] [$values ['opcao']] = $values ['link'];
			}
		}
		
		return $menu;
	}
	public static function possue_permissao($perfil, $link) {
		$sql = "SELECT EXISTS(
                SELECT * FROM opcoes_menu
                INNER JOIN permissao_perfil ON opcoes_menu.id = permissao_perfil.menu
                INNER JOIN perfil ON permissao_perfil.perfil = perfil.id
                WHERE perfil.perfil = :perfil
                AND opcoes_menu.link = :link) AS permissao";
		
		$model = new Model ();
		
		$result = $model->select ( $sql, array (
				'perfil' => $perfil,
				'link' => $link 
		), FALSE );
		
		return $result ['permissao'];
	}
}