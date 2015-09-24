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
 * Mantem Horarios
 *
 * @author Ednei Leite da Silva
 */
class Horarios extends CI_Controller {

    /**
     * Método construtor verifica se usuário esta logado
     * e instancia objeto de conexão com banco de dados
     */
    public function __construct() {
        parent::__construct();
        if (!Autenticacao::verifica_login()) {
            $this->redir('Login/index');
        } else {
            $this->load->model('horarios_model');
        }
    }

    /**
     * Mostra tela com calendário
     */
    public function manterFeriados() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possue_permissao($_SESSION['perfil'], $permissao)) {
            $title = array("title" => "Feriados");

            $this->load->view("template/header", $title);
            $this->load->view("horarios/feriados");
            $this->load->view("template/footer");
        } else {
            redirect('Main/index');
        }
    }

    /**
     * Mostra apenas os dias que não são feriados
     */
    public function mostrarCalendario() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {

            echo json_encode($this->horarios_model->getFeriados(false));
        }
    }

    /**
     * Mostra apenas os dias que são feriados
     */
    public function mostrarFeriados() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {

            echo json_encode($this->horarios_model->getFeriados(true));
        }
    }

    /**
     * Cadastra um ou mais feriados
     */
    public function cadastraFeriados() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
            $replicar = filter_input(INPUT_POST, 'replicar', FILTER_VALIDATE_BOOLEAN);

            $result = $this->horarios_model->addFeriados($data, $nome, $replicar);

            $dados_log ['dados'] = $result;
            $dados_log ['aplicacao'] = $permissao;
            $dados_log ['operacao'] = 'Inserir feriado';

            Logs::gravar($dados_log, $_SESSION ['id']);
        }
    }

    /**
     * Retorna dados do feriado a partir do dia selecionado
     */
    public function getFeriadoByDia() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $dia = empty($_POST ['dia']) ? NULL : $_POST ['dia'];

            echo json_encode($this->horarios_model->getFeriadoByDia($dia));
        }
    }

    /**
     * Altera o nome do feriado
     */
    public function alteraFeriados() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);

            $result = $this->horarios_model->updateFeriados($data, $nome);

            $dados_log ['dados'] = array(
                'dados' => array(
                    'data' => $data,
                    'nome' => $nome
                ),
                'result' => $result,
                'aplicacao' => $permissao,
                'operacao' => 'alterar feriado'
            );

            Logs::gravar($dados_log, $_SESSION ['id']);
        }
    }

    /**
     * Exclui feriado selecionado
     */
    public function deleteFeriado() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);

            $result = $this->horarios_model->deleteFeriados($data);

            $dados_log ['dados'] = array(
                'dados' => array(
                    'data' => $data
                ),
                'result' => $result,
                'aplicacao' => $permissao,
                'operacao' => 'excluir feriado'
            );

            Logs::gravar($dados_log, $_SESSION ['id']);
        }
    }

    /**
     * Mostra tela com os horários de expediente
     */
    public function alterarExpediente() {
        $permissao = 'Horarios/manterFeriados';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $this->load->helper('form');

            $title = array(
                'title' => 'Expediente'
            );

            $vars = array(
                'expediente' => $this->horarios_model->getExpediente()
            );

            $this->load->view("template/header", $title);
            $this->load->view("horarios/expediente", $vars);
            $this->load->view("template/footer");
        } else {
            redirect('Main/index');
        }
    }

    /**
     * Altera horário de entrada ou saida de determinado dia.
     */
    public function setExpediente() {
        $permissao = 'Horarios/manterFeriados';
        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $value = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_STRING);
            $coluna = filter_input(INPUT_POST, 'coluna', FILTER_SANITIZE_STRING);

            if ($this->horarios_model->setExpediente($id, $value, $coluna)) {
                $result = array(
                    "status" => "OK"
                );
            } else {
                $result = array(
                    "status" => "NOT"
                );
            }

            echo json_encode($result);

            $dados['msg'] = $result ['status'];
            $dados['dados'] = array(
                'id' => $id,
                'horario' => $value,
                'coluna' => $coluna
            );
            $dados['aplicacao'] = $permissao;

            Logs::gravar($dados, $_SESSION ['id']);
        }
    }

}
