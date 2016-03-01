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
 * Mantem usuários
 *
 * @author Ednei Leite da Silva
 */
class Usuarios extends Admin_Controller {

    /**
     * Método construtor verifica se usuário esta logado
     * e instancia objeto de conexão com banco de dados
     */
    public function __construct() {
        parent::__construct('usuario');

        $this->load->model('usuarios_model', 'model');
    }

    public function index() {
        $permissao = 'usuarios/index';
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $nivel = $_SESSION['nivel'];

            $vars = array(
                'perfil' => $this->model->get_perfil($nivel),
                'empresas' => $this->model->get_empresas()
            );

            $this->load_view(array("usuarios/index", "usuarios/form"), $vars);
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

            $this->response($this->model->relacao_projetos($id));
        }
    }

    public function get_usuarios() {
        $permissao = 'usuarios/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $limit = filter_input(INPUT_POST, 'limit', FILTER_SANITIZE_NUMBER_INT);
            $offset = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_NUMBER_INT);
            $sort = filter_input(INPUT_POST, 'sort', FILTER_SANITIZE_STRING);
            $order = filter_input(INPUT_POST, 'order', FILTER_SANITIZE_STRING);
            $search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);

            $nivel = $_SESSION['nivel'];

            if (!empty($sort)) {
                $order_by = "$sort $order";
            } else {
                $order_by = "id asc";
            }

            $records = $this->model->get_quantidades_usuarios($nivel, $search);

            $return = array(
                "total" => $records,
                "rows" => $this->model->get_usuarios($nivel, $search, $order_by, $limit, $offset)
            );

            $this->response($return);
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

            $dados_usuario = $this->model->get_dados_usuarios($usuario);
            $dados_usuario['projeto'] = $this->model->relacao_projetos($usuario);

            $this->response($dados_usuario);
        }
    }

    /**
     * Realiza a inserção de um novo usuário no sistema
     */
    public function novo_usuario() {
        $permissao = 'usuarios/index';

        if (Menu::possue_permissao($_SESSION['perfil'], $permissao)) {
            $dados = $this->get_dados_post_usuario();

            if ($this->model->inserir_usuario($dados ['usuario'])) {
                $return = $this->model->liga_usuario_projeto($dados['usuario']['usuario'], $dados['projeto']);
                $msg['status'] = true;
                $msg['msg'] = $this->translate['insert_user_success'];
                $dados['dados'] = $return;
            } else {
                $msg['status'] = false;
                $msg['msg'] = $this->translate['insert_user_error'];
            }

            $dados['aplicacao'] = $permissao;
            $dados['msg'] = $msg['msg'];

            Logs::gravar($dados, $_SESSION['id']);

            $this->response($msg);
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

            $result['status'] = $this->model->valida_usuario($user, $id);
            $this->response($result);
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
                $this->response(array('status' => $this->model->get_email($email, $id)));
            } else {
                $this->response(array('status' => TRUE));
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

            if ($this->model->atualiza_usuario($dados['usuario'], $id)) {
                $return = $this->model->liga_usuario_projeto($dados['usuario']['usuario'], $dados['projeto']);
                $msg['status'] = true;
                $msg['msg'] = $this->translate['update_user_success'];
                $dados['dados'] = $return;
            } else {
                $msg['status'] = false;
                $msg['msg'] = $this->translate['update_user_error'];
            }

            $dados['aplicacao'] = $permissao;
            $dados['msg'] = $msg['msg'];

            Logs::gravar($dados, $_SESSION ['id']);
            $this->response($msg);
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

            if ($this->model->excluir_usuario($id, $nivel)) {
                $msg['status'] = true;
                $msg['msg'] = $this->translate['delete_user_success'];
            } else {
                $msg['status'] = false;
                $msg['msg'] = $this->translate['delete_user_error'];
            }

            $dados['msg'] = $msg['msg'];
            $dados['aplicacao'] = $permissao;

            Logs::gravar($dados, $_SESSION['id']);

            $this->response($msg);
        }
    }

    /**
     * Formulario de alteraçao de senha.
     */
    public function alterar_senha() {
        $this->load->helper('form');

        $this->load_view('usuarios/alterar');
    }

    /**
     * Realiza a alteraçao de senha
     */
    public function nova_senha() {
        $nova_senha = filter_input(INPUT_POST, 'nova_senha');
        $redigite = filter_input(INPUT_POST, 'redigite');

        if (strlen($nova_senha) >= 5 && (strcmp($nova_senha, $redigite) === 0)) {
            $result = $_SESSION;
            $usuario = $_SESSION['id'];

            /*
             * Atualiza senha em caso de sucesso gera mensagem de sucesso
             */
            if ($this->model->atualiza_senha($usuario, $nova_senha)) {
                $result['situacao'] = 'Senha alterada com sucesso.';
                $_SESSION['msg_sucesso'] = $result['situacao'];
            } else {
                $result['situacao'] = 'Erro ao alterar senha.';
                $_SESSION['msg_erro'] = $result['situacao'];
            }

            /*
             * Gera log da operaçao realizada
             */
            $result['status'] = 'Usuarios/nova_senha';
            Logs::gravar($result, $result ['id']);
        } else {
            $_SESSION['msg_erro'] = 'Erro ao alterar senha. Digite uma senha com mais de 5 caracteres' .
                    (strcmp($nova_senha, $redigite) ? ', senhas digitadas não conferem.' : '');
        }

        redirect("usuarios/alterar_senha");
    }

}
