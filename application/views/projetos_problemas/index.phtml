
<link href="<?= HTTP_CSS . '/multi-select-transfer.css' ?>" rel="stylesheet" />
<script type="text/javascript" src="<?= HTTP_JS . '/multi-select-transfer.js' ?>"></script>
<script type="text/javascript" src="<?= HTTP_JS . '/jquery.mask.min.js' ?>"></script>

<script type="text/javascript">

    /*
     * Instancia objeto para exiber mensagem de aguarde.
     */
    var aguarde = new Aguarde('<?= HTTP_IMG . '/change.gif' ?>');


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
                url: '<?= HTTP . '/ProjetosProblemas/' ?>' + formulario,
                data: $('form[name=projetoProblemas]').serialize(),
                dataType: 'JSON',
                type: 'POST',
                async: false,
                success: function (data) {
                    GeraTabelaProjetoProblemas(data.listaProjetoProblemas);

                    if (data.status) {
                        $('#msg-status').removeClass('hide alert-danger').addClass('alert-success');
                        $('#msg-status').html(data.msg);
                    } else {
                        $('#msg-status').removeClass('hide alert-success').addClass('alert-danger');
                        $('#msg-status').html(data.msg);
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
                url: '<?= HTTP . '/ProjetosProblemas/excluir' ?>',
                data: 'projetoProblema=' + projeto_problema + '&projeto=' + projeto,
                dataType: 'json',
                type: 'post',
                async: false,
                success: function (data) {
                    GeraTabelaProjetoProblemas(data.listaProjetoProblemas);

                    if (data.status) {
                        $('#msg-status').removeClass('hide alert-danger').addClass('alert-success');
                        $('#msg-status').html(data.msg);
                    } else {
                        $('#msg-status').removeClass('hide alert-success').addClass('alert-danger');
                        $('#msg-status').html(data.msg);
                    }
                }
            });

            aguarde.ocultar();
        };
    };

    projeto = new ProjetoProblemas();

    /*
     * Função que gera uma tabela de projetos e tipos de problemas
     * @param {JSON} listaProjetoProblemas Objeto JSON com os dados
     */
    var GeraTabelaProjetoProblemas = function (listaProjetoProblemas) {

        var table = '<table class="u-full-width">';
        table += '<thead>';
        table += '<tr>';
        table += '<th class="col4">Projeto</th><th class="col8">Tipos de Problema</th>';
        table += '</tr>';
        table += '</thead>';
        table += '<tbody>';

        $.each(listaProjetoProblemas, function (nome_projeto, projeto) {
            table += '<tr>';
            table += '<td rowspan="' + Object.keys(projeto.projeto_tipo_problema).length + '">';
            table += nome_projeto;
            table += '</td>';

            var count = 0;
            $.each(projeto.projeto_tipo_problema, function (key, values) {
                table += count++ > 0 ? '</tr><tr>' : '';
                table += '<td>';
                table += values;
                table += '<button type="button" name="editar" projeto_tipo_problema="' + key + '" projeto="' + projeto.id_projeto + '">Editar</button>';
                table += '<button type="button" name="excluir" projeto_tipo_problema="' + key + '" projeto="' + projeto.id_projeto + '">Excluir</button>';
                table += '</td>';
            });
        });

        table += '</tr>';
        table += '</tbody>';
        table += '</table>';

        $('#relacaoProjetoProblemas').html(table);
        ButtonEditar();
        ButtonExcluir();
    };

    /*
     * Gera botão para edição de projeto tipo problema 
     */
    var ButtonEditar = function () {

        $('button[name=editar]').button({
            text: false,
            icons: {
                primary: 'ui-icon-pencil'
            }
        }).on('click', function () {
            aguarde.mostrar();

            projeto.setAlterar();

            var id = $(this).attr('projeto_tipo_problema');

            $.ajax({
                url: '<?= HTTP . '/ProjetosProblemas/getDadosProjetosProblemas' ?>',
                data: 'id=' + id,
                dataType: 'json',
                type: 'post',
                async: false,
                success: function (data) {
                    $('#inputProjeto').val(data.id_projeto);
                    $('#inputProblema').val(data.id_problema);
                    $('#inputProjetoProblema').val(id);
                    $('#inputNomeProjeto').val(data.nome_projeto);
                    $('#textProjeto').val(data.descricao_projeto);
                    $('#inputNomeProblema').val(data.nome_problema);
                    $('#textDescricao').val(data.descricao);
                    $('#inputResposta').val(data.resposta);
                    $('#inputSolucao').val(data.solucao);
                    $('#inputNomeProjeto').focusout();
                }
            });

            $('#dialogProjetosProblemas').dialog('option', 'title', 'Alterar projeto tipo de problema');
            $('#dialogProjetosProblemas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('Alterar');
            $('#dialogProjetosProblemas').dialog('open');

            aguarde.ocultar();
        });

    };

    var ButtonExcluir = function () {

        $('button[name=excluir]').button({
            text: false,
            icons: {
                primary: 'ui-icon-close'
            }
        }).on('click', function () {
            $('#alertaExclusao').dialog('open');
            projeto.setProjetoProblemaExcluir($(this).attr('projeto_tipo_problema'), $(this).attr('projeto'));
        });

    };


    $(document).ready(function () {

        multi = new MultiSelectTransfer('#relacaoUsuarios', {name_select_destiny: 'participantes'});
        multi.init();

        ButtonEditar();
        ButtonExcluir();

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

            $('#dialogProjetosProblemas').dialog('option', 'title', 'Cadastrar projeto tipo de problema');
            $('#dialogProjetosProblemas + div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button:first-child > span.ui-button-text').html('Cadastrar');
            $('#dialogProjetosProblemas').dialog('open');

            aguarde.ocultar();
        });

        /*
         * Gera dialog para confirmação de exclusão
         */
        $('#alertaExclusao').dialog({
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
                        $('#alertaExclusao').dialog('close');
                        projeto.excluirProjetoProblema();
                    }
                },
                {
                    text: 'Cancelar',
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $('#alertaExclusao').dialog('close');
                    }
                }
            ],
            position: {my: 'center', at: 'center', of: window}
        });

    });
</script>

<style type="text/css">

    #relacaoProjetoProblemas td > button{
        float: right;
        margin: 0 5px;
    }

</style>

<div class="container">

    <div class="row">
        <div id="msg-status" class="alert hide text-center"></div>
    </div>

    <div class="row">
        <button type="button" name="cadastrar" id="cadastrar">Cadastrar</button>
    </div>

    <div id="relacaoProjetoProblemas">
        <table class="u-full-width">
            <thead>
                <tr>
                    <th class="col4">Projeto</th>
                    <th class="col8">Tipos de Problema</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaProjeto as $nome_projeto => $projeto) { ?>
                    <tr>
                        <td rowspan="<?= count($projeto['projeto_tipo_problema']) ?>">
                            <?= $nome_projeto ?>
                        </td>
                        <?php
                        $count = 0;
                        foreach ($projeto['projeto_tipo_problema'] as $key => $values) {
                            echo $count++ > 0 ? '</tr><tr>' : '';
                            echo "<td>";
                            echo "{$values}";
                            echo "<button type=\"button\" name=\"editar\" projeto_tipo_problema=\"{$key}\" projeto=\"{$projeto['id_projeto']}\">Editar</button>";
                            echo "<button type=\"button\" name=\"excluir\" projeto_tipo_problema=\"{$key}\" projeto=\"{$projeto['id_projeto']}\">Excluir</button>";
                            echo "</td>";
                        }
                        ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<div id="alertaExclusao" title="Alerta de remoção">
    <p class="ui-state-error-text">
        Deseja realmente remover o projeto / tipo de problema?
    </p>
</div>
