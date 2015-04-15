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

/**
 * Manipula cadastro das empresas
 *
 * @author Ednei Leite da Silva
 */
class Empresas extends \system\Model {

    /**
     * Cadastra uma nova empresa
     * @param Array $dados Dados necessarios para gravar uma empresa.
     * @return boolean <b>TRUE</b> se sucesso.
     */
    public function cadastraEmpresa($dados) {
        return $this->insert('phpmycall.empresas', $dados);
    }

    /**
     * Verifica se empresa esta cadastrada.
     * @param String $empresa Nome da empresa.
     * @return Array Retorna array com resultado da consulta.
     */
    public function existeEmpresa($empresa) {
        $sql = "SELECT COUNT(empresas.empresa) AS status FROM phpmycall.empresas WHERE empresas.empresa = :empresa";

        return $this->select($sql, array('empresa' => "$empresa"), FALSE);
    }

    /**
     * Busca as empresas cadastradas.
     * @param type $empresa Nome parcial da empresa
     * @return Array Retorna array com os nomes das empresas
     */
    public function getNomeEmpresa($empresa) {
        $sql = "SELECT empresas.empresa AS value FROM phpmycall.empresas WHERE empresas.empresa ILIKE :empresa";

        return $this->select($sql, array('empresa' => "%$empresa%"));
    }

    /**
     * Busca dados da empresa a partir do nome.
     * @param String $empresa Nome da empresa.
     * @return Array Retorna dados da empresa.
     */
    public function getDadosEmpresas($empresa) {
        $sql = "SELECT * FROM phpmycall.empresas WHERE empresas.empresa = :empresa";

        return $this->select($sql, array('empresa' => "$empresa"), FALSE);
    }

    /**
     * Atualiza dados da empresa.
     * @param int $id ID da empresa
     * @param Array $dados Array com dados a ser alterados
     * @return boolean Retorna <b>TRUE</b> se operação realizada com sucesso.
     */
    public function atualizaEmpresa($id, $dados) {
        $where = "id = {$id}";

        return $this->update('phpmycall.empresas', $dados, $where);
    }

    /**
     * Exclui cadastro de um empresa.
     * @param int $id ID da empresa
     * @return boolean Retorna <b>TRUE</b> se operação de exclusão ocorrer com sucesso.
     */
    public function excluirEmpresa($id) {
        $where = "id = {$id}";

        return $this->delete('phpmycall.empresas', $where);
    }

}
