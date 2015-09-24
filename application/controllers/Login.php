<?php

defined('BASEPATH') OR exit('No direct script access allowed');

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

class Login extends CI_Controller {

    /**
     * Construtor
     */
    public function __construct() {
        parent::__construct();

        $this->load->model('login_model');
    }

    /**
     * Exibe tela de login.
     */
    public function index() {
        if (Autenticacao::verificaLogin()) {
            redirect('main/index');
        } else {
            $vars = array('title' => 'Efetuar Login');

            $this->load->view('login/index', $vars);
        }
    }

    /**
     * Recebe login e senha via <b>POST</b> efetua login, caso dados estejam corretos
     * cria sessão e redireciona a página inicial
     */
    public function autenticar() {
        if (Autenticacao::verificaLogin()) {
            redirect('main/index');
        } else {
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

            if ((!empty($usuario)) && (!empty($senha))) {

                $result = $this->login_model->getDadosLogin($usuario, $senha);

                if (count($result) > 0) {
                    $this->session->set_userdata($result);

                    $result['status'] = 'Login/autenticar';
                    Logs::gravar($result, $result['id']);


                    redirect("main/index");
                } else {
                    redirect("login/index");
                }
            } else {
                redirect("login/index");
            }
        }
    }

    /**
     * Remove variáveis de sessão do usuário, e redireciona
     * para tela de login.
     */
    public function logout() {
        $result = $_SESSION;
        $result['status'] = 'Login/efetuarLogout';

        Logs::gravar($result, $result ['id']);

        unset($_SESSION ['id']);
        unset($_SESSION ['usario']);
        unset($_SESSION ['name']);
        unset($_SESSION ['email']);
        unset($_SESSION ['perfil']);
        unset($_SESSION ['nivel']);

        redirect('login/index');
    }

    /**
     * Formulario de alteraçao de senha.
     */
    public function alterarSenha() {
        if (Autenticacao::verificaLogin()) {
            $this->load->helper('form');
            $this->load->library('form_validation');

            $title = array('title' => 'Alterar senha');

            $this->load->view('template/header', $title);
            $this->load->view('login/alterar');
            $this->load->view('template/footer');
        } else {
            redirect("login/index");
        }
    }

    /**
     * Realiza a alteraçao de senha
     */
    public function novaSenha() {
        $nova_senha = filter_input(INPUT_POST, 'novaSenha');
        $redigite = filter_input(INPUT_POST, 'redigite');

        if (strlen($nova_senha) >= 5 && (strcmp($nova_senha, $redigite) === 0)) {
            $result = $_SESSION;
            $usuario = $_SESSION['id'];

            /*
             * Atualiza senha em caso de sucesso gera mensagem de sucesso
             */
            if ($this->login_model->atualizaSenha($usuario, $nova_senha)) {
                $result['situacao'] = 'Senha alterada com sucesso.';
                $_SESSION['msg_sucesso'] = $result['situacao'];
            } else {
                $result['situacao'] = 'Erro ao alterar senha.';
                $_SESSION['msg_erro'] = $result['situacao'];
            }

            /*
             * Gera log da operaçao realizada
             */
            $result['status'] = 'Login/novaSenha';
            Logs::gravar($result, $result ['id']);
        } else {
            $_SESSION['msg_erro'] = 'Erro ao alterar senha. Digite uma senha com mais de 5 caracteres' .
                    (strcmp($nova_senha, $redigite) ? ', senhas digitadas não conferem.' : '');
        }

        redirect("login/alterarSenha");
    }

}
