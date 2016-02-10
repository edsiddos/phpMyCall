<?php

defined('BASEPATH') OR exit('No direct script access allowed');

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

class Login extends Public_Controller {

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
        if (Autenticacao::verifica_login()) {
            redirect('main/index');
        } else {
            $this->load_view('login/index', array(), 'login');
        }
    }

    /**
     * Recebe login e senha via <b>POST</b> efetua login, caso dados estejam corretos
     * cria sessão e redireciona a página inicial
     */
    public function autenticar() {
        if (Autenticacao::verifica_login()) {
            redirect('main/index');
        } else {
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

            if ((!empty($usuario)) && (!empty($senha))) {

                $result = $this->login_model->get_dados_login($usuario, $senha);

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

}
