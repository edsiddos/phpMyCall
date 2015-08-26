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

use \application\models\ProjetosProblemas as ModelProjetosProblemas;
use \libs\Menu;
use \libs\Log;
use \libs\Utils;

/**
 * Manipulas os projetos e tipos de problemas
 *
 * @author Ednei Leite da Silva
 */
class ProjetosProblemas extends \system\Controller {

    /**
     * Objeto de conexão com banco de dados
     * que manipula dados de projetos e problemas
     *
     * @var ProjetosProblemas
     */
    private $model;

    /**
     * Construtor
     */
    public function __construct() {
        parent::__construct();
        if (!Login::verificaLogin()) {
            $this->redir("Login/index");
        }

        $this->model = new ModelProjetosProblemas ();
    }

    /**
     * Gera tela para manutenção de projetos e problemas
     */
    public function index() {
        $permissao = "ProjetosProblemas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {

            $pagina = array(
                'title' => 'Projetos tipo de problema.',
                'listaProjeto' => $this->model->listaProjetoProblemas()
            );

            $this->loadView(array('projetos_problemas/index', 'projetos_problemas/form'), $pagina);
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Busca os tipos de projetos
     */
    public function getProjetos() {
        $permissao = "ProjetosProblemas/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $nome = filter_input(INPUT_POST, 'term', FILTER_SANITIZE_STRING);

            echo json_encode($this->model->getProjetos($nome));
        }
    }

    /**
     * Busca os tipos de problemas existentes
     */
    public function getProblemas() {
        $permissao = "ProjetosProblemas/index";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $nome = filter_input(INPUT_POST, 'term', FILTER_SANITIZE_STRING);

            echo json_encode($this->model->getProblemas($nome));
        }
    }

    /**
     * Busca ID de um projeto
     */
    public function getDadosProjeto() {
        $permissao = "ProjetosProblemas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
            $id = $this->model->getIdProjeto($nome);

            $usuarios = $this->model->relacaoUsuarios($perfil);
            $participantes = $this->model->getRelacaoParticipantes($id);
            $vars = $this->model->getDescricaoProjeto($id);

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
        $permissao = 'ProjetosProblemas/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $utils = new Utils();
            $valida_hora = array('options' => array($utils, 'validaFormatoHora'));

            $participantes = filter_input(INPUT_POST, 'participantes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $projeto = filter_input(INPUT_POST, 'inputNomeProjeto', FILTER_SANITIZE_STRING);
            $problema = filter_input(INPUT_POST, 'inputNomeProblema', FILTER_SANITIZE_STRING);
            $resposta = filter_input(INPUT_POST, 'inputResposta', FILTER_CALLBACK, $valida_hora);
            $solucao = filter_input(INPUT_POST, 'inputSolucao', FILTER_CALLBACK, $valida_hora);
            $descricao_projeto = filter_input(INPUT_POST, 'textProjeto', FILTER_SANITIZE_STRING);
            $descricao = filter_input(INPUT_POST, 'textDescricao', FILTER_SANITIZE_STRING);

            if (!$this->model->existeProjetoProblema($projeto, $problema)) {
                $idProjeto = $this->model->getIdProjeto($projeto);
                $id = $idProjeto;

                if (empty($idProjeto)) {
                    $idProjeto = $this->model->insertProjeto($projeto, $descricao_projeto);

                    if (empty($idProjeto)) {
                        $dados = array('status' => false, 'msg' => "Erro ao criar projeto");
                    } else if (!empty($participantes)) {
                        $this->model->adicionaPartcipantesProjeto($participantes, $idProjeto);
                    }
                }

                if (!empty($idProjeto)) {
                    $idProblema = $this->model->getIdProblema($problema);

                    if (empty($idProblema)) {
                        $idProblema = $this->model->insertTipoProblema($problema);

                        if (empty($idProblema)) {
                            $dados = array('status' => false, 'msg' => "Erro ao criar tipo de problema");
                        }
                    }

                    if (!empty($idProblema)) {
                        $this->model->criaProjetoProblemas($idProjeto, $idProblema, $resposta, $solucao, $descricao);
                        $dados = array('status' => true, 'msg' => 'Projeto criado com sucesso');
                    }
                }

                $dados_log = array(
                    'dados' => array(
                        'operacao' => empty($id) ? 'Criação projeto e problema' : 'Adição de tipo de problema',
                        'id_projeto' => $idProjeto,
                        'nome_projeto' => $projeto,
                        'descricao_projeto' => $descricao_projeto,
                        'id_tipo_problema' => $idProblema,
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

            $dados['listaProjetoProblemas'] = $this->model->listaProjetoProblemas();

            echo json_encode($dados);
        }
    }

    /**
     * Busca informações sobre o projeto.
     */
    public function getDadosProjetosProblemas() {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $permissao = "ProjetosProblemas/index";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            echo json_encode($this->model->getDadosProjetoProblema($id));
        }
    }

    /**
     * Realiza a operação de atualização do projeto (alterar)
     */
    public function alterar() {
        $permissao = 'ProjetosProblemas/index';
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $utils = new Utils();
            $valida_hora = array('options' => array($utils, 'validaFormatoHora'));

            $id_projeto = filter_input(INPUT_POST, 'inputProjeto', FILTER_SANITIZE_NUMBER_INT);
            $id_problema = filter_input(INPUT_POST, 'inputProblema', FILTER_SANITIZE_NUMBER_INT);
            $id_projeto_problema = filter_input(INPUT_POST, 'inputProjetoProblema', FILTER_SANITIZE_NUMBER_INT);
            $participantes = filter_input(INPUT_POST, 'participantes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            $projeto = filter_input(INPUT_POST, 'inputNomeProjeto', FILTER_SANITIZE_STRING);
            $problema = filter_input(INPUT_POST, 'inputNomeProblema', FILTER_SANITIZE_STRING);
            $resposta = filter_input(INPUT_POST, 'inputResposta', FILTER_CALLBACK, $valida_hora);
            $solucao = filter_input(INPUT_POST, 'inputSolucao', FILTER_CALLBACK, $valida_hora);
            $descricao_projeto = filter_input(INPUT_POST, 'textProjeto', FILTER_SANITIZE_STRING);
            $descricao = filter_input(INPUT_POST, 'textDescricao', FILTER_SANITIZE_STRING);

            $update_projeto = array(
                'nome' => $projeto,
                'descricao' => $descricao_projeto
            );

            if ($this->model->alteraProjeto($update_projeto, $id_projeto)) {
                $participantes_old = $this->model->getRelacaoParticipantes($id_projeto);
                $insert = empty($participantes) ? array() : $participantes;
                $delete = array();

                foreach ($participantes_old as $value) {
                    if (!in_array($value, $insert)) {
                        $this->model->deleteParticipantesProjeto($value, $id_projeto);
                        $delete [] = $value;
                    }

                    $key = array_search($value, $insert);
                    if ($key !== false) {
                        unset($insert [$key]);
                    }
                }

                $this->model->adicionaPartcipantesProjeto($insert, $id_projeto);

                $delete = implode(',', $delete);
                $insert = implode(',', $insert);

                $id_problema = $this->model->getIdProblema($problema);

                if (empty($id_problema)) {
                    $id_problema = $this->model->insertTipoProblema($problema);

                    if (empty($id_problema)) {
                        $dados = array('status' => false, 'msg' => "Erro ao criar tipo de problema");
                    }
                }

                /*
                 * Verifica se foi inserido ou existe tipo do problema
                 */
                if (!empty($id_problema)) {
                    $this->model->atualizaProjetoProblemas($id_projeto_problema, $id_projeto, $id_problema, $resposta, $solucao, $descricao);
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

                $dados['listaProjetoProblemas'] = $this->model->listaProjetoProblemas();

                echo json_encode($dados);
            }
        }
    }

    /**
     * Realiza a exclusão do projeto tipo de problema selecionado
     */
    public function excluir() {
        $permissao = "ProjetosProblemas/index";

        if (Menu::possuePermissao($_SESSION ['perfil'], $permissao)) {
            $id_projeto = filter_input(INPUT_POST, 'projeto', FILTER_SANITIZE_NUMBER_INT);
            $id_projeto_problema = filter_input(INPUT_POST, 'projetoProblema', FILTER_SANITIZE_NUMBER_INT);

            if ($this->model->excluirProjetoProblemas($id_projeto, $id_projeto_problema)) {
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

            $dados['listaProjetoProblemas'] = $this->model->listaProjetoProblemas();

            echo json_encode($dados);
        }
    }

}
