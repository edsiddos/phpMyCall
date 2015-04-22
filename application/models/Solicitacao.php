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

use system\Model;

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

}
