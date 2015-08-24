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

namespace system;

use \PDO;

/**
 * Classe base para os models
 *
 * @author Ednei Leite da Silva
 */
class Model {

    /**
     *
     * @var PDO Objeto de conexão com banco de dados.
     */
    private $conn;

    /**
     *
     * @var PDOStatement Prepara uma instrução SQL para ser executada.
     */
    private $statement;

    /**
     *
     * @var Array Array contendo todas sequencias do banco de dados
     */
    protected $sequences = array(
        'arquivos' => 'phpmycall.arquivos_id_seq',
        'empresas' => 'phpmycall.empresas_id_seq',
        'feedback' => 'phpmycall.feedback_id_seq',
        'feriado' => 'phpmycall.feriado_id_seq',
        'log' => 'phpmycall.log_id_seq',
        'opcoes_menu' => 'phpmycall.opcoes_menu_id_seq',
        'perfil' => 'phpmycall.perfil_id_seq',
        'permissao_perfil' => 'phpmycall.permissao_perfil_id_seq',
        'prioridade' => 'phpmycall.prioridade_id_seq',
        'projeto' => 'phpmycall.projeto_id_seq',
        'projeto_responsaveis' => 'phpmycall.projeto_responsaveis_id_seq',
        'projeto_tipo_problema' => 'phpmycall.projeto_tipo_problema_id_seq',
        'solicitacao' => 'phpmycall.solicitacao_id_seq',
        'tipo_feedback' => 'phpmycall.tipo_feedback_id_seq',
        'tipo_problema' => 'phpmycall.tipo_problema_id_seq',
        'usuario' => 'phpmycall.usuario_id_seq'
    );

    /**
     * Método construtor que inicializa conexão com banco de dados
     */
    public function __construct() {
        try {

            $this->conn = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NOME . ";", DB_USER, DB_PASS);
            /* Verifica se devemos debugar */
            if (DEBUG === true) {
                /* Configura o PDO ERROR MODE */
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            }

            $this->conn->exec("SET NAMES 'UTF8'");
        } catch (PDOException $ex) {
            echo "Erro ao conectar banco de dados: {$ex->getMessage()}";
        }
    }

    /**
     * Realiza consulta ao banco de dados.
     *
     * @param string $sql Query a ser executada.
     * @param array $array Array com os dados necessários para a consulta.
     * @param boolean $fecthall Retorna toda as linha (TRUE) ou apenas uma linha (FALSE).
     * @param PDO $fecthmode Modo de retorno da consulta.
     * @return Array Retorna Array com os resultados.
     */
    public function select($sql, $array = array(), $fecthall = TRUE, $fecthmode = PDO::FETCH_ASSOC) {
        $this->statement = $this->conn->prepare($sql);

        if (count($array) > 0) {
            foreach ($array as $key => $value) {
                $this->statement->bindValue("$key", $value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
            }
        }

        $this->statement->execute();

        if ($fecthall) {
            return $this->statement->fetchAll($fecthmode);
        } else {
            return $this->statement->fetch($fecthmode);
        }
    }

    /**
     * Insere um dado no banco de dados.
     *
     * @param String $table Nome da tabela.
     * @param Array $data Campos e seus respectivos valores.
     * @return boolean Retorna TRUE caso sucesso ou FALSE caso contrário.
     */
    public function insert($table, $data) {
        // Campos e valores
        $camposNomes = implode(', ', array_keys($data));
        $camposValores = ':' . implode(', :', array_keys($data));

        // Prepara a Query
        $sth = $this->conn->prepare("INSERT INTO {$table} ({$camposNomes}) VALUES ({$camposValores})");

        // Define os dados
        foreach ($data as $key => $value) {
            // Se o tipo do dado for inteiro, usa PDO::PARAM_INT, caso contrário, PDO::PARAM_STR
            $tipo = (is_string($value)) ? PDO::PARAM_STR : PDO::PARAM_INT;

            // Define o dado
            $sth->bindValue(":$key", $value, $tipo);
        }

        // Executa
        return $sth->execute();
    }

    /**
     * Atualiza uma tabela no banco de dados.
     *
     * @param String $table Nome da tabela.
     * @param Array $data Campos e seus respectivos valores.
     * @param String $where Condição de atualização.
     * @return boolean
     */
    public function update($table, $data, $where) {
        // Define os dados que serão atualizados
        $novosDados = NULL;

        foreach ($data as $key => $value) {
            $novosDados .= "{$key}=:{$key},";
        }

        $novosDados = rtrim($novosDados, ',');

        // Prepara a Query
        $sth = $this->conn->prepare("UPDATE {$table} SET {$novosDados} WHERE {$where}");

        // Define os dados
        foreach ($data as $key => $value) {
            $tipo = (is_string($value)) ? PDO::PARAM_STR : PDO::PARAM_INT;

            // Define o dado
            $sth->bindValue(":{$key}", $value, $tipo);
        }

        // Sucesso ou falha?
        return $sth->execute();
    }

    /**
     * Deleta um dado da tabela.
     *
     * @param String $table Nome da tabela.
     * @param String $where Condição de atualização.
     * @return booelan
     */
    public function delete($table, $where) {
        // Deleta
        $sth = $this->conn->prepare("DELETE FROM {$table} WHERE {$where}");
        return $sth->execute();
    }

}
