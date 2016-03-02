
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
            $('select[name="participantes[]"] option').prop('selected', true);

            $.ajax({
                url: '<?= base_url() . 'projetos_problemas/' ?>' + formulario,
                data: $('form[name=projetos_problemas]').serialize(),
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

                $tabela_projeto_problema.bootstrapTable('refresh', {silent: true});
            });

            aguarde.ocultar();
        };

        /*
         * Solicita a exclusão do projeto
         */
        this.excluirProjetoProblema = function () {
            aguarde.mostrar();

            $.ajax({
                url: '<?= base_url('projetos_problemas/excluir') ?>',
                data: 'projeto_problema=' + projeto_problema + '&projeto=' + projeto,
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

                $tabela_projeto_problema.bootstrapTable('refresh', {silent: true});
            });

            aguarde.ocultar();
        };
    };

    var projeto = new ProjetoProblemas();

    var buscaRelacaoProjetoProblema = function (params) {
        var data = JSON.parse(params.data);

        $.ajax({
            url: '<?= base_url('projetos_problemas/lista_projeto_problemas') ?>',
            data: data,
            type: 'post',
            dataType: 'json'
        }).done(function (data) {
            params.success(data);
        });
    };

    function actionFormatter() {
        return [
            '<button type="button" class="btn btn-default btn-sm editar" title="<?= $button_edit_project_problem ?>">',
            '<i class="fa fa-pencil"></i>',
            '</button>',
            '<button type="button" class="btn btn-default btn-sm excluir" title="<?= $button_delete_project_problem ?>">',
            '<i class="fa fa-trash"></i>',
            '</button>'
        ].join('');
    }

    window.actionEvents = {
        'click .editar': function (e, value, row, index) {
            aguarde.mostrar();

            $multi.set_values([]);
            projeto.setAlterar();

            var id_projeto_problema = row.id;

            $.ajax({
                url: '<?= base_url('projetos_problemas/get_dados_projeto_problemas') ?>',
                data: 'id=' + id_projeto_problema,
                dataType: 'json',
                type: 'post'
            }).done(function (data) {
                $('input[name=input_projeto]').val(data.id_projeto);
                $('input[name=input_problema]').val(data.id_problema);
                $('input[name=input_projeto_problema]').val(id_projeto_problema);
                $('input[name=input_nome_projeto]').val(data.nome_projeto).focusout();
                $('textarea[name=text_projeto]').val(data.descricao_projeto);
                $('input[name=input_nome_problema]').val(data.nome_problema);
                $('textarea[name=text_descricao]').val(data.descricao);
                $('input[name=input_resposta]').val(data.resposta);
                $('input[name=input_solucao]').val(data.solucao);
            });

            $('#dialog_projetos_problemas').dialog('option', 'title', '<?= $update_project_problem ?>');
            $('#dialog_projetos_problemas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $button_update_project_problem ?>');
            $('#dialog_projetos_problemas').dialog('open');

            aguarde.ocultar();
        },
        'click .excluir': function (e, value, row, index) {
            var id_projeto_problema = row.id;
            var id_projeto = row.id_projeto;

            projeto.setProjetoProblemaExcluir(id_projeto_problema, id_projeto);
            $('#alerta_exclusao').dialog('open');
        }
    };

    $(document).ready(function () {

        $tabela_projeto_problema = $('#relacao_projeto_problemas');

        $multi = $('#relacao_usuarios').bootstrapTransfer({
            remaining_name: 'opcoes_usuarios',
            target_name: 'participantes[]',
            hilite_selection: true
        });

        $multi.populate(<?= json_encode($usuarios) ?>);

        /*
         * Gera botão de cadastrar usuário e ação de clica-lo
         */
        $('button[type=button][name=cadastrar]').button({
            icons: {
                primary: 'ui-icon-circle-plus'
            }
        }).on('click', function () {
            aguarde.mostrar();

            $multi.set_values([]);
            projeto.setCadastrar();

            $('input, textarea').val('');
            $('input[type=hidden]').val(0);

            $('#dialog_projetos_problemas').dialog('option', 'title', '<?= $add_project_problem ?>');
            $('#dialog_projetos_problemas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('<?= $button_add_project_problem ?>');
            $('#dialog_projetos_problemas').dialog('open');

            aguarde.ocultar();
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
                    text: '<?= $add_project_problem ?>',
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
                    text: '<?= $cancel_add_or_update_project_problem ?>',
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
                    text: "<?= $button_confirm_delete_project_problem ?>",
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
                    text: '<?= $button_cancel_delete_project_problem ?>',
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
            <?= $button_add_project_problem ?>
        </button>
    </div>

    <div class="row">

        <table
            id="relacao_projeto_problemas"
            data-toggle="table"
            data-ajax="buscaRelacaoProjetoProblema"
            data-side-pagination="server"
            data-pagination="true"
            data-method="post"
            data-page-list="[5, 10, 20, 50, 100, 200]"
            data-locale="pt-BR"
            data-search="true"
            data-sort-name="id_projeto"
            data-sort-order="asc">
            <thead>
                <tr>
                    <th data-field="action" data-formatter="actionFormatter" data-events="actionEvents"></th>
                    <th data-field="id_projeto" data-sortable="true"><?= $label_column_id_project ?></th>
                    <th data-field="projeto" data-sortable="true"><?= $label_column_name_project ?></th>
                    <th data-field="problema" data-sortable="true"><?= $label_column_name_problem ?></th>
                </tr>
            </thead>

        </table>

    </div>

</div>

<div id="alerta_exclusao" class="hidden" title="<?= $title_dialog_confirm_remove_project_problem ?>">
    <p class="ui-state-error-text">
        <?= $info_before_remove_project_problem ?>
    </p>
</div>