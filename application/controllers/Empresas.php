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
    public function cadastrar() {
        $permissao = 'Empresas/cadastrar';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $title = array(
                "title" => "Cadastro de empresa"
            );

            $vars = array(
                'link' => HTTP . '/Empresas/novaEmpresa',
                'botao' => array(
                    'value' => "Cadastrar Empresa",
                    'type' => "submit"
                )
            );

            $this->loadView("default/header", $title);
            $this->loadView("empresas/cadastrar");
            $this->loadView("empresas/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Verifica se um empresa já esta cadastrada
     */
    public function existeEmpresa() {
        $permissao = "Empresas/cadastrar";

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $empresa = trim($_POST['empresa']);

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
    public function novaEmpresa() {
        $permissao = 'Empresas/cadastrar';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {

            $dados = array(
                'empresa' => $_POST['inputEmpresa'],
                'endereco' => (empty($_POST['inputEndereco']) ? NULL : $_POST['inputEndereco']),
                'telefone_fixo' => $_POST['inputTelefoneFixo'],
                'telefone_celular' => (empty($_POST['inputTelefoneCelular']) ? NULL : $_POST['inputTelefoneCelular'])
            );

            if ($this->model->cadastraEmpresa($dados)) {
                $_SESSION ['msg_sucesso'] = 'Sucesso ao cadastrar empresa';
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao cadastar empresa';
            }

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);

            $this->redir('Empresas/cadastrar');
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Busca empresa para realizar alteração
     */
    public function alterar() {
        $permissao = 'Empresas/alterar';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $title = array(
                "title" => "Alterar empresa"
            );

            $vars = array(
                'link' => HTTP . '/Empresas/atualizaEmpresa',
                'botao' => array(
                    "value" => "Alterar Empresa",
                    "type" => "submit"
                )
            );

            $this->loadView("default/header", $title);
            $this->loadView("empresas/alterar");
            $this->loadView("empresas/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Busca empresas a partir de parte do nome
     */
    public function getEmpresas() {
        $permissao = "Empresas/alterar";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $empresa = $_POST['term'];

            echo json_encode($this->model->getNomeEmpresa($empresa));
        }
    }

    /**
     * Busca dados da empresa a partir do nome
     */
    public function getDadosEmpresas() {
        $permissao = "Empresas/alterar";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $empresa = $_POST['empresa'];

            echo json_encode($this->model->getDadosEmpresas($empresa));
        }
    }

    /**
     * Realiza a atualização do dados da empresa
     */
    public function atualizaEmpresa() {
        $permissao = 'Empresas/alterar';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {

            $dados = array(
                'empresa' => $_POST['inputEmpresa'],
                'endereco' => (empty($_POST['inputEndereco']) ? NULL : $_POST['inputEndereco']),
                'telefone_fixo' => $_POST['inputTelefoneFixo'],
                'telefone_celular' => (empty($_POST['inputTelefoneCelular']) ? NULL : $_POST['inputTelefoneCelular'])
            );

            $id = $_POST['inputID'];

            if ($this->model->atualizaEmpresa($id, $dados)) {
                $_SESSION ['msg_sucesso'] = 'Sucesso ao alterar dados da empresa';
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao alterar dados da empresa';
            }

            $dados['id'] = $id;

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);

            $this->redir('Empresas/alterar');
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Exibe tela de exclusão de empresa
     */
    public function excluir() {
        $permissao = 'Empresas/excluir';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $title = array(
                "title" => "Excluir empresa"
            );

            $vars = array(
                'link' => HTTP . '/Empresas/removeEmpresa',
                'botao' => array(
                    "value" => "Excluir Empresa",
                    "type" => "submit"
                )
            );

            $this->loadView("default/header", $title);
            $this->loadView("empresas/delete");
            $this->loadView("empresas/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Remove empresa selecionado
     */
    public function removeEmpresa() {
        $permissao = 'Empresas/excluir';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {

            $dados = array(
                'empresa' => $_POST['inputEmpresa'],
                'endereco' => (empty($_POST['inputEndereco']) ? NULL : $_POST['inputEndereco']),
                'telefone_fixo' => $_POST['inputTelefoneFixo'],
                'telefone_celular' => (empty($_POST['inputTelefoneCelular']) ? NULL : $_POST['inputTelefoneCelular'])
            );

            $id = $_POST['inputID'];

            if ($this->model->excluirEmpresa($id)) {
                $_SESSION ['msg_sucesso'] = 'Empresa excluida com sucesso';
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao excluir empresa';
            }

            $dados['id'] = $id;

            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);

            $this->redir('Empresas/excluir');
        } else {
            $this->redir('Main/index');
        }
    }

}
