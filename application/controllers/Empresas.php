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

    /**
     * Verifica se usuários esta logado antes de executar operação
     */
    public function __construct() {
        parent::__construct();
        if (Autenticacao::verifica_login()) {
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

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $vars = array(
                "title" => "Empresa",
            );

            $this->load->view("template/header", $vars);
            $this->load->view("empresas/index");
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
                    'msg' => 'Sucesso ao cadastrar empresa'
                );
            } else {
                $status = array(
                    'status' => false,
                    'msg' => 'Erro ao cadastar empresa'
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
            echo json_encode($this->model->get_empresas());
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

            if ($this->model->atualizaEmpresa($id, $dados)) {
                $status = array(
                    'status' => true,
                    'msg' => 'Sucesso ao alterar dados da empresa'
                );
            } else {
                $status = array(
                    'status' => false,
                    'msg' => 'Erro ao alterar dados da empresa'
                );
            }

            $status['empresas'] = $this->model->get_empresas();

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
                    'msg' => 'Empresa excluida com sucesso'
                );
            } else {
                $status = array(
                    'status' => false,
                    'msg' => 'Erro ao excluir empresa'
                );
            }

            $status['empresas'] = $this->model->get_empresas();

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
