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

/**
 * Mantem usuários
 *
 * @author Ednei Leite da Silva
 */
class Usuarios extends CI_Controller {

    private $translate = array();

    /**
     * Verifica se usuários esta logado antes de executar operação
     */
    public function __construct() {
        parent::__construct();
        if (!Autenticacao::verifica_login()) {
            redirect('Login/index');
        } else {
            $this->translate = $this->lang->load('usuario', 'portuguese-brazilian', TRUE);
            $this->load->model('usuarios_model');
        }
    }

    public function index() {
        $permissao = 'usuarios/index';
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $nivel = $_SESSION['nivel'];

            $vars = array(
                'perfil' => $this->usuarios_model->get_perfil($nivel),
                'empresas' => $this->usuarios_model->get_empresas()
            );

            $vars = array_merge($vars, $this->translate);

            $var_header = array(
                'title' => $this->translate['titulo_janela'],
                'js_path_translation_bootstrap_select' => $this->translate['js_path_translation_bootstrap_select']
            );

            $this->load->view("template/header", $var_header);
            $this->load->view("usuarios/index", $vars);
            $this->load->view("usuarios/form");
            $this->load->view("template/footer");
        } else {
            redirect('Main/index');
        }
    }

    /**
     * Busca os projetos que o usuário está cadastro
     * e os projetos disponiveis.
     */
    public function get_projetos() {
        $permissao = "usuarios/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            echo json_encode($this->usuarios_model->relacao_projetos($id));
        }
    }

    public function get_usuarios() {
        $permissao = 'usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $columns = filter_input(INPUT_POST, 'columns', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $draw = filter_input(INPUT_POST, 'draw', FILTER_SANITIZE_NUMBER_INT);
            $limit = filter_input(INPUT_POST, 'length', FILTER_SANITIZE_NUMBER_INT);
            $offset = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_NUMBER_INT);
            $order = filter_input(INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $search = filter_input(INPUT_POST, 'search', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            $nivel = $_SESSION['nivel'];

            if (!empty($columns[$order[0]['column']]['data'])) {
                $order_by = "{$columns[$order[0]['column']]['data']} {$order[0]['dir']}";
            } else {
                $order_by = "id asc";
            }

            $records = $this->usuarios_model->get_quantidades_usuarios($nivel, $search['value']);

            $return = array(
                "draw" => empty($draw) ? 0 : $draw,
                "recordsTotal" => $records['recordsTotal'],
                "recordsFiltered" => $records['recordsFiltered'],
                "data" => $this->usuarios_model->get_usuarios($nivel, $search['value'], $order_by, $limit, $offset)
            );

            echo json_encode($return);
        }
    }

    /**
     * Busca dados do usuario selecionado para alteração
     */
    public function get_dados_usuarios() {
        $permissao = 'usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_NUMBER_INT);

            $dados_usuario = $this->usuarios_model->get_dados_usuarios($usuario);
            $dados_usuario['projeto'] = $this->usuarios_model->relacao_projetos($usuario);

            echo json_encode($dados_usuario);
        }
    }

    /**
     * Realiza a inserção de um novo usuário no sistema
     */
    public function novo_usuario() {
        $permissao = 'usuarios/index';

        if (Menu::possue_permissao($_SESSION['perfil'], $permissao)) {
            $dados = $this->get_dados_post_usuario();

            if ($this->usuarios_model->inserir_usuario($dados ['usuario'])) {
                $return = $this->usuarios_model->liga_usuario_projeto($dados['usuario']['usuario'], $dados['projeto']);
                $msg['status'] = true;
                $msg['msg'] = $this->translate['inserido_com_sucesso'];
                $dados['dados'] = $return;
            } else {
                $msg['status'] = false;
                $msg['msg'] = $this->translate['erro_ao_inserir'];
            }

            $dados['aplicacao'] = $permissao;
            $dados['msg'] = $msg['msg'];

            Logs::gravar($dados, $_SESSION['id']);

            echo json_encode($msg);
        }
    }

    /**
     * Processa dados para atualização ou inserção de um usuário.
     *
     * @return Array Retorna um array com os dados do usuário.
     */
    private function get_dados_post_usuario() {
        $nome = filter_input(INPUT_POST, 'input_nome', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $usuario = filter_input(INPUT_POST, 'input_usuario', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $senha = filter_input(INPUT_POST, 'input_senha', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $changeme = filter_input(INPUT_POST, 'input_changeme', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL) === 'changeme' ? TRUE : FALSE;
        $email = filter_input(INPUT_POST, 'input_email', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $telefone = filter_input(INPUT_POST, 'input_telefone', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
        $perfil = filter_input(INPUT_POST, 'select_perfil', FILTER_SANITIZE_NUMBER_INT);
        $empresa = filter_input(INPUT_POST, 'select_empresa', FILTER_SANITIZE_NUMBER_INT);
        $projeto = filter_input(INPUT_POST, 'input_projetos', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        /* Verifica se todos os dados necessários foram informados */
        $datetime = NULL;

        // caso o usuário tenha selecionado "Senha temporária"
        // seta data de troca para "HOJE"
        if ($changeme) {
            $datetime = new DateTime ();
        } else {
            $datetime = new DateTime ();
            $datetime->add(new DateInterval('P30D'));
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
    public function valida_usuario() {
        $permissao = 'usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            echo json_encode($this->usuarios_model->valida_usuario($user, $id));
        }
    }

    /**
     * Verifica se existe email para algum usuário
     * e se este é valido
     */
    public function valida_email() {
        $permissao = 'usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

            if (preg_match("/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.([a-zA-Z]{2,4})$/", $email)) {
                echo json_encode($this->usuarios_model->get_email($email, $id));
            } else {
                echo json_encode(TRUE);
            }
        }
    }

    /**
     * Realiza a atualização do usuário
     */
    public function atualiza_usuario() {
        $permissao = 'usuarios/index';

        if (Menu::possue_permissao($_SESSION['perfil'], $permissao)) {
            $dados = $this->get_dados_post_usuario();

            $id = filter_input(INPUT_POST, 'input_id', FILTER_SANITIZE_NUMBER_INT);

            if ($this->usuarios_model->atualiza_usuario($dados['usuario'], $id)) {
                $return = $this->usuarios_model->liga_usuario_projeto($dados['usuario']['usuario'], $dados['projeto']);
                $msg['status'] = true;
                $msg['msg'] = $this->translate['alterado_com_sucesso'];
                $dados['dados'] = $return;
            } else {
                $msg['status'] = false;
                $msg['msg'] = $this->translate['erro_ao_alterar'];
            }

            $dados['aplicacao'] = $permissao;
            $dados['msg'] = $msg['msg'];

            Logs::gravar($dados, $_SESSION ['id']);
            echo json_encode($msg);
        }
    }

    /**
     * Remove usuário selecionado
     */
    public function remove_usuario() {
        $permissao = 'usuarios/index';
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $nivel = $_SESSION['nivel'];

            $dados = array(
                'dados' => array(
                    'id' => $id,
                    'perfil' => $perfil
                )
            );

            if ($this->usuarios_model->excluir_usuario($id, $nivel)) {
                $msg['status'] = true;
                $msg['msg'] = $this->translate['excluido_com_sucesso'];
            } else {
                $msg['status'] = false;
                $msg['msg'] = $this->translate['erro_ao_excluir'];
            }

            $dados['msg'] = $msg['msg'];
            $dados['aplicacao'] = $permissao;

            Logs::gravar($dados, $_SESSION['id']);

            echo json_encode($msg);
        }
    }

}
