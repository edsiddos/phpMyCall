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
        $result = $this->db->where_in('parametro', $where)->get()->result_array();

        return $result;
    }

    public function get_perfil() {
        $this->db->select('*');
        return $this->db->from('openmycall.perfil')->get()->result_array();
    }

    public function get_prioridades() {
        $this->db->select('*');
        return $this->db->from('openmycall.prioridade')->get()->result_array();
    }

}
