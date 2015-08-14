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

use \application\models\Empresas as ModelEmpresas;
use \libs\Menu;
use \libs\Log;

/**
 * Mantem dados das empresas
 *
 * @author Ednei Leite da Silva
 */
class Empresas extends \system\Controller {

    /**
     * Objeto para obtenção de dados das empresas.
     *
     * @var ModelEmpresas
     */
    private $model;

    /**
     * Verifica se usuários esta logado antes de executar operação
     */
    public function __construct() {
        parent::__construct();
        if (!Login::verificaLogin()) {
            $this->redir('Login/index');
        } else {
            $this->model = new ModelEmpresas ();
        }
    }

    /**
     * Gera tela com formulário para inserção de nova empresa
     */
    public function index() {
        $permissao = 'Empresas/index';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $vars = array(
                "title" => "Cadastro de empresa",
                "empresas" => $this->model->getEmpresas()
            );

            $this->loadView(array("empresas/index", "empresas/form"), $vars);
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Verifica se um empresa já esta cadastrada
     */
    public function existeEmpresa() {
        $permissao = "Empresas/index";

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $empresa = trim(filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING));

            if (empty($empresa)) {
                echo json_encode(array('status' => 1));
            } else {
                echo json_encode($this->model->existeEmpresa($empresa));
            }
        }
    }

    /**
     * Realiza a inserção de uma nova empresa
     */
    public function cadastrar() {
        $permissao = 'Empresas/index';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {

            $dados = array(
                'empresa' => filter_input(INPUT_POST, 'inputEmpresa', FILTER_SANITIZE_STRING),
                'endereco' => filter_input(INPUT_POST, 'inputEndereco', FILTER_SANITIZE_STRING),
                'telefone_fixo' => filter_input(INPUT_POST, 'inputTelefoneFixo', FILTER_SANITIZE_STRING),
                'telefone_celular' => filter_input(INPUT_POST, 'inputTelefoneCelular', FILTER_SANITIZE_STRING)
            );

            if ($this->model->cadastraEmpresa($dados)) {
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

            $status['empresas'] = $this->model->getEmpresas();

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => $status['msg']
            );

            Log::gravar($log, $_SESSION ['id']);
            echo json_encode($status);
        }
    }

    /**
     * Busca empresas a partir de parte do nome
     */
    public function getEmpresas() {
        $permissao = "Empresas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            echo json_encode($this->model->getEmpresas());
        }
    }

    /**
     * Busca dados da empresa a partir do nome
     */
    public function getDadosEmpresa() {
        $permissao = "Empresas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $empresa = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_NUMBER_INT);

            echo json_encode($this->model->getDadosEmpresa($empresa));
        }
    }

    /**
     * Realiza a atualização do dados da empresa
     */
    public function alterar() {
        $permissao = 'Empresas/index';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {

            $dados = array(
                'empresa' => filter_input(INPUT_POST, 'inputEmpresa', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
                'endereco' => filter_input(INPUT_POST, 'inputEndereco', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
                'telefone_fixo' => filter_input(INPUT_POST, 'inputTelefoneFixo', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
                'telefone_celular' => filter_input(INPUT_POST, 'inputTelefoneCelular', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL)
            );

            $id = filter_input(INPUT_POST, 'inputID', FILTER_SANITIZE_NUMBER_INT);

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

            $status['empresas'] = $this->model->getEmpresas();

            $dados['id'] = $id;

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);

            echo json_encode($status);
        }
    }

    /**
     * Remove empresa selecionado
     */
    public function excluir() {
        $permissao = 'Empresas/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {

            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            $dados = $this->model->getDadosEmpresa($id);

            if ($this->model->excluirEmpresa($id)) {
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

            $status['empresas'] = $this->model->getEmpresas();

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => $status['msg']
            );

            Log::gravar($log, $_SESSION ['id']);

            echo json_encode($status);
        }
    }

}
