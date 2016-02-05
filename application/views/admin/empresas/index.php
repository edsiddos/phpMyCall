
<script type="text/javascript" src="<?= base_url('static/jquery-mask-plugin/js/jquery.mask.min.js') ?>"></script>
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
                type: 'POST',
                async: false,
                success: function (data) {

                    if (data.status) {
                        $('#msg_status').removeClass('hidden alert-danger').addClass('alert-success');
                        $('#msg_status').html(data.msg);
                    } else {
                        $('#msg_status').removeClass('hidden alert-success').addClass('alert-danger');
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
                url: '<?= base_url() . '/empresas/excluir' ?>',
                data: 'id=' + empresa,
                dataType: 'json',
                type: 'post',
                async: false,
                success: function (data) {

                    if (data.status) {
                        $('#msg_status').removeClass('hidden alert-danger').addClass('alert-success');
                        $('#msg_status').html(data.msg);
                    } else {
                        $('#msg_status').removeClass('hidden alert-success').addClass('alert-danger');
                        $('#msg_status').html(data.msg);
                    }
                }
            });

            aguarde.ocultar();
        };
    };

    empresa = new Empresas();


    $(document).ready(function () {

        var datatable = $('#empresa').DataTable({
            ordering: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url() . 'empresas/get_empresas' ?>",
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
                        var html = '<button name="editar" empresa="' + data.id + '"><?= $edit_businesses ?></button>';
                        html += '<button name="excluir" empresa="' + data.id + '"><?= $del_businesses ?></button>';
                        return html;
                    }
                },
                {data: "id"},
                {data: "empresa"},
                {data: "endereco"},
                {data: "telefone_fixo"},
                {data: "telefone_celular"}
            ]
        }).on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                datatable.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
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

                empresa.setAlterar();

                var id = $(this).attr('empresa');

                $.ajax({
                    url: '<?= base_url() . '/empresas/get_dados_empresa' ?>',
                    data: 'empresa=' + id,
                    dataType: 'json',
                    type: 'post',
                    async: false,
                    success: function (data) {
                        $('input[name=input_id]').val(data.id);
                        $('input[name=input_empresa]').val(data.empresa);
                        $('input[name=input_endereco]').val(data.endereco);
                        $('input[name=input_telefone_fixo]').val(data.telefone_fixo);
                        $('input[name=input_telefone_celular]').val(data.telefone_celular);
                    }
                });

                $('#dialog_empresas').dialog('option', 'title', '<?= $update_title_businesses ?>');
                $('#dialog_empresas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $title_button_update_businesses ?>');
                $('#dialog_empresas').dialog('open');

                aguarde.ocultar();
            });

            /*
             * Cria botão de exclusão e adiciona evento ao clica-lo
             */

            $('button[name=excluir]').button({
                text: false,
                icons: {
                    primary: 'ui-icon-trash'
                }
            }).on('click', function () {
                $('#alerta_exclusao').dialog('open');

                var id = $(this).attr('empresa');

                empresa.setExcluir(id);
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
                        datatable.ajax.reload();
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
                        datatable.ajax.reload();
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


<div class="container">

    <div class="row">
        <div id="msg_status" class="alert hidden text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar"><?= $add_businesses ?></button>
    </div>

    <div class="row">

        <table id="empresa" class="display responsive nowrap" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th><?= $table_column_id_businesses ?></th>
                    <th><?= $table_column_name_businesses ?></th>
                    <th><?= $table_column_address_businesses ?></th>
                    <th><?= $table_column_telephone_businesses ?></th>
                    <th><?= $table_column_cell_businesses ?></th>
                    <th></th>
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