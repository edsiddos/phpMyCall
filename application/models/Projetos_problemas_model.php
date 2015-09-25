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
 * Manipula inserção, atualização e consultas de projetos e tipos de problemas.
 *
 * @author Ednei Leite da Silva
 */
class Projetos_problemas extends CI_Model {

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
     * Busca os nome dos projetos existentes
     *
     * @param string $nome Nome do projeto.
     * @return Array Com os nome dos projetos.
     */
    public function get_projetos($nome) {
        $this->db->select('nome')->from('phpmycall.projeto');
        $result = $this->db->where("nome ILIKE '%{$nome}%'")->get()->result_array();

        $return = array();
        foreach ($result as $key => $value) {
            $return[$key]['label'] = $value['nome'];
            $return[$key]['value'] = $value['nome'];
        }

        return $return;
    }

    /**
     * Busca os tipos de problemas cadastrados
     *
     * @param string $nome Nome do tipo de problema
     * @return Array Retorna array com os nome dos projetos
     */
    public function get_problemas($nome) {
        $this->db->select('nome')->form('phpmycall.tipo_problema');
        $result = $this->db->where("nome ILIKE '%{$nome}%'")->get()->result_array();

        $return = array();
        foreach ($result as $key => $value) {
            $return [$key] ['label'] = $value ['nome'];
            $return [$key] ['value'] = $value ['nome'];
        }

        return $return;
    }

    /**
     * Busca o ID do projeto selecionado
     *
     * @param string $nome Nome do projeto
     * @return array Resultado da pesquisa
     */
    public function get_id_projeto($nome) {
        $this->db->select('id')->from('phpmycall.projeto');
        $result = $this->db->where(array('nome' => $nome))->get()->row_array();

        return $result['id'];
    }

    /**
     * Busca todos os usuários com menor permissão
     *
     * @param int $nivel Nivel de permissão do perfil
     */
    public function relacao_usuarios($nivel) {
        $this->db->select("usuario.id AS value, usuario.nome || ' - ' || perfil.perfil AS name")->from('phpmycall.usuario');
        $this->db->join('phpmycall.perfil', 'usuario.perfil = perfil.id', 'inner');
        $result = $this->db->where("perfil.nivel < {$nivel}")->get()->result_array();

        return (empty($result) ? array() : $result);
    }

    /**
     * Verifica se já existe Projeto com determinado tipo de problema
     *
     * @param string $projeto Nome do projeto
     * @param string $problema Nome do problema
     * @return boolean <b>True</b> se existir, <b>False</b> caso contrario.
     */
    public function existe_projeto_problema($projeto, $problema) {
        $this->db->select('projeto_tipo_problema.id')->from('phpmycall.projeto_tipo_problema');
        $this->db->join('phpmycall.projeto', 'projeto_tipo_problema.projeto = projeto.id', 'inner');
        $this->db->join('phpmycall.tipo_problema', 'projeto_tipo_problema.problema = tipo_problema.id', 'inner');
        $return = $this->db->where(array('projeto.nome' => $projeto, 'tipo_problema.nome' => $problema))->get()->row_array();

        return (empty($return['id']) ? false : true);
    }

    /**
     * Cria novo projeto e retorna id
     *
     * @param string $nome Nome do projeto
     * @param string $descricao Descrição do projeto
     * @return mixed Retorna <b>id</b> (int) do projeto ou <b>false</b> caso de erro.
     */
    public function insert_projeto($nome, $descricao) {
        $array = array(
            'nome' => $nome,
            'descricao' => $descricao
        );

        if ($this->db->insert('phpmycall.projeto', $array)) {
            if ($this->db->dbdriver == 'pdo' && $this->db->subdriver === 'pgsql') {
                $this->db->insert_id('phpmycall.projeto_id_seq');
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Retorna o ID do problema
     *
     * @param string $nome Nome do problema
     * @return integer <b>ID</b> do problema
     */
    public function get_id_problema($nome) {
        $this->db->select('id')->from('phpmycall.tipo_problema');
        $id = $this->db->where(array('nome' => $nome))->get()->row_array();

        return $id['id'];
    }

    /**
     * Cria novo tipo de problema e retorna id do problema
     *
     * @param string $nome Nome do problema
     * @return mixed Retorna <b>código do problema</b> caso sucesso, <b>FALSE</b> caso erro.
     */
    public function insert_tipo_problema($nome) {
        $sql = "SELECT NEXTVAL('{$this->sequences['tipo_problema']}') AS id";
        $id = $this->select($sql, NULL, false);

        $array = array(
            'id' => $id['id'],
            'nome' => $nome
        );

        if ($this->insert('phpmycall.tipo_problema', $array)) {
            return $id ['id'];
        } else {
            return FALSE;
        }
    }

    /**
     * Adiciona participantes do projeto
     *
     * @param array $participantes Array com os id dos participantes
     * @param int $projeto Código do projeto
     */
    public function adiciona_partcipantes_projeto($participantes, $projeto) {

        if (count($participantes) > 0) {
            foreach ($participantes as $values) {
                $this->insert('phpmycall.projeto_responsaveis', array('usuario' => $values, 'projeto' => $projeto));
            }
        }
    }

    /**
     * Cria relação do projeto com problemas
     *
     * @param int $projeto Código do projeto
     * @param int $problema Código do problema
     * @param string $resposta tempo de resposta
     * @param string $solucao tempo de solução
     * @param string $descricao Descrição do tipo de problema
     */
    public function cria_projeto_problemas($projeto, $problema, $resposta, $solucao, $descricao) {
        $array = array(
            'projeto' => $projeto,
            'problema' => $problema,
            'resposta' => $resposta,
            'solucao' => $solucao,
            'descricao' => $descricao
        );

        $this->insert('phpmycall.projeto_tipo_problema', $array);
    }

    /**
     * Lista todos os tipo de projetos e problemas
     *
     * @return array Retorna lista
     */
    public function lista_projeto_problemas() {
        $this->db->select('projeto_tipo_problema.id, projeto.id AS id_projeto, projeto.nome AS projeto, tipo_problema.nome AS problema');
        $this->db->from('phpmycall.projeto_tipo_problema');
        $this->db->join('phpmycall.projeto', 'projeto_tipo_problema.projeto = projeto.id', 'inner');
        $this->db->join('phpmycall.tipo_problema', 'projeto_tipo_problema.problema = tipo_problema.id', 'inner');
        $result = $this->db->get()->result_array();

        $return = NULL;

        foreach ($result as $values) {
            $return[$values['projeto']]['projeto_tipo_problema'][$values ['id']] = $values ['problema'];
            $return[$values['projeto']]['id_projeto'] = $values ['id_projeto'];
        }

        return $return;
    }

    /**
     * Busca dados do projeto
     *
     * @param int $id Código do projeto
     * @return array
     */
    public function get_dados_projeto_problema($id) {
        $select = "projeto.id AS id_projeto,
                    projeto.nome AS nome_projeto,
                    projeto.descricao AS descricao_projeto,
                    tipo_problema.id AS id_problema,
                    tipo_problema.nome AS nome_problema,
                    projeto_tipo_problema.resposta AS resposta,
                    projeto_tipo_problema.solucao AS solucao,
                    projeto_tipo_problema.descricao AS descricao";

        $this->db->select($select)->from('phpmycall.projeto_tipo_problema');
        $this->db->join('phpmycall.projeto', 'projeto_tipo_problema.projeto = projeto.id', 'inner');
        $this->db->join('phpmycall.tipo_problema', 'projeto_tipo_problema.problema = tipo_problema.id', 'inner');
        return $this->db->where(array('projeto_tipo_problema.id' => $id))->get()->row_array();
    }

    /**
     * Relação de usuários participantes de determinado projeto.
     *
     * @param int $id Código do projeto
     * @return Array
     */
    public function get_relacao_participantes($id) {
        $this->db->select('usuario')->from('phpmycall.projeto_responsaveis');
        $result = $this->db->where(array('projeto' => $id))->get()->result_array();

        $retorno = array();

        foreach ($result as $values) {
            $retorno[] = $values ['usuario'];
        }

        return $retorno;
    }

    /**
     * Descrição do projeto
     *
     * @param int $id Código do projeto
     * @return Array
     */
    public function get_descricao_projeto($id) {
        $this->db->select('TRIM(descricao) AS descricao_projeto')->from('phpmycall.projeto');
        return $this->db->where(array('id' => $id))->get()->result_array();
    }

    /**
     * Altera projeto
     *
     * @param Array $dados Array com os dados
     * @param int $id Código do projeto
     * @return boolean True caso sucesso.
     */
    public function altera_projeto($dados, $id) {
        return $this->update('phpmycall.projeto', $dados, "id = {$id}");
    }

    /**
     * Remove participantes de um projeto
     *
     * @param int $usuario Código do usuário
     * @param int $projeto Código do projeto
     */
    public function delete_participantes_projeto($usuario, $projeto) {
        $this->delete('phpmycall.projeto_responsaveis', "usuario = {$usuario} AND projeto = {$projeto}");
    }

    /**
     * Atualiza relação do projeto com problemas
     *
     * @param int $id Código do projeto tipo problema
     * @param int $projeto Código do projeto
     * @param int $problema Código do problema
     * @param string $resposta tempo de resposta
     * @param string $solucao tempo de solução
     * @param string $descricao Descrição do tipo de problema
     */
    public function atualiza_projeto_problemas($id, $projeto, $problema, $resposta, $solucao, $descricao) {
        $array = array(
            'projeto' => $projeto,
            'problema' => $problema,
            'resposta' => $resposta,
            'solucao' => $solucao,
            'descricao' => $descricao
        );

        $this->update('phpmycall.projeto_tipo_problema', $array, "id = {$id}");
    }

    /**
     * Exclui um projeto tipo de problema
     *
     * @param int $id_projeto Código do projeto
     * @param int $id_projeto_problema Código do tipo projeto problema
     * @return bool <b>True</b> se operação realizada com sucesso.
     */
    public function excluir_projeto_problemas($id_projeto, $id_projeto_problema) {
        $this->db->select('COUNT(id) AS cont')->from('phpmycall.projeto_tipo_problema');
        $result = $this->db->where(array('projeto' => $id_projeto))->get()->row_array();

        $return = true;

        $this->db->where(array('id' => $id_projeto_problema));
        $return &= $this->db->delete('phpmycall.projeto_tipo_problema');

        /*
         * Caso seja o ultimo tipo de problema
         * exclui o projeto
         */
        if ($result ['cont'] == 1) {
            $this->db->where(array('projeto' => $id_projeto));
            $this->db->delete('phpmycall.projeto_responsaveis');

            $this->db->where(array('id' => $id_projeto));
            $return &= $this->db->delete('phpmycall.projeto');
        }

        return $return;
    }

}
