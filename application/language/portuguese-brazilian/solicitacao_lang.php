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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

$lang = array(
    'title_window_new_request' => 'Abrir Solicitação',
    'title_window_list_request' => 'Lista de solicitações',
    'title_window_requests_to_open' => 'Solicitações em aberta',
    'title_window_requests_edit' => 'Editar Solicitação',
    // index.php e visualizar.php
    'label_project_request' => 'Projeto',
    'label_problem_request' => 'Problema',
    'label_attendant_request' => 'Atendente',
    'label_requester_request' => 'Solicitante',
    'label_priority_request' => 'Prioridade',
    'label_technician_resquest' => 'Técnico',
    'label_description_request' => 'Descrição',
    'label_attach_file_request' => 'Anexar arquivos a esta solicitação',
    'label_submit_request' => 'Salvar',
    //editar.php
    'label_file_attachments_on_request' => 'Arquivos anexos na solicitação',
    'label_delete_attachment_request' => 'Excluir',
    'label_success_delete_attachment_request' => 'Arquivo removido com sucesso.',
    'label_error_delete_attachment_request' => 'Falha ao remover arquivo. Caso persista o erro contate administrador.',
    'title_dialog_delete_attachente_request' => 'Atenção',
    'text_confirm_delete_attachment_request' => 'Deseja excluir este arquivo?',
    'title_dialog_alert_edit_request' => 'Atenção',
    //lista.php e visualizar.ph
    'label_visualize_request' => 'Visualizar solicitação',
    'label_status_request' => 'Situação',
    'label_option_all_status_request' => 'Todas',
    'label_option_open_status_request' => 'Aberta',
    'label_option_in_service_request' => 'Atendimento',
    'label_option_closed_request' => 'Encerrada',
    'label_option_all_priority_request' => 'Todas',
    'label_start_time_column_table_request' => 'Abertura',
    'label_project_name_column_table_request' => 'Projeto',
    'label_problem_name_column_table_request' => 'Problema',
    'label_priority_name_column_table_request' => 'Prioridade',
    'label_requester_name_column_table_request' => 'Solicitante',
    'label_technician_name_column_table_request' => 'Atendente',
    'label_files_column_table_request' => 'Q. Arquivos',
    //visualizar.php
    'label_button_confirm_delete_request' => 'Excluir',
    'label_button_cancel_delete_request' => 'Cancelar',
    'label_button_confirm_redirect_request_other_technician' => 'Redirecionar',
    'label_button_cancel_redirect_request_other_technician' => 'Cancelar',
    'label_button_confirm_create_feedback' => 'Criar Feedback',
    'label_button_cancel_create_feedback' => 'Cancelar',
    'label_button_confirm_answer_feedback' => 'Responder Feedback',
    'label_button_cancel_answer_feedback' => 'Cancelar',
    'label_button_close_view_feedback' => 'Fechar',
    'label_button_confirm_terminate_request' => 'Encerrar',
    'label_button_cancel_terminate_request' => 'Cancelar',
    'label_button_edit_request' => 'Editar',
    'label_button_request_responsible' => 'Atender',
    'label_button_son_request' => 'Sub-Chamado',
    'label_button_delete_request' => 'Excluir',
    'label_button_redirect_request_other_technician' => 'Redirecionar',
    'label_button_create_feedback' => 'Feedback',
    'label_button_terminate_request' => 'Encerrar',
    'label_time_end_request' => 'Encerramento',
    'label_header_panel_feedback' => 'Feedback',
    'title_header_table_question_feedback' => 'Pergunta',
    'title_header_table_answer_feedback' => 'Resposta',
    'title_header_table_date_feedback' => 'Data feedback',
    'title_header_table_responsible_for_feedback' => 'Responsável pelo feedback',
    'label_button_answer_feedback' => 'Responder',
    'label_button_view_feedback' => 'Visualizar',
    'title_header_panel_attachments_on_request' => 'Arquivos anexos',
    'label_not_attachments_on_request' => 'Esta solicitação não contém nenhum arquivo em anexo.',
    'title_dialog_delete_request' => 'Atenção',
    'text_alert_delete_request' => 'Deseja excluir está solicitação?',
    'title_dialog_redirect_request_other_technician' => 'Redirecionamento de chamado a outro técnico.',
    'title_dialog_create_feedback' => 'Solicitação de feedback',
    'label_select_type_feedback' => 'Tipo de feedback',
    'label_technician_answer_feedback' => 'Destinátario',
    'label_description_question_feedback' => 'Descrição',
    'title_dialog_answer_feedback' => 'Responder Feedback',
    'label_question_feedback' => 'Perguntar',
    'label_answer_feedback' => 'Responder',
    'title_dialog_terminate_request' => 'Encerramento de solicitação',
    // Solicitacao Controller
    'info_success_create_request' => 'Solicitação criada com sucesso.',
    'info_error_create_request' => 'Erro ao criar Solicitação.',
    'info_error_attachments_on_request' => 'Erro ao adicionar o arquivo:',
    'info_error_edit_terminate_request' => 'Operação ilegal. Esta solicitação está encerrada.',
    'info_error_edit_request_in_service' => 'Operação ilegal. Esta solicitação está em atendimento.',
    'info_success_update_request' => 'Solicitação alterada com sucesso.',
    'info_error_update_request' => 'Erro ao alterar Solicitação',
    'info_error_user_not_responsible_request' => 'Perfil não possui permissão para atender uma solicitação.',
    'info_error_user_not_delete_request' => 'Perfil não possui permissão para excluir uma solicitação.',
    'info_error_user_not_redirect_request' => 'Perfil não possui permissão para redirecionar solicitação.',
    'info_success_add_feedback' => 'Feedback gravado com sucesso.',
    'info_error_add_feedback' => 'Falha ao adicionar feedback.',
    'info_error_user_not_feedback' => 'Perfil do usuário não possui permissão para solicitar feedback.',
    'info_error_user_not_project' => 'Usuário não possui permissão neste projeto.',
    'info_success_answer_feedback' => 'Feedback respondido com sucesso.',
    'info_error_answer_feedback' => 'Falha ao responder feedback.',
    'info_success_terminate_request' => 'Solicitação encerrada com sucesso.',
    'info_error_terminate_request' => 'Falha ao encerrar solicitação.',
    'info_error_user_not_terminate_request' => 'Usuário sem autorização para encerrar esta solicitação.'
);
