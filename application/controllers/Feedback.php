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
    public function cadastrar() {
        $permissao = "Feedback/cadastrar";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $title = array(
                "title" => "Cadastro Feedback"
            );

            $vars = array(
                'link' => HTTP . '/Feedback/novoFeedback',
                'title_botao' => "Cadastrar Feedback"
            );

            $this->loadView("default/header", $title);
            $this->loadView("feedback/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Realiza cadastro de tipo de feedback
     */
    public function novoFeedback() {
        $permissao = "Feedback/cadastrar";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $nome = (empty($_POST ['inputNome']) ? NULL : $_POST ['inputNome']);
            $abrev = (empty($_POST ['inputAbreviatura']) ? NULL : $_POST ['inputAbreviatura']);
            $descontar = (empty($_POST ['inputDescontar'] [0]) ? FALSE : TRUE);
            $descricao = (empty($_POST ['textDescricao']) ? NULL : $_POST ['textDescricao']);

            if ($this->model->cadastrar($nome, $abrev, $descontar, $descricao)) {
                $_SESSION ['msg_sucesso'] = 'Sucesso ao criar tipo de feedback';
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao criar tipo de feedback';
            }

            $log = array(
                'dados' => array(
                    'nome' => $nome,
                    'abreviatura' => $abrev,
                    'descontar' => $descontar,
                    'descricao' => $descricao
                ),
                'aplicacao' => $permissao,
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);

            $this->redir('Feedback/cadastrar');
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Tela de alteração de tipo de feedback
     */
    public function alterar() {
        $permissao = "Feedback/alterar";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $title = array(
                "title" => "Alterar Feedback"
            );

            $vars = array(
                'link' => HTTP . '/Feedback/atualizarFeedback',
                'title_botao' => "Alterar Feedback"
            );

            $this->loadView("default/header", $title);
            $this->loadView("feedback/alterar");
            $this->loadView("feedback/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir("Main/index");
        }
    }

    /**
     * Busca os tipos de feedbacks pelo nome
     */
    public function nomesFeedback() {
        $permissao_1 = "Feedback/alterar";
        $permissao_2 = "Feedback/alterar";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao_1) || Menu::possuePermissao($perfil, $permissao_2)) {
            $nome = $_POST ['term'];
            echo json_encode($this->model->getNomeFeedback($nome));
        }
    }

    /**
     * Busca dados dos tipos de feedbacks
     */
    public function getDadosTipoFeedback() {
        $permissao_1 = "Feedback/alterar";
        $permissao_2 = "Feedback/alterar";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao_1) || Menu::possuePermissao($perfil, $permissao_2)) {
            $nome = $_POST ['nome'];
            echo json_encode($this->model->getDadosTipoFeedback($nome));
        }
    }

    /**
     * Atualiza tipos de feedbacks
     */
    public function atualizarFeedback() {
        $permissao = "Feedback/alterar";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $id = (empty($_POST ['inputID']) ? NULL : $_POST ['inputID']);
            $nome = (empty($_POST ['inputNome']) ? NULL : $_POST ['inputNome']);
            $abrev = (empty($_POST ['inputAbreviatura']) ? NULL : $_POST ['inputAbreviatura']);
            $descontar = (empty($_POST ['inputDescontar'] [0]) ? FALSE : TRUE);
            $descricao = (empty($_POST ['textDescricao']) ? NULL : $_POST ['textDescricao']);

            if ($this->model->alterar($id, $nome, $abrev, $descontar, $descricao)) {
                $_SESSION ['msg_sucesso'] = 'Sucesso ao alterar tipo de feedback';
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao alterar tipo de feedback';
            }

            $log = array(
                'dados' => array(
                    'id' => $id,
                    'nome' => $nome,
                    'abreviatura' => $abrev,
                    'descontar' => $descontar,
                    'descricao' => $descricao
                ),
                'aplicacao' => $permissao,
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);

            $this->redir('Feedback/alterar');
        } else {
            $this->redir("Main/index");
        }
    }

    /**
     * Tela de tipos de feedbacks
     */
    public function excluir() {
        $permissao = "Feedback/excluir";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $title = array(
                "title" => "Excluir Feedback"
            );

            $vars = array(
                'link' => HTTP . '/Feedback/excluirFeedback',
                'title_botao' => "Excluir Feedback"
            );

            $this->loadView("default/header", $title);
            $this->loadView("feedback/delete");
            $this->loadView("feedback/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir("Main/index");
        }
    }

    /**
     * Excluir tipo de feedbacks
     */
    public function excluirFeedback() {
        $permissao = "Feedback/excluir";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $id = (empty($_POST ['inputID']) ? NULL : $_POST ['inputID']);
            $nome = (empty($_POST ['inputNome']) ? NULL : $_POST ['inputNome']);
            $abrev = (empty($_POST ['inputAbreviatura']) ? NULL : $_POST ['inputAbreviatura']);
            $descontar = (empty($_POST ['inputDescontar'] [0]) ? FALSE : TRUE);
            $descricao = (empty($_POST ['textDescricao']) ? NULL : $_POST ['textDescricao']);

            if ($this->model->excluir($id)) {
                $_SESSION ['msg_sucesso'] = 'Sucesso ao excluir tipo de feedback';
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao excluir tipo de feedback';
            }

            $log = array(
                'dados' => array(
                    'id' => $id,
                    'nome' => $nome,
                    'abreviatura' => $abrev,
                    'descontar' => $descontar,
                    'descricao' => $descricao
                ),
                'aplicacao' => $permissao,
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);

            $this->redir('Feedback/excluir');
        } else {
            $this->redir("Main/index");
        }
    }

}
