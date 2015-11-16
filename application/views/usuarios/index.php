
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/jquery.dataTables.min.js' ?>"></script>
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/dataTables.jqueryui.min.js' ?>"></script>
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/dataTables.responsive.min.js' ?>"></script>

<script type="text/javascript" src="<?= base_url() . 'static/js/simple-multi-select.js' ?>"></script>
<script type="text/javascript" src="<?= base_url() . 'static/js/jquery.mask.min.js' ?>"></script>


<link href="<?= base_url() . 'static/css/simple-multi-select.css' ?>" rel="stylesheet" />
<link href="<?= base_url() . 'static/css/datatable/dataTables.jqueryui.min.css' ?>" rel="stylesheet">
<link href="<?= base_url() . 'static/css/datatable/responsive.jqueryui.min.css' ?>" rel="stylesheet">

<script type="text/javascript">

    /*
     * Instancia objeto para exiber mensagem de aguarde.
     */
    var aguarde = new Aguarde('<?= base_url() . 'static/img/change.gif' ?>');

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
                async: false,
                success: function (json) {
                    $('input[name=input_id]').val(json.id);
                    $('input[name=input_nome]').val(json.nome);
                    $('input[name=input_usuario]').val(json.usuario);
                    $('input[name=input_senha]').val('');
                    $('input[name=input_email]').val(json.email);
                    $('input[name=input_telefone]').val(json.telefone);
                    $('select[name=select_perfil]').val(json.perfil).change();
                    $('select[name=select_empresa]').val(json.empresa);

                    multi.setOrigin(json.projeto.projeto);
                    multi.setDestiny(json.projeto.participa);
                }
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
         * Envia dados para cadastro de novo usuário
         * e logo após mostra todos os usuários cadastrados
         * e se a operação foi realizada com sucesso
         */
        var cadastrar = function () {
            // Seleciona os projetos escolhidos
            multi.destinySelect();

            $.ajax({
                url: '<?= base_url() . 'usuarios/novo_usuario' ?>',
                data: $('#form_usuario').serialize(),
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
        };

        /*
         * Envia dados para alteração de usuário
         * e logo após mostra todos os usuários cadastrados
         * e se a operação foi realizada com sucesso
         */
        var alterar = function () {
            // Seleciona os projetos escolhidos
            multi.destinySelect();

            $.ajax({
                url: '<?= base_url() . 'usuarios/atualiza_usuario' ?>',
                data: $('#form_usuario').serialize(),
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
        };
    };


    var usuario = new Usuario();


    $(document).ready(function () {

        var datatable = $('#usuarios').DataTable({
            ordering: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url() . 'usuarios/get_usuarios' ?>",
                type: "POST"
            },
            language: {
                url: "<?= $js_path_translation_datatable ?>"
            },
            columns: [
                {"data": "id"},
                {"data": "nome"},
                {"data": "usuario"},
                {"data": "perfil"},
                {"data": "email"},
                {
                    data: null,
                    ordering: false,
                    render: function (data) {
                        var html = '<button type="button" name="editar" usuario_id="' + data.id + '"><?= $editar_usuario ?></button>';
                        html += '<button type="button" name="excluir" usuario_id="' + data.id + '"><?= $excluir_usuario ?></button>';

                        return html;
                    }
                }
            ]
        });

        datatable.on('draw', function () {
            /*
             * Função chamada para gerar botões de editar usuário
             * e ação ao clicar
             */
            $("button[name=editar]").button({
                text: false,
                icons: {
                    primary: 'fa fa-pencil'
                }
            }).on('click', function () {
                aguarde.mostrar();
                var id = $(this).attr('usuario_id');

                usuario.getDadosUsuario(id);
                usuario.setFormularioAlteracao();

                $('#formulario_cadastro').dialog('option', 'title', '<?= $titulo_alterar_usuario ?>');
                $('#formulario_cadastro + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $titulo_button_alterar_usuario ?>');
                $('#formulario_cadastro').dialog('open');

                aguarde.ocultar();
            });

            /*
             * Função que gera botões de excluir usuários
             * e aplica ação a clica-lo.
             */

            $("button[name=excluir]").button({
                text: false,
                icons: {
                    primary: 'fa fa-trash'
                }
            }).on('click', function () {
                var id = $(this).attr('usuario_id');

                usuario.setIDUsuario(id);
                $('#alerta_exclusao').dialog('open');
            });
        });


        multi = new MultiSelect('#select_projeto', {name_select_destiny: 'input_projetos'});
        multi.init();

        /*
         * Gera botão de cadastrar usuário e ação de clica-lo
         */
        $('button[name=cadastrar]').button({
            icons: {
                primary: 'fa fa-plus-circle'
            }
        }).on('click', function () {
            aguarde.mostrar();
            usuario.setFormularioCadastro();

            $.ajax({
                url: '<?= base_url() . 'usuarios/get_projetos' ?>',
                dataType: 'JSON',
                async: false,
                success: function (data) {
                    multi.setOrigin(data.projeto);
                    multi.setDestiny(data.participa);
                }
            });

            $('input[name=input_id]').val(0);
            $('input[name=input_nome]').val('');
            $('input[name=input_usuario]').val('');
            $('input[name=input_senha]').val('');
            $('input[name=input_email]').val('');
            $('input[name=input_telefone]').val('');
            $('select[name=select_perfil]').val('').change();
            $('select[name=select_empresa]').val('');

            $('#formulario_cadastro').dialog('option', 'title', '<?= $titulo_cadastrar_usuario ?>');
            $('#formulario_cadastro + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $titulo_button_cadastrar_usuario ?>');
            $('#formulario_cadastro').dialog('open');
            aguarde.ocultar();
        });

        /*
         * Gera dialog para inserção de dados cadastro e alteração de usuários
         */

        $('#formulario_cadastro').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            width: '85%',
            height: 600,
            buttons: [
                {
                    text: '<?= $titulo_button_cadastrar_usuario ?>',
                    icons: {
                        primary: 'fa fa-save'
                    },
                    click: function () {
                        $(this).dialog('close');
                        usuario.submitFormularioUsuario();
                        datatable.ajax.reload();
                    }
                },
                {
                    text: '<?= $titulo_button_cancelar_usuario ?>',
                    icons: {
                        primary: 'fa fa-close'
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
        $('#alerta_exclusao').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            buttons: [
                {
                    text: '<?= $titulo_button_excluir_usuario ?>',
                    icons: {
                        primary: 'fa fa-trash'
                    },
                    click: function () {
                        usuario.excluirUsuario();
                        $(this).dialog('close');
                        datatable.ajax.reload();
                    }
                },
                {
                    text: '<?= $titulo_button_cancelar_usuario ?>',
                    icons: {
                        primary: 'fa fa-close'
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
                    text: '<?= $titulo_button_ok ?>',
                    icons: {
                        primary: 'fa fa-check'
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
        <div id="msg_status" class="alert hidden text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar">
            <?= $cadastrar_usuario ?>
        </button>
    </div>

    <div class="row">
        <table id="usuarios" class="display responsive nowrap" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>
                        <?= $tabela_usuarios_id ?>
                    </th>
                    <th>
                        <?= $tabela_usuarios_nome ?>
                    </th>
                    <th>
                        <?= $tabela_usuarios_usuario ?>
                    </th>
                    <th>
                        <?= $tabela_usuarios_perfil ?>
                    </th>
                    <th>
                        <?= $tabela_usuarios_email ?>
                    </th>
                    <th></th>
                </tr>
            </thead>

        </table>
    </div>

</div>

<div id="alerta_exclusao" class="hidden" title="<?= $aviso_exclusao ?>">
    <p>
        <?= $solicita_confirmacao_exclusao ?>
    </p>
</div>

<div id="alert" class="hidden" title="<?= $atencao ?>">
    <p id="msg"></p>
</div>