
<script type="text/javascript">

    $(document).ready(function () {

        /*
         * Verifica se usuário já existe
         *      Caso exista a borda do input ficará vermelha
         *      Caso não exista a borda ficará verde
         */
        $('input[name=input_usuario]').on('focusout', function () {
            if ($(this).val() != '') {
                $.ajax({
                    url: '<?= base_url('usuarios/valida_usuario') ?>',
                    data: 'user=' + $(this).val() + '&id=' + $('input[name=input_id]').val(),
                    dataType: 'json',
                    type: 'POST',
                    success: function (values) {

                        if (values.status == true) {
                            $("#div_usuario").removeClass('has-success');
                            $("#div_usuario").addClass('has-error');
                        } else {
                            $("#div_usuario").removeClass('has-error');
                            $("#div_usuario").addClass('has-success');
                        }
                    }
                });
            } else {
                $("#div_usuario").removeClass('has-success');
                $("#div_usuario").addClass('has-error');
            }
        });

        /*
         * Verifica se o e endereço de e-mail informa já
         * existe ou é inválido neste caso o input ficará vermelho.
         * Caso sejá valido ou inexistente ficará verde.
         */
        $('input[name=input_email]').on('focusout', function () {
            if ($(this).val() != '') {
                $.ajax({
                    url: '<?= base_url('usuarios/valida_email') ?>',
                    data: 'email=' + $(this).val() + '&id=' + $('input[name=input_id]').val(),
                    dataType: 'json',
                    type: 'POST',
                    success: function (values) {

                        if (values.status == true) {
                            $("#div_email").removeClass('has-success');
                            $("#div_email").addClass('has-error');
                        } else {
                            $("#div_email").removeClass('has-error');
                            $("#div_email").addClass('has-success');
                        }
                    }
                });
            } else {
                $("#div_email").removeClass('has-success');
                $("#div_email").addClass('has-error');
            }
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

        $('input[name=input_telefone]').mask(MascaraNonoDigito, NonoDigitoOpcoes);

        /*
         * Oculta o select com as empresas
         */

        $('#empresa').hide();

        /*
         * Ao selecionar o perfil cliente mostra as empresas cadastradas
         */

        $('select[name=select_perfil]').on('change', function () {
            if ($(this).val() == 1) {
                $('#empresa').show();
            } else {
                $('#empresa').hide();
            }
        });

        $('#form_usuario').tabs();

    });

</script>

<div id="formulario_cadastro" class="hidden" title="">
    <form method="post" name="form_usuario" id="form_usuario" class="form-horizontal">

        <ul>
            <li>
                <a href="#dados_usuario">
                    <?= $title_tab_user ?>
                </a>
            </li>
            <li>
                <a href="#projetos">
                    <?= $title_tab_project_user ?>
                </a>
            </li>
        </ul>

        <div id="dados_usuario">
            <input type="hidden" name="input_id" id="input_id" value="0" />

            <div class="form-group">
                <label class="col-md-4 control-label" for="input_nome">
                    <?= $label_name_user ?>
                </label>  
                <div class="col-md-8">
                    <input id="input_nome" name="input_nome" placeholder="<?= $label_name_user ?>" class="form-control input-md" type="text">    
                </div>
            </div>

            <div class="form-group" id="div_usuario">
                <label for="input_usuario" class="col-md-4 control-label">
                    <?= $label_username_user ?>
                </label>
                <div class="col-md-8">
                    <input type="text" class="form-control input-md" id="input_usuario" name="input_usuario" placeholder="<?= $label_username_user ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="input_senha" class="col-md-4 control-label">
                    <?= $label_password_user ?>
                </label>
                <div class="col-md-4">
                    <input type="password" class="form-control input-md" id="input_senha" required name="input_senha" placeholder="<?= $label_password_user ?>">
                </div>
                <div class="col-md-4">
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="input_changeme" name="input_changeme" value="changeme">
                        <label for="input_changeme">
                            <?= $label_password_temporary_user ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group" id="div_email">
                <label for="input_email" class="col-md-4 control-label">
                    <?= $label_email_user ?>
                </label>
                <div class="col-md-8">
                    <input type="text" class="form-control input-md" id="input_email" name="input_email" placeholder="<?= $label_email_user ?>">
                </div>
            </div>

            <div class="form-group" id="div_telefone">
                <label for="input_telefone" class="col-md-4 control-label">
                    <?= $label_telephone_user ?>
                </label>
                <div class="col-md-8">
                    <input type="text" class="form-control input-md" id="input_telefone" name="input_telefone" placeholder="<?= $label_telephone_user ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="select_perfil">
                    <?= $label_profile_user ?>
                </label>
                <div class="col-md-4">
                    <select id="select_perfil" name="select_perfil" class="selectpicker form-control">
                        <option disabled selected><?= $label_profile_option_user ?></option>
                        <?php
                        foreach ($perfil as $values) {
                            ?>
                            <option value="<?= $values['id']; ?>"><?= $values['perfil']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group" id="empresa">
                <label class="col-md-4 control-label" for="select_empresa">
                    <?= $businesses_label ?>
                </label>
                <div class="col-md-4">
                    <select id="select_empresa" name="select_empresa" class="selectpicker form-control">
                        <option disabled selected><?= $label_bussinesses_option_user ?></option>
                        <?php
                        foreach ($empresas as $values) {
                            ?>
                            <option value="<?= $values['id']; ?>"><?= $values['empresa']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

        </div>

        <div id="projetos">
            <div id="select_projeto" style="height: 300px"></div>
        </div>

    </form>
</div>