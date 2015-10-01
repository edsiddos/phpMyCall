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
        $parametros = $this->get_parametros();

        $tecnicos = "'" . implode("', '", $parametros['ATENDER_SOLICITACAO']) . "'";

        $this->db->select("usuario.id, usuario.nome, perfil.perfil IN ({$tecnicos})::int AS tecnico");
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
        $parametros = $this->get_parametros();

        $tecnicos = "'" . implode("', '", $parametros['ATENDER_SOLICITACAO']) . "'";

        $this->db->select("usuario.id, usuario.nome, perfil.perfil IN ({$tecnicos})::int AS tecnico");
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
    public function get_solicitacoes($usuario, $perfil, $situacao = 1) {
        $config = $this->get_parametros();

        $sql = "SELECT projeto.nome AS projeto,
                    tipo_problema.nome AS problema,
                    prioridade.nome AS prioridade,
                    solicitante.nome AS solicitante,
                    atendente.nome AS atendente,
                    TO_CHAR(solicitacao.abertura, 'FMDD/MM/YYYY  HH24:MI:SS') AS abertura,
                    solicitacao.id AS solicitacao,
                    COUNT(arquivos.id) AS arquivos
                FROM phpmycall.solicitacao
                INNER JOIN phpmycall.usuario AS solicitante ON solicitante.id = solicitacao.solicitante
                INNER JOIN phpmycall.usuario AS atendente ON atendente.id = solicitacao.atendente
                INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                INNER JOIN phpmycall.projeto ON projeto_tipo_problema.projeto = projeto.id
                INNER JOIN phpmycall.tipo_problema ON projeto_tipo_problema.problema = tipo_problema.id
                INNER JOIN phpmycall.prioridade ON solicitacao.prioridade = prioridade.id
                INNER JOIN phpmycall.projeto_responsaveis ON projeto.id = projeto_responsaveis.projeto
                LEFT JOIN phpmycall.arquivos ON solicitacao.id = arquivos.solicitacao
                WHERE projeto_responsaveis.usuario = :usuario ";

        /*
         * Conforme o status da solicitação muda na montagem da sql.
         */
        if ($situacao == 1) {
            $sql .= " AND solicitacao.abertura = solicitacao.atendimento AND solicitacao.encerramento = solicitacao.atendimento";
        } else if ($situacao == 2) {
            $sql .= " AND solicitacao.abertura < solicitacao.atendimento AND solicitacao.encerramento = solicitacao.atendimento";
        } else {
            $sql .= " AND solicitacao.abertura < solicitacao.atendimento AND solicitacao.atendimento < solicitacao.encerramento";
        }

        /*
         * Verifica se o perfil tem autorização de visualizar todas as solicitações
         * dentro do projeto que o mesmo esteja vinculado
         */
        if (array_search($perfil, $config['VISUALIZAR_SOLICITACAO']) === FALSE) {
            $sql .= " AND (solicitacao.solicitante = :usuario OR solicitacao.atendente = :usuario OR solicitacao.tecnico = :usuario)";
        }

        $sql .= " GROUP BY projeto.nome,
                    tipo_problema.nome,
                    prioridade.nome,
                    solicitante.nome,
                    atendente.nome,
                    solicitacao.abertura,
                    solicitacao.id,
                    prioridade.nivel
                ORDER BY prioridade.nivel, solicitacao.abertura";

        return $this->select($sql, array('usuario' => $usuario));
    }

    /**
     * Método que busca todos os parametros referente a solicitações.
     * @return Array Retorna todos os parametros referente a solicitações.
     */
    public function getParametros() {
        $parametros = Cache::getCache(PARAMETROS);

        if (empty($parametros['VISUALIZAR_SOLICITACAO']) || empty($parametros['CORES_SOLICITACOES']) ||
                empty($parametros['DIRECIONAR_CHAMADO']) || empty($parametros['REDIRECIONAR_CHAMADO']) ||
                empty($parametros['EDITAR_SOLICITACAO']) || empty($parametros['ATENDER_SOLICITACAO']) ||
                empty($parametros['ENCERRAR_SOLICITACAO']) || empty($parametros['EXCLUIR_SOLICITACAO'])) {
            Cache::deleteCache(PARAMETROS);

            unset($parametros['VISUALIZAR_SOLICITACAO']);
            unset($parametros['CORES_SOLICITACOES']);
            unset($parametros['DIRECIONAR_CHAMADO']);
            unset($parametros['REDIRECIONAR_CHAMADO']);
            unset($parametros['EDITAR_SOLICITACAO']);
            unset($parametros['ATENDER_SOLICITACAO']);
            unset($parametros['ENCERRAR_SOLICITACAO']);
            unset($parametros['EXCLUIR_SOLICITACAO']);

            $parametros['VISUALIZAR_SOLICITACAO'] = $this->getDadosParametros('VISUALIZAR_SOLICITACAO');
            $parametros['CORES_SOLICITACOES'] = $this->getDadosParametros('CORES_SOLICITACOES');
            $parametros['DIRECIONAR_CHAMADO'] = $this->getDadosParametros('DIRECIONAR_CHAMADO');
            $parametros['REDIRECIONAR_CHAMADO'] = $this->getDadosParametros('REDIRECIONAR_CHAMADO');
            $parametros['EDITAR_SOLICITACAO'] = $this->getDadosParametros('EDITAR_SOLICITACAO');
            $parametros['ATENDER_SOLICITACAO'] = $this->getDadosParametros('ATENDER_SOLICITACAO');
            $parametros['ENCERRAR_SOLICITACAO'] = $this->getDadosParametros('ENCERRAR_SOLICITACAO');
            $parametros['EXCLUIR_SOLICITACAO'] = $this->getDadosParametros('EXCLUIR_SOLICITACAO');

            $sql = "SELECT prioridade.nome, prioridade.cor FROM phpmycall.prioridade ORDER BY prioridade.id";
            $result = $this->select($sql, array());
            foreach ($result as $values) {
                $parametros['CORES_SOLICITACOES'][$values['nome']] = $values['cor'];
            }

            Cache::setCache(PARAMETROS, $parametros);
        }

        return $parametros;
    }

    /**
     * Pesquisa dados dos parametros de configuração referentes a solicitação
     * @param string $parametro Nome do parametro
     * @return Array Retorna um <b>Array</b> com os perfil.
     */
    private function getDadosParametros($parametro) {
        $sql = "SELECT config.texto FROM phpmycall.config WHERE config.parametro = :parametro";
        $perfil = $this->select($sql, array('parametro' => $parametro), FALSE);

        $sql = "SELECT perfil.perfil FROM phpmycall.perfil WHERE perfil.id IN ({$perfil['texto']})";
        $result = $this->select($sql);
        foreach ($result as $values) {
            $return[] = $values['perfil'];
        }

        return $return;
    }

    /**
     * Busca dados da solicitação caso o usuário seja participantes do projeto.
     * @param int $solicitacao Código da Solicitação.
     * @param string $perfil Perfil do usuário.
     * @param int $usuario Código do Usuário.
     * @return Array Retorna <b>Array</b> com dados de uma determinada solicitação
     */
    public function getDadosSolicitacao($solicitacao, $perfil, $usuario) {
        $config = $this->getParametros();

        $sql = "SELECT projeto_tipo_problema.id AS projeto_problema,
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
                    TO_CHAR(solicitacao.abertura, 'FMDD/MM/YYYY  HH24:MI:SS') AS abertura,
                    CASE WHEN solicitacao.abertura = solicitacao.atendimento THEN NULL
                    ELSE TO_CHAR(solicitacao.atendimento, 'FMDD/MM/YYYY  HH24:MI:SS') END AS atendimento,
                    CASE WHEN solicitacao.atendimento = solicitacao.encerramento THEN NULL
                    ELSE TO_CHAR(solicitacao.encerramento, 'FMDD/MM/YYYY  HH24:MI:SS') END AS encerramento
                FROM phpmycall.solicitacao
                INNER JOIN phpmycall.usuario AS solicitante ON solicitante.id = solicitacao.solicitante
                INNER JOIN phpmycall.usuario AS atendente ON atendente.id = solicitacao.atendente
                LEFT JOIN phpmycall.usuario AS tecnico ON tecnico.id = solicitacao.tecnico
                INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                INNER JOIN phpmycall.projeto ON projeto_tipo_problema.projeto = projeto.id
                INNER JOIN phpmycall.tipo_problema ON projeto_tipo_problema.problema = tipo_problema.id
                INNER JOIN phpmycall.prioridade ON solicitacao.prioridade = prioridade.id
                INNER JOIN phpmycall.projeto_responsaveis ON projeto.id = projeto_responsaveis.projeto
                WHERE solicitacao.id = :solicitacao AND projeto_responsaveis.usuario = :usuario";

        if (array_search($perfil, $config['VISUALIZAR_SOLICITACAO']) === FALSE) {
            $sql .= " AND (solicitacao.solicitante = :usuario OR solicitacao.atendente = :usuario OR solicitacao.tecnico = :usuario)";
        }

        $result = $this->select($sql, array('solicitacao' => $solicitacao, 'usuario' => $usuario), FALSE);

        $sql = "SELECT arquivos.id,
                    arquivos.nome
                FROM phpmycall.arquivos
                INNER JOIN phpmycall.solicitacao ON arquivos.solicitacao = solicitacao.id
                INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                INNER JOIN phpmycall.projeto_responsaveis ON projeto_tipo_problema.projeto = projeto_responsaveis.projeto
                WHERE arquivos.solicitacao = :solicitacao
                    AND projeto_responsaveis.usuario = :usuario";

        $result['arquivos'] = $this->select($sql, array('solicitacao' => $solicitacao, 'usuario' => $usuario));

        return $result;
    }

    /**
     * Busca dados de um solicitação
     * @param int $solicitacao <b>ID</b> da solicitação
     * @param int $usuario <b>ID</b> do usuário que deseja visualizar dados
     * @param string $perfil <b>perfil</b> do usuário.
     * @return Array Retorna um <b>Array</b> com dados referentes a solicitação.
     */
    public function getSolicitacao($solicitacao, $usuario, $perfil) {
        $config = $this->getParametros();

        $sql = "SELECT projeto_tipo_problema.id AS projeto_problema,
                    projeto.id AS projeto,
                    tipo_problema.id AS problema,
                    solicitacao.id AS solicitacao,
                    solicitacao.prioridade AS prioridade,
                    solicitacao.solicitante AS solicitante,
                    solicitacao.atendente AS atendente,
                    solicitacao.tecnico AS tecnico,
                    solicitacao.solicitacao_origem AS solicitacao_origem,
                    solicitacao.descricao AS descricao
                FROM phpmycall.solicitacao
                INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                INNER JOIN phpmycall.projeto ON projeto_tipo_problema.projeto = projeto.id
                INNER JOIN phpmycall.tipo_problema ON projeto_tipo_problema.problema = tipo_problema.id
                INNER JOIN phpmycall.projeto_responsaveis ON projeto.id = projeto_responsaveis.projeto
                WHERE solicitacao.id = :solicitacao AND projeto_responsaveis.usuario = :usuario";

        if (array_search($perfil, $config['VISUALIZAR_SOLICITACAO']) === FALSE) {
            $sql .= " AND (solicitacao.solicitante = :usuario OR solicitacao.atendente = :usuario OR solicitacao.tecnico = :usuario)";
        }

        /*
         * Dados referente a solicitação
         */
        $result = $this->select($sql, array('solicitacao' => $solicitacao, 'usuario' => $usuario), FALSE);

        $sql = "SELECT arquivos.id,
                    arquivos.nome
                FROM phpmycall.arquivos WHERE arquivos.solicitacao = :solicitacao";

        /*
         * Dados referentes aos arquivos anexos
         */
        $result['arquivos'] = $this->select($sql, array('solicitacao' => $solicitacao));

        return $result;
    }

    /**
     * Remove arquivo anexo a uma solicitação
     * @param int $arquivo <b>ID</b> do anexo.
     * @param int $projeto_tipo_problema <b>ID</b> do tipo de problema.
     * @param int $usuario <b>ID</b> do usuário.
     * @return boolean Retorna <b>TRUE</b> se sucesso, <b>FALSE</b> falha.
     */
    public function removerArquivo($arquivo, $projeto_tipo_problema, $usuario) {
        $sql = "SELECT EXISTS(SELECT projeto_responsaveis.usuario
                    FROM phpmycall.projeto_responsaveis
                    INNER JOIN phpmycall.projeto_tipo_problema ON projeto_responsaveis.projeto = projeto_tipo_problema.projeto
                    WHERE projeto_tipo_problema.id = :projeto_tipo_problema
                        AND projeto_responsaveis.usuario = :usuario
                ) AS result";

        $result = $this->select($sql, array('usuario' => $usuario, 'projeto_tipo_problema' => $projeto_tipo_problema), FALSE);

        $sql = "SELECT caminho FROM phpmycall.arquivos WHERE id = :id";
        $caminho = $this->select($sql, array('id' => $arquivo), FALSE);

        if ($result['result'] && unlink($caminho['caminho'])) {

            $result = $this->delete("phpmycall.arquivos", "id = {$arquivo}");
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
    public function atualizaSolicitacao($dados, $solicitacao) {
        /*
         * Atualiza apenas se a solicitação não esta
         * sendo atendida por um técnico.
         */
        return $this->update('phpmycall.solicitacao', $dados, "id = {$solicitacao} AND abertura = atendimento");
    }

    /**
     * Busca lista de tipo de feedback.
     * @return Array Retorno array com código e tipo de feedback.
     */
    public function getTipoFeedback() {
        $sql = "SELECT id, nome FROM phpmycall.tipo_feedback";

        return $this->select($sql);
    }

    /**
     * Busca todas os feedback para esta solicitação.
     * @param int $solicitacao Código da solicitação.
     * @return Array Dados referentes aos feedback da solicitação.
     */
    public function feedbackPendentesAtendidos($solicitacao) {
        $sql = "SELECT feedback.id,
                    substring(pergunta from 0 for 30) || '...' AS pergunta,
                    substring(resposta from 0 for 30) || '...' AS resposta,
                    TO_CHAR(inicio, 'FMDD/MM/YYYY  HH24:MI:SS') AS inicio,
                    CASE WHEN fim = inicio THEN NULL
                    ELSE TO_CHAR(fim, 'FMDD/MM/YYYY  HH24:MI:SS') END AS fim,
                    CASE WHEN fim = inicio THEN TRUE
                    ELSE FALSE END AS aberta,
                    usuario.nome AS nome_responsavel,
                    responsavel
                FROM phpmycall.feedback
                INNER JOIN phpmycall.usuario ON feedback.responsavel = usuario.id
                WHERE solicitacao = :solicitacao
                ORDER BY feedback.inicio DESC";

        return $this->select($sql, array('solicitacao' => $solicitacao));
    }

    /**
     * Realiza o atendimento de um solicitação em aberto.
     * @param string $hoje <b>Data e Hora</b> do inicio do atendimento, no formato <i>ANO-MÊS-DIA HORA:MINUTOS:SEGUNDOS</i>.
     * @param int $solicitacao <b>ID</b> da solicitação.
     * @param int $usuario <b>ID</b> do usuário.
     * @return Array Retorna array com mensagem da operação, e <b>TRUE</b> se sucesso ou <b>FALSE</b> se erro.
     */
    public function atenderSolicitacao($hoje, $solicitacao, $usuario) {
        $sql = "SELECT EXISTS(
                    SELECT solicitacao.id
                    FROM phpmycall.solicitacao
                    INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                    INNER JOIN phpmycall.projeto_responsaveis ON projeto_tipo_problema.projeto = projeto_responsaveis.projeto
                    WHERE (solicitacao.tecnico IS NULL OR solicitacao.tecnico = :usuario)
                        AND projeto_responsaveis.usuario = :usuario
                        AND solicitacao.id = :solicitacao
                        AND solicitacao.abertura = solicitacao.atendimento
                ) AS autorizado";

        $result = $this->select($sql, array('usuario' => $usuario, 'solicitacao' => $solicitacao), FALSE);

        if ($result['autorizado'] == TRUE) {
            $dados = array(
                'atendimento' => $hoje,
                'encerramento' => $hoje,
                'tecnico' => $usuario
            );

            $where = "id = {$solicitacao}";

            if ($this->update('phpmycall.solicitacao', $dados, $where)) {
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
    public function statusSolicitacao($solicitacao) {
        $sql = "SELECT CASE WHEN abertura = atendimento THEN 'aberta'
                    WHEN abertura < atendimento AND atendimento = encerramento THEN 'atendimento'
                    ELSE 'encerrada' END AS status
                FROM phpmycall.solicitacao
                WHERE solicitacao.id = :solicitacao";

        $result = $this->select($sql, array('solicitacao' => $solicitacao), FALSE);

        return $result['status'];
    }

    /**
     * Exclui uma solicitação em aberto ou em atendimento.
     * @param int $solicitacao <b>ID</b> da solicitação.
     * @param int $usuario <b>ID</b> do usuário.
     * @return Array Retorna array com mensagem da operação, e <b>TRUE</b> se sucesso ou <b>FALSE</b> se erro.
     */
    public function excluirSolicitacao($solicitacao, $usuario) {
        $sql = "SELECT EXISTS(
                    SELECT solicitacao.id
                    FROM phpmycall.solicitacao
                    INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                    INNER JOIN phpmycall.projeto_responsaveis ON projeto_tipo_problema.projeto = projeto_responsaveis.projeto
                    WHERE projeto_responsaveis.usuario = :usuario
                        AND solicitacao.id = :solicitacao
                        AND (solicitacao.abertura = solicitacao.atendimento OR solicitacao.atendimento = solicitacao.encerramento)
                ) AS autorizado";

        $result = $this->select($sql, array('usuario' => $usuario, 'solicitacao' => $solicitacao), FALSE);

        if ($result['autorizado'] == TRUE) {

            $where = "id = {$solicitacao}";
            $arquivos = "solicitacao = {$solicitacao}";
            $feedback = "solicitacao = {$solicitacao}";

            /*
             * Remove os arquivos anexos para
             * depois remover os arquivos no banco de dados
             */
            $sql = "SELECT caminho FROM phpmycall.arquivos WHERE solicitacao = :solicitacao";
            $caminho = $this->select($sql, array('solicitacao' => $solicitacao));
            $delete_files = true;

            foreach ($caminho as $values) {
                $delete_files &= unlink($values['caminho']);
            }

            if ($this->delete('phpmycall.feedback', $feedback) && $this->delete('phpmycall.arquivos', $arquivos) && $this->delete('phpmycall.solicitacao', $where)) {
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
    public function usuarioParticipante($usuario, $solicitacao) {
        $sql = "SELECT EXISTS(SELECT solicitacao.id FROM phpmycall.projeto_responsaveis
                    INNER JOIN phpmycall.projeto_tipo_problema ON projeto_responsaveis.projeto = projeto_tipo_problema.problema
                    INNER JOIN phpmycall.solicitacao ON projeto_tipo_problema.id = solicitacao.projeto_problema
                    WHERE projeto_responsaveis.usuario = :usuario
                        AND solicitacao.id = :solicitacao
                ) AS responsavel";

        $result = $this->select($sql, array('usuario' => $usuario, 'solicitacao' => $solicitacao), FALSE);

        return $result['responsavel'];
    }

    /**
     * Realiza redirecionamento de uma solicitação em atendimento para
     * outro técnico
     * @param int $usuario Código do usuário.
     * @param int $solicitacao Código da solicitação.
     * @param int $tecnico Código do técnico que ficara responsavel.
     * @return Array Retorna um array com o resultado da operação realizada.
     */
    public function redirecionarSolicitacao($usuario, $solicitacao, $tecnico) {
        $sql = "SELECT EXISTS(
                    SELECT solicitacao.id
                    FROM phpmycall.solicitacao
                    INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                    INNER JOIN phpmycall.projeto_responsaveis ON projeto_tipo_problema.projeto = projeto_responsaveis.projeto
                    WHERE projeto_responsaveis.usuario = :usuario
                        AND solicitacao.id = :solicitacao
                        AND (solicitacao.abertura < solicitacao.atendimento OR solicitacao.atendimento = solicitacao.encerramento)
                ) AS autorizado";

        $result = $this->select($sql, array('usuario' => $usuario, 'solicitacao' => $solicitacao), FALSE);

        /*
         * Verifica se o perfil do usuário tem autorização para
         * redirecionar uma solicitação e se este tem permissão dentro do projeto.
         */
        if ($result['autorizado'] == TRUE) {

            $dados = array('tecnico' => $tecnico);
            $where = "id = {$solicitacao}";

            /*
             * Realização operação de redirecionamento e informa resultado da operação.
             */
            if ($this->update('phpmycall.solicitacao', $dados, $where)) {
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
        return $this->insert('phpmycall.feedback', $dados);
    }

    /**
     * Busca Pergunta e resposta de um feedback.
     * @param int $id_feedback Código do feedback.
     * @param int $usuario Código do solicitante.
     * @return Array Retorna array com pergunta e resposta.
     */
    public function getPerguntaRespostaFeedback($id_feedback, $usuario) {
        $sql = "SELECT feedback.pergunta,
                    feedback.resposta
                FROM phpmycall.feedback
                INNER JOIN phpmycall.solicitacao ON feedback.solicitacao = solicitacao.id
                INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                INNER JOIN phpmycall.projeto_responsaveis ON projeto_tipo_problema.projeto = projeto_responsaveis.projeto
                WHERE feedback.id = :feedback
                    AND projeto_responsaveis.usuario = :usuario";

        return $this->select($sql, array('feedback' => $id_feedback, 'usuario' => $usuario), FALSE);
    }

    /**
     * Método que grava resposta do feedback.
     * @param array $dados Array com resposta e horario da resposta.
     * @param int $id_feedback Código do feedback.
     * @return boolean Retorna <b>TRUE</b> sucesso, <b>FALSE</b> erro.
     */
    public function responderFeedback($dados, $id_feedback) {
        return $this->update('phpmycall.feedback', $dados, "id = {$id_feedback}");
    }

    /**
     * Encerra uma solicitação.
     * @param int $solicitacao Código da solicitação.
     * @param Array $dados Array com horário da finalização da solicitação.
     * @return boolean Retorna <b>TRUE</b> se sucesso, <b>FALSE</b> se erro.
     */
    public function encerrar($solicitacao, $dados) {
        return $this->update('phpmycall.solicitacao', $dados, "id = {$solicitacao}");
    }

    /**
     * Método que busca dados referente ao arquivo anexo.
     * @param type $arquivo Código do arquivo
     * @param type $usuario Código do usuário
     * @return Array Retorna dados do arquivo anexo de um solicitação.
     */
    public function getContentArquivo($arquivo, $usuario) {
        $sql = "SELECT arquivos.nome,
                    arquivos.tipo,
                    arquivos.caminho
                FROM phpmycall.arquivos
                INNER JOIN phpmycall.solicitacao ON arquivos.solicitacao = solicitacao.id
                INNER JOIN phpmycall.projeto_tipo_problema ON solicitacao.projeto_problema = projeto_tipo_problema.id
                INNER JOIN phpmycall.projeto_responsaveis ON projeto_tipo_problema.projeto = projeto_responsaveis.projeto
                WHERE arquivos.id = :arquivo
                    AND projeto_responsaveis.usuario = :usuario";

        $arquivos = $this->select($sql, array('arquivo' => $arquivo, 'usuario' => $usuario), FALSE);

        return $arquivos;
    }

}
