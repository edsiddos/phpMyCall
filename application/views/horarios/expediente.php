
<script type="text/javascript" src="<?= site_url() . 'static/js/jquery.mask.min.js' ?>"></script>

<script type="text/javascript">

    var ValidaHora = function () {

        var anterior = '';

        this.setAnterior = function (value) {
            anterior = value;
        };

        this.getAnterior = function () {
            return anterior;
        };

        this.valida = function (value) {
            var str = '([01][0-9]|2[0-3]):([0-5][0-9])';
            var exp = new RegExp(str);

            return exp.test(value);
        };
    };

    var valida = new ValidaHora();

    $(document).ready(function () {

        $("input[type='text']").mask('99:99').focus(function () {
            valida.setAnterior($(this).val());
        }).focusout(function () {
            var value = $(this).val();
            var anterior = valida.getAnterior();

            if (value !== anterior && (valida.valida(value) || value.length === 0)) {

                $.ajax({
                    url: '<?= site_url() . '/Horarios/setExpediente' ?>',
                    type: 'POST',
                    data: 'id=' + $(this).attr('idHorario') + '&value=' + value +
                            '&coluna=' + $(this).attr('coluna'),
                    dataType: 'json',
                    success: function (json) {
                        if (json.status == 'OK') {
                            $("#status").html('<div class="alert alert-success text-center">Horário alterado com sucesso.</div>');
                        } else {
                            $("#status").html('<div class="alert alert-danger text-center">Falha ao alterar horário.</div>');
                        }
                    }
                });

            } else if (valida.valida(value) === false && value.length !== 0) {
                $("#status").html('<div class="alert alert-danger text-center">Formato ou Horário informado inválido.</div>');
            }
        });

    });
</script>

<style type="text/css">
    input[type='text']{
        width: 100%;
        text-align: center;
    }

    thead tr th, tfoot tr td{
        text-align: center;
    }

    .td_width{
        width: 12.5%;
    }

    td[rowspan='2']{
        -webkit-transform: rotate(270deg);	
        -moz-transform: rotate(270deg);
        -ms-transform: rotate(270deg);
        -o-transform: rotate(270deg);
        transform: rotate(270deg);
        text-align: center;
        vertical-align: middle !important;
        padding: 0px !important;
    }
</style>

<div class="container">

    <div id="status">
        <div class="alert alert-info text-center">
            Alteração de horários de expediente
        </div>
    </div>

    <div class="well">
        <table class="table">
            <thead>
                <tr>
                    <th colspan="2" class="td_width"></th>
                    <?php foreach ($expediente['dia_semana'] as $values) { ?>
                        <th class="td_width">
                            <?= $values ?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="2">
                        1º Período
                    </td>
                    <td>
                        Entrada
                    </td>
                    <?php
                    foreach ($expediente['entrada_manha'] as $key => $values) {
                        $dados = array(
                            'value' => $values,
                            'idHorario' => $key,
                            'coluna' => 'entrada_manha',
                            'maxlength' => 5,
                            'class' => 'form-control'
                        );
                        ?>
                        <td>
                            <?= form_input($dados) ?>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td>
                        Saída
                    </td>
                    <?php
                    foreach ($expediente['saida_manha'] as $key => $values) {
                        $dados = array(
                            'value' => $values,
                            'idHorario' => $key,
                            'coluna' => 'saida_manha',
                            'maxlength' => 5,
                            'class' => 'form-control'
                        );
                        ?>
                        <td>
                            <?= form_input($dados) ?>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td rowspan="2">
                        2º Período
                    </td>
                    <td>
                        Entrada
                    </td>
                    <?php
                    foreach ($expediente['entrada_tarde'] as $key => $values) {
                        $dados = array(
                            'value' => $values,
                            'idHorario' => $key,
                            'coluna' => 'entrada_tarde',
                            'maxlength' => 5,
                            'class' => 'form-control'
                        );
                        ?>
                        <td>
                            <?= form_input($dados) ?>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td>
                        Saída
                    </td>
                    <?php
                    foreach ($expediente['saida_tarde'] as $key => $values) {
                        $dados = array(
                            'value' => $values,
                            'idHorario' => $key,
                            'coluna' => 'saida_tarde',
                            'maxlength' => 5,
                            'class' => 'form-control'
                        );
                        ?>
                        <td>
                            <?= form_input($dados); ?>
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
    </div>

</div>