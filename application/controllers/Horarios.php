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

namespace application\controllers;

use application\models\Horarios as ModelHorarios;
use libs\Menu;
use libs\Log;
use system\Controller;

/**
 * Mantem Horarios
 *
 * @author Ednei Leite da Silva
 */
class Horarios extends Controller {

    /**
     * Manipula dados referentes a horarios
     *
     * @var ModelHorarios
     */
    private $model;

    /**
     * Método construtor verifica se usuário esta logado
     * e instancia objeto de conexão com banco de dados
     */
    public function __construct() {
        parent::__construct();
        if (!Login::verificaLogin()) {
            $this->redir('Login/index');
        } else {
            $this->model = new ModelHorarios ();
        }
    }

    /**
     * Mostra tela com calendário
     */
    public function manterFeriados() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $title = array("title" => "Feriados");

            $this->loadView(array("horarios/feriados"), $title);
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Mostra apenas os dias que não são feriados
     */
    public function mostrarCalendario() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {

            echo json_encode($this->model->getFeriados(false));
        }
    }

    /**
     * Mostra apenas os dias que são feriados
     */
    public function mostrarFeriados() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {

            echo json_encode($this->model->getFeriados(true));
        }
    }

    /**
     * Cadastra um ou mais feriados
     */
    public function cadastraFeriados() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
            $replicar = filter_input(INPUT_POST, 'replicar', FILTER_VALIDATE_BOOLEAN);

            $result = $this->model->addFeriados($data, $nome, $replicar);

            $dados_log ['dados'] = $result;
            $dados_log ['aplicacao'] = $permissao;
            $dados_log ['operacao'] = 'Inserir feriado';

            Log::gravar($dados_log, $_SESSION ['id']);
        }
    }

    /**
     * Retorna dados do feriado a partir do dia selecionado
     */
    public function getFeriadoByDia() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $dia = empty($_POST ['dia']) ? NULL : $_POST ['dia'];

            echo json_encode($this->model->getFeriadoByDia($dia));
        }
    }

    /**
     * Altera o nome do feriado
     */
    public function alteraFeriados() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);

            $result = $this->model->updateFeriados($data, $nome);

            $dados_log ['dados'] = array(
                'dados' => array(
                    'data' => $data,
                    'nome' => $nome
                ),
                'result' => $result,
                'aplicacao' => $permissao,
                'operacao' => 'alterar feriado'
            );

            Log::gravar($dados_log, $_SESSION ['id']);
        }
    }

    /**
     * Exclui feriado selecionado
     */
    public function deleteFeriado() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);

            $result = $this->model->deleteFeriados($data);

            $dados_log ['dados'] = array(
                'dados' => array(
                    'data' => $data
                ),
                'result' => $result,
                'aplicacao' => $permissao,
                'operacao' => 'excluir feriado'
            );

            Log::gravar($dados_log, $_SESSION ['id']);
        }
    }

    /**
     * Mostra tela com os horários de expediente
     */
    public function alterarExpediente() {
        $permissao = 'Horarios/manterFeriados';
        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $vars = array(
                'title' => 'Expediente',
                'expediente' => $this->model->getExpediente()
            );

            $this->loadView(array("horarios/expediente"), $vars);
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Altera horário de entrada ou saida de determinado dia.
     */
    public function setExpediente() {
        $permissao = 'Horarios/manterFeriados';
        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING);
            $coluna = filter_input(INPUT_POST, 'coluna', FILTER_SANITIZE_STRING);

            if ($this->model->setExpediente($id, $value, $coluna)) {
                $result = array(
                    "status" => "OK"
                );
            } else {
                $result = array(
                    "status" => "NOT"
                );
            }

            echo json_encode($result);

            $dados ['msg'] = $result ['status'];
            $dados ['dados'] = array(
                'id' => $id,
                'horario' => $value,
                'coluna' => $coluna
            );
            $dados ['aplicacao'] = $permissao;

            Log::gravar($dados, $_SESSION ['id']);
        }
    }

}
