

<script type="text/javascript" src="<?= base_url() . 'static/js/jquery.mask.min.js' ?>"></script>


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

            $('#dialog_empresas').dialog('option', 'title', 'Cadastrar empresa');
            $('#dialog_empresas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('Cadastrar');
            $('#dialog_empresas').dialog('open');

            aguarde.ocultar();
        });

        /*
         * Gera botão para edição de feedback
         */

        $('button[name=editar]').button({
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

            $('#dialog_empresas').dialog('option', 'title', 'Alterar empresa');
            $('#dialog_empresas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('Alterar');
            $('#dialog_empresas').dialog('open');

            aguarde.ocultar();
        });

        /*
         * Cria botão de exclusão e adiciona evento ao clica-lo
         */

        $('button[name=excluir]').button({
            icons: {
                primary: 'ui-icon-close'
            }
        }).on('click', function () {
            $('#alerta_exclusao').dialog('open');
            empresa.setExcluir($(this).attr('empresa'));
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
                    text: 'Excluir',
                    icons: {
                        primary: 'ui-icon-trash'
                    },
                    click: function () {
                        empresa.excluir();
                        $(this).dialog('close');
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
        });

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

        <table id='tabela_empresas'>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Telefone Fixo</th>
                    <th>Telefone Celular</th>
                    <th></th>
                </tr>
            </thead>
        </table>

    </div>

</div>

<div id="alerta_exclusao" title="Alerta de remoção">
    <p class="ui-state-error-text">
        Deseja realmente remover esta empresa?
    </p>
</div>