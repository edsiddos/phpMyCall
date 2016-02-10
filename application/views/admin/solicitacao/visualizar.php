
<script type="text/javascript" src="<?= base_url('static/tinymce/tinymce.min.js') ?>"></script>

<script type="text/javascript">

    tinymce.init({
        selector: '#pergunta_feedback, #resposta_feedback, #resolucao_solicitacao',
        language: 'pt_BR'
    });

    $(document).ready(function () {

        /******************************************************************/
        /*                  Dialog Excluir Solicitações                   */
        /******************************************************************/

        var $excluir_solicitacao = $('#excluir_solicitacao').dialog({
            autoOpen: false,
            modal: true,
            buttons: [
                {
                    text: "<?= $label_button_confirm_delete_request ?>",
                    icons: {
                        primary: 'ui-icon-closethick'
                    },
                    click: function () {
                        $(location).attr('href', '<?= base_url("solicitacao/excluir/{$id_solicitacao}") ?>');
                        $excluir_solicitacao.dialog('close');
                    }
                },
                {
                    text: "<?= $label_button_cancel_delete_request ?>",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $excluir_solicitacao.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        /******************************************************************/
        /*            Dialog redirecionamento de solicitações             */
        /******************************************************************/

        var $redirecionar_solicitacao = $('#redirecionar_solicitacao').dialog({
            autoOpen: false,
            modal: true,
            width: '35%',
            height: $(window).height() * 0.5,
            buttons: [
                {
                    text: "<?= $label_button_confirm_redirect_request_other_technician ?>",
                    icons: {
                        primary: 'ui-icon-transferthick-e-w'
                    },
                    click: function () {
                        var tecnico = $('select[name=select_tecnico]').val();
                        $(location).attr('href', '<?= base_url("solicitacao/redirecionar/{$id_solicitacao}") ?>/' + tecnico);
                        $redirecionar_solicitacao.dialog('close');
                    }
                },
                {
                    text: "<?= $label_button_cancel_redirect_request_other_technician ?>",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $redirecionar_solicitacao.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        /******************************************************************/
        /*                Dialog feedback de solicitações                 */
        /******************************************************************/

        var $feedback_solicitacao = $('#feedback_solicitacao').dialog({
            autoOpen: false,
            modal: true,
            width: 920,
            buttons: [
                {
                    text: "<?= $label_button_confirm_create_feedback ?>",
                    icons: {
                        primary: 'ui-icon-comment'
                    },
                    click: function () {
                        $feedback_solicitacao.dialog('close');
                        $('form[name=solicitar_feedback]').submit();
                    }
                },
                {
                    text: "<?= $label_button_cancel_create_feedback ?>",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $feedback_solicitacao.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        /******************************************************************/
        /*                   Dialog responder feedback                    */
        /******************************************************************/

        var $responder_feedback = $('#responder_feedback').dialog({
            autoOpen: false,
            modal: true,
            width: 850,
            buttons: [
                {
                    text: "<?= $label_button_confirm_answer_feedback ?>",
                    icons: {
                        primary: 'ui-icon-check'
                    },
                    click: function () {
                        $responder_feedback.dialog('close');
                        $('form[name=resposta_feedback]').submit();
                    }
                },
                {
                    text: "<?= $label_button_cancel_answer_feedback ?>",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $responder_feedback.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        $("#responder_feedback > div").accordion();

        /******************************************************************/
        /*               Dialog visualizaçao de feedbacks                 */
        /******************************************************************/

        var $visualizar_feedback = $('#visualizar_feedback').dialog({
            autoOpen: false,
            modal: true,
            width: 850,
            buttons: [
                {
                    text: "<?= $label_button_close_view_feedback ?>",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $visualizar_feedback.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        /******************************************************************/
        /*               Dialog visualizaçao de feedbacks                 */
        /******************************************************************/

        var $encerrar_dialog = $('#encerramento_solicitacao').dialog({
            autoOpen: false,
            modal: true,
            width: 850,
            buttons: [
                {
                    text: "<?= $label_button_confirm_terminate_request ?>",
                    icons: {
                        primary: 'ui-icon-check'
                    },
                    click: function () {
                        $('form[name=encerrar_solicitacao]').submit();
                        $encerrar_dialog.dialog('close');
                    }
                },
                {
                    text: "<?= $label_button_cancel_terminate_request ?>",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $encerrar_dialog.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        /******************************************************************/
        /*        Acordion para exibiçao das perguntas e resposta         */
        /******************************************************************/

        $("#visualizar_feedback > div").accordion();

        /******************************************************************/
        /*           Cria botoes e adiciona eventos ao clica-los          */
        /******************************************************************/

        $("button[class=feedback_aberto]").button({
            icons: {
                primary: 'ui-icon-comment'
            }
        }).on('click', function () {
            var id_feedback = $(this).attr("feedback");
            $("input[type=hidden][name=feedback_id]").val(id_feedback);

            $.ajax({
                url: '<?= base_url('solicitacao/get_pergunta_resposta_feedback') ?>',
                data: 'feedback_id=' + id_feedback,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('#feedback_resposta_pergunta').html(data.pergunta);
                }
            });

            $responder_feedback.dialog('open');
        });

        $("button[class=feedback_atendida]").button({
            icons: {
                primary: 'ui-icon-check'
            }
        }).on('click', function () {
            var id_feedback = $(this).attr("feedback");

            $.ajax({
                url: '<?= base_url('solicitacao/get_pergunta_resposta_feedback') ?>',
                data: 'feedback_id=' + id_feedback,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('#visualizar_feedback_pergunta').html(data.pergunta);
                    $('#visualizar_feedback_resposta').html(data.resposta);
                }
            });

            $visualizar_feedback.dialog('open');
        });

        $('#editar').button({
            disabled: <?= $editar ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-pencil'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= base_url("solicitacao/editar/{$id_solicitacao}") ?>');
        });

        $('#atender').button({
            disabled: <?= $atender ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-wrench'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= base_url("solicitacao/atender/{$id_solicitacao}") ?>');
        });

        $('#sub_chamado').button({
            disabled: <?= ($sub_chamado) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= base_url("solicitacao/sub_chamado/{$id_solicitacao}") ?>');
        });

        $('#excluir').button({
            disabled: <?= ($excluir) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-closethick'
            }
        }).on('click', function () {
            $excluir_solicitacao.dialog('open');
        });

        $('#redirecionar').button({
            disabled: <?= ($redirecionar) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-transferthick-e-w'
            }
        }).on('click', function () {
            $redirecionar_solicitacao.dialog('open');
        });

        $('#feedback').button({
            disabled: <?= ($feedback) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-comment'
            }
        }).on('click', function () {
            $feedback_solicitacao.dialog('open');
        });

        $('#encerrar').button({
            disabled: <?= ($encerrar) ? 'false' : 'true' ?>,
            icons: {
                primary: 'ui-icon-check'
            }
        }).on('click', function () {
            $encerrar_dialog.dialog('open');
        });
    });

</script>

<style type="text/css">

    #visualizar_feedback_pergunta, #visualizar_feedback_resposta {
        overflow: auto;
        height: 250px;
    }

    #feedback_resposta_pergunta, #feedback_resposta{
        height: 250px;
    }

</style>

<div class="container">

    <?php
    if ((!empty($_SESSION ['msg_erro'])) || (!empty($_SESSION ['msg_sucesso']))) {
        ?>
        <div class="alert <?= empty($_SESSION['msg_erro']) ? 'alert-success' : 'alert-danger'; ?> text-center">
            <?= empty($_SESSION['msg_erro']) ? $_SESSION['msg_sucesso'] : $_SESSION['msg_erro']; ?>
        </div>
        <?php
        unset($_SESSION ['msg_erro']);
        unset($_SESSION ['msg_sucesso']);
    }
    ?>

    <div class="row">
        <div class="ui-widget-header ui-corner-all">
            <button id="editar">
                <?= $label_button_edit_request ?>
            </button>
            <button id="atender">
                <?= $label_button_request_responsible ?>
            </button>
            <button id="sub_chamado">
                <?= $label_button_son_request ?>
            </button>
            <button id="excluir">
                <?= $label_button_delete_request ?>
            </button>
            <button id="redirecionar">
                <?= $label_button_redirect_request_other_technician ?>
            </button>
            <button id="feedback">
                <?= $label_button_create_feedback ?>
            </button>
            <button id="encerrar">
                <?= $label_button_terminate_request ?>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_project_request ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['projeto'] ?>" disabled class="form-control" />
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_problem_request ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['problema'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_priority_request ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['prioridade'] ?>" disabled class="form-control" />
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_requester_request ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['solicitante'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>


    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_attendant_request ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['atendente'] ?>" disabled class="form-control" />
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_technician_resquest ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['tecnico'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_start_time_column_table_request ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['abertura'] ?>" disabled class="form-control" />
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_option_in_service_request ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['atendimento'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                <?= $label_time_end_request ?>:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['encerramento'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>

    <?php
    if (!empty($feedback_solicitado)) {
        ?>

        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= $label_header_panel_feedback ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="col-md-2 text-center"><?= $title_header_table_question_feedback ?></th>
                                <th rowspan="2" class="col-md-2 text-center"><?= $title_header_table_answer_feedback ?></th>
                                <th colspan="2" class="col-md-4 text-center"><?= $title_header_table_date_feedback ?></th>
                                <th rowspan="2" class="col-md-4 text-center"><?= $title_header_table_responsible_for_feedback ?></th>
                            </tr>
                            <tr>
                                <th class="col-md-2 text-center">
                                    <?= $title_header_table_question_feedback ?>
                                </th>
                                <th class="col-md-2 text-center">
                                    <?= $title_header_table_answer_feedback ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($feedback_solicitado as $values) {
                                ?>
                                <tr class="<?= $values['aberta'] ? 'warning' : 'success'; ?>">
                                    <td><?= strip_tags($values['pergunta']) ?></td>
                                    <td><?= strip_tags($values['resposta']) ?></td>
                                    <td><?= $values['inicio'] ?></td>
                                    <td><?= $values['fim'] ?></td>
                                    <td class="row">
                                        <div class="col-md-6">
                                            <?php echo $values['nome_responsavel']; ?>
                                        </div>
                                        <div class="col-md-6 text-center" style="float: right;">
                                            <?php
                                            if ($values['aberta'] && $values['responsavel'] === $_SESSION['id']) {
                                                ?>
                                                <button type="button" class="feedback_aberto" feedback="<?= $values['id'] ?>">
                                                    <?= $label_button_answer_feedback ?>
                                                </button>
                                                <?php
                                            } else {
                                                ?>
                                                <button type="button" class="feedback_atendida" feedback="<?= $values['id'] ?>">
                                                    <?= $label_button_view_feedback ?>
                                                </button>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php
    }
    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $label_description_request ?>:
                </h3>
            </div>
            <div class="panel-body">
                <?= $solicitacao['descricao'] ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $title_header_panel_attachments_on_request ?>:
                </h3>
            </div>
            <div class="panel-body text-center">
                <?php
                if (empty($solicitacao['arquivos'])) {
                    echo $label_not_attachments_on_request;
                } else {
                    foreach ($solicitacao['arquivos'] as $values) {
                        ?>
                        <a href="<?= base_url() . "solicitacao/download_arquivo/{$values['id']}" ?>" target="_blank">
                            <?= $values['nome'] ?>
                        </a><br>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>


<div id="excluir_solicitacao" title="<?= $title_dialog_delete_request ?>">
    <p><?= $text_alert_delete_request ?></p>
</div>

<div id="redirecionar_solicitacao" title="<?= $title_dialog_redirect_request_other_technician ?>">
    <div class="form-group col-md-12">
        <label class="col-md-4 control-label">
            <?= $label_technician_resquest ?>:
        </label>
        <div class="col-md-8">
            <select name="select_tecnico" id="select_tecnico" class="selectpicker" data-size="5" data-live-search="true">
                <option value=""></option>
                <?php
                foreach ($tecnicos as $values) {
                    if ($values['tecnico'] == 1) {
                        ?>
                        <option value="<?= $values['id'] ?>"><?= $values['nome']; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>
</div>

<div id="feedback_solicitacao" title="<?= $title_dialog_create_feedback ?>">
    <form action="<?= base_url() . "solicitacao/feedback" ?>" class="form-horizontal" name="solicitar_feedback" method="POST">
        <input type="hidden" name="solicitacao" value="<?= "{$id_solicitacao}" ?>" />

        <div class="row">
            <div class="form-group col-md-12">
                <label class="col-md-4 control-label">
                    <?= $label_select_type_feedback ?>:
                </label>
                <div class="col-md-8">
                    <select name="select_feedback" id="select_feedback" class="selectpicker" data-size="5" data-live-search="true">
                        <option value=""></option>
                        <?php
                        foreach ($tipos_feedback AS $values) {
                            ?>
                            <option value="<?= $values['id'] ?>">
                                <?= $values['nome'] ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                <label class="col-md-4 control-label">
                    <?= $label_technician_answer_feedback ?>:
                </label>
                <div class="col-md-8">
                    <select name="select_destinatario" id="select_destinatario" class="selectpicker" data-size="5" data-live-search="true">
                        <option value=""></option>
                        <?php
                        foreach ($tecnicos as $values) {
                            ?>
                            <option value="<?= $values['id'] ?>"><?= $values['nome']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <label for="pergunta_feedback" class="col-md-12">
                <?= $label_description_question_feedback ?>:
            </label>
            <div class="col-md-12">
                <textarea name="pergunta_feedback" id="pergunta_feedback"></textarea>
            </div>
        </div>
    </form>
</div>


<div id="responder_feedback" title="<?= $title_dialog_answer_feedback ?>">
    <div>
        <h3>Pergunta</h3>
        <div id="feedback_resposta_pergunta"></div>

        <h3>Resposta</h3>
        <div id="feedback_resposta">
            <form method="post" name="resposta_feedback" class="form-horizontal" action="<?= base_url() . "solicitacao/responder_feedback" ?>">
                <input type="hidden" name="feedback_id" id="feedback_id" />
                <input type="hidden" name="solicitacao" value="<?= "{$id_solicitacao}" ?>" />
                <textarea name="resposta_feedback" id="resposta_feedback"></textarea>
            </form>
        </div>
    </div>
</div>


<div id="visualizar_feedback" title="<?= $title_dialog_answer_feedback ?>">
    <div>
        <h3><?= $label_question_feedback ?></h3>
        <div id="visualizar_feedback_pergunta"></div>

        <h3><?= $label_answer_feedback ?></h3>
        <div id="visualizar_feedback_resposta"></div>
    </div>
</div>


<div id="encerramento_solicitacao" title="<?= $title_dialog_terminate_request ?>">
    <div>
        <form method="post" name="encerrar_solicitacao" class="form-horizontal" action="<?= base_url() . "solicitacao/encerrar" ?>">
            <input type="hidden" name="solicitacao" value="<?= "{$id_solicitacao}" ?>" />
            <textarea name="resolucao_solicitacao" id="resolucao_solicitacao"></textarea>
        </form>
    </div>
</div>