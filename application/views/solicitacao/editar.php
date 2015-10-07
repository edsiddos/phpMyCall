

<script type="text/javascript">
    $(document).ready(function () {

        /*
         * Dados em formato json
         */
        var participantes = <?= $participantes ?>;
        var solicitacao = <?= $solicitacao ?>;

        /*
         * Gera caixa se seleção de solicitantes e técnicos
         */
        $("select[name='select_solicitante'] > option").remove();
        $("select[name='select_tecnico'] > option").remove();
        $("select[name='select_solicitante']").append('<option value="0" disabled selected>Solicitante</option>');
        $("select[name='select_tecnico']").append('<option value="0" disabled selected>Técnico</option>');

        $.each(participantes, function (key, value) {
            $("select[name='select_solicitante']").append('<option value="' + value.id + '">' + value.nome + '</option>');

            if (value.tecnico) {
                $("select[name='select_tecnico']").append('<option value="' + value.id + '">' + value.nome + '</option>');
            }
        });

        /*
         * Preenche campos com dados da solicitação.
         */
        $("input[name='input_id']").val(solicitacao.solicitacao);
        $("input[name='solicitacao_origem']").val(solicitacao.solicitacao_origem);
        $("select[name='select_projeto']").val(solicitacao.projeto_problema);
        $("select[name='select_prioridade']").val(solicitacao.prioridade);
        $("select[name='select_solicitante']").val(solicitacao.solicitante);
        $("select[name='select_tecnico']").val(solicitacao.tecnico);
        $("textarea[name='textarea_descricao']").val(solicitacao.descricao);

        /*
         * Monta tabela com relação dos arquivos anexos
         */

        if (solicitacao.arquivos.length > 0) {
            $('#arquivos_antigos').addClass('well well-sm');

            var table = '<table class="table" id="table_arquivos">' +
                    '<thead><tr><th colspan="2" class="text-center">Arquivos anexos na solicitação</th></tr></thead>' +
                    '<tbody></tbody>' +
                    '</table>';

            $('#arquivos_antigos').html(table);

            var table = '';

            $.each(solicitacao.arquivos, function (key, value) {
                table += '<tr id="arquivos' + value.id + '"><td>' + value.nome + '</td>' +
                        '<td><button class="excluir" data-id="' + value.id + '" type="button">Excluir</button></td></tr>';
            });

            $('#table_arquivos > tbody').html(table);
        }

        /*
         * Implementa operações referentes a remoção de anexos
         */

        $('td > button[type=button][class=excluir]').button({
            icons: {
                primary: 'ui-icon-closethick'
            }
        }).on('click', function () {
            $("input[type='hidden'][name='id_arquivo']").val($(this).attr('data-id'));
            delete_file.dialog('open');
        });

        /*
         * Dialog de confirmação antes da remoção do anexo.
         */

        var delete_file = $('#confirm_delete_file').dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                "Remover": function () {

                    $.ajax({
                        url: "<?= base_url() . 'solicitacao/remover_arquivo' ?>",
                        data: 'id=' + $("input[type='hidden'][name='id_arquivo']").val() +
                                '&projeto_tipo_problema=' + $("select[name='selectProjeto']").val(),
                        type: 'POST',
                        dataType: 'json',
                        success: function (data) {
                            $("#status").prop('title', 'Atenção');
                            $("#msg_status").html(data.status ? "Arquivo removido com sucesso." : "Falha ao remover arquivo. Caso persista o erro contate administrador.");

                            status_msg.dialog("open");

                            if (data.status) {
                                var id = $("input[type='hidden'][name='id_arquivo']").val();
                                $("#arquivos" + id).remove();

                                if ($("#table_arquivos > tbody > tr").length == 0) {
                                    $("#arquivos_antigos").hide();
                                }
                            }
                        }
                    });

                    delete_file.dialog('close');
                },
                "Cancelar": function () {
                    delete_file.dialog('close');
                }
            },
            position: {my: "center center-150", of: window}
        });

        /*
         * Monta dialog para exibição de mensagens de aviso.
         */

        var status_msg = $('#status').dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                "OK": function () {
                    status_msg.dialog("close");
                }
            },
            position: {my: "center center-150", of: window}
        });

    });
</script>

<input type="hidden" name="id_arquivo" />

<div id="confirm_delete_file" title="Atenção">
    <p>Deseja excluir este arquivo?</p>
</div>

<div id="status" title="Atenção">
    <p id="msg_status"></p>
</div>