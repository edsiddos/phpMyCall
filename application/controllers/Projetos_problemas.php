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
 * Manipulas os projetos e tipos de problemas
 *
 * @author Ednei Leite da Silva
 */
class Projetos_problemas extends CI_Controller {

    /**
     * Construtor
     */
    public function __construct() {
        parent::__construct();
        if (!Autenticacao::verifica_login()) {
            redirect("Login/index");
        } else {

            $this->load->model('projetos_problemas_model', 'model');
        }
    }

    /**
     * Gera tela para manutenção de projetos e problemas
     */
    public function index() {
        $permissao = "Projeto_problemas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {

            $title['title'] = 'Projetos tipo de problema.';

            $this->load->view('template/header', $title);
            $this->load->view('projetos_problemas/index');
            $this->load->view('projetos_problemas/form');
            $this->load->view('template/footer');
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Busca os tipos de projetos
     */
    public function get_projetos() {
        $permissao = "Projetos_problemas/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $nome = filter_input(INPUT_POST, 'term', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);

            echo json_encode($this->model->get_projetos($nome));
        }
    }

    /**
     * Busca os tipos de problemas existentes
     */
    public function get_problemas() {
        $permissao = "Projeto_problemas/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $nome = filter_input(INPUT_POST, 'term', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);

            echo json_encode($this->model->get_problemas($nome));
        }
    }

    /**
     * Busca ID de um projeto
     */
    public function get_dados_projeto() {
        $permissao = "Projeto_problemas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $id = $this->model->get_id_projeto($nome);

            $usuarios = $this->model->relacao_usuarios($perfil);
            $participantes = $this->model->get_relacao_participantes($id);
            $vars = $this->model->get_descricao_projeto($id);

            $vars['usuarios'] = array();
            $vars['participantes'] = array();

            foreach ($usuarios as $values) {
                if (in_array($values ['value'], $participantes)) {
                    $vars['participantes'][] = $values;
                } else {
                    $vars['usuarios'][] = $values;
                }
            }

            echo json_encode($vars);
        }
    }

    /**
     * Insere um novo projeto com os respectivos participantes
     */
    public function cadastrar() {
        $permissao = '_projeto_problemas/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $utils = new Utils();
            $valida_hora = array('options' => array($utils, 'validaFormatoHora'));

            $participantes = filter_input(INPUT_POST, 'participantes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $projeto = filter_input(INPUT_POST, 'input_nome_projeto', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $problema = filter_input(INPUT_POST, 'input_nome_problema', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $resposta = filter_input(INPUT_POST, 'input_resposta', FILTER_CALLBACK, $valida_hora);
            $solucao = filter_input(INPUT_POST, 'input_solucao', FILTER_CALLBACK, $valida_hora);
            $descricao_projeto = filter_input(INPUT_POST, 'text_projeto', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $descricao = filter_input(INPUT_POST, 'textDescricao', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);

            if (!$this->model->existe_projeto_problema($projeto, $problema)) {
                $id_projeto = $this->model->get_id_projeto($projeto);
                $id = $id_projeto;

                if (empty($id_projeto)) {
                    $id_projeto = $this->model->insert_projeto($projeto, $descricao_projeto);

                    if (empty($id_projeto)) {
                        $dados = array('status' => false, 'msg' => "Erro ao criar projeto");
                    } else if (!empty($participantes)) {
                        $this->model->adiciona_partcipantes_projeto($participantes, $id_projeto);
                    }
                }

                if (!empty($id_projeto)) {
                    $id_problema = $this->model->get_id_problema($problema);

                    if (empty($id_problema)) {
                        $id_problema = $this->model->insert_tipo_problema($problema);

                        if (empty($id_problema)) {
                            $dados = array('status' => false, 'msg' => "Erro ao criar tipo de problema");
                        }
                    }

                    if (!empty($id_problema)) {
                        $this->model->cria_projeto_problemas($id_projeto, $id_problema, $resposta, $solucao, $descricao);
                        $dados = array('status' => true, 'msg' => 'Projeto criado com sucesso');
                    }
                }

                $dados_log = array(
                    'dados' => array(
                        'operacao' => empty($id) ? 'Criação projeto e problema' : 'Adição de tipo de problema',
                        'id_projeto' => $id_projeto,
                        'nome_projeto' => $projeto,
                        'descricao_projeto' => $descricao_projeto,
                        'id_tipo_problema' => $id_problema,
                        'nome_tipo_problema' => $problema,
                        'tempo_resposta' => $resposta,
                        'tempo_solucao' => $solucao,
                        'novos_usuarios' => (empty($participantes) ? array() : implode(',', $participantes))
                    )
                );
            } else {
                $dados = array('status' => false, 'msg' => "Já existe projeto com este tipo de problema.");
            }

            $dados_log ['msg'] = $dados['msg'];
            $dados_log ['aplicacao'] = $permissao;

            Log::gravar($dados_log, $_SESSION ['id']);

            $dados['lista_projeto_problemas'] = $this->model->lista_projeto_problemas();

            echo json_encode($dados);
        }
    }

    /**
     * Busca informações sobre o projeto.
     */
    public function get_dados_projeto_problemas() {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $permissao = "Projeto_problemas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            echo json_encode($this->model->get_dados_projeto_problema($id));
        }
    }

    /**
     * Realiza a operação de atualização do projeto (alterar)
     */
    public function alterar() {
        $permissao = 'Projeto_problemas/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $valida_hora = array('options' => array('Utils', 'validaFormatoHora'));

            $id_projeto = filter_input(INPUT_POST, 'input_projeto', FILTER_SANITIZE_NUMBER_INT);
            $id_problema = filter_input(INPUT_POST, 'input_problema', FILTER_SANITIZE_NUMBER_INT);
            $id_projeto_problema = filter_input(INPUT_POST, 'input_projeto_problema', FILTER_SANITIZE_NUMBER_INT);
            $participantes = filter_input(INPUT_POST, 'participantes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $projeto = filter_input(INPUT_POST, 'input_nome_projeto', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $problema = filter_input(INPUT_POST, 'input_nome_problema', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $resposta = filter_input(INPUT_POST, 'input_resposta', FILTER_CALLBACK, $valida_hora);
            $solucao = filter_input(INPUT_POST, 'input_solucao', FILTER_CALLBACK, $valida_hora);
            $descricao_projeto = filter_input(INPUT_POST, 'text_projeto', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);
            $descricao = filter_input(INPUT_POST, 'text_descricao', FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL);

            $update_projeto = array(
                'nome' => $projeto,
                'descricao' => $descricao_projeto
            );

            if ($this->model->altera_projeto($update_projeto, $id_projeto)) {
                $participantes_old = $this->model->get_relacao_participantes($id_projeto);
                $insert = empty($participantes) ? array() : $participantes;
                $delete = array();

                foreach ($participantes_old as $value) {
                    if (!in_array($value, $insert)) {
                        $this->model->delete_participantes_projeto($value, $id_projeto);
                        $delete [] = $value;
                    }

                    $key = array_search($value, $insert);
                    if ($key !== false) {
                        unset($insert [$key]);
                    }
                }

                $this->model->adiciona_partcipantes_projeto($insert, $id_projeto);

                $delete = implode(',', $delete);
                $insert = implode(',', $insert);

                $id_problema = $this->model->get_id_problema($problema);

                if (empty($id_problema)) {
                    $id_problema = $this->model->insert_tipo_problema($problema);

                    if (empty($id_problema)) {
                        $dados = array('status' => false, 'msg' => "Erro ao criar tipo de problema");
                    }
                }

                /*
                 * Verifica se foi inserido ou existe tipo do problema
                 */
                if (!empty($id_problema)) {
                    $this->model->atualiza_projeto_problemas($id_projeto_problema, $id_projeto, $id_problema, $resposta, $solucao, $descricao);
                    $dados = array('status' => true, 'msg' => "Atualização de projeto problema realizada com sucesso");
                }

                $dados_log = array(
                    'dados' => array(
                        'id_projeto' => $id_projeto,
                        'nome_projeto' => $projeto,
                        'descricao_projeto' => $descricao_projeto,
                        'id_tipo_problema' => $id_problema,
                        'nome_tipo_problema' => $problema,
                        'id_projeto_tipo_problema' => $id_projeto_problema,
                        'tempo_resposta' => $resposta,
                        'tempo_solucao' => $solucao,
                        'projeto_tipo_problema' => $descricao,
                        'novos_usuarios' => $insert,
                        'excluir_usuarios' => $delete
                    ),
                    'msg' => $dados['msg'],
                    'aplicacao' => $permissao
                );

                Log::gravar($dados, $_SESSION ['id']);

                $dados['lista_projeto_problemas'] = $this->model->lista_projeto_problemas();

                echo json_encode($dados);
            }
        }
    }

    /**
     * Realiza a exclusão do projeto tipo de problema selecionado
     */
    public function excluir() {
        $permissao = "Projeto_problemas/index";

        if (Menu::possue_permissao($_SESSION ['perfil'], $permissao)) {
            $id_projeto = filter_input(INPUT_POST, 'projeto', FILTER_SANITIZE_NUMBER_INT);
            $id_projeto_problema = filter_input(INPUT_POST, 'projeto_problema', FILTER_SANITIZE_NUMBER_INT);

            if ($this->model->excluir_projeto_problemas($id_projeto, $id_projeto_problema)) {
                $dados = array('status' => true, 'msg' => 'Sucesso ao excluir projeto tipo de problema');
            } else {
                $dados = array('status' => false, 'msg' => 'Erro ao excluir projeto tipo de problema');
            }

            $log = array(
                'dados' => array(
                    'id_projeto' => $id_projeto,
                    'id_projeto_problema' => $id_projeto_problema
                ),
                'aplicacao' => $permissao,
                'msg' => $dados['msg']
            );

            Log::gravar($log, $_SESSION ['id']);

            $dados['lista_projeto_problemas'] = $this->model->lista_projeto_problemas();

            echo json_encode($dados);
        }
    }

}
