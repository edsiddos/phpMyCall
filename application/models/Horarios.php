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
 * Manipula horarios de trabalho e dias de trabalhados
 *
 * @author Ednei Leite da Silva
 */
class Horarios extends Model {

    /**
     * Busca todos os feriados cadastrado no sistema
     *
     * @return array
     */
    public function getFeriados() {
        $sql = "SELECT dia FROM phpmycall.feriado";

        $result = $this->select($sql);

        foreach ($result as $values) {
            $return [] = '"' . $values ['dia'] . '"';
        }

        return $return;
    }

    /**
     * Adiciona um ou mais feriados
     *
     * @param string $data Data do feriado
     * @param string $nome Nome do feriado
     * @param boolean $replicar Replicar o feriado nos próximos 15 anos se valor igual a TRUE
     * @return Array Retorna dados para gravação de log
     */
    public function addFeriados($data, $nome, $replicar) {
        $return = array();

        /*
         * Se valor de replicar igual a true replica a data nos 15 anos seguintes
         */
        if ($replicar) {
            for ($inicio = 0; $inicio < 15; $inicio ++) {
                $obj_data = new \DateTime($data);
                $obj_data = $obj_data->add(new \DateInterval('P' . $inicio . 'Y'));

                unset($array);

                $array = array(
                    'dia' => $obj_data->format('Y-m-d'),
                    'nome' => $nome
                );

                $return [$inicio] ['dados'] = $array;
                $return [$inicio] ['result'] = $this->insert('phpmycall.feriado', $array);
            }
        } else {
            /*
             * Se replicar igual a false grava feriado
             * apenas para a data selecionada
             */
            $obj_data = new \DateTime($data);

            $array = array(
                'dia' => $obj_data->format('Y-m-d'),
                'nome' => $nome
            );

            $return [0] ['dados'] = $array;
            $return [0] ['result'] = $this->insert('phpmycall.feriado', $array);
        }

        return $return;
    }

    /**
     * Retorna o nome do feriado
     *
     * @param string $dia
     *        	Data do feriado
     * @return array Retorna array com o nome do feriado
     */
    public function getFeriadoByDia($dia) {
        $sql = "SELECT nome FROM phpmycall.feriado WHERE dia = :dia";

        return $this->select($sql, array('dia' => $dia), FALSE);
    }

    /**
     * Atualiza nome do feriado
     *
     * @param string $data Data do feriado.
     * @param string $nome Nome do feriado.
     * @return boolean Retorna <b>TRUE</b> em caso de sucesso.
     */
    public function updateFeriados($data, $nome) {
        return $this->update('phpmycall.feriado', array('nome' => $nome), "dia = '{$data}'");
    }

    /**
     * Exclui um feriado
     *
     * @param string $data Dia do feriado.
     * @return boolean <b>TRUE</b> sucesso, <b>FALSE</b> falha.
     */
    public function deleteFeriados($data) {
        return $this->delete('phpmycall.feriado', "dia = '{$data}'");
    }

    /**
     * Busca os dias e horarios de expediente.
     *
     * @return Array Retorna array com os dia da semana e horários de entrada e saída do 1º e 2º periodo.
     */
    public function getExpediente() {
        $sql = "SELECT id,
                    dia_semana,
                    TO_CHAR(entrada_manha, 'HH24:MM') AS entrada_manha,
                    TO_CHAR(saida_manha, 'HH24:MM') AS saida_manha,
                    TO_CHAR(entrada_tarde, 'HH24:MM') AS entrada_tarde,
                    TO_CHAR(saida_tarde, 'HH24:MM') AS saida_tarde
                FROM phpmycall.expediente ORDER BY id;";

        $result = $this->select($sql);

        foreach ($result as $values) {
            $return ['dia_semana'] [$values ['id']] = $values ['dia_semana'];
            $return ['entrada_manha'] [$values ['id']] = $values ['entrada_manha'];
            $return ['saida_manha'] [$values ['id']] = $values ['saida_manha'];
            $return ['entrada_tarde'] [$values ['id']] = $values ['entrada_tarde'];
            $return ['saida_tarde'] [$values ['id']] = $values ['saida_tarde'];
        }

        return $return;
    }

    /**
     * Altera horário de entrada ou saida de determinado dia.
     *
     * @param unknown $id ID do dia da semana
     * @param unknown $value Novo horário
     * @param unknown $coluna Qual periodo será alterado
     * @return boolean <b>TRUE</b> sucesso, <b>FALSE</b> falha.
     */
    public function setExpediente($id, $value, $coluna) {
        $dados = array(
            $coluna => $value
        );

        return $this->update('phpmycall.expediente', $dados, "id = {$id}");
    }

}
