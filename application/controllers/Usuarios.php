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

    /**
     * Gera tela com formulário para inserção de novo usuário
     */
    public function cadastrar() {
        $permissao = 'Usuarios/cadastrar';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $title = array(
                "title" => "Cadastro de usuário"
            );

            $existe_projeto = $this->model->existeProjeto();

            $vars = array(
                'perfil' => $this->model->getPerfil($_SESSION ['perfil']),
                'empresas' => $this->model->getEmpresas(),
                'link' => HTTP . '/Usuarios/novoUsuario',
                'botao' => array(
                    'value' => ($existe_projeto ? "Próximo" : "Cadastrar Usuário"),
                    'type' => ($existe_projeto ? "button" : "submit")
                )
            );

            $this->loadView("default/header", $title);
            $this->loadView("usuarios/cadastrar");
            $this->loadView("usuarios/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Realiza a inserção de um novo usuário no sistema
     */
    public function novoUsuario() {
        $permissao = 'Usuarios/cadastrar';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $dados = $this->getDadosPostUsuario();

            if ($this->model->inserirUsuario($dados ['usuario'])) {
                $return = $this->model->ligaUsuarioProjeto($dados ['usuario'] ['usuario'], $dados ['projeto']);
                $_SESSION ['msg_sucesso'] = "Usuário inserido com sucesso.";
                $dados ['dados'] = $return;
            } else {
                $_SESSION ['msg_erro'] = "Erro ao inserir novo usuário. Verifique dados e tente novamente.";
            }

            $dados ['aplicacao'] = $permissao;
            $dados ['msg'] = (empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']);

            Log::gravar($dados, $_SESSION ['id']);

            $this->redir('Usuarios/cadastrar');
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Processa dados para atualização ou inserção de um usuário.
     *
     * @return Array Retorna um array com os dados do usuário.
     */
    private function getDadosPostUsuario() {
        $nome = $_POST ['inputNome'];
        $usuario = $_POST ['inputUsuario'];
        $senha = (isset($_POST ['inputSenha']) ? $_POST ['inputSenha'] : NULL);
        $changeme = (isset($_POST ['inputChangeme'] [0]) ? TRUE : FALSE);
        $email = $_POST ['inputEMail'];
        $telefone = (empty($_POST['inputTelefone']) ? NULL : $_POST['inputTelefone']);
        $perfil = $_POST ['selectPerfil'];
        $empresa = (empty($_POST ['selectEmpresa']) ? NULL : $_POST ['selectEmpresa']);
        $projeto = $_POST ['projeto'];

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
        $permissao_1 = 'Usuarios/cadastrar';
        $permissao_2 = 'Usuarios/alterar';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao_1) || Menu::possuePermissao($perfil, $permissao_2)) {
            $user = $_POST ['user'];
            $id = $_POST ['id'];

            echo json_encode($this->model->getUsuario($user, $id));
        }
    }

    /**
     * Verifica se existe email para algum usuário
     */
    public function validaEmail() {
        $permissao_1 = 'Usuarios/cadastrar';
        $permissao_2 = 'Usuarios/alterar';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao_1) || Menu::possuePermissao($perfil, $permissao_2)) {
            $email = $_POST ['email'];
            $id = $_POST ['id'];

            echo json_encode($this->model->getEmail($email, $id));
        }
    }

    /**
     * Busca usuário para realizar alteração
     */
    public function alterar() {
        $permissao = 'Usuarios/alterar';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $title = array(
                "title" => "Alterar usuário"
            );

            $existe_projeto = $this->model->existeProjeto();

            $vars = array(
                'perfil' => $this->model->getPerfil($_SESSION ['perfil']),
                'empresas' => $this->model->getEmpresas(),
                'link' => HTTP . '/Usuarios/atualizaUsuario',
                'botao' => array(
                    "value" => ($existe_projeto ? "Próximo" : "Alterar Usuário"),
                    "type" => ($existe_projeto ? "button" : "submit")
                )
            );

            $this->loadView("default/header", $title);
            $this->loadView("usuarios/alterar");
            $this->loadView("usuarios/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Busca os nomes de usuários
     */
    public function getUsuarioNome() {
        $permissao_1 = 'Usuarios/alterar';
        $permissao_2 = 'Usuarios/excluir';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao_1) || Menu::possuePermissao($perfil, $permissao_2)) {
            $usuario = $_POST ['term'];

            echo json_encode($this->model->getUsuarioNome($usuario, $perfil));
        }
    }

    /**
     * Busca dados do usuario selecionado para alteração
     */
    public function getDadosUsuarios() {
        $permissao_1 = 'Usuarios/alterar';
        $permissao_2 = 'Usuarios/excluir';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao_1) || Menu::possuePermissao($perfil, $permissao_2)) {
            $usuario = $_POST ['usuario'];

            echo json_encode($this->model->getDadosUsuarios($usuario));
        }
    }

    /**
     * Realiza a atualização do usuário
     */
    public function atualizaUsuario() {
        $permissao = 'Usuarios/alterar';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $dados = $this->getDadosPostUsuario();

            $id = $_POST ['inputID'];

            if ($this->model->atualizaUsuario($dados ['usuario'], $id)) {
                $return = $this->model->ligaUsuarioProjeto($dados ['usuario'] ['usuario'], $dados ['projeto']);
                $_SESSION ['msg_sucesso'] = "Usuário alterado com sucesso.";
                $dados ['status'] = $permissao . ' - ok';
                $dados = array_merge($dados, $return);
            } else {
                $_SESSION ['msg_erro'] = "Erro ao alterar usuário. Verifique dados e tente novamente.";
                $dados ['status'] = $permissao . ' - falha';
            }

            Log::gravar($dados, $_SESSION ['id']);

            $this->redir('Usuarios/alterar');
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Exibe tela de exclusão de usuários
     */
    public function excluir() {
        $permissao = 'Usuarios/excluir';

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $title = array(
                "title" => "Excluir usuário"
            );

            $vars = array(
                'perfil' => $this->model->getPerfil($_SESSION ['perfil']),
                'link' => HTTP . '/Usuarios/removeUsuario',
                'botao' => array(
                    "value" => "Excluir Usuário",
                    "type" => "button"
                )
            );

            $this->loadView("default/header", $title);
            $this->loadView("usuarios/delete");
            $this->loadView("usuarios/index", $vars);
            $this->loadView("default/footer");
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Remove usuário selecionado
     */
    public function removeUsuario() {
        $permissao = 'Usuarios/excluir';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $id = $_POST ['inputID'];
            $usuario = $_POST ['inputUsuario'];
            $email = $_POST ['inputEMail'];

            $dados = array(
                'dados' => array(
                    'id' => $id,
                    'usuario' => $usuario,
                    'email' => $email,
                    'perfil' => $perfil
                )
            );

            if ($this->model->excluirUsuario($id, $usuario, $email, $perfil)) {
                $_SESSION ['msg_sucesso'] = "Usuário excluido com sucesso.";
            } else {
                $_SESSION ['msg_erro'] = "Erro ao excluir usuário. Verifique dados e tente novamente.";
            }

            $dados ['msg'] = (empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']);
            $dados ['aplicacao'] = $permissao;

            Log::gravar($dados, $_SESSION ['id']);

            $this->redir('Usuarios/excluir');
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Busca os projetos que o usuário está cadastro
     * e os projetos disponiveis.
     */
    public function getProjetos() {
        $permissao_1 = "Usuarios/alterar";
        $permissao_2 = "Usuarios/cadastrar";
        $perfil = $_SESSION ['perfil'];

        if ((Menu::possuePermissao($perfil, $permissao_1)) || (Menu::possuePermissao($perfil, $permissao_2))) {
            $id = $_POST ['id'];
            $vars ['projetos'] = $this->model->relacaoProjetos($id);

            $this->loadView('usuarios/projeto_problemas', $vars);
        }
    }

}
