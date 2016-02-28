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
 * Manipula inserção, atualização e consultas de solicitações.
 *
 * @author Ednei Leite da Silva
 */
class Solicitacao_model extends CI_Model {

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
     * Retorna os projetos na qual o usuário é participantes.
     * @param int $usuario Código do usuário.
     * @return Array Retorna informação dos projetos e dos tipos de problemas
     */
    public function get_projetos($usuario) {
        $this->db->select('projeto_tipo_problema.id, projeto.id AS id_projeto, projeto.nome AS projeto, tipo_problema.nome AS problema');
        $this->db->from('phpmycall.projeto');
        $this->db->join('phpmycall.projeto_tipo_problema', 'projeto.id = projeto_tipo_problema.projeto', 'inner');
        $this->db->join('phpmycall.tipo_problema', 'projeto_tipo_problema.problema = tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto.id = projeto_responsaveis.projeto', 'inner');
        $this->db->where(array('projeto_responsaveis.usuario' => $usuario))->order_by('projeto.nome, tipo_problema.nome');
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Busca todas prioridades cadastradas.
     * @return Array Retorna relação de prioridades.
     */
    public function get_prioridades() {
        $query = $this->db->select('id, nome, padrao')->from('phpmycall.prioridade')->get();

        return $query->result_array();
    }

    /**
     * Relação de participantes do projeto.
     * @param int $projeto Código do projeto.
     * @return Array Retorna todos os usuários participantes de um projeto.
     */
    public function get_solicitantes($projeto) {
        $parametros = Parametros_solicitacoes::get_parametros();

        $tecnicos = "'" . implode("', '", $parametros['ATENDER_SOLICITACAO']) . "'";

        $this->db->select("usuario.id, usuario.nome, perfil.perfil IN ({$tecnicos}) AS tecnico");
        $this->db->from('phpmycall.usuario');
        $this->db->join('phpmycall.projeto_responsaveis', 'usuario.id = projeto_responsaveis.usuario', 'inner');
        $this->db->join('phpmycall.projeto_tipo_problema', 'projeto_responsaveis.projeto = projeto_tipo_problema.projeto', 'inner');
        $this->db->join('phpmycall.perfil', 'usuario.perfil = perfil.id', 'inner');
        $this->db->where(array('projeto_tipo_problema.id' => $projeto))->order_by('usuario.nome');
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Relação de participantes do projeto a partir do ID Solicitação.
     * @param int $solicitacao Código da solicitação.
     * @return Array Retorna um array.
     */
    public function get_solicitantes_solicitacao($solicitacao) {
        $parametros = Parametros_solicitacoes::get_parametros();

        $tecnicos = "'" . implode("', '", $parametros['ATENDER_SOLICITACAO']) . "'";

        $this->db->select("usuario.id, usuario.nome, perfil.perfil IN ({$tecnicos}) AS tecnico");
        $this->db->from('phpmycall.usuario');
        $this->db->join('phpmycall.projeto_responsaveis', 'usuario.id = projeto_responsaveis.usuario', 'inner');
        $this->db->join('phpmycall.projeto_tipo_problema', 'projeto_responsaveis.projeto = projeto_tipo_problema.projeto', 'inner');
        $this->db->join('phpmycall.perfil', 'usuario.perfil = perfil.id', 'inner');
        $this->db->join('phpmycall.solicitacao', 'projeto_tipo_problema.id = solicitacao.projeto_problema', 'inner');
        $this->db->where(array('solicitacao.id' => $solicitacao))->order_by('usuario.nome');
        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Grava uma nova solicitação.
     * @param int $dados Array com dados da solicitação.
     * @return Mixed Retorna <b>Array</b> com id da solicitação, retorna <b>FALSE</b> se ocorrer ao gravar solicitação.
     */
    public function grava_solicitacao($dados) {
        if ($this->db->insert('phpmycall.solicitacao', $dados)) {
            if ($this->db->dbdriver == 'pdo' && $this->db->subdriver === 'pgsql') {
                return $this->db->insert_id('phpmycall.solicitacao_id_seq');
            } else {
                return $this->db->insert_id();
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Grava arquivo no banco de dados.
     * @param Array $dados Array com ID da solicitação, nome do arquivo, tipo de arquivo e caminho do arquivo.
     * @return boolean Retorna <b>TRUE</b> se sucesso, <b>FALSE</b> se falha
     */
    public function grava_arquivo_solicitacao($dados) {
        return $this->db->insert('phpmycall.arquivos', $dados);
    }

    /**
     * Busca todas as solicitações dos projetos que o usuário pertença.
     * @param int $usuario Id do usuário que solicitou visualização.
     * @param string $perfil Perfil do usuário que solicitou visualização.
     * @param int $situacao Status da solicitação 1 - <b>aberta</b>, 2 - <b>atendimento</b>, 3 - <b>encerrada</b>.
     * @return Array Retorna um array com todas as solicitações de um determinada situação
     */
    public function get_solicitacoes($usuario, $perfil, $situacao, $prioridade, $search, $order_by, $limit, $offset) {
        $config = Parametros_solicitacoes::get_parametros();

        $result = $this->count_solicitacoes($usuario, $perfil, $situacao, $prioridade, $search);

        $select = "projeto.nome AS projeto, tipo_problema.nome AS problema, prioridade.nome AS prioridade, ";
        $select .= "solicitante.nome AS solicitante, atendente.nome AS atendente, solicitacao.abertura, ";
        $select .= "solicitacao.id AS solicitacao, COUNT(arquivos.id) AS num_arquivos";

        $this->db->select($select);
        $this->db->from('phpmycall.solicitacao');
        $this->db->join('phpmycall.usuario AS solicitante', 'solicitante.id = solicitacao.solicitante', 'inner');
        $this->db->join('phpmycall.usuario AS atendente', 'atendente.id = solicitacao.atendente', 'inner');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto', 'projeto_tipo_problema.projeto = projeto.id', 'inner');
        $this->db->join('phpmycall.tipo_problema', 'projeto_tipo_problema.problema = tipo_problema.id', 'inner');
        $this->db->join('phpmycall.prioridade', 'solicitacao.prioridade = prioridade.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto.id = projeto_responsaveis.projeto', 'inner');
        $this->db->join('phpmycall.arquivos', 'solicitacao.id = arquivos.solicitacao', 'left');
        $this->db->where(array('projeto_responsaveis.usuario' => $usuario));

        /*
         * Conforme o status da solicitação muda na montagem da sql.
         */
        if ($situacao == 1) {
            $this->db->where("solicitacao.abertura = solicitacao.atendimento AND solicitacao.encerramento = solicitacao.atendimento");
        } elseif ($situacao == 2) {
            $this->db->where("solicitacao.abertura < solicitacao.atendimento AND solicitacao.encerramento = solicitacao.atendimento");
        } elseif ($situacao == 3) {
            $this->db->where("solicitacao.abertura < solicitacao.atendimento AND solicitacao.atendimento < solicitacao.encerramento");
        }

        if (!empty($prioridade)) {
            $this->db->where('prioridade.id', $prioridade);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->or_where("LOWER(projeto.nome) LIKE LOWER('%$search%')")->or_where("LOWER(tipo_problema.nome) LIKE LOWER('%$search%')");
            $this->db->or_where("LOWER(prioridade.nome) LIKE LOWER('%$search%')")->or_where("LOWER(solicitante.nome) LIKE LOWER('%$search%')");
            $this->db->or_where("LOWER(atendente.nome) LIKE LOWER('%$search%')");
            $this->db->group_end();
        }

        /*
         * Verifica se o perfil tem autorização de visualizar todas as solicitações
         * dentro do projeto que o mesmo esteja vinculado
         */
        if (array_search($perfil, $config['VISUALIZAR_SOLICITACAO']) === FALSE) {
            $this->db->group_start();
            $this->db->or_where(array('solicitacao.solicitante' => $usuario, 'solicitacao.atendente' => $usuario, 'solicitacao.tecnico' => $usuario));
            $this->db->group_end();
        }

        $this->db->group_by('projeto.nome, tipo_problema.nome, prioridade.nome, solicitante.nome, atendente.nome, solicitacao.abertura, solicitacao.id, prioridade.nivel');
        $this->db->order_by($order_by);

        $this->db->limit($limit, $offset);

        $result['rows'] = $this->db->get()->result_array();

        foreach ($result['rows'] as $key => $values) {
            $data = new DateTime($values['abertura']);
            $result['rows'][$key]['abertura'] = $data->format('d/m/Y H:i:s');
        }

        return $result;
    }

    /**
     * Busca todas as solicitações dos projetos que o usuário pertença.
     * @param int $usuario Id do usuário que solicitou visualização.
     * @param string $perfil Perfil do usuário que solicitou visualização.
     * @param int $situacao Status da solicitação 1 - <b>aberta</b>, 2 - <b>atendimento</b>, 3 - <b>encerrada</b>.
     * @return Array Retorna um array com todas as solicitações de um determinada situação
     */
    private function count_solicitacoes($usuario, $perfil, $situacao, $prioridade, $search) {
        $config = Parametros_solicitacoes::get_parametros();

        $this->db->from('phpmycall.solicitacao');
        $this->db->join('phpmycall.usuario AS solicitante', 'solicitante.id = solicitacao.solicitante', 'inner');
        $this->db->join('phpmycall.usuario AS atendente', 'atendente.id = solicitacao.atendente', 'inner');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto', 'projeto_tipo_problema.projeto = projeto.id', 'inner');
        $this->db->join('phpmycall.tipo_problema', 'projeto_tipo_problema.problema = tipo_problema.id', 'inner');
        $this->db->join('phpmycall.prioridade', 'solicitacao.prioridade = prioridade.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto.id = projeto_responsaveis.projeto', 'inner');
        $this->db->where(array('projeto_responsaveis.usuario' => $usuario));

        /*
         * Conforme o status da solicitação muda na montagem da sql.
         */
        if ($situacao == 1) {
            $this->db->where("solicitacao.abertura = solicitacao.atendimento AND solicitacao.encerramento = solicitacao.atendimento");
        } elseif ($situacao == 2) {
            $this->db->where("solicitacao.abertura < solicitacao.atendimento AND solicitacao.encerramento = solicitacao.atendimento");
        } elseif ($situacao == 3) {
            $this->db->where("solicitacao.abertura < solicitacao.atendimento AND solicitacao.atendimento < solicitacao.encerramento");
        }

        if (!empty($prioridade)) {
            $this->db->where('prioridade.id', $prioridade);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->or_where("LOWER(projeto.nome) LIKE LOWER('%$search%')")->or_where("LOWER(tipo_problema.nome) LIKE LOWER('%$search%')");
            $this->db->or_where("LOWER(prioridade.nome) LIKE LOWER('%$search%')")->or_where("LOWER(solicitante.nome) LIKE LOWER('%$search%')");
            $this->db->or_where("LOWER(atendente.nome) LIKE LOWER('%$search%')");
            $this->db->group_end();
        }

        /*
         * Verifica se o perfil tem autorização de visualizar todas as solicitações
         * dentro do projeto que o mesmo esteja vinculado
         */
        if (array_search($perfil, $config['VISUALIZAR_SOLICITACAO']) === FALSE) {
            $this->db->group_start();
            $this->db->or_where(array('solicitacao.solicitante' => $usuario, 'solicitacao.atendente' => $usuario, 'solicitacao.tecnico' => $usuario));
            $this->db->group_end();
        }

        $result['recordsFiltered'] = $this->db->count_all_results();
        $result['total'] = $this->db->count_all_results('phpmycall.solicitacao');

        return $result;
    }

    /**
     * Busca dados da solicitação caso o usuário seja participantes do projeto.
     * @param int $solicitacao Código da Solicitação.
     * @param string $perfil Perfil do usuário.
     * @param int $usuario Código do Usuário.
     * @return Array Retorna <b>Array</b> com dados de uma determinada solicitação
     */
    public function get_dados_solicitacao($solicitacao, $perfil, $usuario) {
        $config = Parametros_solicitacoes::get_parametros();

        $select = "projeto_tipo_problema.id AS projeto_problema,
                    projeto.nome AS projeto,
                    tipo_problema.nome AS problema,
                    prioridade.nome AS prioridade,
                    solicitante.nome AS solicitante,
                    atendente.nome AS atendente,
                    tecnico.nome AS tecnico,
                    solicitacao.descricao AS descricao,
                    solicitacao.atendente AS id_atendente,
                    solicitacao.solicitante AS id_solicitante,
                    solicitacao.tecnico AS id_tecnico,
                    solicitacao.abertura,
                    CASE WHEN solicitacao.abertura = solicitacao.atendimento THEN NULL
                    ELSE solicitacao.atendimento END AS atendimento,
                    CASE WHEN solicitacao.atendimento = solicitacao.encerramento THEN NULL
                    ELSE solicitacao.encerramento END AS encerramento";

        $this->db->select($select, false);
        $this->db->from('phpmycall.solicitacao');
        $this->db->join('phpmycall.usuario AS solicitante', 'solicitante.id = solicitacao.solicitante', 'inner');
        $this->db->join('phpmycall.usuario AS atendente', 'atendente.id = solicitacao.atendente', 'inner');
        $this->db->join('phpmycall.usuario AS tecnico', 'tecnico.id = solicitacao.tecnico', 'left');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto', 'projeto_tipo_problema.projeto = projeto.id', 'inner');
        $this->db->join('phpmycall.tipo_problema', 'projeto_tipo_problema.problema = tipo_problema.id', 'inner');
        $this->db->join('phpmycall.prioridade', 'solicitacao.prioridade = prioridade.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto.id = projeto_responsaveis.projeto', 'inner');

        $where = "solicitacao.id = {$solicitacao} AND projeto_responsaveis.usuario = {$usuario}";

        if (array_search($perfil, $config['VISUALIZAR_SOLICITACAO']) === FALSE) {
            $where .= " AND (solicitacao.solicitante = {$usuario} OR solicitacao.atendente = {$usuario} OR solicitacao.tecnico = {$usuario})";
        }

        $query = $this->db->where($where)->get();

        $result = $query->row_array();

        $data = new DateTime($result['abertura']);
        $result['abertura'] = $data->format('d/m/Y H:i:s');

        if ($result['atendimento'] !== NULL) {
            $data = new DateTime($result['atendimento']);
            $result['atendimento'] = $data->format('d/m/Y H:i:s');
        }

        if ($result['encerramento'] !== NULL) {
            $data = new DateTime($result['encerramento']);
            $result['encerramento'] = $data->format('d/m/Y H:i:s');
        }

        $this->db->select('arquivos.id, arquivos.nome')->from('phpmycall.arquivos');
        $this->db->join('phpmycall.solicitacao', 'arquivos.solicitacao = solicitacao.id', 'inner');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto_tipo_problema.projeto = projeto_responsaveis.projeto', 'inner');
        $this->db->where(array('arquivos.solicitacao' => $solicitacao, 'projeto_responsaveis.usuario' => $usuario));

        $result['arquivos'] = $this->db->get()->result_array();

        return $result;
    }

    /**
     * Busca dados de um solicitação
     * @param int $solicitacao <b>ID</b> da solicitação
     * @param int $usuario <b>ID</b> do usuário que deseja visualizar dados
     * @param string $perfil <b>perfil</b> do usuário.
     * @return Array Retorna um <b>Array</b> com dados referentes a solicitação.
     */
    public function get_solicitacao($solicitacao, $usuario, $perfil) {
        $config = Parametros_solicitacoes::get_parametros();

        $select = "projeto_tipo_problema.id AS projeto_problema,
                    projeto.id AS projeto,
                    tipo_problema.id AS problema,
                    solicitacao.id AS solicitacao,
                    solicitacao.prioridade AS prioridade,
                    solicitacao.solicitante AS solicitante,
                    solicitacao.atendente AS atendente,
                    solicitacao.tecnico AS tecnico,
                    solicitacao.solicitacao_origem AS solicitacao_origem,
                    solicitacao.descricao AS descricao";

        $this->db->select($select);
        $this->db->from('phpmycall.solicitacao');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto', 'projeto_tipo_problema.projeto = projeto.id', 'inner');
        $this->db->join('phpmycall.tipo_problema', 'projeto_tipo_problema.problema = tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto.id = projeto_responsaveis.projeto', 'inner');
        $this->db->where(array('solicitacao.id' => $solicitacao, 'projeto_responsaveis.usuario' => $usuario));

        if (array_search($perfil, $config['VISUALIZAR_SOLICITACAO']) === FALSE) {
            $this->db->group_start();
            $this->db->or_where(array('solicitacao.solicitante' => $usuario, 'solicitacao.atendente' => $usuario, 'solicitacao.tecnico' => $usuario));
            $this->db->group_end();
        }

        /*
         * Dados referente a solicitação
         */
        $result = $this->db->get()->row_array();

        /*
         * Dados referentes aos arquivos anexos
         */
        $this->db->select('id, nome')->from('phpmycall.arquivos');
        $result['arquivos'] = $this->db->where(array('solicitacao' => $solicitacao))->get()->result_array();

        return $result;
    }

    /**
     * Remove arquivo anexo a uma solicitação
     * @param int $arquivo <b>ID</b> do anexo.
     * @param int $projeto_tipo_problema <b>ID</b> do tipo de problema.
     * @param int $usuario <b>ID</b> do usuário.
     * @return boolean Retorna <b>TRUE</b> se sucesso, <b>FALSE</b> falha.
     */
    public function remover_arquivo($arquivo, $projeto_tipo_problema, $usuario) {
        $this->db->select('projeto_responsaveis.usuario')->from('phpmycall.projeto_responsaveis');
        $this->db->join('phpmycall.projeto_tipo_problema', 'projeto_responsaveis.projeto = projeto_tipo_problema.projeto', 'inner');
        $this->db->where(array('projeto_responsaveis.usuario' => $usuario, 'projeto_tipo_problema.id' => $projeto_tipo_problema));
        $result = $this->db->get()->row_array();

        $caminho = $this->select('caminho')->from('phpmycall.arquivos')->where(array('id' => $arquivo))->get()->row_array();

        if (isset($result['usuario']) && unlink($caminho['caminho'])) {
            $this->db->where(array('id' => $arquivo));
            $result = $this->db->delete("phpmycall.arquivos");
        } else {
            $result = FALSE;
        }

        return $result;
    }

    /**
     * Atualiza dados de uma solicitação em aberto
     * @param Array $dados Array com dados a ser alterados da solicitação.
     * @param int $solicitacao <b>ID</b> da solicitação a ser alterada.
     * @return boolean Retorna <b>TRUE</b> se sucesso, <b>FALSE</b> falha.
     */
    public function atualiza_solicitacao($dados, $solicitacao) {
        /*
         * Atualiza apenas se a solicitação não esta
         * sendo atendida por um técnico.
         */
        $this->db->where("id = {$solicitacao} AND abertura = atendimento");
        return $this->db->update('phpmycall.solicitacao', $dados);
    }

    /**
     * Busca lista de tipo de feedback.
     * @return Array Retorno array com código e tipo de feedback.
     */
    public function get_tipo_feedback() {
        return $this->db->select('id, nome')->from('phpmycall.tipo_feedback')->get()->result_array();
    }

    /**
     * Busca todas os feedback para esta solicitação.
     * @param int $solicitacao Código da solicitação.
     * @return Array Dados referentes aos feedback da solicitação.
     */
    public function feedback_pendentes_atendidos($solicitacao) {
        $sql = "SELECT feedback.id,
                    CONCAT(SUBSTRING(pergunta from 0 for 40), '...') AS pergunta,
                    CONCAT(SUBSTRING(resposta from 0 for 40), '...') AS resposta,
                    inicio,
                    CASE WHEN fim = inicio THEN NULL
                    ELSE fim END AS fim,
                    CASE WHEN fim = inicio THEN TRUE
                    ELSE FALSE END AS aberta,
                    usuario.nome AS nome_responsavel,
                    responsavel
                FROM phpmycall.feedback
                INNER JOIN phpmycall.usuario ON feedback.responsavel = usuario.id
                WHERE solicitacao = ?
                ORDER BY feedback.inicio DESC";

        $result = $this->db->query($sql, array($solicitacao))->result_array();

        foreach ($result as $key => $values) {
            $data = new DateTime($values['inicio']);
            $result[$key]['inicio'] = $data->format('d/m/Y H:i:s');

            $data = new DateTime($values['fim']);
            $result[$key]['fim'] = $data->format('d/m/Y H:i:s');
        }

        return $result;
    }

    /**
     * Realiza o atendimento de um solicitação em aberto.
     * @param string $hoje <b>Data e Hora</b> do inicio do atendimento, no formato <i>ANO-MÊS-DIA HORA:MINUTOS:SEGUNDOS</i>.
     * @param int $solicitacao <b>ID</b> da solicitação.
     * @param int $usuario <b>ID</b> do usuário.
     * @return Array Retorna array com mensagem da operação, e <b>TRUE</b> se sucesso ou <b>FALSE</b> se erro.
     */
    public function atender_solicitacao($hoje, $solicitacao, $usuario) {
        $this->db->select('solicitacao.id, solicitacao.abertura')->from('phpmycall.solicitacao');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto_tipo_problema.projeto = projeto_responsaveis.projeto', 'inner');
        $this->db->group_start()->or_where(array('solicitacao.tecnico' => NULL))->or_where(array('solicitacao.tecnico' => $usuario))->group_end();
        $this->db->where(array('projeto_responsaveis.usuario' => $usuario, 'solicitacao.id' => $solicitacao));
        $this->db->where('solicitacao.abertura = solicitacao.atendimento');
        $result = $this->db->get()->row_array();

        if (isset($result['id'])) {
            $dados = array(
                'abertura' => $result['abertura'],
                'atendimento' => $hoje,
                'encerramento' => $hoje,
                'tecnico' => $usuario
            );

            $this->db->where('id', $solicitacao);

            if ($this->db->update('phpmycall.solicitacao', $dados)) {
                $result['msg'] = "Solicitação em atendimento.";
                $result['status'] = TRUE;
            } else {
                $result['msg'] = "Falha ao iniciar atendimento";
                $result['status'] = FALSE;
            }
        } else {
            $result['msg'] = "Não permitida o atendimento desta solicitação. Verifique se está solicitação já possui técnico.";
            $result['status'] = FALSE;
        }

        return $result;
    }

    /**
     * Verifica status da solicitação.
     * @param int $solicitacao
     * @return string Retorna o status da solicitação <b>aberta</b>, <b>atendimento</b> e <b>encerrada</b>.
     */
    public function status_solicitacao($solicitacao) {
        $select = "CASE WHEN abertura = atendimento THEN 'aberta'
                    WHEN abertura < atendimento AND atendimento = encerramento THEN 'atendimento'
                    ELSE 'encerrada' END AS status";

        $result = $this->db->select($select)->from('phpmycall.solicitacao')->where(array('id' => $solicitacao))->get()->row_array();

        return $result['status'];
    }

    /**
     * Exclui uma solicitação em aberto ou em atendimento.
     * @param int $solicitacao <b>ID</b> da solicitação.
     * @param int $usuario <b>ID</b> do usuário.
     * @return Array Retorna array com mensagem da operação, e <b>TRUE</b> se sucesso ou <b>FALSE</b> se erro.
     */
    public function excluir_solicitacao($solicitacao, $usuario) {
        $this->db->select('solicitacao.id')->from('phpmycall.solicitacao');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto_tipo_problema.projeto = projeto_responsaveis.projeto', 'inner');
        $this->db->where(array('projeto_responsaveis.usuario' => $usuario, 'solicitacao.id' => $solicitacao));
        $this->db->group_start()->where('solicitacao.abertura = solicitacao.atendimento');
        $result = $this->db->or_where('solicitacao.atendimento = solicitacao.encerramento')->group_end()->get()->row_array();


        if (isset($result['id'])) {

            /*
             * Remove os arquivos anexos para
             * depois remover os arquivos no banco de dados
             */
            $caminho = $this->db->select('caminho')->from('phpmycall.arquivos')->where(array('solicitacao' => $solicitacao))->get()->result_array();
            $delete_files = true;

            foreach ($caminho as $values) {
                $delete_files &= unlink($values['caminho']);
            }

            $this->db->where(array('solicitacao' => $solicitacao));
            $delete_files &= $delete_files ? $this->db->delete('phpmycall.feedback') : FALSE;

            $this->db->where(array('solicitacao' => $solicitacao));
            $delete_files &= $delete_files ? $this->db->delete('phpmycall.arquivos') : FALSE;

            $this->db->where(array('id' => $solicitacao));
            $delete_files &= $delete_files ? $this->db->delete('phpmycall.solicitacao') : FALSE;

            if ($delete_files) {
                $result['msg'] = "Solicitação excluida.";
                $result['status'] = TRUE;
            } else {
                $result['msg'] = "Falha ao excluir solicitação";
                $result['status'] = FALSE;
            }
        } else {
            $result['msg'] = "Exclusão não permitida desta solicitação.";
            $result['status'] = FALSE;
        }

        return $result;
    }

    /**
     * Verifica se usuário é participantes do projeto.
     * @param int $usuario Código do usuário
     * @param int $solicitacao Código da solicitação
     * @return boolean Retorna <b>TRUE</b> se usuário tem permissão, <b>FALSE</b> caso contrário.
     */
    public function usuario_participante($usuario, $solicitacao) {
        $this->db->select('solicitacao.id')->from('phpmycall.projeto_responsaveis');
        $this->db->join('phpmycall.projeto_tipo_problema', 'projeto_responsaveis.projeto = projeto_tipo_problema.projeto', 'inner');
        $this->db->join('phpmycall.solicitacao', 'projeto_tipo_problema.id = solicitacao.projeto_problema', 'inner');
        $this->db->where(array('projeto_responsaveis.usuario' => $usuario, 'solicitacao.id' => $solicitacao));
        $result = $this->db->get()->row_array();

        return isset($result['id']);
    }

    /**
     * Realiza redirecionamento de uma solicitação em atendimento para
     * outro técnico
     * @param int $usuario Código do usuário.
     * @param int $solicitacao Código da solicitação.
     * @param int $tecnico Código do técnico que ficara responsavel.
     * @return Array Retorna um array com o resultado da operação realizada.
     */
    public function redirecionar_solicitacao($usuario, $solicitacao, $tecnico) {
        $this->db->select('solicitacao.id')->from('phpmycall.solicitacao');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto_tipo_problema.projeto = projeto_responsaveis.projeto', 'inner');
        $this->db->where(array('projeto_responsaveis.usuario' => $usuario, 'solicitacao.id' => $solicitacao));
        $this->db->group_start()->where('solicitacao.abertura < solicitacao.atendimento');
        $result = $this->db->or_where('solicitacao.atendimento = solicitacao.encerramento')->group_end()->get()->row_array();

        /*
         * Verifica se o perfil do usuário tem autorização para
         * redirecionar uma solicitação e se este tem permissão dentro do projeto.
         */
        if (isset($result['id'])) {

            $dados = array('tecnico' => $tecnico);

            /*
             * Realização operação de redirecionamento e informa resultado da operação.
             */
            $this->db->where(array('id' => $solicitacao));

            if ($this->db->update('phpmycall.solicitacao', $dados)) {
                $result['msg'] = "Solicitação redirecionada com sucesso";
                $result['status'] = TRUE;
            } else {
                $result['msg'] = "Erro ao redirecionar solicitação a outro técnico.";
                $result['status'] = FALSE;
            }
        } else {
            /*
             * Informa erro de permissão no redirecionamento.
             */
            $result['msg'] = "Redirecionamento de solicitação não permitida.";
            $result['status'] = FALSE;
        }

        return $result;
    }

    /**
     * Grava um novo feedback
     * @param Array $dados Array com os dados necessários.
     * @return boolean <b>TRUE</b> sucesso, <b>FALSE</b> erro.
     */
    public function feedback($dados) {
        return $this->db->insert('phpmycall.feedback', $dados);
    }

    /**
     * Busca Pergunta e resposta de um feedback.
     * @param int $id_feedback Código do feedback.
     * @param int $usuario Código do solicitante.
     * @return Array Retorna array com pergunta e resposta.
     */
    public function get_pergunta_resposta_feedback($id_feedback, $usuario) {
        $this->db->select('feedback.pergunta, feedback.resposta')->from('phpmycall.feedback');
        $this->db->join('phpmycall.solicitacao', 'feedback.solicitacao = solicitacao.id', 'inner');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto_tipo_problema.projeto = projeto_responsaveis.projeto', 'inner');
        $result = $this->db->where(array('feedback.id' => $id_feedback, 'projeto_responsaveis.usuario' => $usuario))->get();

        return $result->row_array();
    }

    /**
     * Método que grava resposta do feedback.
     * @param array $dados Array com resposta e horario da resposta.
     * @param int $id_feedback Código do feedback.
     * @return boolean Retorna <b>TRUE</b> sucesso, <b>FALSE</b> erro.
     */
    public function responder_feedback($dados, $id_feedback) {
        $this->db->where(array('id' => $id_feedback));
        return $this->db->update('phpmycall.feedback', $dados);
    }

    /**
     * Encerra uma solicitação.
     * @param int $solicitacao Código da solicitação.
     * @param Array $dados Array com horário da finalização da solicitação.
     * @return boolean Retorna <b>TRUE</b> se sucesso, <b>FALSE</b> se erro.
     */
    public function encerrar($solicitacao, $dados) {
        $this->db->where(array('id' => $solicitacao));
        return $this->db->update('phpmycall.solicitacao', $dados);
    }

    /**
     * Método que busca dados referente ao arquivo anexo.
     * @param type $arquivo Código do arquivo
     * @param type $usuario Código do usuário
     * @return Array Retorna dados do arquivo anexo de um solicitação.
     */
    public function get_content_arquivo($arquivo, $usuario) {
        $this->db->select('arquivos.nome, arquivos.tipo, arquivos.caminho')->from('phpmycall.arquivos');
        $this->db->join('phpmycall.solicitacao', 'arquivos.solicitacao = solicitacao.id', 'inner');
        $this->db->join('phpmycall.projeto_tipo_problema', 'solicitacao.projeto_problema = projeto_tipo_problema.id', 'inner');
        $this->db->join('phpmycall.projeto_responsaveis', 'projeto_tipo_problema.projeto = projeto_responsaveis.projeto', 'inner');
        $arquivos = $this->db->where(array('arquivos.id' => $arquivo, 'projeto_responsaveis.usuario' => $usuario))->get();

        return $arquivos->row_array();
    }

}
