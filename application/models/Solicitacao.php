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

namespace application\models;

use system\Model,
    libs\Cache;

/**
 * Manipula inserção, atualização e consultas de solicitações.
 *
 * @author Ednei Leite da Silva
 */
class Solicitacao extends Model {

    /**
     * Retorna os projetos na qual o usuário é participantes.
     * @param int $usuario Código do usuário.
     * @return Array Retorna informação dos projetos e dos tipos de problemas
     */
    public function getProjetos($usuario) {
        $sql = "SELECT projeto_tipo_problema.id,
                    projeto.id AS id_projeto,
                    projeto.nome AS projeto,
                    tipo_problema.nome AS problema
                FROM phpmycall.projeto
                INNER JOIN phpmycall.projeto_tipo_problema ON projeto.id = projeto_tipo_problema.projeto
                INNER JOIN phpmycall.tipo_problema ON projeto_tipo_problema.problema = tipo_problema.id
                INNER JOIN phpmycall.projeto_responsaveis ON projeto.id = projeto_responsaveis.projeto
                WHERE projeto_responsaveis.usuario = :usuario
                ORDER BY projeto.nome, tipo_problema.nome";

        return $this->select($sql, array("usuario" => $usuario));
    }

    /**
     * Busca todas prioridades cadastradas.
     * @return Array Retorna relação de prioridades.
     */
    public function getPrioridades() {
        $sql = "SELECT id, nome, padrao FROM phpmycall.prioridade";

        return $this->select($sql);
    }

    /**
     * Relação de participantes do projeto.
     * @param int $projeto Código do projeto.
     * @return Array Retorna todos os usuários participantes de um projeto.
     */
    public function getSolicitantes($projeto) {
        $sql = "SELECT usuario.id,
                    usuario.nome,
                    usuario.perfil
                FROM phpmycall.usuario
                INNER JOIN phpmycall.projeto_responsaveis ON usuario.id = projeto_responsaveis.usuario
                INNER JOIN phpmycall.projeto_tipo_problema ON projeto_responsaveis.projeto = projeto_tipo_problema.projeto
                WHERE projeto_tipo_problema.id = :projeto
                ORDER BY usuario.nome";

        return $this->select($sql, array('projeto' => $projeto));
    }

    /**
     * Grava uma nova solicitação.
     * @param int $dados Array com dados da solicitação.
     * @return Mixed Retorna <b>Array</b> com id da solicitação, retorna <b>FALSE</b> se ocorrer ao gravar solicitação.
     */
    public function gravaSolicitacao($dados) {
        if ($this->insert('phpmycall.solicitacao', $dados)) {
            $sql = "SELECT solicitacao.id
                    FROM phpmycall.solicitacao
                    WHERE projeto_problema = :projeto_problema
                        AND descricao = :descricao
                        AND solicitante = :solicitante
                        AND prioridade = :prioridade
                        AND atendente = :atendente
                        AND abertura = :abertura
                        AND atendimento = :atendimento
                        AND encerramento = :encerramento
                        AND solicitacao_origem IS NULL
                        AND avaliacao IS NULL
                        AND justificativa_avaliacao IS NULL";

            $sql .= empty($dados['tecnico']) ? " AND tecnico IS NULL" : " AND tecnico = :tecnico";

            return $this->select($sql, $dados, FALSE);
        } else {
            return FALSE;
        }
    }

    /**
     * Grava arquivo no banco de dados.
     * @param Array $dados Array com ID da solicitação, nome do arquivo, tipo de arquivo.
     * @param Array $arquivos Array com caminho do arquivo.
     * @return boolean Retorna <b>TRUE</b> se sucesso, <b>FALSE</b> se falha
     */
    public function gravaArquivoSolicitacao($dados, $arquivos) {
        return $this->insertFile('phpmycall.arquivos', $dados, $arquivos);
    }

    /**
     * Busca todas as solicitações dos projetos que o usuário pertença.
     * @param int $usuario Id do usuário que solicitou visualização.
     * @param string $perfil Perfil do usuário que solicitou visualização.
     * @param int $situacao Status da solicitação 1 - <b>aberta</b>, 2 - <b>atendimento</b>, 3 - <b>encerrada</b>.
     * @return Array Retorna um array com todas as solicitações de um determinada situação
     */
    public function getSolicitacoes($usuario, $perfil, $situacao = 1) {
        $config = $this->getParametros();

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
    private function getParametros() {
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
     * @param int $usuario Código do Usuário.
     * @return Array Retorna <b>Array</b> com dados de uma determinada solicitação
     */
    public function getDadosSolicitacao($solicitacao, $usuario) {
        $config = $this->getParametros();

        $sql = "SELECT projeto.nome AS projeto,
                    tipo_problema.nome AS problema,
                    prioridade.nome AS prioridade,
                    solicitante.nome AS solicitante,
                    atendente.nome AS atendente,
                    tecnico.nome AS tecnico,
                    solicitacao.descricao AS descricao,
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
                LEFT JOIN phpmycall.arquivos ON solicitacao.id = arquivos.solicitacao
                WHERE solicitacao.id = :solicitacao AND projeto_responsaveis.usuario = :usuario";

        if (array_search($perfil, $config['VISUALIZAR_SOLICITACAO']) === FALSE) {
            $sql .= " AND (solicitacao.solicitante = :usuario OR solicitacao.atendente = :usuario OR solicitacao.tecnico = :usuario)";
        }

        $result = $this->select($sql, array('solicitacao' => $solicitacao, 'usuario' => $usuario), FALSE);

        $sql = "SELECT arquivos.id,
                    arquivos.nome
                FROM phpmycall.arquivos WHERE arquivos.solicitacao = :solicitacao";

        $result['arquivos'] = $this->select($sql, array('solicitacao' => $solicitacao));

        return $result;
    }

}
