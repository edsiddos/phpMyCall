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

use system\Controller;
use application\models\Feedback as ModelFeedback;
use libs\Menu;
use libs\Log;

/**
 * Realiza as operações relacionadas a feedback
 *
 * @author Ednei Leite da Silva
 */
class Feedback extends Controller {

    /**
     * Objeto de conexão com banco de dados
     *
     * @var ModelFeedback
     */
    private $model;

    /**
     * Método construtor verifica se usuário
     * esta logado caso esteja instancia objeto
     * de conexão com banco de dados
     */
    public function __construct() {
        parent::__construct();
        if (!Login::verificaLogin()) {
            $this->redir("Login/index");
        } else {
            $this->model = new ModelFeedback ();
        }
    }

    /**
     * Telas de cadastro de tipo de feedback
     */
    public function index() {
        $permissao = "Feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $vars = array(
                "title" => "Cadastro Feedback",
                "feedback" => $this->model->getDadosTipoFeedback()
            );

            $this->loadView(array('feedback/index', 'feedback/form'), $vars);
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Realiza cadastro de tipo de feedback
     */
    public function cadastrar() {
        $permissao = "Feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $nome = trim(filter_input(INPUT_POST, 'inputNome', FILTER_SANITIZE_STRING));
            $abrev = trim(filter_input(INPUT_POST, 'inputAbreviatura', FILTER_SANITIZE_STRING));
            $descontar = (filter_input(INPUT_POST, 'inputDescontar', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)[0] === 'descontar' ? TRUE : FALSE);
            $descricao = filter_input(INPUT_POST, 'textDescricao', FILTER_SANITIZE_STRING);

            if (!(empty($nome) || empty($abrev))) {
                if ($this->model->cadastrar($nome, $abrev, $descontar, $descricao)) {
                    $dados['status'] = true;
                    $dados['msg'] = 'Sucesso ao criar tipo de feedback';
                } else {
                    $dados['status'] = false;
                    $dados['msg'] = 'Erro ao criar tipo de feedback';
                }

                $dados['feedback'] = $this->model->getDadosTipoFeedback();

                $log = array(
                    'dados' => array(
                        'nome' => $nome,
                        'abreviatura' => $abrev,
                        'descontar' => $descontar,
                        'descricao' => $descricao
                    ),
                    'aplicacao' => $permissao,
                    'msg' => $dados['msg']
                );

                Log::gravar($log, $_SESSION ['id']);
                echo json_encode($dados);
            }
        }
    }

    /**
     * Busca dados dos tipos de feedbacks
     */
    public function getDadosTipoFeedback() {
        $permissao = "Feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            echo json_encode($this->model->getDadosTipoFeedback());
        }
    }

    /**
     * Busca dados de um tipo de feedback
     */
    public function getFeedback() {
        $permissao = "Feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $feedback = filter_input(INPUT_POST, 'feedback', FILTER_SANITIZE_NUMBER_INT);
            echo json_encode($this->model->getFeedback($feedback));
        }
    }

    /**
     * Atualiza tipos de feedbacks
     */
    public function alterar() {
        $permissao = "Feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $id = filter_input(INPUT_POST, 'inputID', FILTER_SANITIZE_NUMBER_INT);
            $nome = trim(filter_input(INPUT_POST, 'inputNome', FILTER_SANITIZE_STRING));
            $abrev = trim(filter_input(INPUT_POST, 'inputAbreviatura', FILTER_SANITIZE_STRING));
            $descontar = (filter_input(INPUT_POST, 'inputDescontar', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)[0] === 'descontar' ? TRUE : FALSE);
            $descricao = filter_input(INPUT_POST, 'textDescricao', FILTER_SANITIZE_STRING);

            if (!(empty($nome) || empty($abrev))) {
                if ($this->model->alterar($id, $nome, $abrev, $descontar, $descricao)) {
                    $dados['status'] = true;
                    $dados['msg'] = 'Sucesso ao alterar tipo de feedback';
                } else {
                    $dados['status'] = false;
                    $dados['msg'] = 'Erro ao alterar tipo de feedback';
                }

                $dados['feedback'] = $this->model->getDadosTipoFeedback();

                $log = array(
                    'dados' => array(
                        'id' => $id,
                        'nome' => $nome,
                        'abreviatura' => $abrev,
                        'descontar' => $descontar,
                        'descricao' => $descricao
                    ),
                    'aplicacao' => $permissao,
                    'msg' => $dados['msg']
                );

                Log::gravar($log, $_SESSION ['id']);

                echo json_encode($dados);
            }
        }
    }

    /**
     * Excluir tipo de feedbacks
     */
    public function excluir() {
        $permissao = "Feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            if ($this->model->excluir($id)) {
                $dados = array(
                    'status' => true,
                    'msg' => 'Sucesso ao excluir tipo de feedback'
                );
            } else {
                $dados = array(
                    'status' => false,
                    'msg' => 'Erro ao excluir tipo de feedback'
                );
            }

            $dados['feedback'] = $this->model->getDadosTipoFeedback();

            $log = array(
                'dados' => $this->model->getFeedback($id),
                'aplicacao' => $permissao,
                'msg' => $dados['msg']
            );

            Log::gravar($log, $_SESSION ['id']);

            echo json_encode($dados);
        }
    }

}
