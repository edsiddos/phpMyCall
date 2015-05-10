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
use application\models\Solicitacao as ModelSolicitacao;
use libs\Menu;
use libs\Log;
use libs\Cache;
use DateTime;

class Solicitacao extends Controller {

    /**
     * Objeto de conexão com banco de dados
     * que manipula dados das solicitações
     *
     * @var Solicitacao
     */
    private $model;

    /**
     * Construtor
     */
    public function __construct() {
        if (!Login::verificaLogin()) {
            $this->redir("Login/index");
        }

        $this->model = new ModelSolicitacao ();
    }

    /**
     * Tela de abertura de chamados
     */
    public function abrir() {
        $permissao = "Solicitacao/abrir";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $title = array(
                'title' => 'Abrir Solicitação'
            );

            $var = array(
                'link' => HTTP . '/Solicitacao/novaSolicitacao',
                'projetos' => $this->model->getProjetos($_SESSION['id']),
                'prioridade' => $this->model->getPrioridades()
            );

            $this->loadView('default/header', $title);
            $this->loadView('solicitacao/index', $var);
            $this->loadView('default/footer');
        }
    }

    /**
     * Retorna todos os participantes de um determinado projeto
     */
    public function getSolicitantes() {
        $permissao = "Solicitacao/abrir";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $projeto = $_POST['projeto'];
            echo json_encode($this->model->getSolicitantes($projeto));
        }
    }

    /**
     * Cria uma nova solicitação
     */
    public function novaSolicitacao() {
        $permissao = "Solicitacao/abrir";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $hoje = new DateTime();
            $hoje = $hoje->format('Y-m-d H:i:s');

            /*
             * Manipulação dos dados a ser inseridos.
             */
            $dados = array(
                'projeto_problema' => $_POST['selectProjeto'],
                'descricao' => $_POST['textareaDescricao'],
                'solicitante' => $_POST['selectSolicitante'],
                'prioridade' => $_POST['selectPrioridade'],
                'atendente' => $_SESSION['id'],
                'tecnico' => empty($_POST['selectTecnico']) ? NULL : $_POST['selectTecnico'],
                'abertura' => $hoje,
                'atendimento' => $hoje,
                'encerramento' => $hoje,
                'solicitacao_origem' => empty($_POST['solicitacaoOrigem'] ? NULL : $_POST['solicitacaoOrigem']),
                'avaliacao' => NULL,
                'justificativa_avaliacao' => NULL
            );

            $solicitacao = $this->model->gravaSolicitacao($dados);

            /*
             * Dados manipulados para geração de log
             */
            $dados = array_merge($dados, $solicitacao);

            if (!empty($solicitacao['id'])) {

                $_SESSION['msg_sucesso'] = 'Solicitação criada com sucesso.<br/>';

                /*
                 * Se foi enviados arquivos armazena arquivos no banco de dados.
                 */
                if (array_search(0, $_FILES['inputArquivos']['error']) !== FALSE) {
                    foreach ($_FILES['inputArquivos']['tmp_name'] as $key => $values) {
                        $dados_arquivos[$key] = array(
                            'nome' => $_FILES['inputArquivos']['name'][$key],
                            'tipo' => $_FILES['inputArquivos']['type'][$key],
                            'solicitacao' => $solicitacao['id']
                        );

                        $arquivos[$key] = array(
                            'conteudo' => $values
                        );

                        if (!$this->model->gravaArquivoSolicitacao($dados_arquivos[$key], $arquivos[$key])) {
                            $_SESSION['msg_sucesso'] .= 'Erro ao adicionar o arquivo: ' . $_FILES['inputArquivos']['name'][$key] . '<br/>';
                        }
                    }
                }
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao criar Solicitação';
            }

            /*
             * Grava log da solicitação
             */
            $log = array(
                'dados' => $dados,
                'aplicacao' => $permissao,
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);
            $this->redir('Solicitacao/abrir');
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Gera tela de solicitações e aberto
     */
    public function aberta() {
        $permissao = "Solicitacao/aberta";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $title = array(
                'title' => 'Solicitações em aberta'
            );

            $this->listaSolicitacoes(1, $title);
        }
    }

    /**
     * Gera tela de solicitações e andamento
     */
    public function andamento() {
        $permissao = "Solicitacao/andamento";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $title = array(
                'title' => 'Solicitações em andamento'
            );

            $this->listaSolicitacoes(2, $title);
        }
    }

    /**
     * Solicitações finalizadas
     */
    public function finalizadas() {
        $permissao = "Solicitacao/finalizadas";
        $perfil = $_SESSION['perfil'];

        if (Menu::possuePermissao($perfil, $permissao)) {
            $title = array(
                'title' => 'Solicitações encerradas'
            );

            $this->listaSolicitacoes(3, $title);
        }
    }

    /**
     * Gera dados e tela para mostrar lista de solicitações
     * @param int $status Status da solicitação 1 - <b>aberta</b>, 2 - <b>atendimento</b>, 3 - <b>encerrada</b>.
     * @param Array $title Array com title da página.
     */
    private function listaSolicitacoes($status, $title) {
        $perfil = $_SESSION['perfil'];
        $var['solicitacoes'] = $this->model->getSolicitacoes($_SESSION['id'], $perfil, $status);

        $parametros = Cache::getCache(PARAMETROS);
        $var['prioridades'] = $parametros['CORES_SOLICITACOES'];

        $this->loadView('default/header', $title);
        $this->loadView('solicitacao/lista', $var);
        $this->loadView('default/footer');
    }

    /**
     * Visualiza dados da solicitação.
     * @param Array $id_solicitacao <b>Array</b> com o código da solicitação.
     */
    public function visualizar($id_solicitacao) {
        $perfil = $_SESSION['perfil'];

        $title = array(
            'title' => 'Solicitações em aberta'
        );

        /*
         * Paramentros necessarios para exibição das opções.
         */
        $parametros = Cache::getCache(PARAMETROS);

        /*
         * Busca dados da solicitação.
         */
        $solicitacao = $this->model->getDadosSolicitacao($id_solicitacao[0], $perfil, $_SESSION['id']);
        $vars['solicitacao'] = $solicitacao;
        $vars['id_solicitacao'] = $id_solicitacao[0];

        $vars['editar'] = (array_search($perfil, $parametros['EDITAR_SOLICITACAO']) !== FALSE) && empty($solicitacao['atendimento']);
        $vars['atender'] = (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) && empty($solicitacao['atendimento']);
        $vars['sub_chamado'] = (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) && (empty($solicitacao['atendimento']) || empty($solicitacao['encerramento']));
        $vars['excluir'] = (array_search($perfil, $parametros['EXCLUIR_SOLICITACAO']) !== FALSE) && (empty($solicitacao['atendimento']) || empty($solicitacao['encerramento']));
        $vars['redirecionar'] = (array_search($perfil, $parametros['REDIRECIONAR_CHAMADO']) !== FALSE) && (!empty($solicitacao['atendimento'])) && empty($solicitacao['encerramento']);
        $vars['feedback'] = (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) && (!empty($solicitacao['atendimento'])) && empty($solicitacao['encerramento']);
        $vars['encerrar'] = (array_search($perfil, $parametros['ENCERRAR_SOLICITACAO']) !== FALSE) && (!empty($solicitacao['atendimento'])) && empty($solicitacao['encerramento']);

        $this->loadView('default/header', $title);
        $this->loadView('solicitacao/visualizar', $vars);
        $this->loadView('default/footer');
    }

    /**
     * Abre tela para edição de uma solicitação
     * @param Array $id_solicitacao Array com id da solicitação
     */
    public function editar($id_solicitacao) {
        $perfil = $_SESSION['perfil'];
        $parametros = Cache::getCache(PARAMETROS);

        $status = $this->model->statusSolicitacao($id_solicitacao[0]);

        /*
         * Verifica status da solicitação
         */
        if ($status !== "aberta") {

            /* Caso esteja encerrada ou em atendimento exibe mensagem de operação ilegal */
            $_SESSION['msg_erro'] = "Operação ilegal. Esta solicitação está ";
            $_SESSION['msg_erro'] .= $status === 'encerrada' ? $status : "em {$status}.";

            $this->redir("Solicitacao/visualizar/{$id_solicitacao[0]}");
        } else if (array_search($perfil, $parametros['EDITAR_SOLICITACAO']) !== FALSE) {

            /* Caso solicitação esteja aberta busca dados para alteração */
            $title = array(
                'title' => 'Editar Solicitação'
            );

            $dados_solicitacao = $this->model->getSolicitacao($id_solicitacao[0], $_SESSION['id'], $perfil);

            /*
             * Dados necessarios para alteração
             */
            $var = array(
                'link' => HTTP . '/Solicitacao/atualizarSolicitacao',
                'projetos' => $this->model->getProjetos($_SESSION['id']),
                'prioridade' => $this->model->getPrioridades(),
                'solicitacao' => json_encode($dados_solicitacao),
                'participantes' => json_encode($this->model->getSolicitantes($dados_solicitacao['projeto']))
            );

            $this->loadView('default/header', $title);
            $this->loadView('solicitacao/index', $var);
            $this->loadView('solicitacao/editar', $var);
            $this->loadView('default/footer');
        }
    }

    /**
     * Remove um arquivo anexo a solicitação
     */
    public function removerArquivo() {
        $perfil = $_SESSION['perfil'];
        $parametros = Cache::getCache(PARAMETROS);

        /* Verifica se perfil do usuário tem a permissão de editar solicitação */
        if (array_search($perfil, $parametros['EDITAR_SOLICITACAO']) !== FALSE) {
            $arquivo = $_POST['id'];
            $projeto_tipo_problema = $_POST['projeto_tipo_problema'];

            /* Verifica se arquivo foi removido */
            if ($this->model->removerArquivo($arquivo, $projeto_tipo_problema, $_SESSION['id'])) {
                echo json_encode(array('id' => $arquivo, 'status' => TRUE));
            } else {
                echo json_encode(array('id' => $arquivo, 'status' => FALSE));
            }
        }
    }

    /**
     * Realiza a atualização de um solicitação
     * <b>método chamado ao submeter formulario edição de solicitação</b>
     */
    public function atualizarSolicitacao() {
        $perfil = $_SESSION['perfil'];
        $parametros = Cache::getCache(PARAMETROS);

        if (array_search($perfil, $parametros['EDITAR_SOLICITACAO']) !== FALSE) {
            /*
             * Manipulação dos dados a ser inseridos.
             */
            $dados = array(
                'projeto_problema' => $_POST['selectProjeto'],
                'descricao' => $_POST['textareaDescricao'],
                'solicitante' => $_POST['selectSolicitante'],
                'prioridade' => $_POST['selectPrioridade'],
                'atendente' => $_SESSION['id'],
                'tecnico' => empty($_POST['selectTecnico']) ? NULL : $_POST['selectTecnico'],
                'solicitacao_origem' => empty($_POST['solicitacaoOrigem'] ? NULL : $_POST['solicitacaoOrigem']),
                'avaliacao' => NULL,
                'justificativa_avaliacao' => NULL
            );

            $solicitacao = $_POST['inputID'];

            if ($this->model->atualizaSolicitacao($dados, $solicitacao)) {

                /*
                 * Dados manipulados para geração de log
                 */
                $dados['id'] = $solicitacao;

                $_SESSION['msg_sucesso'] = 'Solicitação alterada com sucesso.<br/>';

                /*
                 * Se foi enviados arquivos armazena arquivos no banco de dados.
                 */
                if (array_search(0, $_FILES['inputArquivos']['error']) !== FALSE) {
                    foreach ($_FILES['inputArquivos']['tmp_name'] as $key => $values) {
                        $dados_arquivos[$key] = array(
                            'nome' => $_FILES['inputArquivos']['name'][$key],
                            'tipo' => $_FILES['inputArquivos']['type'][$key],
                            'solicitacao' => $solicitacao
                        );

                        $arquivos[$key] = array(
                            'conteudo' => $values
                        );

                        if (!$this->model->gravaArquivoSolicitacao($dados_arquivos[$key], $arquivos[$key])) {
                            $_SESSION['msg_sucesso'] .= 'Erro ao adicionar o arquivo: ' . $_FILES['inputArquivos']['name'][$key] . '<br/>';
                        }
                    }
                }
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao alterar Solicitação';
            }

            /*
             * Grava log da solicitação
             */
            $log = array(
                'dados' => $dados,
                'aplicacao' => "Solicitacao/editar",
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Log::gravar($log, $_SESSION ['id']);
            $this->redir("Solicitacao/visualizar/{$solicitacao}");
        } else {
            $this->redir('Main/index');
        }
    }

    /**
     * Atribui um atendente a uma solicitação e inicia atendimento
     * @param Array $solicitacao Array contendo o <b>ID</b> da solicitação.
     */
    public function atender($solicitacao) {
        $perfil = $_SESSION['perfil'];
        $parametros = Cache::getCache(PARAMETROS);

        /* Verifica se usuário tem permissão de atender um solicitação */
        if (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) {
            $hoje = new DateTime();
            $hoje = $hoje->format('Y-m-d H:i:s');

            /*
             * Realiza atendimento de uma solicitação e retorna
             * informações de erro ou de sucesso.
             */
            $result = $this->model->atenderSolicitacao($hoje, $solicitacao[0], $_SESSION['id']);

            $dados = array(
                'id' => $solicitacao[0],
                'atendimento' => $hoje,
                'encerramento' => $hoje,
                'tecnico' => $_SESSION['id']
            );

            if ($result['status']) {
                $_SESSION['msg_sucesso'] = $result['msg'];
            } else {
                $_SESSION['msg_erro'] = $result['msg'];
            }

            /*
             * Gera dados para gravação de log.
             */
            $log = array(
                'dados' => $dados,
                'aplicacao' => "Solicitacao/editar",
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            /*
             * Grava dados da operação realizada
             */
            Log::gravar($log, $_SESSION ['id']);
            $this->redir("Solicitacao/visualizar/{$solicitacao[0]}");
        } else {
            $_SESSION['msg_erro'] = "Perfil não possui permissão para atender um solicitação.";
            $this->redir("Solicitacao/visualizar/{$solicitacao[0]}");
        }
    }

}
