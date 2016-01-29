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

class Solicitacao extends CI_Controller {

    private $manipula_inteiro = array(
        'options' => array('Utils', 'valida_inteiro_chave')
    );

    /**
     * Construtor
     */
    public function __construct() {
        parent::__construct();

        if (Autenticacao::verifica_login()) {
            $this->load->model('solicitacao_model', 'model');
            $this->load->library('parametros_solicitacoes');
        } else {
            redirect("login/index");
        }
    }

    /**
     * Tela de abertura de chamados
     * @param int $cod_solicitacao Código da solicitação para abrir sub chamado
     */
    public function abrir($cod_solicitacao = NULL) {
        $permissao = "solicitacao/abrir";
        $perfil = $_SESSION ['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $title = array(
                'title' => 'Abrir Solicitação',
            );

            $var = array(
                'link' => base_url() . '/solicitacao/nova_solicitacao',
                'projetos' => $this->model->get_projetos($_SESSION['id']),
                'prioridade' => $this->model->get_prioridades(),
                'solicitacao_origem' => (empty($cod_solicitacao) ? 0 : $cod_solicitacao)
            );

            $this->load->view('template/header', $title);
            $this->load->view('solicitacao/index', $var);
            $this->load->view('template/footer');
        }
    }

    /**
     * Abre um sub chamado
     * @param int $cod_solicitacao Codigo da solicitaçao
     */
    public function sub_chamado($cod_solicitacao) {
        $perfil = $_SESSION['perfil'];
        $solicitacao = $this->model->get_dados_solicitacao($cod_solicitacao, $perfil, $_SESSION['id']);
        $parametros = Parametros_solicitacoes::get_parametros();

        $sub_chamado = (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE);
        $sub_chamado &= (empty($solicitacao['atendimento']) || ((!empty($solicitacao['atendimento'])) && ($solicitacao['id_tecnico'] == $_SESSION['id']) && empty($solicitacao['encerramento'])));
        $sub_chamado &= $this->model->usuario_participante($_SESSION['id'], $cod_solicitacao);

        if (empty($cod_solicitacao) || (!$sub_chamado)) {
            redirect('main/index');
        } else {
            $this->abrir($cod_solicitacao);
        }
    }

    /**
     * Retorna todos os participantes de um determinado projeto
     */
    public function get_solicitantes() {
        $permissao = "solicitacao/abrir";
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $projeto = filter_input(INPUT_POST, 'projeto', FILTER_SANITIZE_NUMBER_INT);
            echo json_encode($this->model->get_solicitantes($projeto));
        }
    }

    /**
     * Cria uma nova solicitação
     */
    public function nova_solicitacao() {
        $permissao = "solicitacao/abrir";
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $hoje = new DateTime();
            $hoje = $hoje->format('Y-m-d H:i:s');

            /*
             * Manipulação dos dados a ser inseridos.
             */
            $dados = array(
                'projeto_problema' => filter_input(INPUT_POST, 'select_projeto', FILTER_CALLBACK, $this->manipula_inteiro),
                'descricao' => filter_input(INPUT_POST, 'textarea_descricao', FILTER_DEFAULT, FILTER_FLAG_EMPTY_STRING_NULL),
                'solicitante' => filter_input(INPUT_POST, 'select_solicitante', FILTER_CALLBACK, $this->manipula_inteiro),
                'prioridade' => filter_input(INPUT_POST, 'select_prioridade', FILTER_CALLBACK, $this->manipula_inteiro),
                'atendente' => $_SESSION['id'],
                'tecnico' => filter_input(INPUT_POST, 'select_tecnico', FILTER_CALLBACK, $this->manipula_inteiro),
                'abertura' => $hoje,
                'atendimento' => $hoje,
                'encerramento' => $hoje,
                'solicitacao_origem' => filter_input(INPUT_POST, 'solicitacao_origem', FILTER_CALLBACK, $this->manipula_inteiro),
                'avaliacao' => NULL,
                'justificativa_avaliacao' => NULL
            );

            $solicitacao = $this->model->grava_solicitacao($dados);

            /*
             * Dados manipulados para geração de log
             */
            $dados = array_merge($dados, $solicitacao);

            if (!empty($solicitacao)) {

                $_SESSION['msg_sucesso'] = 'Solicitação criada com sucesso.<br/>';

                /*
                 * Se foi enviados arquivos armazena arquivos no banco de dados.
                 */
                if (array_search(0, $_FILES['input_arquivos']['error']) !== FALSE) {
                    $this->grava_arquivos_anexos($_FILES, $solicitacao);
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

            Logs::gravar($log, $_SESSION ['id']);
            redirect('solicitacao/lista');
        } else {
            redirect('main/index');
        }
    }

    private function grava_arquivos_anexos($files, $solicitacao) {
        foreach ($files['input_arquivos']['tmp_name'] as $key => $values) {
            $caminho_arquivo = FILES . '/' . md5($files['input_arquivos']['name'][$key]);

            $dados_arquivos[$key] = array(
                'nome' => $files['input_arquivos']['name'][$key],
                'tipo' => $files['input_arquivos']['type'][$key],
                'solicitacao' => $solicitacao,
                'caminho' => $caminho_arquivo
            );

            if (move_uploaded_file($values, $caminho_arquivo)) {
                if (!$this->model->grava_arquivo_solicitacao($dados_arquivos[$key])) {
                    $_SESSION['msg_sucesso'] .= 'Erro ao adicionar o arquivo: ' . $files['input_arquivos']['name'][$key] . '<br/>';
                }
            } else {
                $_SESSION['msg_sucesso'] .= 'Erro ao adicionar o arquivo: ' . $files['input_arquivos']['name'][$key] . '<br/>';
            }
        }
    }

    /**
     * Gera tela de solicitações e aberto
     */
    public function lista() {
        $permissao = "solicitacao/lista";
        $perfil = $_SESSION['perfil'];

        if (Menu::possue_permissao($perfil, $permissao)) {
            $title = array(
                'title' => 'Lista de solicitações'
            );

            $var['prioridades'] = $this->model->get_prioridades();

            $this->load->view('template/header', $title);
            $this->load->view('solicitacao/lista', $var);
            $this->load->view('template/footer');
        }
    }

    /**
     * Gera dados e tela para mostrar lista de solicitações
     * @param int $status Status da solicitação 1 - <b>aberta</b>, 2 - <b>atendimento</b>, 3 - <b>encerrada</b>.
     * @param Array $title Array com title da página.
     */
    public function lista_solicitacoes() {
        $perfil = $_SESSION['perfil'];
        $usuario = $_SESSION['id'];

        $situacao = filter_input(INPUT_POST, 'situacao', FILTER_CALLBACK, $this->manipula_inteiro);
        $prioridade = filter_input(INPUT_POST, 'prioridade', FILTER_CALLBACK, $this->manipula_inteiro);

        $draw = filter_input(INPUT_POST, 'draw', FILTER_SANITIZE_NUMBER_INT);
        $limit = filter_input(INPUT_POST, 'length', FILTER_SANITIZE_NUMBER_INT);
        $offset = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_NUMBER_INT);
        $order = filter_input(INPUT_POST, 'order', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $search = filter_input(INPUT_POST, 'search', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $columns = array(
            0 => 'solicitacao.abertura',
            1 => 'projeto.nome',
            2 => 'tipo_problema.nome',
            3 => 'prioridade.nome',
            4 => 'solicitante.nome',
            5 => 'atendente.nome',
            6 => 'num_arquivos'
        );

        $order_by = "{$columns[$order[0]['column']]} {$order[0]['dir']}";

        $result = $this->model->get_solicitacoes($usuario, $perfil, $situacao, $prioridade, $search['value'], $order_by, $limit, $offset);

        $result["draw"] = empty($draw) ? 0 : $draw;

        echo json_encode($result);
    }

    /**
     * Visualiza dados da solicitação.
     * @param int $id_solicitacao Código da solicitação.
     */
    public function visualizar($id_solicitacao) {
        $perfil = $_SESSION['perfil'];
        $usuario = $_SESSION['id'];

        $title['title'] = 'Solicitações em aberta';

        /*
         * Paramentros necessarios para exibição das opções.
         */
        $parametros = Parametros_solicitacoes::get_parametros();

        /*
         * Busca dados da solicitação.
         */
        $solicitacao = $this->model->get_dados_solicitacao($id_solicitacao, $perfil, $usuario);
        $vars['solicitacao'] = $solicitacao;
        $vars['id_solicitacao'] = $id_solicitacao;

        /*
         * Habilita as opções de editar, atender, sub-chamado, excluir, redimencionar, feedback e encerrar
         * ==> Editar e Atender se estiver em aberto.
         * ==> Sub-Chamado disponível a todos participantes se em aberto, quando em atendimento
         *      somente o técnico responsavel pela solicitação.
         * ==> Excluir quando tiver em aberto ou em atendimento.
         * ==> Redirecionar, Feedback e Encerrar quando estiver em atendimento.
         */
        $vars['editar'] = (array_search($perfil, $parametros['EDITAR_SOLICITACAO']) !== FALSE) && empty($solicitacao['atendimento']);
        $vars['atender'] = (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) && empty($solicitacao['atendimento']);
        $vars['sub_chamado'] = (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE);
        $vars['sub_chamado'] &= (empty($solicitacao['atendimento']) || ((!empty($solicitacao['atendimento'])) && ($solicitacao['id_tecnico'] == $_SESSION['id']) && empty($solicitacao['encerramento'])));
        $vars['excluir'] = (array_search($perfil, $parametros['EXCLUIR_SOLICITACAO']) !== FALSE) && (empty($solicitacao['atendimento']) || empty($solicitacao['encerramento']));
        $vars['redirecionar'] = (array_search($perfil, $parametros['REDIRECIONAR_CHAMADO']) !== FALSE) && empty($solicitacao['encerramento']);
        $vars['feedback'] = (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) && (!empty($solicitacao['atendimento'])) && empty($solicitacao['encerramento']);
        $vars['encerrar'] = (array_search($perfil, $parametros['ENCERRAR_SOLICITACAO']) !== FALSE) && ($solicitacao['id_tecnico'] == $usuario) && (!empty($solicitacao['atendimento'])) && empty($solicitacao['encerramento']);

        $vars['feedback_solicitado'] = $this->model->feedback_pendentes_atendidos($id_solicitacao);

        if ($vars['redirecionar'] || $vars['feedback']) {
            $vars['tecnicos'] = $this->model->get_solicitantes_solicitacao($id_solicitacao);
        }

        if ((array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) && (!empty($solicitacao['atendimento']))) {
            $vars['tipos_feedback'] = $this->model->get_tipo_feedback();
        }

        $this->load->view('template/header', $title);
        $this->load->view('solicitacao/visualizar', $vars);
        $this->load->view('template/footer');
    }

    /**
     * Método que realiza download dos arquivos anexos a uma solicitação
     * @param int $arquivo Código do anexo.
     */
    public function download_arquivo($arquivo) {
        $dados_arquivos = $this->model->get_content_arquivo($arquivo, $_SESSION['id']);

        header('Cache-control: private');
        header('Content-Type: ' . $dados_arquivos['tipo']);
        header('Content-Length: ' . filesize($dados_arquivos['caminho']));
        header('Content-Disposition: filename=' . $dados_arquivos['nome']);

        flush();
        $file = fopen($dados_arquivos['caminho'], "r");
        $download_rate = 20.5;
        while (!feof($file)) {

            print fread($file, round($download_rate * 1024));

            flush();
        }
        fclose($file);
    }

    /**
     * Abre tela para edição de uma solicitação
     * @param int $id_solicitacao Código da solicitação
     */
    public function editar($id_solicitacao) {
        $perfil = $_SESSION['perfil'];
        $parametros = Parametros_solicitacoes::get_parametros();

        $status = $this->model->status_solicitacao($id_solicitacao);

        /*
         * Verifica status da solicitação
         */
        if ($status !== "aberta") {

            /* Caso esteja encerrada ou em atendimento exibe mensagem de operação ilegal */
            $_SESSION['msg_erro'] = "Operação ilegal. Esta solicitação está ";
            $_SESSION['msg_erro'] .= $status === 'encerrada' ? $status : "em {$status}.";

            redirect("solicitacao/visualizar/{$id_solicitacao}");
        } else if (array_search($perfil, $parametros['EDITAR_SOLICITACAO']) !== FALSE) {

            /* Caso solicitação esteja aberta busca dados para alteração */
            $title = array(
                'title' => 'Editar Solicitação'
            );

            $dados_solicitacao = $this->model->get_solicitacao($id_solicitacao, $_SESSION['id'], $perfil);

            /*
             * Dados necessarios para alteração
             */
            $var = array(
                'link' => base_url() . '/solicitacao/atualizar_solicitacao',
                'projetos' => $this->model->get_projetos($_SESSION['id']),
                'prioridade' => $this->model->get_prioridades(),
                'solicitacao' => json_encode($dados_solicitacao),
                'participantes' => json_encode($this->model->get_solicitantes($dados_solicitacao['projeto_problema'])),
                'solicitacao_origem' => 0
            );

            $this->load->view('template/header', $title);
            $this->load->view('solicitacao/index', $var);
            $this->load->view('solicitacao/editar', $var);
            $this->load->view('template/footer');
        }
    }

    /**
     * Remove um arquivo anexo a solicitação
     */
    public function remover_arquivo() {
        $perfil = $_SESSION['perfil'];
        $parametros = Parametros_solicitacoes::get_parametros();

        /* Verifica se perfil do usuário tem a permissão de editar solicitação */
        if (array_search($perfil, $parametros['EDITAR_SOLICITACAO']) !== FALSE) {
            $arquivo = filter_input(INPUT_POST, 'id', FILTER_CALLBACK, $this->manipula_inteiro);
            $projeto_tipo_problema = filter_input(INPUT_POST, 'projeto_tipo_problema', FILTER_CALLBACK, $this->manipula_inteiro);

            /* Verifica se arquivo foi removido */
            if ($this->model->remover_arquivo($arquivo, $projeto_tipo_problema, $_SESSION['id'])) {
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
    public function atualizar_solicitacao() {
        $perfil = $_SESSION['perfil'];
        $parametros = Parametros_solicitacoes::get_parametros();

        if (array_search($perfil, $parametros['EDITAR_SOLICITACAO']) !== FALSE) {
            /*
             * Manipulação dos dados a ser inseridos.
             */
            $dados = array(
                'projeto_problema' => filter_input(INPUT_POST, 'select_projeto', FILTER_CALLBACK, $this->manipula_inteiro),
                'descricao' => filter_input(INPUT_POST, 'textarea_descricao', FILTER_DEFAULT, FILTER_FLAG_EMPTY_STRING_NULL),
                'solicitante' => filter_input(INPUT_POST, 'select_solicitante', FILTER_CALLBACK, $this->manipula_inteiro),
                'prioridade' => filter_input(INPUT_POST, 'select_prioridade', FILTER_CALLBACK, $this->manipula_inteiro),
                'atendente' => $_SESSION['id'],
                'tecnico' => filter_input(INPUT_POST, 'select_tecnico', FILTER_CALLBACK, $this->manipula_inteiro),
                'solicitacao_origem' => filter_input(INPUT_POST, 'solicitacao_origem', FILTER_CALLBACK, $this->manipula_inteiro),
                'avaliacao' => NULL,
                'justificativa_avaliacao' => NULL
            );

            $solicitacao = filter_input(INPUT_POST, 'input_id', FILTER_CALLBACK, $this->manipula_inteiro);

            if ($this->model->atualiza_solicitacao($dados, $solicitacao)) {

                /*
                 * Dados manipulados para geração de log
                 */
                $dados['id'] = $solicitacao;

                $_SESSION['msg_sucesso'] = 'Solicitação alterada com sucesso.<br/>';

                /*
                 * Se foi enviados arquivos armazena arquivos no banco de dados.
                 */
                if (array_search(0, $_FILES['input_arquivos']['error']) !== FALSE) {
                    $this->grava_arquivos_anexos($_FILES, $solicitacao);
                }
            } else {
                $_SESSION ['msg_erro'] = 'Erro ao alterar Solicitação';
            }

            /*
             * Grava log da solicitação
             */
            $log = array(
                'dados' => $dados,
                'aplicacao' => "solicitacao/editar",
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            Logs::gravar($log, $_SESSION ['id']);
            redirect("solicitacao/visualizar/{$solicitacao}");
        } else {
            redirect('main/index');
        }
    }

    /**
     * Atribui um atendente a uma solicitação e inicia atendimento
     * @param int $solicitacao <b>ID</b> da solicitação.
     */
    public function atender($solicitacao) {
        $perfil = $_SESSION['perfil'];
        $parametros = Parametros_solicitacoes::get_parametros();

        /* Verifica se usuário tem permissão de atender um solicitação */
        if (array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) {
            $hoje = new DateTime();
            $hoje = $hoje->format('Y-m-d H:i:s');

            /*
             * Realiza atendimento de uma solicitação e retorna
             * informações de erro ou de sucesso.
             */
            $result = $this->model->atender_solicitacao($hoje, $solicitacao, $_SESSION['id']);

            $dados = array(
                'id' => $solicitacao,
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
                'aplicacao' => "solicitacao/atender",
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            /*
             * Grava dados da operação realizada
             */
            Logs::gravar($log, $_SESSION ['id']);
            redirect("solicitacao/visualizar/{$solicitacao}");
        } else {
            $_SESSION['msg_erro'] = "Perfil não possui permissão para atender uma solicitação.";
            redirect("solicitacao/visualizar/{$solicitacao}");
        }
    }

    /**
     * Exclui solicitação com dados relacionados
     * @param int $dados Array com código da solicitação
     */
    public function excluir($dados = array()) {
        $perfil = $_SESSION['perfil'];
        $parametros = Parametros_solicitacoes::get_parametros();
        $solicitacao = $this->model->get_dados_solicitacao($dados[0], $perfil, $_SESSION['id']);

        /* Verifica se usuário tem permissão de excluir um solicitação */
        if ((array_search($perfil, $parametros['EXCLUIR_SOLICITACAO']) !== FALSE) && (empty($solicitacao['atendimento']) || empty($solicitacao['encerramento']))) {
            $result = $this->model->excluir_solicitacao($dados[0], $_SESSION['id']);

            $dados = array(
                'id' => $dados[0]
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
                'aplicacao' => "solicitacao/excluir",
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            /*
             * Grava dados da operação realizada
             */
            Logs::gravar($log, $_SESSION ['id']);
            redirect("solicitacao/lista");
        } else {
            $_SESSION['msg_erro'] = "Perfil não possui permissão para excluir uma solicitação.";
            redirect("solicitacao/visualizar/{$solicitacao[0]}");
        }
    }

    /**
     * Implementa opção de redirecionamento a outro técnico.
     * @param int $id_solicitacao Código da solicitação.
     * @param int $id_tecnico Codigo do tecnico.
     */
    public function redirecionar($id_solicitacao, $id_tecnico) {
        $perfil = $_SESSION['perfil'];
        $parametros = Parametros_solicitacoes::get_parametros();
        $solicitacao = $this->model->get_dados_solicitacao($id_solicitacao, $perfil, $_SESSION['id']);

        /*
         * Verifica se o perfil do usuário tem autorização para
         * realizar o redirecionamento da solicitação.
         */
        if ((array_search($perfil, $parametros['REDIRECIONAR_CHAMADO']) !== FALSE) && empty($solicitacao['encerramento'])) {
            $result = $this->model->redirecionar_solicitacao($_SESSION['id'], $id_solicitacao, $id_tecnico);

            /*
             * Prepara dados para gravação de log da operação realizada.
             */
            $dados = array(
                'id' => $id_solicitacao,
                'tecnico' => $id_tecnico
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
                'aplicacao' => "solicitacao/redirecionar",
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            /*
             * Grava dados da operação realizada
             */
            Logs::gravar($log, $_SESSION ['id']);
            redirect("solicitacao/visualizar/{$dados['id']}");
        } else {
            $_SESSION['msg_erro'] = "Perfil não possui permissão para redirecionar solicitação.";
            redirect("solicitacao/visualizar/{$dados[0]}");
        }
    }

    /**
     * Implementa método que grava solicitação de resposta sobre determinada solicitação.
     */
    public function feedback() {
        $id_solicitacao = filter_input(INPUT_POST, 'solicitacao', FILTER_CALLBACK, $this->manipula_inteiro);
        $feedback = filter_input(INPUT_POST, 'select_feedback', FILTER_CALLBACK, $this->manipula_inteiro);
        $destinatario = filter_input(INPUT_POST, 'select_destinatario', FILTER_CALLBACK, $this->manipula_inteiro);
        $pergunta = filter_input(INPUT_POST, 'pergunta_feedback', FILTER_DEFAULT, FILTER_FLAG_EMPTY_STRING_NULL);

        $perfil = $_SESSION['perfil'];
        $parametros = Parametros_solicitacoes::get_parametros();
        $solicitacao = $this->model->get_dados_solicitacao($id_solicitacao, $perfil, $_SESSION['id']);

        /*
         * Verifica se usuário tem permissão para realizar solicitação de feedback
         */
        if ((array_search($perfil, $parametros['ATENDER_SOLICITACAO']) !== FALSE) && (!empty($solicitacao['atendimento'])) && empty($solicitacao['encerramento'])) {
            if ($this->model->usuario_participante($_SESSION['id'], $id_solicitacao)) {
                $hoje = new DateTime();
                $hoje = $hoje->format('Y-m-d H:i:s');

                /*
                 * Dados necessários para gravação de um feedback
                 */
                $dados = array(
                    'tipo_feedback' => $feedback,
                    'pergunta' => $pergunta,
                    'resposta' => NULL,
                    'inicio' => $hoje,
                    'fim' => $hoje,
                    'solicitacao' => $id_solicitacao,
                    'responsavel' => $destinatario
                );

                if ($this->model->feedback($dados)) {
                    $_SESSION['msg_sucesso'] = "Feedback gravado com sucesso.";
                } else {
                    $_SESSION['msg_erro'] = "Falha ao adicionar feedback.";
                }

                /*
                 * Gera dados para gravação de log.
                 */
                $log = array(
                    'dados' => $dados,
                    'aplicacao' => "solicitacao/feedback",
                    'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
                );

                /*
                 * Grava dados da operação realizada
                 */
                Logs::gravar($log, $_SESSION ['id']);
                redirect("solicitacao/visualizar/{$id_solicitacao}");
            } else {
                $_SESSION['msg_erro'] = "Usuário não possui permissão neste projeto.";

                redirect("solicitacao/visualizar/{$id_solicitacao}");
            }
        } else {
            $_SESSION['msg_erro'] = "Perfil do usuário não possui permissão para solicitar feedback.";

            redirect("solicitacao/visualizar/{$id_solicitacao}");
        }
    }

    /**
     * Método que busca dados sobre o feedback a partir do <b>ID</b> do feedback
     */
    public function get_pergunta_resposta_feedback() {
        $id_feedback = filter_input(INPUT_POST, 'feedback_id', FILTER_CALLBACK, $this->manipula_inteiro);
        $usuario = $_SESSION['id'];

        echo json_encode($this->model->get_pergunta_resposta_feedback($id_feedback, $usuario));
    }

    /**
     * Grava resposta de um feedback.
     */
    public function responder_feedback() {
        $id_feedback = filter_input(INPUT_POST, 'feedback_id', FILTER_CALLBACK, $this->manipula_inteiro);
        $id_solicitacao = filter_input(INPUT_POST, 'solicitacao', FILTER_CALLBACK, $this->manipula_inteiro);

        /*
         * Verifica se o usuário e participante do projeto
         * OBS.: Não é necessario mais verificação pois pode ser solicitado
         * dados de qualquer participante do projeto.
         */
        if ($this->model->usuario_participante($_SESSION['id'], $id_solicitacao)) {
            $resposta = filter_input(INPUT_POST, 'resposta_feedback', FILTER_DEFAULT, FILTER_FLAG_EMPTY_STRING_NULL);
            $hoje = new DateTime();
            $hoje = $hoje->format('Y-m-d H:i:s');

            $dados = array(
                'resposta' => $resposta,
                'fim' => $hoje
            );

            if ($this->model->responder_feedback($dados, $id_feedback)) {
                $_SESSION['msg_sucesso'] = "Feedback respondido com sucesso.";
            } else {
                $_SESSION['msg_erro'] = "Falha ao responder feedback.";
            }

            /*
             * Gera dados para gravação de log.
             */
            $log = array(
                'dados' => $dados,
                'aplicacao' => "solicitacao/responderFeedback",
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            /*
             * Grava dados da operação realizada
             */
            Logs::gravar($log, $_SESSION ['id']);
            redirect("solicitacao/visualizar/{$id_solicitacao}");
        } else {
            $_SESSION['msg_erro'] = "Usuário não é participante deste projeto.";

            redirect("solicitacao/visualizar/{$id_solicitacao}");
        }
    }

    /**
     * Finaliza um solicitação que esta em atendimento.
     */
    public function encerrar() {
        $perfil = $_SESSION['perfil'];
        $usuario = $_SESSION['id'];

        $id_solicitacao = filter_input(INPUT_POST, 'solicitacao', FILTER_SANITIZE_NUMBER_INT);
        $resolucao = filter_input(INPUT_POST, 'resolucao_solicitacao', FILTER_DEFAULT, FILTER_FLAG_EMPTY_STRING_NULL);

        $parametros = Parametros_solicitacoes::get_parametros();
        $solicitacao = $this->model->get_dados_solicitacao($id_solicitacao, $perfil, $usuario);

        /*
         * Verifica se usuário tem permissão para encerra solicitação e
         * se o usuário é o técnico responsavel pelo projeto e se a solicitação não esta encerrada.
         */
        if ((array_search($perfil, $parametros['ENCERRAR_SOLICITACAO']) !== FALSE) && ($solicitacao['id_tecnico'] == $usuario) && (!empty($solicitacao['atendimento'])) && empty($solicitacao['encerramento'])) {
            $hoje = new DateTime();
            $hoje = $hoje->format('Y-m-d H:i:s');

            /*
             * Passa os dados referente ao encerramento da solicitação
             */
            $dados = array(
                'encerramento' => $hoje,
                'resolucao' => $resolucao
            );

            /*
             * Realização o encerramento da solicitação e informa o resultado da execução
             */
            if ($this->model->encerrar($id_solicitacao, $dados)) {
                $_SESSION['msg_sucesso'] = "Solicitação encerrada com sucesso.";
            } else {
                $_SESSION['msg_erro'] = "Falha ao encerrar solicitação.";
            }

            /*
             * Gera dados para gravação de log.
             */
            $log = array(
                'dados' => $dados,
                'aplicacao' => "solicitacao/encerrar",
                'msg' => empty($_SESSION ['msg_sucesso']) ? $_SESSION ['msg_erro'] : $_SESSION ['msg_sucesso']
            );

            /*
             * Grava dados da operação realizada
             */
            Logs::gravar($log, $_SESSION ['id']);
            redirect("solicitacao/visualizar/{$id_solicitacao}");
        } else {
            $_SESSION['msg_erro'] = "Usuário sem autorização para encerrar esta solicitação.";

            redirect("solicitacao/visualizar/{$id_solicitacao}");
        }
    }

}
