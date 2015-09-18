
<script type="text/javascript">

    var Calendario = function () {

        var array_date = [];
        var mostrar = false;

        this.mostrarCalendario = function () {
            $.ajax({
                url: '<?= site_url() . 'horarios/mostrarCalendario'; ?>',
                dataType: 'JSON',
                type: 'POST',
                async: false,
                success: function (json) {
                    array_date = json;
                }
            });

            mostrar = true;

            geraDatepicker();
        };

        this.mostrarFeriados = function () {
            $.ajax({
                url: '<?= site_url() . 'horarios/mostrarFeriados'; ?>',
                dataType: 'JSON',
                type: 'POST',
                async: false,
                success: function (json) {
                    array_date = json;
                }
            });

            mostrar = false;

            geraDatepicker();
        };

        var geraDatepicker = function () {
            $("#calendario").datepicker({
                buttonImageOnly: true,
                dateFormat: 'yy-mm-dd',
                dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                nextText: 'Próximo',
                prevText: 'Anterior',
                changeMonth: true,
                changeYear: true,
                numberOfMonths: [3, 4],
                onSelect: function (date, inst) {
                    if ($("#mostrar-feriados").prop('disabled')) {
                        $("#opcao-feriados-dialog").dialog('open');
                    } else {
                        $("#feriados-dialog").dialog('open');
                    }

                    $("input[name='data-feriado']").val(date);
                },
                beforeShowDay: function (date) {
                    var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                    return (typeof array_date[string] === 'undefined' ? [mostrar] : array_date[string]);
                }
            });
        };
    };

    var calendario = new Calendario();

</script>

<script type="text/javascript">
    $(document).ready(function () {

        calendario.mostrarCalendario();

        $('#feriados-dialog').dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            buttons: [
                {
                    text: "Adicionar",
                    click: function () {
                        var url = '<?= site_url() ?>' + ($("#mostrar-feriados").prop('disabled') ? 'horarios/alteraFeriados' : 'horarios/cadastraFeriados');
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: 'data=' + $('#data-feriado').val() + '&nome=' + $('#inputNome').val() +
                                    '&replicar=' + ($('#inputDataFixa:checked').length > 0 ? 'true' : 'false'),
                            success: function (html) {
                                $("#calendario").datepicker("destroy");

                                if ($("#mostrar-feriados").prop('disabled')) {
                                    calendario.mostrarFeriados();
                                } else {
                                    calendario.mostrarCalendario();
                                }

                                $("#feriados-dialog").dialog('close');
                                $('#inputNome').val('');
                                $('#inputDataFixa:checked').prop('checked', false);
                            }
                        });
                    }
                },
                {
                    text: "Cancelar",
                    click: function () {
                        $("#feriados-dialog").dialog('close');
                        $('#inputNome').val('');
                        $('#inputDataFixa:checked').prop('checked', false);
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        $('#opcao-feriados-dialog').dialog({
            autoOpen: false,
            modal: true,
            buttons: [
                {
                    text: "Alterar",
                    click: function () {
                        $("#opcao-feriados-dialog").dialog('close');

                        $.ajax({
                            url: '<?= site_url() . 'horarios/getFeriadoByDia' ?>',
                            type: 'POST',
                            data: 'dia=' + $('#data-feriado').val(),
                            dataType: 'json',
                            success: function (json) {
                                $('#inputNome').val(json.nome);
                                $('#inputDataFixa').prop('disabled', true);
                                $("#feriados-dialog").dialog('open');
                            }
                        });
                    }
                },
                {
                    text: "Excluir",
                    click: function () {
                        $("#opcao-feriados-dialog").dialog('close');

                        $.ajax({
                            url: '<?= site_url() . 'horarios/deleteFeriado' ?>',
                            type: 'POST',
                            data: 'data=' + $('#data-feriado').val(),
                            success: function () {
                                $("#calendario").datepicker("destroy");
                                calendario.mostrarFeriados();
                            }
                        });
                    }
                },
                {
                    text: "Cancelar",
                    click: function () {
                        $("#opcao-feriados-dialog").dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        $("#mostrar-feriados").button().on('click', function () {
            $("#calendario").datepicker("destroy");
            $("#mostrar-feriados").button({disabled: true});
            $("#mostrar-calendario").button({disabled: false});
            $('#inputDataFixa').prop('disabled', true);
            calendario.mostrarFeriados();
        });

        $("#mostrar-calendario").button({
            disabled: true
        }).on('click', function () {
            $("#calendario").datepicker("destroy");
            $("#mostrar-calendario").button({disabled: true});
            $("#mostrar-feriados").button({disabled: false});
            $('#inputDataFixa').prop('disabled', false);
            calendario.mostrarCalendario();
        });
    });
</script>

<style type="text/css">
    div #calendario>div {
        margin: auto !important;
        width: 90% !important;
    }

    .botoes-mostrar {
        width: 100%;
        text-align: center;
        margin: 10px auto;
    }
</style>

<div class="container">

    <div class="well">
        <div class="botoes-mostrar">
            <button id="mostrar-feriados" type="button">
                Mostrar Feriados
            </button>
            <button id="mostrar-calendario" type="button">
                Mostrar Calendário
            </button>
        </div>

        <div id="calendario"></div>
    </div>

</div>

<div id="feriados-dialog" title="Feriados">

    <div class="form-horizontal">
        <input type="hidden" name="data-feriado" id="data-feriado" />

        <div class="form-group">
            <label class="col-md-4 control-label" for="inputNome">Nome do Feriado</label>  
            <div class="col-md-8">
                <input id="inputNome" name="inputNome" placeholder="Nome do Feriado" class="form-control input-md" type="text" maxlength="50">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <div class="checkbox-inline">
                    <label for="inputDataFixa">
                        <input name="inputDataFixa" id="inputDataFixa" type="checkbox">
                        Data fixa (este feriado será replicado nos anos seguintes)
                    </label>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="opcao-feriados-dialog" title="Feriados">
    <p>Qual operação deseja realizar?</p>
</div>