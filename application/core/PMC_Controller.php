<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Classe Principal do projeto
 */
class PMC_Controller extends CI_Controller {

    /**
     * @var Array           Array contendo que será utilizado nas views 
     */
    protected $data = array();

    /**
     * @var Array/String    Array contendo as views para a geração da página
     */
    protected $views = array();

    /**
     * @var Array           Array contendo as traduções
     */
    protected $translate = array();

    /**
     * Renderiza as views da aplicação
     */
    protected function render() {
        $this->data = array_merge($this->translate, $this->data);

        $this->load->view($this->views['head'], $this->data);

        if (is_array($this->views['content'])) {
            foreach ($this->views['content'] as $view) {
                $this->load->view($this->views['path_content'] . $view, $this->data);
            }
        } else {
            $this->load->view($this->views['path_content'] . $this->views['content'], $this->data);
        }

        $this->load->view($this->views['footer'], $this->data);
    }

    protected function response($data) {
        if (is_array($data)) {
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo $data;
        }
    }

}

/**
 * Classe Responsável pela Área Administrativa
 */
class Admin_Controller extends PMC_Controller {

    /**
     * Contrutor do controller da área administrativa
     * Verifica se usuários esta logado antes de executar operação
     * @param array/string  $translate_files        Array com nomes dos arquivos de trandução
     * @param string        $translate_language     Idioma que sera exibido
     */
    function __construct($translate_files = NULL, $translate_language = 'portuguese-brazilian') {
        parent::__construct();

        if (Autenticacao::verifica_login() === TRUE) {
            if (empty($translate_files) === FALSE) {
                /*
                 * Caso exista mais de um arquivo de traduçao para a view
                 * carrega todos e atribui a $this->translate
                 */
                if (is_array($translate_files)) {
                    foreach ($translate_files as $values) {
                        $this->translate += $this->lang->load($values, $translate_language, TRUE);
                    }
                } else {
                    $this->translate = $this->lang->load($translate_files, $translate_language, TRUE);
                }
            }

            $this->data['title'] = isset($this->translate['title_window']) ? $this->translate['title_window'] : 'openMyCall - Área Administrativa';
        } else {
            redirect('login/index', 'location');
        }
    }

    /**
     * Renderiza as views da Área Administrativa
     * @param array/string  $views                  Caminho das view
     * @param array         $data                   Dados a enviados para as views
     */
    protected function load_view($views, $data = array()) {
        $this->views['head'] = 'template/admin/header';
        $this->views['footer'] = 'template/admin/footer';
        $this->views['path_content'] = 'admin/';

        if (is_array($data) && count($data) > 0) {
            $this->data += $data;
        }

        $this->views['content'] = $views;

        parent::render();
    }

}

/**
 * Classe Responsável pela Área Pública
 */
class Public_Controller extends PMC_Controller {

    /**
     * Renderiza as views da Área publica da aplicaçao
     * @param array/string      $views      Caminho das view
     * @param array             $data       Dados a enviados para as views
     * @param array/string  $translate_files        Array com nomes dos arquivos de trandução
     * @param string        $translate_language     Idioma que sera exibido
     */
    protected function load_view($views, $data, $translate_files = NULL, $translate_language = 'portuguese-brazilian') {
        if (empty($translate_files) === FALSE) {

            /*
             * Caso exista mais de um arquivo de traduçao para a view
             * carrega todos e atribui a $this->translate
             */
            if (is_array($translate_files)) {
                foreach ($translate_files as $values) {
                    $this->translate += $this->lang->load($values, $translate_language, TRUE);
                }
            } else {
                $this->translate = $this->lang->load($translate_files, $translate_language, TRUE);
            }
        }

        $this->data['title'] = isset($this->translate['title_window']) ? $this->translate['title_window'] : 'openMyCall';

        $this->views['head'] = 'template/public/header';
        $this->views['footer'] = 'template/public/footer';
        $this->views['path_content'] = 'public/';

        if (is_array($data)) {
            $this->data += $data;
        }

        $this->views['content'] = $views;

        parent::render();
    }

}
