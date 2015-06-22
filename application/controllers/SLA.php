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

use system\Controller;
use application\models\SLA as ModelSLA;
use libs\Menu;
use libs\Log;
use libs\Cache;
use DateTime;

class SLA extends Controller {

    /**
     * Objeto de conexão com banco de dados.
     * @var SLAModel
     */
    private $model;

    /**
     * Construtor
     */
    public function __construct() {
        if (!Login::verificaLogin()) {
            $this->redir("Login/index");
        }

        $this->model = new ModelSLA();
    }

    public function index() {
        $permissao = "SLA/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $title = array(
                'title' => 'Abrir Solicitação'
            );

            $vars = array(
                'projetos' => $this->model->getProjetos($_SESSION['id']),
                'prioridade' => $this->model->getPrioridades(),
                'link' => HTTP . '/SLA/gerar'
            );

            $this->loadView('default/header', $title);
            $this->loadView('sla/index', $vars);
            $this->loadView('default/footer');
        }
    }

    public function getParticipantes() {
        $permissao = "SLA/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $projeto = $_POST['projeto'];
            echo json_encode($this->model->getParticipantes($projeto));
        }
    }

    public function gerar() {
        print_r($this->model->getExpedienteByDiaSemana());
    }

}
