
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table-locale-all.min.js') ?>"></script>

<link href="<?= base_url('static/bootstrap-table/css/bootstrap-table.min.css') ?>" rel="stylesheet">

<script type="text/javascript">

    /*
     * Instancia objeto para exiber mensagem de aguarde.
     */
    var aguarde = new Aguarde('<?= base_url() . 'static/img/change.gif' ?>');

    var Feedback = function () {

        var formulario = ''; // cadastrar ou alterar
        var feedback = 0;

        /**
         * Seta formulario para cadastro
         */
        this.setCadastrar = function () {
            formulario = 'cadastrar';
        };

        /**
         * Seta formulario de alteração
         */
        this.setAlterar = function () {
            formulario = 'alterar';
        };

        /**
         * Seta dados para solicitar posterior exclusão
         * @param {int} id_feedback Código do feedback
         */
        this.setExcluir = function (id_feedback) {
            feedback = id_feedback;
        };

        /**
         * Envia formulario para cadastro ou alteração
         */
        this.submitFormulario = function () {
            aguarde.mostrar();

            var dados = $('form[name=formulario]').find('input:not(input[name=input_descontar]), textarea').serialize();
            dados += ($('form[name=formulario] input[name=input_descontar]').prop('checked') ? '&input_descontar=descontar' : '');

            $.ajax({
                url: '<?= base_url() . 'feedback/' ?>' + formulario,
                data: dados,
                dataType: 'JSON',
                type: 'POST'
            }).done(function (data) {

                if (data.status) {
                    $('#msg_status').removeClass('hide alert-danger').addClass('alert-success');
                    $('#msg_status').html(data.msg);
                } else {
                    $('#msg_status').removeClass('hide alert-success').addClass('alert-danger');
                    $('#msg_status').html(data.msg);
                }

                $table_feedback.bootstrapTable('refresh', {silent: true});
            });

            aguarde.ocultar();
        };

        /*
         * Solicita a exclusão do projeto
         */
        this.excluir = function () {
            aguarde.mostrar();

            $.ajax({
                url: '<?= base_url('feedback/excluir') ?>',
                data: 'id=' + feedback,
                dataType: 'json',
                type: 'post'
            }).done(function (data) {

                if (data.status) {
                    $('#msg_status').removeClass('hide alert-danger').addClass('alert-success');
                    $('#msg_status').html(data.msg);
                } else {
                    $('#msg_status').removeClass('hide alert-success').addClass('alert-danger');
                    $('#msg_status').html(data.msg);
                }

                $table_feedback.bootstrapTable('refresh', {silent: true});
            });

            aguarde.ocultar();
        };
    };

    feedback = new Feedback();

    var buscaRelacaoFeedback = function (params) {
        var data = JSON.parse(params.data);

        $.ajax({
            url: '<?= base_url('feedback/get_dados_tipo_feedback') ?>',
            data: data,
            type: 'post',
            dataType: 'json'
        }).done(function (data) {
            params.success(data);
        });
    };

    function actionFormatter() {
        return [
            '<button type="button" class="btn btn-default btn-sm editar" title="<?= $edit_feedback ?>">',
            '<i class="fa fa-pencil"></i>',
            '</button>',
            '<button type="button" class="btn btn-default btn-sm excluir" title="<?= $delete_feedback ?>">',
            '<i class="fa fa-trash"></i>',
            '</button>'
        ].join('');
    }

    window.actionEvents = {
        'click .editar': function (e, value, row, index) {
            aguarde.mostrar();
            feedback.setAlterar();
            var id_feedback = row.id;

            $.ajax({
                url: '<?= base_url('feedback/get_feedback') ?>',
                data: 'feedback=' + id_feedback,
                dataType: 'json',
                type: 'post'
            }).done(function (data) {
                $('input[name=input_id]').val(data.id);
                $('input[name=input_nome]').val(data.nome);
                $('input[name=input_abreviatura]').val(data.abreviatura);
                $('input[name=input_descontar]').prop('checked', data.descontar === true);
                $('textarea[name=text_descricao]').val(data.descricao);
            });

            $formulario_cadastro.dialog('option', 'title', '<?= $title_update_feedback ?>');
            $('#dialog_feedback + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $button_update_feedback ?>');
            $formulario_cadastro.dialog('open');

            aguarde.ocultar();
        },
        'click .excluir': function (e, value, row, index) {
            $dialog_excluir.dialog('open');

            var id_feedback = row.id;

            feedback.setExcluir(id_feedback);
        }
    };

    $(document).ready(function () {
        $table_feedback = $('#feedback');

        /*
         * Gera botão de cadastrar usuário e ação de clica-lo
         */
        $('button[type=button][name=cadastrar]').button({
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        }).on('click', function () {
            aguarde.mostrar();
            feedback.setCadastrar();

            $('input, textarea').val('');
            $('input[type=hidden]').val(0);

            $formulario_cadastro.dialog('option', 'title', '<?= $title_dialog_add_feedback ?>');
            $('#dialog_feedback + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $button_add_feedback ?>');
            $formulario_cadastro.dialog('open');

            aguarde.ocultar();
        });

        /*
         * Abre dialog para cadastro e alteraçao de feedback
         */
        $formulario_cadastro = $('#dialog_feedback').dialog({
            autoOpen: false,
            closeOnEscape: false,
            modal: true,
            width: '90%',
            height: $(window).height() * 0.95,
            buttons: [
                {
                    text: '<?= $button_add_feedback ?>',
                    icons: {
                        primary: 'ui-icon-disk'
                    },
                    click: function () {
                        feedback.submitFormulario();
                        $(this).dialog('close');
                    }
                },
                {
                    text: '<?= $cancel_add_or_update_feedback ?>',
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ],
            position: {my: 'center', at: 'center', of: window}
        });

        /*
         * dialog solicitando confirmação para exclusão.
         */
        $dialog_excluir = $('#alerta_exclusao').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            buttons: [
                {
                    text: '<?= $button_confirm_delete_feedback ?>',
                    icons: {
                        primary: 'ui-icon-trash'
                    },
                    click: function () {
                        feedback.excluir();
                        $(this).dialog('close');
                    }
                },
                {
                    text: '<?= $button_cancel_delete_feedback ?>',
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ]
        });

        /*
         * Cria dialog para exibir mensagens
         */
        $('#alert').dialog({
            autoOpen: false,
            modal: true,
            buttons: [
                {
                    text: '<?= $confirm_alert_feedback ?>',
                    icons: {
                        primary: 'ui-icon-check'
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ]
        }).removeClass('hidden');

    });

</script>

<style type="text/css">
    .editar {
        margin-right: 5px;
    }

    td:first-child {
        text-align: center;
    }
</style>

<div class="container">

    <div class="row">
        <div id="msg_status" class="alert hide text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar">
            <?= $button_add_feedback ?>
        </button>
    </div>

    <div class="row">

        <table
            id="feedback"
            data-toggle="table"
            data-ajax="buscaRelacaoFeedback"
            data-side-pagination="server"
            data-pagination="true"
            data-method="post"
            data-page-list="[5, 10, 20, 50, 100, 200]"
            data-locale="pt-BR"
            data-search="true"
            data-sort-name="id"
            data-sort-order="asc">
            <thead>
                <tr>
                    <th data-field="action" data-formatter="actionFormatter" data-events="actionEvents"></th>
                    <th data-field="id" data-sortable="true"><?= $table_feedback_column_id ?></th>
                    <th data-field="nome" data-sortable="true"><?= $table_feedback_column_name_feedback ?></th>
                    <th data-field="abreviatura" data-sortable="true"><?= $table_feedback_column_abbreviation_feedback ?></th>
                    <th data-field="descontar" data-sortable="true"><?= $table_feedback_column_cashing_total_time_feedback ?></th>
                    <th data-field="descricao" data-sortable="true"><?= $table_feedback_description_feedback ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div id="alerta_exclusao" title="<?= $title_dialog_confirm_remove_feedback ?>">
    <p class="text-danger">
        <?= $info_before_remove_feedback ?>
    </p>
</div>

<div id="alert" class="hidden" title="<?= $title_dialog_attention ?>">
    <p id="msg"></p>
</div>