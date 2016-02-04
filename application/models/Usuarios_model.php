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
 * Manipula cadastro de usuários
 *
 * @author Ednei Leite da Silva
 */
class Usuarios_model extends CI_Model {

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
     * Obtem os perfils que o usuário novo podera ter a partir
     * do usuário que esta criando (um gerente não poderá criar outro gerente)
     *
     * @param string $nivel Nivel do perfil.
     * @return Array Array com os perfils disponiveis.
     */
    public function get_perfil($nivel) {
        $sql = "SELECT id, perfil FROM phpmycall.perfil WHERE nivel < {$nivel}";

        $return = $this->db->query($sql);

        return $return->result_array();
    }

    /**
     * Grava novo usuário.
     *
     * @param Array $dados Array com os dados necessários para criação de novo usuário.
     * @return boolean TRUE se inserido.
     */
    public function inserir_usuario($dados) {
        $result = $this->db->insert('phpmycall.usuario', $dados);

        return $result;
    }

    /**
     * Busca todos os usuários que tem perfil com menor permissão.
     * 
     * @param string $nivel Nivel do perfil.
     * @return Array Retorna Array relação de usuários.
     */
    public function get_usuarios($nivel, $where, $order, $limit, $offset) {
        $str_where = "perfil.nivel < {$nivel}";

        if (!empty($where)) {
            $str_where .= " AND (LOWER(usuario) LIKE LOWER('%{$where}%') OR LOWER(nome) LIKE LOWER('%{$where}%')"
                    . " OR LOWER(perfil.perfil) LIKE LOWER('%{$where}%') OR LOWER(email) LIKE LOWER('%{$where}%'))";
        }

        $this->db->select('usuario.id, usuario.usuario, usuario.nome, perfil.perfil, usuario.email');
        $this->db->from('phpmycall.usuario');
        $this->db->join('phpmycall.perfil', 'usuario.perfil = perfil.id', 'inner');
        $this->db->where($str_where)->order_by($order);
        $query = $this->db->limit($limit, $offset)->get();

        return $query->result_array();
    }

    public function get_quantidades_usuarios($nivel, $where) {
        $str_where = "perfil.nivel < {$nivel}";

        $this->db->select('COUNT(usuario.id) AS quant');
        $this->db->from('phpmycall.usuario');
        $this->db->join('phpmycall.perfil', 'usuario.perfil = perfil.id', 'inner');
        $query = $this->db->where($str_where)->get();

        $result = $query->row_array();

        $return['recordsTotal'] = $result['quant'];

        if (!empty($where)) {
            $str_where .= " AND (LOWER(usuario) LIKE LOWER('%{$where}%') OR LOWER(nome) LIKE LOWER('%{$where}%')"
                    . " OR LOWER(perfil.perfil) LIKE LOWER('%{$where}%') OR LOWER(email) LIKE LOWER('%{$where}%'))";
        }

        $this->db->select('COUNT(usuario.id) AS quant');
        $this->db->from('phpmycall.usuario');
        $this->db->join('phpmycall.perfil', 'usuario.perfil = perfil.id', 'inner');
        $query = $this->db->where($str_where)->get();

        $result = $query->row_array();

        $return['recordsFiltered'] = $result['quant'];

        return $return;
    }

    /**
     * Verifica se usuário existe
     *
     * @param string $user Usuário
     * @param int $id Código do usuário que esta acessando a página
     * @return Array
     */
    public function valida_usuario($user, $id) {
        $this->db->select('id')->from('phpmycall.usuario')->where("usuario = '{$user}' AND id <> '{$id}'");
        $query = $this->db->get();

        $result = $query->row_array();

        return ((boolean) count($result) > 0);
    }

    /**
     * Verifica se email existe
     *
     * @param stirng $email
     * @param int $id ID que não será verificado
     * @return Boolean True se existe email
     */
    public function get_email($email, $id) {
        $this->db->select('email')->from('phpmycall.usuario')->where("email = '{$email}' AND id <> {$id}");
        $query = $this->db->get();

        $result = $query->row_array();

        return ((boolean) count($result) > 0);
    }

    /**
     * Busca dados do usuário a partir do ID
     *
     * @param int $id ID do usuário
     * @return Array Retorna array com os dados do usuário
     */
    public function get_dados_usuarios($id) {
        $this->db->select("id, usuario, nome, email, telefone, perfil, empresa")->from('phpmycall.usuario');
        $query = $this->db->where("id = {$id}")->get();

        return $query->row_array();
    }

    /**
     * Atualiza dados dos usuários (Alterar).
     *
     * @param Array $dados Array com os dados a ser alterado.
     * @param int $id Id do usuário.
     * @return boolean True alteração com sucesso.
     */
    public function atualiza_usuario($dados, $id) {
        $this->db->where("id = {$id}");
        $result = $this->db->update('phpmycall.usuario', $dados);

        return $result;
    }

    /**
     * Realiza a exclusão de usuários.
     *
     * @param int $id Id do usuário.
     * @param string $nivel Nivel de permissão do usuário solicitante da exclusão.
     * @return boolean True se excluido com sucesso, False se falha.
     */
    public function excluir_usuario($id, $nivel) {
        $this->db->select('usuario.id')->from('phpmycall.usuario');
        $this->db->join('phpmycall.perfil', 'usuario.perfil = perfil.id', 'inner');
        $result = $this->db->where(array('usuario.id' => $id, 'perfil.nivel <' => $nivel))->get()->row_array();

        $this->db->where(array('usuario' => $result['id']));
        $status = $this->db->delete('phpmycall.projeto_responsaveis');

        $this->db->where(array('id' => $result['id']));
        $status = $this->db->delete('phpmycall.usuario');

        return $status;
    }

    /**
     * Busca todos os projetos existentes
     * e verifica se o usuário é participante
     *
     * @return Array
     */
    public function relacao_projetos($id_usuario) {
        $this->db->select("projeto.id AS value")->from('phpmycall.projeto');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto.id = projeto_responsaveis.projeto', 'inner');
        $query = $this->db->where("usuario = {$id_usuario}")->get();

        $participa = $query->result_array();

        $dados['participa'] = array();
        foreach ($participa AS $values) {
            $dados['participa'][] = $values['value'];
        }

        $this->db->select('id AS value, nome AS content')->from('phpmycall.projeto');

        if (count($dados['participa']) > 0) {
            $query = $this->db->where_not_in('id', $dados['participa'])->get();
        } else {
            $query = $this->db->get();
        }

        $dados['projeto'] = $query->result_array();

        return $dados;
    }

    /**
     * Relaciona usuário com projetos.
     *
     * @param string $usuario login do usuário.
     * @param array $projetos Array com os códigos do projetos.
     * @return Array Retorna dois <b>arrays</b> relação de projetos inseridos e excluidos.
     */
    public function liga_usuario_projeto($usuario, $projetos) {
        $query = $this->db->select('id')->from('phpmycall.usuario')->where('usuario', $usuario)->get();

        $id = $query->row_array();

        /*
         * Get projetos
         */
        $this->db->select('projeto.id')->from('phpmycall.projeto');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto.id = projeto_responsaveis.projeto', 'inner');
        $this->db->join('phpmycall.usuario', 'projeto_responsaveis.usuario = usuario.id', 'inner');
        $projeto_participante = $this->db->where('usuario.usuario', $usuario)->get();

        $delete = array();
        $insert = is_array($projetos) ? $projetos : array();

        foreach ($projeto_participante->result_array() as $values) {
            if (!in_array($values ['id'], $insert)) {
                $this->db->where("projeto = {$values['id']} AND usuario = {$id['id']}");
                $this->db->delete('phpmycall.projeto_responsaveis');
                $delete [] = $values ['id'];
            }

            $key = array_search($values ['id'], $insert);
            if ($key !== false) {
                unset($insert [$key]);
            }
        }

        foreach ($insert as $values) {
            $this->db->insert('phpmycall.projeto_responsaveis', array('usuario' => $id ['id'], 'projeto' => $values));
        }

        $return = array(
            'delete' => implode(',', $delete),
            'insert' => implode(',', $insert)
        );

        return $return;
    }

    /**
     * Busca empresas cadastradas.
     * @return Array Retorna empresas cadastradas.
     */
    public function get_empresas() {
        $query = $this->db->select('empresas.id, empresas.empresa')->from('phpmycall.empresas')->get();

        return $query->result_array();
    }

}
