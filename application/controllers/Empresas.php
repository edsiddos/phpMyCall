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
 * Mantem dados das empresas
 *
 * @author Ednei Leite da Silva
 */
class Empresas extends CI_Controller {

    private $translate = array();

    /**
     * Verifica se usuários esta logado antes de executar operação
     */
    public function __construct() {
        parent::__construct();
        if (Autenticacao::verifica_login()) {
            $this->translate = $this->lang->load('empresas', 'portuguese-brazilian', TRUE);
            $this->load->model('empresas_model', 'model');
        } else {
            redirect('login/index');
        }
    }

    /**
     * Gera tela com formulário para inserção de nova empresa
     */
    public function index() {
        $permissao = 'empresas/index';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $this->load->helper('form');

            $var_header = array(
                'title' => $this->translate['titulo_janela'],
                'js_path_translation_bootstrap_select' => $this->translate['js_path_translation_bootstrap_select']
            );

            $vars = $this->translate;

            $this->load->view("template/header", $var_header);
            $this->load->view("empresas/index", $vars);
            $this->load->view("empresas/form");
            $this->load->view("template/footer");
        } else {
            redirect('main/index');
        }
    }

    /**
     * Verifica se um empresa já esta cadastrada
     */
    public function existe_empresa() {
        $permissao = "empresas/index";

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $empresa = trim(filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING));

            if (empty($empresa)) {
                echo json_encode(array('status' => 1));
            } else {
                echo json_encode($this->model->existe_empresa($empresa));
            }
        }
    }

    /**
     * Realiza a inserção de uma nova empresa
     */
    public function cadastrar() {
        $permissao = 'empresas/index';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {

            $dados = array(
                'empresa' => filter_input(INPUT_POST, 'input_empresa', FILTER_SANITIZE_STRING),
                'endereco' => filter_input(INPUT_POST, 'input_endereco', FILTER_SANITIZE_STRING),
                'telefone_fixo' => filter_input(INPUT_POST, 'input_telefone_fixo', FILTER_SANITIZE_STRING),
                'telefone_celular' => filter_input(INPUT_POST, 'input_telefone_celular', FILTER_SANITIZE_STRING)
            );

            if ($this->model->cadastra_empresa($dados)) {
                $status = array(
                    'status' => true,
                    'msg' => $this->translate['inserido_com_sucesso']
                );
            } else {
                $status = array(
                    'status' => false,
                    'msg' => $this->translate['erro_ao_inserir']
                );
            }

            $status['empresas'] = $this->model->get_empresas();

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => $status['msg']
            );

            Logs::gravar($log, $_SESSION ['id']);
            echo json_encode($status);
        }
    }

    /**
     * Busca empresas a partir de parte do nome
     */
    public function get_empresas() {
        $permissao = "empresas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $columns = filter_input(INPUT_POST, 'columns', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $draw = filter_input(INPUT_POST, 'draw', FILTER_SANITIZE_NUMBER_INT);
            $limit = filter_input(INPUT_POST, 'length', FILTER_SANITIZE_NUMBER_INT);
            $offset = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_NUMBER_INT);
            $order = filter_input(INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $search = filter_input(INPUT_POST, 'search', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            if (empty($columns[$order[0]['column']]['data'])) {
                $order_by = "id {$order[0]['dir']}";
            } else {
                $order_by = "{$columns[$order[0]['column']]['data']} {$order[0]['dir']}";
            }

            $array['draw'] = (empty($draw) ? 1 : $draw);
            $array = $this->model->get_empresas($search['value'], $order_by, $limit, $offset);
            echo json_encode($array);
        }
    }

    /**
     * Busca dados da empresa a partir do nome
     */
    public function get_dados_empresa() {
        $permissao = "empresas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $empresa = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_NUMBER_INT);

            echo json_encode($this->model->get_dados_empresa($empresa));
        }
    }

    /**
     * Realiza a atualização do dados da empresa
     */
    public function alterar() {
        $permissao = 'empresas/index';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {

            $dados = array(
                'empresa' => filter_input(INPUT_POST, 'input_empresa', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
                'endereco' => filter_input(INPUT_POST, 'input_endereco', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
                'telefone_fixo' => filter_input(INPUT_POST, 'input_telefone_fixo', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
                'telefone_celular' => filter_input(INPUT_POST, 'input_telefone_celular', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL)
            );

            $id = filter_input(INPUT_POST, 'input_id', FILTER_SANITIZE_NUMBER_INT);

            if ($this->model->atualiza_empresa($id, $dados)) {
                $status = array(
                    'status' => true,
                    'msg' => $this->translate['alterado_com_sucesso']
                );
            } else {
                $status = array(
                    'status' => false,
                    'msg' => $this->translate['erro_ao_alterar']
                );
            }

            $dados['id'] = $id;

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => $status['msg']
            );

            Logs::gravar($log, $_SESSION ['id']);

            echo json_encode($status);
        }
    }

    /**
     * Remove empresa selecionado
     */
    public function excluir() {
        $permissao = 'empresas/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {

            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            $dados = $this->model->get_dados_empresa($id);

            if ($this->model->excluir_empresa($id)) {
                $status = array(
                    'status' => true,
                    'msg' => $this->translate['excluido_com_sucesso']
                );
            } else {
                $status = array(
                    'status' => false,
                    'msg' => $this->translate['erro_ao_excluir']
                );
            }

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => $status['msg']
            );

            Logs::gravar($log, $_SESSION['id']);

            echo json_encode($status);
        }
    }

}
