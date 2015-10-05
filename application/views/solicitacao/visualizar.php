
<script type="text/javascript" src="<?= base_url() . 'static/js/tinymce/tinymce.min.js' ?>"></script>

<script type="text/javascript">

    tinymce.init({
        selector: '#pergunta_feedback, #resposta_feedback',
        language: 'pt_BR'
    });

    $(document).ready(function () {

        var excluir_solicitacao = $('#excluir_solicitacao').dialog({
            autoOpen: false,
            modal: true,
            buttons: [
                {
                    text: "Excluir",
                    icons: {
                        primary: 'ui-icon-closethick'
                    },
                    click: function () {
                        $(location).attr('href', '<?= base_url() . "solicitacao/excluir/{$id_solicitacao}" ?>');
                        excluir_solicitacao.dialog('close');
                    }
                },
                {
                    text: "Cancelar",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        excluir_solicitacao.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        var redirecionar_solicitacao = $('#redirecionar_solicitacao').dialog({
            autoOpen: false,
            modal: true,
            width: 450,
            buttons: [
                {
                    text: "Redirecionar",
                    icons: {
                        primary: 'ui-icon-transferthick-e-w'
                    },
                    click: function () {
                        var tecnico = $('select[name=select_tecnico]').val();
                        $(location).attr('href', '<?= base_url() . "solicitacao/redirecionar/{$id_solicitacao}" ?>/' + tecnico);
                        redirecionar_solicitacao.dialog('close');
                    }
                },
                {
                    text: "Cancelar",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        redirecionar_solicitacao.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        var feedback_solicitacao = $('#feedback_solicitacao').dialog({
            autoOpen: false,
            modal: true,
            width: 920,
            buttons: [
                {
                    text: "Criar Feedback",
                    icons: {
                        primary: 'ui-icon-comment'
                    },
                    click: function () {
                        feedback_solicitacao.dialog('close');
                        $('form[name=solicitar_feedback]').submit();
                    }
                },
                {
                    text: "Cancelar",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        feedback_solicitacao.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        var responder_feedback = $('#responder_feedback').dialog({
            autoOpen: false,
            modal: true,
            width: 850,
            buttons: [
                {
                    text: "Responder Feedback",
                    icons: {
                        primary: 'ui-icon-check'
                    },
                    click: function () {
                        responder_feedback.dialog('close');
                        $('form[name=resposta_feedback]').submit();
                    }
                },
                {
                    text: "Cancelar",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        responder_feedback.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        $("#responder_feedback > div").accordion();

        var visualizar_feedback = $('#visualizar_feedback').dialog({
            autoOpen: false,
            modal: true,
            width: 850,
            buttons: [
                {
                    text: "Fechar",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        visualizar_feedback.dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        $("#visualizar_feedback > div").accordion();

        /*******************************************************/

        $("button[class=feedback_aberto]").button({
            icons: {
                primary: 'ui-icon-comment'
            }
        }).on('click', function () {
            var id_feedback = $(this).attr("feedback");
            $("input[type=hidden][name=feedback_id]").val(id_feedback);

            $.ajax({
                url: '<?= base_url() . 'solicitacao/get_pergunta_resposta_feedback' ?>',
                data: 'feedback_id=' + id_feedback,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('#feedback_resposta_pergunta').html(data.pergunta);
                }
            });

            responder_feedback.dialog('open');
        });

        $("button[class=feedback_atendida]").button({
            icons: {
                primary: 'ui-icon-check'
            }
        }).on('click', function () {
            var id_feedback = $(this).attr("feedback");

            $.ajax({
                url: '<?= base_url() . 'solicitacao/get_pergunta_resposta_feedback' ?>',
                data: 'feedback_id=' + id_feedback,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('#visualizar_feedback_pergunta').html(data.pergunta);
                    $('#visualizar_feedback_resposta').html(data.resposta);
                }
            });

            visualizar_feedback.dialog('open');
        });

        $('#editar').button({
            disabled: <?= $editar ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-pencil'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= base_url() . "solicitacao/editar/{$id_solicitacao}" ?>');
        });

        $('#atender').button({
            disabled: <?= $atender ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-wrench'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= base_url() . "solicitacao/atender/{$id_solicitacao}" ?>');
        });

        $('#sub_chamado').button({
            disabled: <?= ($sub_chamado) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= base_url() . "solicitacao/sub_chamado/{$id_solicitacao}" ?>');
        });

        $('#excluir').button({
            disabled: <?= ($excluir) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-closethick'
            }
        }).on('click', function () {
            excluir_solicitacao.dialog('open');
        });

        $('#redirecionar').button({
            disabled: <?= ($redirecionar) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-transferthick-e-w'
            }
        }).on('click', function () {
            redirecionar_solicitacao.dialog('open');
        });

        $('#feedback').button({
            disabled: <?= ($feedback) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-comment'
            }
        }).on('click', function () {
            feedback_solicitacao.dialog('open');
        });

        $('#encerrar').button({
            disabled: <?= ($encerrar) ? 'false' : 'true' ?>,
            icons: {
                primary: 'ui-icon-check'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= base_url() . "solicitacao/encerrar/{$id_solicitacao}" ?>');
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
                Editar
            </button>
            <button id="atender">
                Atender
            </button>
            <button id="sub_chamado">
                Sub-Chamado
            </button>
            <button id="excluir">
                Excluir
            </button>
            <button id="redirecionar">
                Redirecionar
            </button>
            <button id="feedback">
                Feedback
            </button>
            <button id="encerrar">
                Encerrar
            </button>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Projeto:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['projeto'] ?>" disabled class="form-control" />
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Problema:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['problema'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Prioridade:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['prioridade'] ?>" disabled class="form-control" />
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Solicitante:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['solicitante'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>


    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Atendente:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['atendente'] ?>" disabled class="form-control" />
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Técnico:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['tecnico'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Abertura:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['abertura'] ?>" disabled class="form-control" />
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Atendimento:
            </label>
            <div class="col-md-8">
                <input type="text" value="<?= $solicitacao['atendimento'] ?>" disabled class="form-control" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="col-md-4 control-label">
                Encerramento:
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
            <div class="panel panel-info">
                <div class="panel-heading">
                    Feedback
                </div>
                <div class="panel-body text-center">
                    <table class="u-full-width">
                        <thead>
                            <tr>
                                <th rowspan="2" class="col2">Pergunta</th>
                                <th rowspan="2" class="col2">Resposta</th>
                                <th colspan="2" class="col4">Data feedback</th>
                                <th rowspan="2" class="col4">Responsável pelo feedback</th>
                            </tr>
                            <tr>
                                <th class="col2">
                                    Pegunta
                                </th>
                                <th class="col2">
                                    Resposta
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
                                                    Responder
                                                </button>
                                                <?php
                                            } else {
                                                ?>
                                                <button type="button" class="feedback_atendida" feedback="<?= $values['id'] ?>">
                                                    Visualizar
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
                    Descrição:
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
                    Arquivos anexos:
                </h3>
            </div>
            <div class="panel-body text-center">
                <?php
                if (empty($solicitacao['arquivos'])) {
                    echo "Esta solicitação não contém nenhum arquivo em anexo.";
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


<div id="excluir_solicitacao" title="Atenção">
    <p>Deseja excluir está solicitação?</p>
</div>

<div id="redirecionar_solicitacao" title="Redirecionamento de chamado a outro técnico.">
    <div class="form-group col-md-12">
        <label class="col-md-4 control-label">
            Técnico:
        </label>
        <div class="col-md-8">
            <select name="select_tecnico" id="select_tecnico" class="selectpicker">
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

<div id="feedback_solicitacao" title="Solicitação de feedback">
    <form action="<?= base_url() . "solicitacao/feedback" ?>" name="solicitar_feedback" method="POST">
        <input type="hidden" name="solicitacao" value="<?= "{$id_solicitacao}" ?>" />

        <div class="row">
            <div class="form-group col-md-12">
                <label class="col-md-4 control-label">Tipo de feedback:</label>
                <div class="col-md-8">
                    <select name="select_feedback" id="select_feedback" class="selectpicker">
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
                <label class="col-md-4 control-label">Destinátario:</label>
                <div class="col-md-8">
                    <select name="select_destinatario" id="select_destinatario" class="selectpicker">
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
            <label for="pergunta_feedback" class="twelve columns">Descrição:</label>
            <div class="twelve columns">
                <textarea name="pergunta_feedback" id="pergunta_feedback"></textarea>
            </div>
        </div>
    </form>
</div>


<div id="responder_feedback" title="Responder Feedback">
    <div>
        <h3>Pergunta</h3>
        <div id="feedback_resposta_pergunta"></div>

        <h3>Resposta</h3>
        <div id="feedback_resposta">
            <form method="post" name="resposta_feedback" action="<?= base_url() . "solicitacao/responder_feedback" ?>">
                <input type="hidden" name="feedback_id" id="feedback_id" />
                <input type="hidden" name="solicitacao" value="<?= "{$id_solicitacao}" ?>" />
                <textarea name="resposta_feedback" id="resposta_feedback"></textarea>
            </form>
        </div>
    </div>
</div>


<div id="visualizar_feedback" title="Visualização de Feedback">
    <div>
        <h3>Pergunta</h3>
        <div id="visualizar_feedback_pergunta"></div>

        <h3>Resposta</h3>
        <div id="visualizar_feedback_resposta"></div>
    </div>
</div>