<?php

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

/**
 * Classe manten as configuraçoes do ambiente
 *
 * @author Ednei Leite da Silva
 */
class Administracao extends Admin_Controller {

    /**
     * Verifica se usuários esta logado antes de executar operação
     */
    public function __construct() {
        parent::__construct('administracao');

        $this->load->model('administracao_model', 'model');
    }

    /**
     * Gera tela com formulário para inserção de nova empresa
     */
    public function index() {
        $permissao = 'administracao/index';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $this->load->helper('form');

            $views = array('administracao/index');

            $this->load_view($views);
        } else {
            redirect('main/index');
        }
    }

}
