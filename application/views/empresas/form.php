
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
                        $("input[name=input_empresa]").removeClass('has-success');
                        $("input[name=input_empresa]").addClass('has-error');
                    } else {
                        $("input[name=input_empresa]").removeClass('has-error');
                        $("input[name=input_empresa]").addClass('has-success');
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

        $('#dialog_empresas').dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            width: '80%',
            height: $(window).height() * 0.95,
            buttons: [
                {
                    text: 'Cadastrar',
                    icons: {
                        primary: 'ui-icon-disk',
                    },
                    click: function () {
                        empresa.submitFormulario();
                        $(this).dialog('close');
                    }
                },
                {
                    text: 'Cancelar',
                    icons: {
                        primary: 'ui-icon-close',
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ],
            close: function () {
                $('form[name=formulario] input[type=text]').val('');
            },
            position: {my: 'center', at: 'center', of: window}
        });

    });
</script>

<div id="dialog_empresas">
    <form name="formulario">
        <input type="hidden" name="inputID" id="inputID" value="0" />

        <div class="row" id="divEmpresa">
            <label for="inputEmpresa" class="two columns">Empresa</label>
            <input type="text" class="ten columns" id="inputEmpresa" required name="inputEmpresa" placeholder="Empresa">
        </div>

        <div class="row">
            <label for="inputEndereco" class="two columns">Endereço</label>
            <input type="text" class="ten columns" id="inputEndereco" name="inputEndereco" placeholder="Endereço">
        </div>

        <div class="row">
            <label for="inputTelefoneFixo" class="two columns">Telefone Fixo</label>
            <input type="text" class="ten columns" id="inputTelefoneFixo" name="inputTelefoneFixo" required placeholder="Telefone Fixo">
        </div>

        <div class="row">
            <label for="inputTelefoneCelular" class="two columns">Telefone Celular</label>
            <input type="text" class="ten columns" id="inputTelefoneCelular" name="inputTelefoneCelular" placeholder="Telefone Celular">
        </div>
    </form>
</div>