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

use \application\models\Login as ModelLogin;
use \libs\Log;

/**
 * Classe que realiza login e verifica se o usuário esta
 * autenticado no sistema.
 *
 * @author Ednei Leite da Silva
 */
class Login extends \system\Controller {

    /**
     * Verifica se usuários esta logado, caso esteja rediciona para página
     * inicial da aplicação.
     * Caso contrário exibe tela de login.
     *
     * @param Array $parametros Dados passados via url amigavel
     */
    public function index($parametros = array()) {
        if (!Login::verificaLogin()) {
            $this->loadView(array('login/index'), array('title' => 'Efetuar Login'));
        } else {
            $this->redir("Main/index");
        }
    }

    /**
     * Verifica se usuário esta logado.
     *
     * @return boolean Retorna <b>TRUE</b> se usuário devidamente logado, <b>FALSE</b> caso contrário.
     */
    public static function verificaLogin() {
        session_start();

        $username = $_SESSION ['username'];
        $nome = $_SESSION ['nome'];
        $perfil = $_SESSION ['perfil'];
        $email = $_SESSION ['email'];

        if (empty($username) || empty($nome) || empty($perfil) || empty($email)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Recebe login e senha via <b>POST</b> efetua login, caso dados estejam corretos
     * cria sessão e redireciona a página inicial
     */
    public function efetuarLogin() {
        $usuario = (is_string($_POST ['usuario']) ? $_POST ['usuario'] : '');
        $senha = (is_string($_POST ['senha']) ? $_POST ['senha'] : '');

        if ((!empty($usuario)) && (!empty($senha))) {
            $db = new ModelLogin ();
            $result = $db->getDadosLogin($usuario, $senha);

            if (count($result) > 0) {
                session_start();
                $_SESSION ['id'] = $result ['id'];
                $_SESSION ['username'] = $result ['usuario'];
                $_SESSION ['nome'] = $result ['nome'];
                $_SESSION ['email'] = $result ['email'];
                $_SESSION ['perfil'] = $result ['perfil'];

                $result ['status'] = 'Login/efetuarLogin';
                Log::gravar($result, $result ['id']);

                $this->redir("Main/index");
            } else {
                $this->redir("Login/index");
            }
        } else {
            $this->redir("Login/index");
        }
    }

    /**
     * Remove variáveis de sessão do usuário, e redireciona
     * para tela de login.
     */
    public function efetuarLogout() {
        session_start();

        $result = $_SESSION;
        $result['status'] = 'Login/efetuarLogout';
        Log::gravar($result, $result ['id']);

        unset($_SESSION ['id']);
        unset($_SESSION ['username']);
        unset($_SESSION ['name']);
        unset($_SESSION ['email']);
        unset($_SESSION ['perfil']);

        $this->redir("Login/index");
    }

}
