
<script type="text/javascript" src="<?= HTTP_JS . '/tinymce/tinymce.min.js' ?>"></script>

<script type="text/javascript">

    tinymce.init({
        selector: '#perguntaFeedback, #respostaFeedback',
        language: 'pt_BR'
    });

    $(document).ready(function () {

        var excluir_solicitacao = $('#excluir-solicitacao').dialog({
            autoOpen: false,
            modal: true,
            buttons: [
                {
                    text: "Excluir",
                    icons: {
                        primary: 'ui-icon-closethick'
                    },
                    click: function () {
                        $(location).attr('href', '<?= HTTP . "/Solicitacao/excluir/{$id_solicitacao}" ?>');
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

        var redirecionar_solicitacao = $('#redirecionar-solicitacao').dialog({
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
                        var tecnico = $('select[name=selectTecnico]').val();
                        $(location).attr('href', '<?= HTTP . "/Solicitacao/redirecionar/{$id_solicitacao}" ?>/' + tecnico);
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

        var feedback_solicitacao = $('#feedback-solicitacao').dialog({
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
                        $('form[name=solicitar-feedback]').submit();
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

        var responder_feedback = $('#responderFeedback').dialog({
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
                        $('form[name=respostaFeedback]').submit();
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

        $("#responderFeedback > div").accordion();

        var visualizar_feedback = $('#visualizarFeedback').dialog({
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

        $("#visualizarFeedback > div").accordion();

        $("button[class=feedback-aberto]").button({
            icons: {
                primary: 'ui-icon-comment'
            }
        }).on('click', function () {
            var id_feedback = $(this).attr("feedback");
            $("input[type=hidden][name=feedback_id]").val(id_feedback);

            $.ajax({
                url: '<?= HTTP . '/Solicitacao/getPerguntaRespostaFeedback' ?>',
                data: 'feedback_id=' + id_feedback,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('#feedbackRespostaPergunta').html(data.pergunta);
                }
            });

            responder_feedback.dialog('open');
        });

        $("button[class=feedback-atendida]").button({
            icons: {
                primary: 'ui-icon-check'
            }
        }).on('click', function () {
            var id_feedback = $(this).attr("feedback");

            $.ajax({
                url: '<?= HTTP . '/Solicitacao/getPerguntaRespostaFeedback' ?>',
                data: 'feedback_id=' + id_feedback,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('#visualizarFeedbackPergunta').html(data.pergunta);
                    $('#visualizarFeedbackResposta').html(data.resposta);
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
            $(location).attr('href', '<?= HTTP . "/Solicitacao/editar/{$id_solicitacao}" ?>');
        });

        $('#atender').button({
            disabled: <?= $atender ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-wrench'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= HTTP . "/Solicitacao/atender/{$id_solicitacao}" ?>');
        });

        $('#sub-chamado').button({
            disabled: <?= ($sub_chamado) ? 'false' : 'true'; ?>,
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        }).on('click', function () {
            $(location).attr('href', '<?= HTTP . "/Solicitacao/subChamado/{$id_solicitacao}" ?>');
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
            $(location).attr('href', '<?= HTTP . "/Solicitacao/encerrar/{$id_solicitacao}" ?>');
        });
    });

</script>

<style type="text/css">

    #visualizarFeedbackPergunta, #visualizarFeedbackResposta {
        overflow: auto;
        height: 250px;
    }

    #feedbackRespostaPergunta, #feedbackResposta{
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
            <button id="sub-chamado">
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
        <div class="six columns">
            <label class="four columns">
                Projeto:
            </label>
            <input type="text" value="<?= $solicitacao['projeto'] ?>" disabled class="eight columns" />
        </div>
        <div class="six columns">
            <label class="four columns">
                Problema:
            </label>
            <input type="text" value="<?= $solicitacao['problema'] ?>" disabled class="eight columns" />
        </div>
    </div>

    <div class="row">
        <div class="six columns">
            <label class="four columns">
                Prioridade:
            </label>
            <input type="text" value="<?= $solicitacao['prioridade'] ?>" disabled class="eight columns" />
        </div>
        <div class="six columns">
            <label class="four columns">
                Solicitante:
            </label>
            <input type="text" value="<?= $solicitacao['solicitante'] ?>" disabled class="eight columns" />
        </div>
    </div>


    <div class="row">
        <div class="six columns">
            <label class="four columns">
                Atendente:
            </label>
            <input type="text" value="<?= $solicitacao['atendente'] ?>" disabled class="eight columns" />
        </div>
        <div class="six columns">
            <label class="four columns">
                Técnico:
            </label>
            <input type="text" value="<?= $solicitacao['tecnico'] ?>" disabled class="eight columns" />
        </div>
    </div>

    <div class="row">
        <div class="six columns">
            <label class="four columns">
                Abertura:
            </label>
            <input type="text" value="<?= $solicitacao['abertura'] ?>" disabled class="eight columns" />
        </div>
        <div class="six columns">
            <label class="four columns">
                Atendimento:
            </label>
            <input type="text" value="<?= $solicitacao['atendimento'] ?>" disabled class="eight columns" />
        </div>
    </div>

    <div class="row">
        <div class="six columns">
            <label class="four columns">
                Encerramento:
            </label>
            <input type="text" value="<?= $solicitacao['encerramento'] ?>" disabled class="eight columns" />
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
                                        <div class="six columns">
                                            <?php echo $values['nome_responsavel']; ?>
                                        </div>
                                        <div class="six columns text-center" style="float: right;">
                                            <?php
                                            if ($values['aberta'] && $values['responsavel'] === $_SESSION['id']) {
                                                ?>
                                                <button type="button" class="feedback-aberto" feedback="<?= $values['id'] ?>">
                                                    Responder
                                                </button>
                                                <?php
                                            } else {
                                                ?>
                                                <button type="button" class="feedback-atendida" feedback="<?= $values['id'] ?>">
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
        <div class="panel panel-info">
            <div class="panel-heading">
                Descrição:
            </div>
            <div class="panel-body">
                <?= $solicitacao['descricao'] ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="panel panel-info">
            <div class="panel-heading">
                Arquivos anexos:
            </div>
            <div class="panel-body text-center">
                <?php
                if (empty($solicitacao['arquivos'])) {
                    echo "Esta solicitação não contém nenhum arquivo em anexo.";
                } else {
                    foreach ($solicitacao['arquivos'] as $values) {
                        ?>
                        <a href="<?= HTTP . "/Solicitacao/downloadArquivo/{$values['id']}" ?>" target="_blank">
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


<div id="excluir-solicitacao" title="Atenção">
    <p>Deseja excluir está solicitação?</p>
</div>

<div id="redirecionar-solicitacao" title="Redirecionamento de chamado a outro técnico.">
    <div class="twelve columns">
        <label class="four columns">
            Técnico:
        </label>
        <select name="selectTecnico" id="selectTecnico" class="eight columns">
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

<div id="feedback-solicitacao" title="Solicitação de feedback">
    <form action="<?= HTTP . "/Solicitacao/feedback" ?>" name="solicitar-feedback" method="POST">
        <input type="hidden" name="solicitacao" value="<?= "{$id_solicitacao}" ?>" />

        <div class="row">
            <div class="twelve columns">
                <label class="four columns">Tipo de feedback:</label>
                <select name="selectFeedback" id="selectFeedback" class="eight columns">
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

        <div class="row">
            <div class="twelve columns">
                <label class="four columns">Destinátario:</label>
                <select name="selectDestinatario" id="selectDestinatario" class="eight columns">
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

        <div class="row">
            <label for="perguntaFeedback" class="twelve columns">Descrição:</label>
            <div class="twelve columns">
                <textarea name="perguntaFeedback" id="perguntaFeedback"></textarea>
            </div>
        </div>
    </form>
</div>


<div id="responderFeedback" title="Responder Feedback">
    <div>
        <h3>Pergunta</h3>
        <div id="feedbackRespostaPergunta"></div>

        <h3>Resposta</h3>
        <div id="feedbackResposta">
            <form method="post" name="respostaFeedback" action="<?= HTTP . "/Solicitacao/responderFeedback" ?>">
                <input type="hidden" name="feedback_id" id="feedback_id" />
                <input type="hidden" name="solicitacao" value="<?= "{$id_solicitacao}" ?>" />
                <textarea name="respostaFeedback" id="respostaFeedback"></textarea>
            </form>
        </div>
    </div>
</div>


<div id="visualizarFeedback" title="Visualização de Feedback">
    <div>
        <h3>Pergunta</h3>
        <div id="visualizarFeedbackPergunta"></div>

        <h3>Resposta</h3>
        <div id="visualizarFeedbackResposta"></div>
    </div>
</div>