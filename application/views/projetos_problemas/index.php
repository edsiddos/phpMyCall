
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


    var ProjetoProblemas = function () {

        var formulario = ''; // cadastrar ou alterar
        var projeto_problema = 0;
        var projeto = 0;

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
         * @param {int} id_projeto_problema Código do projeto tipo de problema
         * @param {int} id_projeto Código do projeto
         */
        this.setProjetoProblemaExcluir = function (id_projeto_problema, id_projeto) {
            projeto_problema = id_projeto_problema;
            projeto = id_projeto;
        };

        /**
         * Envia formulario para cadastro ou alteração
         */
        this.submitFormulario = function () {
            aguarde.mostrar();
            multi.destinySelect();

            $.ajax({
                url: '<?= base_url() . 'projetos_problemas/' ?>' + formulario,
                data: $('form[name=projetos_problemas]').serialize(),
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
        this.excluirProjetoProblema = function () {
            aguarde.mostrar();

            $.ajax({
                url: '<?= base_url() . 'projetos_problemas/excluir' ?>',
                data: 'projeto_problema=' + projeto_problema + '&projeto=' + projeto,
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

    var projeto = new ProjetoProblemas();

    $(document).ready(function () {

        multi = new MultiSelectTransfer('#relacao_usuarios', {name_select_destiny: 'participantes'});
        multi.init();

        var datatable = $('#relacao_projeto_problemas').DataTable({
            ordering: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url() . 'projetos_problemas/lista_projeto_problemas' ?>",
                type: "POST"
            },
            language: {
                url: "<?= base_url() . 'static/js/datatable/pt_br.json' ?>"
            },
            columns: [
                {"data": "id"},
                {"data": "id_projeto"},
                {"data": "projeto"},
                {"data": "problema"}
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

        /*
         * Gera botão de cadastrar usuário e ação de clica-lo
         */
        $('button[type=button][name=cadastrar]').button({
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        }).on('click', function () {
            aguarde.mostrar();

            multi.setOrigin();
            multi.setDestiny();
            projeto.setCadastrar();

            $('input, textarea').val('');
            $('input[type=hidden]').val(0);

            $('#dialog_projetos_problemas').dialog('option', 'title', 'Cadastrar projeto tipo de problema');
            $('#dialog_projetos_problemas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('Cadastrar');
            $('#dialog_projetos_problemas').dialog('open');

            aguarde.ocultar();
        });

        /*
         * Gera botão para edição de projeto tipo problema 
         */

        $('button[type=button][name=editar]').button({
            icons: {
                primary: 'ui-icon-pencil'
            }
        }).on('click', function () {
            aguarde.mostrar();

            projeto.setAlterar();

            var dados = datatable.row('.selected').data();

            var dados = datatable.row('.selected').data();

            if (typeof dados === 'object' && dados.id !== null) {
                $.ajax({
                    url: '<?= base_url() . 'projetos_problemas/get_dados_projeto_problemas' ?>',
                    data: 'id=' + dados.id,
                    dataType: 'json',
                    type: 'post',
                    async: false,
                    success: function (data) {
                        $('input[name=input_projeto]').val(data.id_projeto);
                        $('input[name=input_problema]').val(data.id_problema);
                        $('input[name=input_projeto_problema]').val(dados.id);
                        $('input[name=input_nome_projeto]').val(data.nome_projeto).focusout();
                        $('textarea[name=text_projeto]').val(data.descricao_projeto);
                        $('input[name=input_nome_problema]').val(data.nome_problema);
                        $('textarea[name=text_descricao]').val(data.descricao);
                        $('input[name=input_resposta]').val(data.resposta);
                        $('input[name=input_solucao]').val(data.solucao);
                    }
                });

                $('#dialog_projetos_problemas').dialog('option', 'title', 'Alterar projeto tipo de problema');
                $('#dialog_projetos_problemas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('Alterar');
                $('#dialog_projetos_problemas').dialog('open');
            } else {
                $('#msg').html('Selecione um projeto tipo de problema e tente novamente.');
                $('#alert').dialog('open');
            }

            aguarde.ocultar();
        });

        $('button[type=button][name=excluir]').button({
            icons: {
                primary: 'ui-icon-close'
            }
        }).on('click', function () {
            $('#alerta_exclusao').dialog('open');

            var dados = datatable.row('.selected').data();

            projeto.setProjetoProblemaExcluir(dados.id, dados.id_projeto);
        });


        /*
         * Gera dialog para inserção dos dados do projeto
         * e participantes do projeto
         */
        $('#dialog_projetos_problemas').dialog({
            autoOpen: false,
            closeOnEscape: false,
            modal: true,
            width: '80%',
            height: $(window).height() * 0.95,
            buttons: [
                {
                    text: 'Salvar',
                    icons: {
                        primary: 'ui-icon-disk'
                    },
                    click: function () {
                        $(this).dialog('close');
                        projeto.submitFormulario();
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
            position: {my: 'center', at: 'center', of: window}
        }).removeClass('hidden');

        /*
         * Gera dialog para confirmação de exclusão
         */
        $('#alerta_exclusao').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            buttons: [
                {
                    text: "Excluir",
                    icons: {
                        primary: 'ui-icon-trash'
                    },
                    click: function () {
                        $(this).dialog('close');
                        projeto.excluirProjetoProblema();
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
            position: {my: 'center', at: 'center', of: window}
        }).removeClass('hidden');

        /*
         * Cria dialog para exibir mensagens
         */
        $('#alert').dialog({
            autoOpen: false,
            modal: true,
            buttons: [
                {
                    text: 'OK',
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
        <div id="msg_status" class="alert hidden text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar">Cadastrar</button>
        <button type="button" name="editar" id="editar">Editar</button>
        <button type="button" name="excluir" id="excluir">Excluir</button>
    </div>

    <div class="row">

        <table id="relacao_projeto_problemas" class="display responsive nowrap" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Projeto</th>
                    <th>Projeto</th>
                    <th>Problema</th>
                </tr>
            </thead>

        </table>

    </div>

</div>

<div id="alerta_exclusao" class="hidden" title="Alerta de remoção">
    <p class="ui-state-error-text">
        Deseja realmente remover o projeto / tipo de problema?
    </p>
</div>

<div id="alert" class="hidden" title="Atenção">
    <p id="msg"></p>
</div>