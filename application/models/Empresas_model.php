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
 * Manipula cadastro das empresas
 *
 * @author Ednei Leite da Silva
 */
class Empresas_model extends CI_Model {

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
     * Cadastra uma nova empresa
     * @param Array $dados Dados necessarios para gravar uma empresa.
     * @return boolean <b>TRUE</b> se sucesso.
     */
    public function cadastra_empresa($dados) {
        return $this->db->insert('phpmycall.empresas', $dados);
    }

    /**
     * Verifica se empresa esta cadastrada.
     * @param String $empresa Nome da empresa.
     * @return Array Retorna array com resultado da consulta.
     */
    public function existe_empresa($empresa) {
        $this->db->select('COUNT(empresas.empresa) AS status');
        $query = $this->db->from('phpmycall.empresas')->where("LOWER(empresa) LIKE LOWER('{$empresa}')")->get();

        return $query->row_array();
    }

    /**
     * 
     * Busca as empresas cadastradas.
     * @param string $search String com trecho que será procurado
     * @param string $order_by Coluna que será ordenada
     * @param int $limit Limite de registros retornados
     * @param int $offset A partir de que linha
     * @return Array Retorna array com dados das empresas
     */
    public function get_empresas($search, $order_by, $limit, $offset) {
        $where = "LOWER(empresa) LIKE LOWER('%{$search}%') OR LOWER(endereco) LIKE LOWER('%{$search}%') ";
        $where .= " OR LOWER(telefone_fixo) LIKE LOWER('%{$search}%') OR LOWER(telefone_celular) LIKE LOWER('%{$search}%')";

        $this->db->select('COUNT(id) AS count');
        $this->db->from('phpmycall.empresas');

        if (!empty($search)) {
            $this->db->where($where);
        }

        $query = $this->db->get();
        $aux = $query->row_array();

        $result['total'] = $aux['count'];

        $this->db->select("id, empresa, endereco, telefone_fixo, telefone_celular");
        $this->db->from('phpmycall.empresas');

        if (!empty($search)) {
            $this->db->where($where);
        }

        $result['rows'] = $this->db->order_by($order_by)->limit($limit, $offset)->get()->result_array();

        return $result;
    }

    /**
     * Busca dados da empresa a partir do nome.
     * @param int $empresa Código da empresa.
     * @return Array Retorna dados da empresa.
     */
    public function get_dados_empresa($empresa) {
        return $this->db->from('phpmycall.empresas')->where(array('id' => "$empresa"))->get()->row_array();
    }

    /**
     * Atualiza dados da empresa.
     * @param int $id ID da empresa
     * @param Array $dados Array com dados a ser alterados
     * @return boolean Retorna <b>TRUE</b> se operação realizada com sucesso.
     */
    public function atualiza_empresa($id, $dados) {
        $this->db->where(array('id' => $id));
        return $this->db->update('phpmycall.empresas', $dados);
    }

    /**
     * Exclui cadastro de um empresa.
     * @param int $id ID da empresa
     * @return boolean Retorna <b>TRUE</b> se operação de exclusão ocorrer com sucesso.
     */
    public function excluir_empresa($id) {
        $this->db->where(array('id' => $id));
        return $this->db->delete('phpmycall.empresas');
    }

}
