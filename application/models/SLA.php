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
use application\models\Solicitacao;

/**
 * Consulta de dados para geração de relatório SLA
 *
 * @author Ednei Leite da Silva
 */
class SLA extends Model {

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

    public function getParticipantes($projeto) {
        $solicitacao = new Solicitacao();
        $parametros = $solicitacao->getParametros();

        $tecnicos = "'" . implode("', '", $parametros['ATENDER_SOLICITACAO']) . "'";

        $sql = "SELECT usuario.id,
                    usuario.nome,
                    perfil.perfil IN ({$tecnicos})::int AS tecnico
                FROM phpmycall.usuario
                INNER JOIN phpmycall.projeto_responsaveis ON usuario.id = projeto_responsaveis.usuario
                INNER JOIN phpmycall.perfil ON usuario.perfil = perfil.id
                WHERE projeto_responsaveis.projeto = :projeto
                ORDER BY usuario.nome";

        $result = $this->select($sql, array('projeto' => $projeto));

        return $result;
    }

    /**
     * Busca todas prioridades cadastradas.
     * @return Array Retorna relação de prioridades.
     */
    public function getPrioridades() {
        $sql = "SELECT id, nome, padrao FROM phpmycall.prioridade";

        return $this->select($sql);
    }

    public function getExpedienteByDiaSemana() {
        $sql = "SELECT TO_CHAR(
                        COALESCE(saida_manha::TIME - entrada_manha::TIME, '00:00') +
                        COALESCE(saida_tarde::TIME - entrada_tarde::TIME, '00:00'), 'HH24:MI') AS expediente,
                    expediente.id
                FROM phpmycall.expediente
                ORDER BY id";

        return $this->select($sql);
    }

}
