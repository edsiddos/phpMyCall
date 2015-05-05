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

namespace system;

use \system\Controller;
use \application\controllers\Error;

/**
 * Classe responsável por carregar os controladores
 *
 * @author Ednei Leite da Silva
 */
class System {

    private $url;
    private $explode;
    private $controller;
    private $action;

    /**
     * Método construtor
     *
     * @param string $url Dados informados no formato url amigavel.
     */
    public function __construct($url) {
        if (empty($url)) {
            $this->url = 'Main/index';
        } else {
            $this->url = $url;
        }

        $this->explode = explode('/', $this->url);

        $this->setController();
        $this->setAction();
    }

    /**
     * Retira o controlador da url amigavel
     */
    private function setController() {
        $this->controller = '\\application\\controllers\\' . array_shift($this->explode);
    }

    /**
     * Retira a action da url amigavel
     */
    private function setAction() {
        $this->action = array_shift($this->explode);
    }

    /**
     * Executa controlador com action e passa os parâmetros
     */
    public function run() {
        if (class_exists($this->controller)) {
            $controlador = $this->controller;

            $obj = new $controlador ();

            if (method_exists($obj, $this->action)) {
                $method = $this->action;

                $obj->$method($this->explode);
            } else {
                $error = new Error ();
                $error->erro_404();
            }
        } else {
            $error = new Error ();
            $error->erro_404();
        }
    }

}
