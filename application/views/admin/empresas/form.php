
<script type="text/javascript">
    $(document).ready(function () {

        $('input[name=input_empresa]').on('focusout', function () {
            $.ajax({
                url: "<?= base_url() . 'empresas/existe_empresa' ?>",
                data: "empresa=" + $(this).val(),
                dataType: 'json',
                type: 'POST',
                success: function (dados) {
                    if (dados.status == 1) {
                        $("#nome_empresa").removeClass('has-success');
                        $("#nome_empresa").addClass('has-error');
                    } else {
                        $("#nome_empresa").removeClass('has-error');
                        $("#nome_empresa").addClass('has-success');
                    }
                }
            });
        });

        /*
         * Aplica formato da mascara para os telefones com 9 e 8 digitos
         */
        var MascaraNonoDigito = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        };

        var NonoDigitoOpcoes = {
            onKeyPress: function (val, e, field, options) {
                field.mask(MascaraNonoDigito.apply({}, arguments), options);
            }
        };

        $('input[name=input_telefone_fixo], input[name=input_telefone_celular]').mask(MascaraNonoDigito, NonoDigitoOpcoes);

    });
</script>

<div id="dialog_empresas" class="hidden">

    <?php
    $hidden = array('input_id' => '0');
    $attr_from = array('class' => 'form-horizontal', 'name' => 'formulario', 'id' => 'formulario');

    echo form_open(array(), $attr_from, $hidden);

    $class_label = array('class' => 'col-md-4 control-label');
    $class_input = array('class' => 'form-control input-md');
    ?>

    <div class="form-group" id="nome_empresa">
        <?= form_label($businesses_label, 'input_empresa', $class_label); ?>
        <div class="col-md-8">
            <?= form_input(array('name' => 'input_empresa', 'id' => 'input_empresa', 'placeholder' => $businesses_label), '', $class_input) ?>
        </div>
    </div>

    <div class="form-group">
        <?= form_label($address_label, 'input_endereco', $class_label); ?>
        <div class="col-md-8">
            <?= form_input(array('name' => 'input_endereco', 'id' => 'input_endereco', 'placeholder' => $address_label), '', $class_input) ?>
        </div>
    </div>


    <div class="form-group">
        <?= form_label($telephone_label, 'input_telefone_fixo', $class_label); ?>
        <div class="col-md-8">
            <?= form_input(array('name' => 'input_telefone_fixo', 'id' => 'input_telefone_fixo', 'placeholder' => $telephone_label), '', $class_input) ?>
        </div>
    </div>
    <div class="form-group">
        <?= form_label($cell_label, 'input_telefone_celular', $class_label); ?>
        <div class="col-md-8">
            <?= form_input(array('name' => 'input_telefone_celular', 'id' => 'input_telefone_celular', 'placeholder' => $cell_label), '', $class_input) ?>
        </div>
    </div>

    <?= form_close(); ?>
</div>