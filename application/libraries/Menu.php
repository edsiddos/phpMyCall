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
    private static function consultaMenuPerfil() {
        $CI = &get_instance();
        $CI->load->database();

        $select = "menu.nome AS menu, submenu.nome AS submenu, opcoes_menu.nome AS opcao, opcoes_menu.link, perfil.perfil";

        $CI->db->select($select);
        $CI->db->from('phpmycall.opcoes_menu');
        $CI->db->join('phpmycall.permissao_perfil', 'opcoes_menu.id = permissao_perfil.menu', 'inner');
        $CI->db->join('phpmycall.perfil', 'permissao_perfil.perfil = perfil.id', 'inner');
        $CI->db->join('phpmycall.opcoes_menu AS submenu', 'opcoes_menu.menu_pai = submenu.id', 'left');
        $CI->db->join('phpmycall.opcoes_menu AS menu', 'submenu.menu_pai = menu.id', 'left');
        $query = $CI->db->order_by('perfil.perfil, opcoes_menu.id')->get();

        return $query->result_array();
    }

    /**
     * Menus por perfil de usuários.
     * exemplo:
     * Usuário = array(
     *              Chat => chat/index,
     *              Solicitação => array(
     *              Finalizadas => Solicitacao/finalizada,
     *              Em atendimento => Solicitacao/atendimento,
     *              Abertas => Solicitacao/aberta
     * ))
     *
     * @return Array Menus em uma array separados por perfil.
     */
    public static function geraMenuPorPerfil() {
        $dados = Menu::consultaMenuPerfil();

        $menu = array();

        foreach ($dados as $values) {
            if (empty($values ['menu']) && empty($values ['submenu'])) {
                $menu [$values ['perfil']] [$values ['opcao']] = $values ['link'];
            } else if (empty($values ['menu'])) {
                $menu [$values ['perfil']] [$values ['submenu']] [$values ['opcao']] = $values ['link'];
            } else {
                $menu [$values ['perfil']] [$values ['menu']] [$values ['submenu']] [$values ['opcao']] = $values ['link'];
            }
        }

        return $menu;
    }

    /**
     * Verifica se determinado perfil possui autorização para acessar funcionalidade
     * 
     * @param string $perfil Perfil do usuário
     * @param string $link Link cadastrado no banco de dados
     * @return boolean
     */
    public static function possuePermissao($perfil, $link) {
        $sql = "SELECT EXISTS(
          SELECT * FROM phpmycall.opcoes_menu
          INNER JOIN phpmycall.permissao_perfil ON opcoes_menu.id = permissao_perfil.menu
          INNER JOIN phpmycall.perfil ON permissao_perfil.perfil = perfil.id
          WHERE perfil.perfil = ?
          AND opcoes_menu.link = ?) AS permissao";

        $CI = &get_instance();
        $query = $CI->db->query($sql, array($perfil, $link));

        $result = $query->row_array();

        return $result['permissao'];
    }

}
