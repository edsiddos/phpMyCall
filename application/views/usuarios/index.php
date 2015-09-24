
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/jquery.dataTables.min.js' ?>"></script>
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/dataTables.jqueryui.min.js' ?>"></script>
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/dataTables.responsive.min.js' ?>"></script>

<script type="text/javascript" src="<?= base_url() . 'static/js/multi-select-transfer.js' ?>"></script>
<script type="text/javascript" src="<?= base_url() . 'static/js/jquery.mask.min.js' ?>"></script>


<link href="<?= base_url() . 'static/css/multi-select-transfer.css' ?>" rel="stylesheet" />
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
                url: '<?= base_url() . '/Usuarios/getDadosUsuarios' ?>',
                data: 'usuario=' + id,
                dataType: 'JSON',
                type: 'POST',
                async: false,
                success: function (json) {
                    $('#inputID').val(json.id);
                    $('#inputNome').val(json.nome);
                    $('#inputUsuario').val(json.usuario);
                    $('#inputSenha').val('');
                    $('#inputEMail').val(json.email);
                    $('#inputTelefone').val(json.telefone);
                    $('#selectPerfil').val(json.perfil);
                    $('#selectEmpresa').val(json.empresa);
                    $('#selectPerfil').change();

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
                url: '<?= base_url() . '/Usuarios/removeUsuario' ?>',
                data: 'id=' + id_usuario,
                dataType: 'JSON',
                type: 'POST',
                async: false,
                success: function (data) {
                    if (data.status) {
                        $('#msg-status').removeClass('hidden alert-danger').addClass('alert-success');
                        $('#msg-status').html(data.msg);
                    } else {
                        $('#msg-status').removeClass('hidden alert-success').addClass('alert-danger');
                        $('#msg-status').html(data.msg);
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
                url: '<?= base_url() . '/Usuarios/novoUsuario' ?>',
                data: $('#formUsuario').serialize(),
                dataType: 'JSON',
                type: 'POST',
                async: false,
                success: function (data) {
                    if (data.status) {
                        $('#msg-status').removeClass('hidden alert-danger').addClass('alert-success');
                        $('#msg-status').html(data.msg);
                    } else {
                        $('#msg-status').removeClass('hidden alert-success').addClass('alert-danger');
                        $('#msg-status').html(data.msg);
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
                url: '<?= base_url() . '/Usuarios/atualizaUsuario' ?>',
                data: $('#formUsuario').serialize(),
                dataType: 'JSON',
                type: 'POST',
                async: false,
                success: function (data) {
                    if (data.status) {
                        $('#msg-status').removeClass('hidden alert-danger').addClass('alert-success');
                        $('#msg-status').html(data.msg);
                    } else {
                        $('#msg-status').removeClass('hidden alert-success').addClass('alert-danger');
                        $('#msg-status').html(data.msg);
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
                url: "<?= base_url() . 'usuarios/getUsuarios' ?>",
                type: "POST"
            },
            language: {
                url: "<?= base_url() . 'static/js/datatable/pt_br.json' ?>"
            },
            columns: [
                {"data": "id"},
                {"data": "nome"},
                {"data": "usuario"},
                {"data": "perfil"},
                {"data": "email"}
            ]
        }).on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            }
            else {
                datatable.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });

        multi = new MultiSelectTransfer('#selectProjeto', {name_select_destiny: 'inputProjetos'});
        multi.init();

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

            $.ajax({
                url: '<?= base_url() . '/Usuarios/getProjetos' ?>',
                dataType: 'JSON',
                async: false,
                success: function (data) {
                    multi.setOrigin(data.projeto);
                    multi.setDestiny(data.participa);
                }
            });

            $('#inputID').val(0);
            $('#inputNome').val('');
            $('#inputUsuario').val('');
            $('#inputSenha').val('');
            $('#inputEMail').val('');
            $('#inputTelefone').val('');
            $('#selectPerfil').val('');
            $('#selectEmpresa').val('');
            $('#selectPerfil').change();

            $('#formulario_cadastro').dialog('option', 'title', 'Cadastrar usuário');
            $('#formulario_cadastro + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('Cadastrar');
            $('#formulario_cadastro').dialog('open');
            aguarde.ocultar();
        });

        /*
         * Função chamada para gerar botões de editar usuário
         * e ação ao clicar
         */

        $("button[name=editar]").button({
            icons: {
                primary: 'ui-icon-pencil'
            }
        }).on('click', function () {
            aguarde.mostrar();

            var dados = datatable.row('.selected').data();

            usuario.getDadosUsuario(dados.id);
            usuario.setFormularioAlteracao();

            $('#formulario_cadastro').dialog('option', 'title', 'Alterar usuário');
            $('#formulario_cadastro + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('Alterar');
            $('#formulario_cadastro').dialog('open');

            aguarde.ocultar();
        });

        /*
         * Função que gera botões de excluir usuários
         * e aplica ação a clica-lo.
         */

        $("button[name=excluir]").button({
            icons: {
                primary: 'ui-icon-close'
            }
        }).on('click', function () {
            var dados = datatable.row('.selected').data();

            usuario.setIDUsuario(dados.id);
            $('#alertaExclusao').dialog('open');
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
                    text: 'Cadastrar',
                    icons: {
                        primary: 'ui-icon-disk'
                    },
                    click: function () {
                        $(this).dialog('close');
                        usuario.submitFormularioUsuario();
                        datatable.ajax.reload();
                    }
                },
                {
                    text: 'Cancelar',
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
        $('#alertaExclusao').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            buttons: [
                {
                    text: 'Excluir',
                    icons: {
                        primary: 'ui-icon-trash'
                    },
                    click: function () {
                        usuario.excluirUsuario();
                        $(this).dialog('close');
                        datatable.ajax.reload();
                    }
                },
                {
                    text: 'Cancelar',
                    icons: {
                        primary: 'ui-icon-close'
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
        <div id="msg-status" class="alert hidden text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar">Cadastrar</button>
        <button type="button" name="editar" id="editar">Editar</button>
        <button type="button" name="excluir" id="excluir">Excluir</button>
    </div>

    <div class="row">
        <table id="usuarios" class="display responsive nowrap" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Usuário</th>
                    <th>Perfil</th>
                    <th>E-Mail</th>
                </tr>
            </thead>

        </table>
    </div>

</div>

<div id="alertaExclusao" class="hidden" title="Aviso de exclusão">
    <p>Deseja remover este usuário?</p>
</div>