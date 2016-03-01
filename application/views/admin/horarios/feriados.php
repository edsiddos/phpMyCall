
<script type="text/javascript">

    var Calendario = function () {

        var array_date = [];
        var mostrar = false;

        this.mostrarCalendario = function () {
            $.ajax({
                url: '<?= base_url('horarios/mostrar_calendario'); ?>',
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
                url: '<?= base_url('horarios/mostrar_feriados'); ?>',
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
                    if ($("#mostrar_feriados").prop('disabled')) {
                        $("#opcao_feriados_dialog").dialog('open');
                    } else {
                        $("#feriados_dialog").dialog('open');
                    }

                    $("input[name='data_feriado']").val(date);
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

        $('#feriados_dialog').dialog({
            autoOpen: false,
            modal: true,
            width: '35%',
            buttons: [
                {
                    text: "<?= $add_holiday ?>",
                    icons: {
                        primary: 'ui-icon-check'
                    },
                    click: function () {
                        var url = '<?= base_url() ?>' + ($("#mostrar_feriados").prop('disabled') ? 'horarios/altera_feriados' : 'horarios/cadastra_feriados');
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: 'data=' + $('#data_feriado').val() + '&nome=' + $('#input_nome').val() +
                                    '&replicar=' + ($('#input_data_fixa:checked').length > 0 ? 'true' : 'false'),
                            success: function (html) {
                                $("#calendario").datepicker("destroy");

                                if ($("#mostrar_feriados").prop('disabled')) {
                                    calendario.mostrarFeriados();
                                } else {
                                    calendario.mostrarCalendario();
                                }

                                $("#feriados_dialog").dialog('close');
                                $('#input_nome').val('');
                                $('#input_data_fixa:checked').prop('checked', false);
                            }
                        });
                    }
                },
                {
                    text: "<?= $cancel_holiday ?>",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $("#feriados_dialog").dialog('close');
                        $('#input_nome').val('');
                        $('#input_data_fixa:checked').prop('checked', false);
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        $('#opcao_feriados_dialog').dialog({
            autoOpen: false,
            width: '35%',
            modal: true,
            buttons: [
                {
                    text: "<?= $option_holiday_edit ?>",
                    icons: {
                        primary: 'ui-icon-pencil'
                    },
                    click: function () {
                        $("#opcao_feriados_dialog").dialog('close');

                        $.ajax({
                            url: '<?= base_url('horarios/get_feriado_dia') ?>',
                            type: 'POST',
                            data: 'dia=' + $('#data_feriado').val(),
                            dataType: 'json',
                            success: function (json) {
                                $('#input_nome').val(json.nome);
                                $('#input_data_fixa').prop('disabled', true);
                                $("#feriados_dialog").dialog('open');
                            }
                        });
                    }
                },
                {
                    text: "<?= $option_holiday_remove ?>",
                    icons: {
                        primary: 'ui-icon-trash'
                    },
                    click: function () {
                        $("#opcao_feriados_dialog").dialog('close');

                        $.ajax({
                            url: '<?= base_url('horarios/delete_feriado') ?>',
                            type: 'POST',
                            data: 'data=' + $('#data_feriado').val(),
                            success: function () {
                                $("#calendario").datepicker("destroy");
                                calendario.mostrarFeriados();
                            }
                        });
                    }
                },
                {
                    text: "<?= $option_holiday_close ?>",
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $("#opcao_feriados_dialog").dialog('close');
                    }
                }
            ],
            position: {my: "center center-150", of: window}
        });

        $("#mostrar_feriados").button().on('click', function () {
            $("#calendario").datepicker("destroy");
            $("#mostrar_feriados").button({disabled: true});
            $("#mostrar_calendario").button({disabled: false});
            $('#input_data_fixa').prop('disabled', true);
            calendario.mostrarFeriados();
        });

        $("#mostrar_calendario").button({
            disabled: true
        }).on('click', function () {
            $("#calendario").datepicker("destroy");
            $("#mostrar_calendario").button({disabled: true});
            $("#mostrar_feriados").button({disabled: false});
            $('#input_data_fixa').prop('disabled', false);
            calendario.mostrarCalendario();
        });
    });
</script>

<style type="text/css">
    div #calendario>div {
        margin: auto !important;
        width: 90% !important;
    }

    .botoes_mostrar {
        width: 100%;
        text-align: center;
        margin: 10px auto;
    }
</style>

<div class="container">

    <div class="well">
        <div class="botoes_mostrar">
            <button id="mostrar_feriados" type="button">
                <?= $show_holiday ?>
            </button>
            <button id="mostrar_calendario" type="button">
                <?= $show_calendar ?>
            </button>
        </div>

        <div id="calendario"></div>
    </div>

</div>

<div id="feriados_dialog" title="<?= $dialog_holiday ?>">

    <div class="form-horizontal">
        <input type="hidden" name="data_feriado" id="data_feriado" />

        <div class="form-group">
            <label class="col-md-4 control-label" for="input_nome"><?= $holiday_name ?></label>  
            <div class="col-md-8">
                <input id="input_nome" name="input_nome" placeholder="<?= $holiday_name ?>" class="form-control input-md" type="text" maxlength="50">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">

                <div class="checkbox checkbox-primary">
                    <input type="checkbox" id="input_data_fixa" name="input_data_fixa">
                    <label for="input_data_fixa">
                        <?= $holiday_replicate_next_years ?>
                    </label>
                </div>

            </div>
        </div>

    </div>
</div>

<div id="opcao_feriados_dialog" title="<?= $dialog_option ?>">
    <p><?= $option_text_dialog ?></p>
</div>