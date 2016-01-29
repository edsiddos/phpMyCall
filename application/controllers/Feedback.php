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
class Feedback extends CI_Controller {

    /**
     * Método construtor verifica se usuário
     * esta logado caso esteja instancia objeto
     * de conexão com banco de dados
     */
    public function __construct() {
        parent::__construct();
        if (Autenticacao::verifica_login()) {
            $this->load->model('feedback_model', 'model');
        } else {
            redirect("login/index");
        }
    }

    /**
     * Telas de cadastro de tipo de feedback
     */
    public function index() {
        $permissao = "feedback/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $this->load->helper('form');
            $vars = array(
                "title" => "Feedback"
            );

            $this->load->view('template/header', $vars);
            $this->load->view('feedback/index');
            $this->load->view('feedback/form');
            $this->load->view('template/footer');
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
                    $dados['msg'] = 'Sucesso ao criar tipo de feedback';
                } else {
                    $dados['status'] = false;
                    $dados['msg'] = 'Erro ao criar tipo de feedback';
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
                echo json_encode($dados);
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

            $order_by = "{$columns[$order[0]['column']]['data']} {$order[0]['dir']}";

            $array['draw'] = (empty($draw) ? 1 : $draw);
            $array = $this->model->get_dados_tipo_feedback($search['value'], $order_by, $limit, $offset);
            echo json_encode($array);
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
            echo json_encode($this->model->get_feedback($feedback));
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
                    $dados['msg'] = 'Sucesso ao alterar tipo de feedback';
                } else {
                    $dados['status'] = false;
                    $dados['msg'] = 'Erro ao alterar tipo de feedback';
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

                echo json_encode($dados);
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
                    'msg' => 'Sucesso ao excluir tipo de feedback'
                );
            } else {
                $dados = array(
                    'status' => false,
                    'msg' => 'Erro ao excluir tipo de feedback'
                );
            }

            $log = array(
                'dados' => $this->model->get_feedback($id),
                'aplicacao' => $permissao,
                'msg' => $dados['msg']
            );

            Logs::gravar($log, $_SESSION ['id']);

            echo json_encode($dados);
        }
    }

}
