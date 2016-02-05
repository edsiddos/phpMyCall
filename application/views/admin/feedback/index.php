
<script type="text/javascript" src="<?= base_url('static/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/datatables/js/dataTables.jqueryui.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/datatables-responsive/js/dataTables.responsive.js') ?>"></script>

<link href="<?= base_url('static/datatables/css/dataTables.jqueryui.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('static/datatables-responsive/css/responsive.jqueryui.css') ?>" rel="stylesheet">

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
                type: 'POST',
                async: false,
                success: function (data) {

                    if (data.status) {
                        $('#msg_status').removeClass('hide alert-danger').addClass('alert-success');
                        $('#msg_status').html(data.msg);
                    } else {
                        $('#msg_status').removeClass('hide alert-success').addClass('alert-danger');
                        $('#msg_status').html(data.msg);
                    }
                }
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
                type: 'post',
                async: false,
                success: function (data) {

                    if (data.status) {
                        $('#msg_status').removeClass('hide alert-danger').addClass('alert-success');
                        $('#msg_status').html(data.msg);
                    } else {
                        $('#msg_status').removeClass('hide alert-success').addClass('alert-danger');
                        $('#msg_status').html(data.msg);
                    }
                }
            });

            aguarde.ocultar();
        };
    };

    feedback = new Feedback();


    $(document).ready(function () {

        var datatable = $('#feedback').DataTable({
            ordering: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('feedback/get_dados_tipo_feedback') ?>",
                type: "POST"
            },
            language: {
                url: "<?= base_url('static/datatables/js/pt_br.json') ?>"
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    render: function (data) {
                        var html = '<button name="editar" feedback="' + data.id + '"><?= $edit_feedback ?></button>';
                        html += '<button name="excluir" feedback="' + data.id + '"><?= $delete_feedback ?></button>';
                        return html;
                    }
                },
                {data: "id"},
                {data: "nome"},
                {data: "abreviatura"},
                {data: "descontar"},
                {data: "descricao"}
            ]
        });

        datatable.on('draw', function () {
            /*
             * Gera botão para edição de feedback
             */
            $('button[name=editar]').button({
                text: false,
                icons: {
                    primary: 'ui-icon-pencil'
                }
            }).on('click', function () {
                aguarde.mostrar();

                feedback.setAlterar();

                var id_feedback = $(this).attr('feedback');

                $.ajax({
                    url: '<?= base_url('feedback/get_feedback') ?>',
                    data: 'feedback=' + id_feedback,
                    dataType: 'json',
                    type: 'post',
                    async: false,
                    success: function (data) {
                        $('input[name=input_id]').val(data.id);
                        $('input[name=input_nome]').val(data.nome);
                        $('input[name=input_abreviatura]').val(data.abreviatura);
                        $('input[name=input_descontar]').prop('checked', data.descontar === true);
                        $('textarea[name=text_descricao]').val(data.descricao);
                    }
                });

                $('#dialog_feedback').dialog('option', 'title', '<?= $title_update_feedback ?>');
                $('#dialog_feedback + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $button_update_feedback ?>');
                $('#dialog_feedback').dialog('open');

                aguarde.ocultar();
            });

            /*
             * Cria botão de exclusão e adiciona evento ao clica-lo
             */

            $('button[name=excluir]').button({
                text: false,
                icons: {
                    primary: 'ui-icon-close'
                }
            }).on('click', function () {
                $('#alerta_exclusao').dialog('open');

                var id_feedback = $(this).attr('feedback');

                feedback.setExcluir(id_feedback);
            });
        });

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

            $('#dialog_feedback').dialog('option', 'title', '<?= $title_dialog_add_feedback ?>');
            $('#dialog_feedback + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $button_add_feedback ?>');
            $('#dialog_feedback').dialog('open');

            aguarde.ocultar();
        });

        /*
         * Abre dialog para cadastro e alteraçao de feedback
         */
        $('#dialog_feedback').dialog({
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
                        datatable.ajax.reload();
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
        $('#alerta_exclusao').dialog({
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
                        datatable.ajax.reload();
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

<div class="container">

    <div class="row">
        <div id="msg_status" class="alert hide text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar">Cadastrar</button>
    </div>

    <div class="row">

        <table id="feedback" class="display responsive nowrap" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th class="no-sort"></th>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Abreviatura</th>
                    <th>Descontar tempo total</th>
                    <th>Descrição</th>
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