
<script type="text/javascript" src="<?= base_url('static/jquery-mask-plugin/js/jquery.mask.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-simple-multiselect/js/bootstrap-transfer.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table-locale-all.min.js') ?>"></script>

<link href="<?= base_url('static/bootstrap-simple-multiselect/css/bootstrap-transfer.css') ?>" rel="stylesheet">
<link href="<?= base_url('static/bootstrap-table/css/bootstrap-table.min.css') ?>" rel="stylesheet">

<script type="text/javascript">

    /*
     * Instancia objeto para exiber mensagem de aguarde.
     */
    var aguarde = new Aguarde();

    /*
     * Cria classe para manipula de usuarios responsavel por:
     *      * Criar tela com relação de usuários;
     *      * Cadastrar, alterar e excluir usuários.
     */
    var Usuario = function () {

        var formulario = ''; // cadastro ou alteração
        var id_usuario = 0;

        /*
         * Busca dados do usuário para alteração do cadastro
         * e preenche dialog com formulário
         */
        this.getDadosUsuario = function (id) {

            $.ajax({
                url: '<?= base_url() . 'usuarios/get_dados_usuarios' ?>',
                data: 'usuario=' + id,
                dataType: 'JSON',
                type: 'POST',
            }).done(function (json) {
                $('input[name=input_id]').val(json.id);
                $('input[name=input_nome]').val(json.nome);
                $('input[name=input_usuario]').val(json.usuario);
                $('input[name=input_senha]').val('');
                $('input[name=input_email]').val(json.email);
                $('input[name=input_telefone]').val(json.telefone);
                $('select[name=select_perfil]').val(json.perfil).change();
                $('select[name=select_empresa]').val(json.empresa);

                $multi.set_values(json.projeto.participa);
            });

        };

        this.setFormularioCadastro = function () {
            formulario = 'cadastro';
        };

        this.setFormularioAlteracao = function () {
            formulario = 'alteracao';
        };

        /*
         * Método chamado oa clicar no botão Cadastrar ou Alterar
         */
        this.submitFormularioUsuario = function () {
            aguarde.mostrar();
            if (formulario === 'cadastro') {
                cadastrar();
            } else if (formulario === 'alteracao') {
                alterar();
            }

            aguarde.ocultar();
        };

        /*
         * Se o id do usuário que será excluido
         */
        this.setIDUsuario = function (id) {
            id_usuario = id;
        };

        /*
         * Exclui usuário após confirmação
         */
        this.excluirUsuario = function () {
            aguarde.mostrar();

            $.ajax({
                url: '<?= base_url() . 'usuarios/remove_usuario' ?>',
                data: 'id=' + id_usuario,
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

                $table_usuarios.bootstrapTable('refresh', {silent: true});
            });

            aguarde.ocultar();
        };

        /*
         * Envia dados para cadastro de novo usuário
         * e logo após mostra todos os usuários cadastrados
         * e se a operação foi realizada com sucesso
         */
        var cadastrar = function () {
            // Seleciona os projetos escolhidos
            $('select[name="input_projetos[]"] option').prop('selected', true);

            $.ajax({
                url: '<?= base_url() . 'usuarios/novo_usuario' ?>',
                data: $('#form_usuario').serialize(),
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

                $table_usuarios.bootstrapTable('refresh', {silent: true});
            });
        };

        /*
         * Envia dados para alteração de usuário
         * e logo após mostra todos os usuários cadastrados
         * e se a operação foi realizada com sucesso
         */
        var alterar = function () {
            // Seleciona os projetos escolhidos
            $('select[name="input_projetos[]"] option').prop('selected', true);

            $.ajax({
                url: '<?= base_url() . 'usuarios/atualiza_usuario' ?>',
                data: $('#form_usuario').serialize(),
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

                $table_usuarios.bootstrapTable('refresh', {silent: true});
            });
        };
    };


    var usuario = new Usuario();

    var buscaRelacaoUsuarios = function (params) {
        var data = JSON.parse(params.data);

        $.ajax({
            url: '<?= base_url() . 'usuarios/get_usuarios' ?>',
            data: data,
            type: 'post',
            dataType: 'json'
        }).done(function (data) {
            params.success(data);
            params.complete();
        });
    };

    function actionFormatter() {
        return [
            '<button type="button" class="btn btn-default btn-sm editar" title="<?= $edit_user ?>">',
            '<i class="fa fa-pencil"></i>',
            '</button>',
            '<button type="button" class="btn btn-default btn-sm excluir" title="<?= $delete_user ?>">',
            '<i class="fa fa-trash"></i>',
            '</button>'
        ].join('');
    }

    window.actionEvents = {
        'click .editar': function (e, value, row, index) {
            aguarde.mostrar();
            var id = row.id;

            $multi.set_values([]);
            usuario.getDadosUsuario(id);
            usuario.setFormularioAlteracao();

            formulario_cadastro.dialog('option', 'title', '<?= $title_update_user ?>');
            $('#formulario_cadastro + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $title_button_update_user ?>');
            formulario_cadastro.dialog('open');

            aguarde.ocultar();
        },
        'click .excluir': function (e, value, row, index) {
            var id = row.id;

            usuario.setIDUsuario(id);
            alerta_exclusao.dialog('open');
        }
    };

    $(document).ready(function () {

        $table_usuarios = $('#usuarios');

        $multi = $('#select_projeto').bootstrapTransfer({
            remaining_name: 'opcoes_projetos',
            target_name: 'input_projetos[]',
            hilite_selection: true
        });

        $.ajax({
            url: '<?= base_url('usuarios/get_projetos') ?>',
            dataType: 'JSON',
            type: 'POST'
        }).done(function (data) {
            $multi.populate(data.projeto);
        });

        /*
         * Gera botão de cadastrar usuário e ação de clica-lo
         */
        $('button[name=cadastrar]').button({
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        }).on('click', function () {
            aguarde.mostrar();
            usuario.setFormularioCadastro();
            $multi.set_values([]);

            $('input[name=input_id]').val(0);
            $('input[name=input_nome]').val('');
            $('input[name=input_usuario]').val('');
            $('input[name=input_senha]').val('');
            $('input[name=input_email]').val('');
            $('input[name=input_telefone]').val('');
            $('select[name=select_perfil]').val('').change();
            $('select[name=select_empresa]').val('');

            formulario_cadastro.dialog('option', 'title', '<?= $title_add_user ?>');
            $('#formulario_cadastro + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $title_button_add_user ?>');
            formulario_cadastro.dialog('open');
            aguarde.ocultar();
        });

        /*
         * Gera dialog para inserção de dados cadastro e alteração de usuários
         */

        formulario_cadastro = $('#formulario_cadastro').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            width: '85%',
            height: 600,
            buttons: [
                {
                    text: '<?= $title_button_add_user ?>',
                    icons: {
                        primary: 'ui-icon-disk'
                    },
                    click: function () {
                        $(this).dialog('close');
                        usuario.submitFormularioUsuario();
                    }
                },
                {
                    text: '<?= $title_button_cancel_user ?>',
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ],
            position: {my: 'top', at: 'top', of: window}
        }).removeClass('hidden');

        /*
         * Cria dialog solicitação de confirmação
         * para exclusão de usuário
         */
        alerta_exclusao = $('#alerta_exclusao').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            buttons: [
                {
                    text: '<?= $title_button_remove_user ?>',
                    icons: {
                        primary: 'ui-icon-trash'
                    },
                    click: function () {
                        usuario.excluirUsuario();
                        $(this).dialog('close');
                    }
                },
                {
                    text: '<?= $title_button_cancel_user ?>',
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ]
        }).removeClass('hidden');

        /*
         * Cria dialog para exibir mensagens
         */
        $('#alert').dialog({
            autoOpen: false,
            modal: true,
            buttons: [
                {
                    text: '<?= $title_button_ok_user ?>',
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
        <div id="msg_status" class="alert hidden text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar">
            <?= $add_user ?>
        </button>
    </div>

    <div class="row">
        <table
            id="usuarios"
            data-toggle="table"
            data-ajax="buscaRelacaoUsuarios"
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
                    <th data-field="id" data-sortable="true">
                        <?= $table_id_user ?>
                    </th>
                    <th data-field="nome" data-sortable="true">
                        <?= $table_username_user ?>
                    </th>
                    <th data-field="usuario" data-sortable="true">
                        <?= $table_user_user ?>
                    </th>
                    <th data-field="perfil" data-sortable="true">
                        <?= $table_profile_user ?>
                    </th>
                    <th data-field="email" data-sortable="true">
                        <?= $table_email_user ?>
                    </th>
                </tr>
            </thead>

        </table>
    </div>

</div>

<div id="alerta_exclusao" class="hidden" title="<?= $alert_delete_user ?>">
    <p>
        <?= $message_alert_delete_user ?>
    </p>
</div>

<div id="alert" class="hidden" title="<?= $title_alert_user ?>">
    <p id="msg"></p>
</div>