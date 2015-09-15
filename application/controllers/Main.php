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

/**
 * Controlador da página principal
 *
 * @author Ednei Leite da Silva
 */
class Main extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!Autenticacao::verificaLogin()) {
            redirect('login/index');
        }
    }

    /**
     * Mostra os projetos em aberto e em andamento na tela inicial.
     */
    public function index() {
        /*
          $solicitacao = new Solicitacao();

          $usuario = $_SESSION['id'];
          $perfil = $_SESSION['perfil'];

          $parametros = Cache::getCache(PARAMETROS);

          $var = array(
          'aberta' => $solicitacao->getSolicitacoes($usuario, $perfil, 1),
          'andamento' => $solicitacao->getSolicitacoes($usuario, $perfil, 2),
          'prioridades' => $parametros['CORES_SOLICITACOES'],
          'title' => 'PhpMyCall'
          );
         */

        $this->load->view('template/header', array('title' => 'phpMyCall'));
        $this->load->view('main/index');
        $this->load->view('template/footer');
    }

}
