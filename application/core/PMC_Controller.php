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

    function __construct() {
        parent::__construct();

        $this->data['title'] = $this->translate['title_window'];
        $this->views['head'] = '';
        $this->views['footer'] = '';
    }

    /**
     * Renderiza as views da aplicação
     * @param  string   $view   caminho da view
     * @return view     
     */
    protected function render() {
        $this->load->view($this->views['head'], $this->data);

        if (is_array($this->views['content'])) {
            foreach ($this->views['content'] as $view) {
                $this->load->view($view, $this->data);
            }
        } else {
            $this->load->view($this->views['content'], $this->data);
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
     * @param array/string  $translate_files        Array com nomes dos arquivos de trandução
     * @param string        $translate_language     Idioma que sera exibido
     */
    function __construct($translate_files = NULL, $translate_language = 'portuguese-brazilian') {
        if (Autenticacao::verifica_login()) {
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
                    $this->translate += $this->lang->load($translate_files, $translate_language, TRUE);
                }
            } else {
                $this->translate['title_window'] = 'phpMyCall - Área Administrativa';
            }

            $this->views['head'] = 'template/admin/header';
            $this->views['footer'] = 'template/admin/footer';
        } else {
            redirect('login/index');
        }

        parent::__construct();
    }

    /**
     * Renderiza as views da Área Administrativa
     * @param array/string      $views      Caminho das view
     * @param array             $data       Dados a enviados para as views
     */
    protected function render($views, $data) {
        if (is_array($data)) {
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
     * Contrutor do controller da área administrativa
     * @param array/string  $translate_files        Array com nomes dos arquivos de trandução
     * @param string        $translate_language     Idioma que sera exibido
     */
    function __construct($translate_files = NULL, $translate_language = 'portuguese-brazilian') {
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
                $this->translate += $this->lang->load($translate_files, $translate_language, TRUE);
            }
        } else {
            $this->translate['title_window'] = 'phpMyCall';
        }

        $this->views['head'] = 'template/admin/header';
        $this->views['footer'] = 'template/admin/footer';

        parent::__construct();
    }

    /**
     * Renderiza as views Publicas
     * @param  string $the_view caminho da view
     * @param  string $template nome do template
     * @return view             
     */
    protected function render($the_view = NULL, $template = 'public_master') {
        parent::render();
    }

}
