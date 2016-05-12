<?php

/*
 * Copyright (C) 2015 - 2016, Ednei Leite da Silva
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
 * Manipula dados referentes as configurações do ambiente
 *
 * @author Ednei Leite da Silva
 */
class Administracao_model extends CI_Model {

    /**
     * Metodo construtor utilizado para inicializar transaction
     */
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->db->trans_begin();
    }

    /**
     * Metodo destrutor utilizado para dar commit ou rollback
     */
    public function __destruct() {
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function get_config_solicitacoes() {
        $where = array(
            'VISUALIZAR_SOLICITACAO',
            'DIRECIONAR_CHAMADO',
            'REDIRECIONAR_CHAMADO',
            'EDITAR_SOLICITACAO',
            'ATENDER_SOLICITACAO',
            'EXCLUIR_SOLICITACAO',
            'ENCERRAR_SOLICITACAO'
        );

        $this->db->select('*');
        $this->db->from('openmycall.config');
        $result = $this->db->where_in('parametro', $where)->order_by('parametro')->get()->result_array();

        return $result;
    }

    public function get_relacao_permissao_solicitacao($config) {
        $this->db->select('texto');
        $this->db->from('openmycall.config');
        $result = $this->db->where('parametro', $config)->get()->row_array();

        return explode(', ', $result['texto']);
    }

    public function get_perfil() {
        $this->db->select('*');
        return $this->db->from('openmycall.perfil')->order_by('nivel')->get()->result_array();
    }

    public function get_prioridades() {
        $this->db->select('*');
        return $this->db->from('openmycall.prioridade')->order_by('nivel')->get()->result_array();
    }

    public function get_configuracao_menu() {
        $dados = array(
            'menus' => array(),
            'configuracao' => array()
        );

        $relacao_menu = $this->get_relacao_menus();

        foreach ($relacao_menu as $menu):
            if ($menu['menu_pai'] === NULL):
                $dados['menus'][$menu['id']]['nome'] = $menu['nome'];
            else:
                $dados['menus'][$menu['menu_pai']]['sub_menus'][$menu['id']]['nome'] = $menu['nome'];
            endif;
        endforeach;

        ksort($dados['menus']);

        $relacao_permissoes = $this->get_relacao_configuracao_menus();

        foreach ($relacao_permissoes as $permissoes):
            $dados['configuracao'][$permissoes['menu']][$permissoes['perfil']] = TRUE;
        endforeach;

        return $dados;
    }

    private function get_relacao_menus() {
        $this->db->select('id, nome, menu_pai')->from('openmycall.opcoes_menu')->order_by('menu_pai DESC, nome ASC');
        return $this->db->get()->result_array();
    }

    private function get_relacao_configuracao_menus() {
        $this->db->select('menu, perfil')->from('openmycall.permissao_perfil')->order_by('menu');
        return $this->db->get()->result_array();
    }

    public function atualiza_relacao_permissao_solicitacao($config, $perfis) {
        $where = array(
            'parametro' => $config
        );

        $dados = array(
            'texto' => $perfis
        );

        $this->db->where($where);
        return $this->db->update('openmycall.config', $dados);
    }

    public function grava_prioridade_solicitacao($prioridade_default) {
        $this->db->where(array('id <>' => $prioridade_default));
        $status = $this->db->update('openmycall.prioridade', array('padrao' => FALSE));

        $this->db->where(array('id' => $prioridade_default));
        $status &= $this->db->update('openmycall.prioridade', array('padrao' => TRUE));

        return $status;
    }

    public function altera_cor_prioridade($cod_prioridade, $cor) {
        $this->db->where(array('id' => $cod_prioridade));
        $status = $this->db->update('openmycall.prioridade', array('cor' => $cor));

        return $status;
    }

    public function adiciona_acesso_menus($menu, $perfil) {
        $dados = array(
            'menu' => $menu,
            'perfil' => $perfil
        );

        return $this->db->insert('openmycall.permissao_perfil', $dados);
    }

    public function remove_acesso_menus($menu, $perfil) {
        $dados = array(
            'menu' => $menu,
            'perfil' => $perfil
        );

        $this->db->where($dados);
        return $this->db->delete('openmycall.permissao_perfil');
    }

}
