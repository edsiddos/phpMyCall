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
 * Manipulação de dados referente aos feedbacks
 *
 * @author Ednei Leite da Silva
 */
class Feedback_model extends CI_Model {

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

    /**
     * Cadastra tipo de feedback
     *
     * @param string $nome Nome do feedback
     * @param string $abrev Abreviatura
     * @param boolean $descontar  Descontar tempo
     * @param string $descricao Descrição do tipo de feedback
     * @return boolean
     */
    public function cadastrar($nome, $abrev, $descontar, $descricao) {
        $dados = array(
            'nome' => $nome,
            'abreviatura' => $abrev,
            'descontar' => $descontar,
            'descricao' => $descricao
        );

        return $this->db->insert('phpmycall.tipo_feedback', $dados);
    }

    /**
     * Busca de dados do tipo de feedback
     *
     * @return array Retorna Array com dados do tipo de Feedback
     */
    public function get_dados_tipo_feedback($search, $order_by, $limit, $offset) {
        $where = "nome ILIKE '%{$search}%' OR abreviatura ILIKE '%{$search}%' OR descricao ILIKE '%{$search}%'";

        $this->db->select('COUNT(id) AS count');
        $this->db->from('phpmycall.tipo_feedback');

        if (!empty($search)) {
            $this->db->where($where);
        }

        $query = $this->db->get();
        $aux = $query->row_array();

        $result['recordsFiltered'] = $aux['count'];
        $result['recordsTotal'] = $this->db->count_all_results('phpmycall.tipo_feedback');

        $this->db->select("id, nome, abreviatura, CASE WHEN descontar THEN 'SIM' ELSE 'NÃO' END AS descontar , descricao");
        $this->db->from('phpmycall.tipo_feedback');

        if (!empty($search)) {
            $this->db->where($where);
        }

        $result['data'] = $this->db->order_by($order_by)->limit($limit, $offset)->get()->result_array();

        return $result;
    }

    /**
     * Busca tipo de feedback a partir do id
     *
     * @param int $feedback Código do tipo de feedback
     * @return Array Retorna array com os dados do tipo de feedback
     */
    public function get_feedback($feedback) {
        $this->db->select('*')->from('phpmycall.tipo_feedback');
        $query = $this->db->where(array('id' => $feedback))->get();
        return $query->row_array();
    }

    /**
     * Atualiza o tipo de feedback
     *
     * @param int $id Código do tipo de feedback
     * @param string $nome Nome do tipo de feedback
     * @param string $abrev Abreviatura do tipo de feedback
     * @param boolean $descontar Descontar do tempo de solução
     * @param string $descricao Descrição do tipo de feedback
     * @return boolean Retorna true se sucesso, false caso contrario
     */
    public function alterar($id, $nome, $abrev, $descontar, $descricao) {
        $dados = array(
            'nome' => $nome,
            'abreviatura' => $abrev,
            'descontar' => $descontar,
            'descricao' => $descricao
        );

        $this->db->where(array('id' => $id));
        return $this->db->update('phpmycall.tipo_feedback', $dados);
    }

    /**
     * Remove tipo de feedback
     *
     * @param int $id Código do tipo de feedback
     * @return boolean Retorna true se sucesso, false caso contrario.
     */
    public function excluir($id) {
        $this->db->where(array('id' => $id));
        return $this->db->delete('phpmycall.tipo_feedback');
    }

}
