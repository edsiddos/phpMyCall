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

        if (Autenticacao::verificaLogin()) {
            redirect('main/index');
        } else {
            $this->load->model('login_model');
        }
    }

    /**
     * Exibe tela de login.
     */
    public function index() {
        $vars = array('title' => 'Efetuar Login');

        $this->load->view('login/index', $vars);
    }

    /**
     * Recebe login e senha via <b>POST</b> efetua login, caso dados estejam corretos
     * cria sessão e redireciona a página inicial
     */
    public function autenticar() {
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
