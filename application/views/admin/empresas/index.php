
<script type="text/javascript" src="<?= base_url('static/jquery-mask-plugin/js/jquery.mask.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table-locale-all.min.js') ?>"></script>

<link href="<?= base_url('static/bootstrap-table/css/bootstrap-table.min.css') ?>" rel="stylesheet">

<script type="text/javascript">

    /*
     * Instancia objeto para exiber mensagem de aguarde.
     */
    var aguarde = new Aguarde();

    var Empresas = function () {

        var formulario = ''; // cadastrar ou alterar
        var empresa = 0;

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
         * @param {int} id_empresa Código do feedback
         */
        this.setExcluir = function (id_empresa) {
            empresa = id_empresa;
        };

        /**
         * Envia formulario para cadastro ou alteração
         */
        this.submitFormulario = function () {
            aguarde.mostrar();

            $.ajax({
                url: '<?= base_url() . '/empresas/' ?>' + formulario,
                data: $('form[name=formulario]').serialize(),
                dataType: 'JSON',
                type: 'POST'
            }).done(function (data) {

                if (data.status) {
                    $('#msg_status').removeClass('hidden alert-danger').addClass('alert-success');
                    $('#msg_status').html(data.msg);
                } else {
                    $('#msg_status').removeClass('hidden alert-success').addClass('alert-danger');
                    $('#msg_status').html(data.msg);
                }

                $table_empresas.bootstrapTable('refresh', {silent: true});
                aguarde.ocultar();
            });

        };

        /*
         * Solicita a exclusão do projeto
         */
        this.excluir = function () {
            aguarde.mostrar();

            $.ajax({
                url: '<?= base_url() . '/empresas/excluir' ?>',
                data: 'id=' + empresa,
                dataType: 'json',
                type: 'post'
            }).done(function (data) {

                if (data.status) {
                    $('#msg_status').removeClass('hidden alert-danger').addClass('alert-success');
                    $('#msg_status').html(data.msg);
                } else {
                    $('#msg_status').removeClass('hidden alert-success').addClass('alert-danger');
                    $('#msg_status').html(data.msg);
                }

                $table_empresas.bootstrapTable('refresh', {silent: true});
                aguarde.ocultar();
            });
        };
    };

    empresa = new Empresas();

    var buscaRelacaoEmpresas = function (params) {
        var data = JSON.parse(params.data);

        $.ajax({
            url: '<?= base_url('empresas/get_empresas') ?>',
            data: data,
            type: 'post',
            dataType: 'json'
        }).done(function (data) {
            params.success(data);
        });
    };

    function actionFormatter() {
        return [
            '<button type="button" class="btn btn-default btn-sm editar" title="<?= $edit_businesses ?>">',
            '<i class="fa fa-pencil"></i>',
            '</button>',
            '<button type="button" class="btn btn-default btn-sm excluir" title="<?= $del_businesses ?>">',
            '<i class="fa fa-trash"></i>',
            '</button>'
        ].join('');
    }

    window.actionEvents = {
        'click .editar': function (e, value, row, index) {
            aguarde.mostrar();

            empresa.setAlterar();

            var id = row.id;

            $.ajax({
                url: '<?= base_url() . '/empresas/get_dados_empresa' ?>',
                data: 'empresa=' + id,
                dataType: 'json',
                type: 'post'
            }).done(function (data) {
                $('input[name=input_id]').val(data.id);
                $('input[name=input_empresa]').val(data.empresa);
                $('input[name=input_endereco]').val(data.endereco);
                $('input[name=input_telefone_fixo]').val(data.telefone_fixo);
                $('input[name=input_telefone_celular]').val(data.telefone_celular);

                aguarde.ocultar();
            });

            $('#dialog_empresas').dialog('option', 'title', '<?= $update_title_businesses ?>');
            $('#dialog_empresas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $title_button_update_businesses ?>');
            $('#dialog_empresas').dialog('open');
        },
        'click .excluir': function (e, value, row, index) {
            $('#alerta_exclusao').dialog('open');

            var id = row.id;

            empresa.setExcluir(id);
        }
    };

    $(document).ready(function () {

        $table_empresas = $('#empresa');

        /*
         * Gera botão de cadastrar usuário e ação de clica-lo
         */
        $('button[type=button][name=cadastrar]').button({
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        }).on('click', function () {
            aguarde.mostrar();
            empresa.setCadastrar();

            $('#dialog_empresas').dialog('option', 'title', '<?= $title_add_businesses ?>');
            $('#dialog_empresas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $title_button_add_businesses ?>');
            $('#dialog_empresas').dialog('open');

            aguarde.ocultar();
        });

        /*
         * Dialog para cadastro e ediçao de dados da empresa
         */
        $('#dialog_empresas').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            width: '80%',
            height: $(window).height() * 0.75,
            buttons: [
                {
                    text: '<?= $title_button_add_businesses ?>',
                    icons: {
                        primary: 'ui-icon-disk',
                    },
                    click: function () {
                        empresa.submitFormulario();
                        $(this).dialog('close');
                    }
                },
                {
                    text: '<?= $title_button_cancel_businesses ?>',
                    icons: {
                        primary: 'ui-icon-close',
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ],
            close: function () {
                $('form[name=formulario] input[type=text]').val('');
            },
            position: {my: 'center', at: 'center', of: window}
        }).removeClass('hidden');

        /*
         * dialog solicitando confirmação para exclusão.
         */
        $('#alerta_exclusao').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            buttons: [
                {
                    text: '<?= $title_button_remove_businesses ?>',
                    icons: {
                        primary: 'ui-icon-trash'
                    },
                    click: function () {
                        empresa.excluir();
                        $(this).dialog('close');
                    }
                },
                {
                    text: '<?= $title_button_cancel_businesses ?>',
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ]
        });

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
        <div id="msg_status" class="alert hidden text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar"><?= $add_businesses ?></button>
    </div>

    <div class="row">

        <table
            id="empresa"
            data-toggle="table"
            data-ajax="buscaRelacaoEmpresas"
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
                    <th data-field="id" data-sortable="true"><?= $table_column_id_businesses ?></th>
                    <th data-field="empresa" data-sortable="true"><?= $table_column_name_businesses ?></th>
                    <th data-field="endereco" data-sortable="true"><?= $table_column_address_businesses ?></th>
                    <th data-field="telefone_fixo" data-sortable="true"><?= $table_column_telephone_businesses ?></th>
                    <th data-field="telefone_celular" data-sortable="true"><?= $table_column_cell_businesses ?></th>
                </tr>
            </thead>
        </table>

    </div>

</div>

<div id="alerta_exclusao" title="<?= $alert_delete_businesses ?>">
    <p class="ui-state-error-text">
        <?= $confirm_delete_businesses ?>
    </p>
</div>