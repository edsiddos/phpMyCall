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

use \application\models\Usuarios as ModelUsuarios;
use \libs\Menu;
use \libs\Log;

/**
 * Mantem usuários
 *
 * @author Ednei Leite da Silva
 */
class Usuarios extends \system\Controller {

    /**
     * Objeto para obtenção de dados dos usuários.
     *
     * @var ModelUsuarios
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
            $this->model = new ModelUsuarios ();
        }
    }

    public function index() {
        $permissao = 'Usuarios/index';
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $vars = array(
                "title" => "Usuário",
                'perfil' => $this->model->getPerfil($perfil),
                'usuarios' => $this->model->getUsuarios($perfil),
                'empresas' => $this->model->getEmpresas()
            );

            $this->loadView(array("usuarios/index", "usuarios/form"), $vars);
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Busca os projetos que o usuário está cadastro
     * e os projetos disponiveis.
     */
    public function getProjetos() {
        $permissao = "Usuarios/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            echo json_encode($this->model->relacaoProjetos($id));
        }
    }

    public function getUsuarios() {
        $permissao = 'Usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            echo json_encode($this->model->getUsuarios($perfil));
        }
    }

    /**
     * Busca dados do usuario selecionado para alteração
     */
    public function getDadosUsuarios() {
        $permissao = 'Usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_NUMBER_INT);

            $dados_usuario = $this->model->getDadosUsuarios($usuario);
            $dados_usuario['projeto'] = $this->model->relacaoProjetos($usuario);

            echo json_encode($dados_usuario);
        }
    }

    /**
     * Realiza a inserção de um novo usuário no sistema
     */
    public function novoUsuario() {
        $permissao = 'Usuarios/index';

        if (Menu::possuePermissao($_SESSION['perfil'], $permissao)) {
            $dados = $this->getDadosPostUsuario();

            if ($this->model->inserirUsuario($dados ['usuario'])) {
                $return = $this->model->ligaUsuarioProjeto($dados['usuario']['usuario'], $dados['projeto']);
                $msg['status'] = true;
                $msg['msg'] = "Usuário inserido com sucesso.";
                $dados['dados'] = $return;
            } else {
                $msg['status'] = false;
                $msg['msg'] = "Erro ao inserir novo usuário. Verifique dados e tente novamente.";
            }

            $dados['aplicacao'] = $permissao;
            $dados['msg'] = $msg['msg'];

            Log::gravar($dados, $_SESSION['id']);

            echo json_encode($msg);
        }
    }

    /**
     * Processa dados para atualização ou inserção de um usuário.
     *
     * @return Array Retorna um array com os dados do usuário.
     */
    private function getDadosPostUsuario() {
        $nome = filter_input(INPUT_POST, 'inputNome', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $usuario = filter_input(INPUT_POST, 'inputUsuario', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $senha = filter_input(INPUT_POST, 'inputSenha', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $changeme = filter_input(INPUT_POST, 'inputChangeme', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)[0];
        $changeme = $changeme === 'changeme' ? TRUE : FALSE;
        $email = filter_input(INPUT_POST, 'inputEMail', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $telefone = filter_input(INPUT_POST, 'inputTelefone', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $perfil = filter_input(INPUT_POST, 'selectPerfil', FILTER_SANITIZE_NUMBER_INT);
        $empresa = filter_input(INPUT_POST, 'selectEmpresa', FILTER_SANITIZE_NUMBER_INT);
        $projeto = filter_input(INPUT_POST, 'inputProjetos', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        /* Verifica se todos os dados necessários foram informados */
        $datetime = NULL;

        // caso o usuário tenha selecionado "Senha temporária"
        // seta data de troca para "HOJE"
        if ($changeme) {
            $datetime = new \DateTime ();
        } else {
            $datetime = new \DateTime ();
            $datetime->add(new \DateInterval('P30D'));
        }

        $dados ['usuario'] = array(
            'usuario' => $usuario,
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'perfil' => $perfil,
            'empresa' => $empresa,
            'dt_troca' => $datetime->format('Y-m-d')
        );

        $dados ['projeto'] = $projeto;

        if (!empty($senha)) {
            $dados ['usuario'] ['senha'] = sha1(md5($senha));
        }

        return $dados;
    }

    /**
     * Verifica se o usuário existe
     */
    public function validaUsuario() {
        $permissao = 'Usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING);
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            echo json_encode($this->model->validaUsuario($user, $id));
        }
    }

    /**
     * Verifica se existe email para algum usuário
     * e se este é valido
     */
    public function validaEmail() {
        $permissao = 'Usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            if (eregi("^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.([a-zA-Z]{2,4})$", $email)) {
                echo json_encode($this->model->getEmail($email, $id));
            } else {
                echo json_encode(array('exist' => true));
            }
        }
    }

    /**
     * Realiza a atualização do usuário
     */
    public function atualizaUsuario() {
        $permissao = 'Usuarios/index';

        if (Menu::possuePermissao($_SESSION['perfil'], $permissao)) {
            $dados = $this->getDadosPostUsuario();

            $id = filter_input(INPUT_POST, 'inputID', FILTER_SANITIZE_NUMBER_INT);

            if ($this->model->atualizaUsuario($dados['usuario'], $id)) {
                $return = $this->model->ligaUsuarioProjeto($dados['usuario']['usuario'], $dados['projeto']);
                $msg['status'] = true;
                $msg['msg'] = "Usuário alterado com sucesso.";
                $dados['dados'] = $return;
            } else {
                $msg['status'] = false;
                $msg['msg'] = "Erro ao alterar usuário. Verifique dados e tente novamente.";
            }

            $dados['aplicacao'] = $permissao;
            $dados['msg'] = $msg['msg'];

            Log::gravar($dados, $_SESSION ['id']);
            echo json_encode($msg);
        }
    }

    /**
     * Remove usuário selecionado
     */
    public function removeUsuario() {
        $permissao = 'Usuarios/index';
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            $dados = array(
                'dados' => array(
                    'id' => $id,
                    'perfil' => $perfil
                )
            );

            if ($this->model->excluirUsuario($id, $perfil)) {
                $msg['status'] = true;
                $msg['msg'] = "Usuário excluido com sucesso.";
            } else {
                $msg['status'] = false;
                $msg['msg'] = "Erro ao excluir usuário. Verifique dados e tente novamente.";
            }

            $dados['msg'] = $msg['msg'];
            $dados['aplicacao'] = $permissao;

            Log::gravar($dados, $_SESSION['id']);

            echo json_encode($msg);
        }
    }

}
