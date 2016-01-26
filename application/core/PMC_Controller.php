<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Classe Principal do projeto
 */
class PMC_Controller extends CI_Controller {

    protected $data = array();

    function __construct() {
        parent::__construct();

        $this->data['page_title'] = 'phpMyCall';
        $this->data['before_head'] = '';
        $this->data['before_body'] = '';
    }

    /**
     * Renderiza as views da aplicação
     * @param  string $the_view caminho da view
     * @param  string $template nome do template
     * @return view             
     */
    protected function render($the_view = NULL, $template = 'master') {
        if ($template == 'json' || $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($this->data);
        } else {
            $this->data['the_view_content'] = (is_null($the_view)) ? '' : $this->load->view($the_view, $this->data, TRUE);
            $this->load->view('templates/' . $template . '_view', $this->data);
        }
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

    function __construct() {
        parent::__construct();

        $this->data['page_title'] = 'phpMyCall - Área Administrativa';
    }

    /**
     * Renderiza as views da Área Administrativa
     * @param  string   $view caminho da view
     * @param  array    $data Dados a enviados para as views
     */
    protected function render($view, $data) {
        parent::render($the_view, $template);
    }

}

/**
 * Classe Responsável pela Área Pública
 */
class Public_Controller extends PMC_Controller {

    function __construct() {
        parent::__construct();
    }

    /**
     * Renderiza as views Publicas
     * @param  string $the_view caminho da view
     * @param  string $template nome do template
     * @return view             
     */
    protected function render($the_view = NULL, $template = 'public_master') {
        parent::render($the_view, $template);
    }

}
