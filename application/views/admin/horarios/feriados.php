
<script type="text/javascript" src="<?= base_url('static/bootbox.js/js/bootbox.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-datepicker/js/bootstrap-datepicker.pt-BR.min.js') ?>"></script>

<link href="<?= base_url('static/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('static/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css') ?>" rel="stylesheet">

<script type="text/javascript">

    var Calendario = function () {

        var array_date = [];
        var mostrar = false;
        var $calendario = null;

        var template_feriados_dialog = '\
            <div class="form-horizontal">\
                <input type="hidden" name="data_feriado" id="data_feriado" />\
                <div class="form-group">\
                    <label class="col-md-4 control-label" for="input_nome"><?= $holiday_name ?></label>\
                    <div class="col-md-8">\
                        <input id="input_nome" name="input_nome" placeholder="<?= $holiday_name ?>" class="form-control input-md" type="text" maxlength="50">\
                    </div>\
                </div>\
                <div class="form-group">\
                    <div class="col-md-12">\
                        <div class="checkbox checkbox-primary">\
                            <input type="checkbox" id="input_data_fixa" name="input_data_fixa">\
                            <label for="input_data_fixa"><?= $holiday_replicate_next_years ?></label>\
                        </div>\
                    </div>\
                </div>\
            </div>';

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

            if ($calendario === null) {
                geraDatepicker();
                eventoSelecionaData();
            } else {
                geraDatepicker();
            }
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

            if ($calendario === null) {
                geraDatepicker();
                eventoSelecionaData();
            } else {
                geraDatepicker();
            }
        };

        var formataData = function (date) {
            var ano = date.getFullYear();
            var mes = date.getMonth() + 1;
            mes = mes < 10 ? ('0' + mes) : mes;
            var dia = date.getDate();
            dia = dia < 10 ? ('0' + dia) : dia;

            return (ano + '-' + mes + '-' + dia);
        };

        var geraDatepicker = function () {

            $calendario = $("#calendario").datepicker({
                format: "yy-mm-dd",
                language: "pt-BR",
                autoclose: true,
                beforeShowDay: function (date) {
                    var string = formataData(date);

                    return (typeof array_date[string] === 'undefined' ? {enabled: mostrar} : array_date[string]);
                }
            });
        };

        var eventoSelecionaData = function () {
            $calendario.on('changeDate', function (date) {
                var data = date.date;

                if ($("#mostrar_feriados").prop('disabled')) {
                    opcao_feriados_dialog();
                } else {
                    feriados_dialog();
                }

                $("input[name='data_feriado']").val(data);
            });
        };

        var feriados_dialog = function () {
            bootbox.dialog({
                title: '<?= $dialog_holiday ?>',
                backdrop: true,
                message: template_feriados_dialog,
                buttons: {
                    add_holiday: {
                        label: "<?= $add_holiday ?>",
                        className: "btn-success",
                        callback: function () {
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

                                    $('#input_nome').val('');
                                    $('#input_data_fixa:checked').prop('checked', false);
                                }
                            });
                        }
                    },
                    cancel_holiday: {
                        label: "<?= $cancel_holiday ?>",
                        callback: function () {
                            $('#input_nome').val('');
                            $('#input_data_fixa:checked').prop('checked', false);
                        }
                    }
                }
            });
        };

        var opcao_feriados_dialog = function () {
            bootbox.dialog({
                title: '<?= $dialog_option ?>',
                message: '<p><?= $option_text_dialog ?></p>',
                buttons: {
                    option_holiday_edit: {
                        label: "<?= $option_holiday_edit ?>",
                        callback: function () {
                            $.ajax({
                                url: '<?= base_url('horarios/get_feriado_dia') ?>',
                                type: 'POST',
                                data: 'dia=' + $('#data_feriado').val(),
                                dataType: 'json',
                                success: function (json) {
                                    $('#input_nome').val(json.nome);
                                    $('#input_data_fixa').prop('disabled', true);
                                }
                            });
                        }
                    },
                    option_holiday_remove: {
                        label: "<?= $option_holiday_remove ?>",
                        callback: function () {
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
                    option_holiday_close: {
                        label: "<?= $option_holiday_close ?>"
                    }
                }
            });
        };
    };

    var calendario = new Calendario();

    $(document).ready(function () {

        calendario.mostrarCalendario();

        $("#mostrar_feriados").on('click', function () {
            $("#calendario").datepicker("destroy");
            $("#mostrar_feriados").attr({disabled: true});
            $("#mostrar_calendario").attr({disabled: false});
            $('#input_data_fixa').prop({disabled: true});
            calendario.mostrarFeriados();
        });

        $("#mostrar_calendario").on('click', function () {
            $("#calendario").datepicker("destroy");
            $("#mostrar_calendario").attr({disabled: true});
            $("#mostrar_feriados").attr({disabled: false});
            $('#input_data_fixa').prop({disabled: false});
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
            <button id="mostrar_feriados" type="button" class="btn btn-default">
                <?= $show_holiday ?>
            </button>
            <button id="mostrar_calendario" type="button" class="btn btn-default" disabled>
                <?= $show_calendar ?>
            </button>
        </div>

        <div id="calendario"></div>
    </div>

</div>

<div id="feriados_dialog" title="">


</div>

<div id="opcao_feriados_dialog" title="">

</div>