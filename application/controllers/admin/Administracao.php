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
     * Gera tela para manutenção das configurações
     */
    public function index() {
        $permissao = 'administracao/index';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $this->load->helper('form');

            $dados = array(
                'config_solicitacoes' => $this->model->get_config_solicitacoes(),
                'perfis' => $this->model->get_perfil(),
                'prioridades' => $this->model->get_prioridades(),
                'menus' => $this->model->get_configuracao_menu()
            );

            $views = array('administracao/index');

            $this->load_view($views, $dados);
        } else {
            redirect('main/index');
        }
    }

    public function grava_config_solicitacao() {
        $config = filter_input(INPUT_POST, 'config', FILTER_SANITIZE_STRING);
        $perfil = filter_input(INPUT_POST, 'perfil', FILTER_SANITIZE_NUMBER_INT);
        $checked = filter_input(INPUT_POST, 'checked', FILTER_VALIDATE_BOOLEAN);

        $permissao = 'administracao/index';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)):
            $relacao_permitidos = $this->model->get_relacao_permissao_solicitacao($config);
            $status = FALSE;

            if ($checked === TRUE):
                $chave_add = array_search($perfil, $relacao_permitidos);

                if ($chave_add === FALSE):
                    $relacao_permitidos[] = $perfil;
                    $status = $this->model->atualiza_relacao_permissao_solicitacao($config, implode(', ', $relacao_permitidos));
                endif;
            elseif ($checked === FALSE):
                $chave_removido = array_search($perfil, $relacao_permitidos);

                if ($chave_removido !== FALSE):
                    unset($relacao_permitidos[$chave_removido]);
                    $status = $this->model->atualiza_relacao_permissao_solicitacao($config, implode(', ', $relacao_permitidos));
                endif;
            endif;

            $log = array(
                'dados' => $relacao_permitidos,
                'aplicacao' => 'administracao/grava_config_solicitacao/' . ($checked ? 'adicionar' : 'remover'),
                'msg' => $status['msg']
            );

            Logs::gravar($log, $_SESSION['id']);

            echo json_encode(array('status' => $status));
        endif;
    }

    public function grava_prioridade_solicitacao() {
        $prioridade = filter_input(INPUT_POST, 'prioridade', FILTER_SANITIZE_NUMBER_INT);

        $permissao = 'administracao/index';

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)):

            $log = array(
                'dados' => $relacao_permitidos,
                'aplicacao' => 'administracao/grava_prioridade_solicitacao',
                'msg' => $status['msg']
            );

            Logs::gravar($log, $_SESSION['id']);

            echo json_encode(array('status' => $status));
        endif;
    }

    public function altera_cor_prioridade() {
        
    }

    public function altera_acesso_menus() {
        
    }

}
