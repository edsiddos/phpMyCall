<?php

/*
 * Copyright (C) 2015 - 2016, Ednei Leite da Silva
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
 * Realiza as operações relacionadas a feedback
 *
 * @author Ednei Leite da Silva
 */
class Feedback extends Admin_Controller {

    /**
     * Método construtor verifica se usuário
     * esta logado caso esteja instancia objeto
     * de conexão com banco de dados
     */
    public function __construct() {
        parent::__construct('feedback');
        $this->load->model('feedback_model', 'model');
    }

    /**
     * Telas de cadastro de tipo de feedback
     */
    public function index() {
        $permissao = "feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $this->load->helper('form');
            $this->load_view(array('feedback/index', 'feedback/form'));
        } else {
            redirect('Main/index');
        }
    }

    /**
     * Realiza cadastro de tipo de feedback
     */
    public function cadastrar() {
        $permissao = "feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $nome = trim(filter_input(INPUT_POST, 'input_nome', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL));
            $abrev = trim(filter_input(INPUT_POST, 'input_abreviatura', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL));
            $descontar = (filter_input(INPUT_POST, 'input_descontar', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL) === 'descontar' ? TRUE : FALSE);
            $descricao = filter_input(INPUT_POST, 'text_descricao', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);

            if (!(empty($nome) || empty($abrev))) {
                if ($this->model->cadastrar($nome, $abrev, $descontar, $descricao)) {
                    $dados['status'] = true;
                    $dados['msg'] = $this->translate['response_success_create_feedback'];
                } else {
                    $dados['status'] = false;
                    $dados['msg'] = $this->translate['response_error_create_feedback'];
                }

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

                Logs::gravar($log, $_SESSION ['id']);
                $this->response($dados);
            }
        }
    }

    /**
     * Busca dados dos tipos de feedbacks
     */
    public function get_dados_tipo_feedback() {
        $permissao = "feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $columns = filter_input(INPUT_POST, 'columns', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $draw = filter_input(INPUT_POST, 'draw', FILTER_SANITIZE_NUMBER_INT);
            $limit = filter_input(INPUT_POST, 'length', FILTER_SANITIZE_NUMBER_INT);
            $offset = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_NUMBER_INT);
            $order = filter_input(INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $search = filter_input(INPUT_POST, 'search', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            $column = $order[0]['column'] == 0 ? 1 : $order[0]['column'];

            $order_by = "{$columns[$column]['data']} {$order[0]['dir']}";

            $array['draw'] = (empty($draw) ? 1 : $draw);
            $array = $this->model->get_dados_tipo_feedback($search['value'], $order_by, $limit, $offset);
            $this->response($array);
        }
    }

    /**
     * Busca dados de um tipo de feedback
     */
    public function get_feedback() {
        $permissao = "feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $feedback = filter_input(INPUT_POST, 'feedback', FILTER_SANITIZE_NUMBER_INT);
            $this->response($this->model->get_feedback($feedback));
        }
    }

    /**
     * Atualiza tipos de feedbacks
     */
    public function alterar() {
        $permissao = "feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $id = filter_input(INPUT_POST, 'input_id', FILTER_SANITIZE_NUMBER_INT);
            $nome = trim(filter_input(INPUT_POST, 'input_nome', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL));
            $abrev = trim(filter_input(INPUT_POST, 'input_abreviatura', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL));
            $descontar = (filter_input(INPUT_POST, 'input_descontar', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL) === 'descontar' ? TRUE : FALSE);
            $descricao = filter_input(INPUT_POST, 'text_descricao', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);

            if (!(empty($nome) || empty($abrev))) {
                if ($this->model->alterar($id, $nome, $abrev, $descontar, $descricao)) {
                    $dados['status'] = true;
                    $dados['msg'] = $this->translate['response_success_update_feedback'];
                } else {
                    $dados['status'] = false;
                    $dados['msg'] = $this->translate['response_error_update_feedback'];
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
                    'msg' => $dados['msg']
                );

                Logs::gravar($log, $_SESSION ['id']);

                $this->response($dados);
            }
        }
    }

    /**
     * Excluir tipo de feedbacks
     */
    public function excluir() {
        $permissao = "feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            if ($this->model->excluir($id)) {
                $dados = array(
                    'status' => true,
                    'msg' => $this->translate['response_success_remove_feedback']
                );
            } else {
                $dados = array(
                    'status' => false,
                    'msg' => $this->translate['response_error_remove_feedback']
                );
            }

            $log = array(
                'dados' => $this->model->get_feedback($id),
                'aplicacao' => $permissao,
                'msg' => $dados['msg']
            );

            Logs::gravar($log, $_SESSION ['id']);

            $this->response($dados);
        }
    }

}
