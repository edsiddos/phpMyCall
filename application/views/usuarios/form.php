
<script type="text/javascript">

    $(document).ready(function () {

        /*
         * Verifica se usuário já existe
         *      Caso exista a borda do input ficará vermelha
         *      Caso não exista a borda ficará verde
         */
        $('#inputUsuario').on('focusout', function () {
            if ($(this).val() != '') {
                $.ajax({
                    url: '<?= base_url() . '/Usuarios/validaUsuario' ?>',
                    data: 'user=' + $(this).val() + '&id=' + $('#inputID').val(),
                    dataType: 'json',
                    type: 'POST',
                    success: function (values) {

                        if (values == true) {
                            $("#divUsuario").removeClass('has-success');
                            $("#divUsuario").addClass('has-error');
                        } else {
                            $("#divUsuario").removeClass('has-error');
                            $("#divUsuario").addClass('has-success');
                        }
                    }
                });
            } else {
                $("#divtUsuario").removeClass('has-success');
                $("#divUsuario").addClass('has-error');
            }
        });

        /*
         * Verifica se o e endereço de e-mail informa já
         * existe ou é inválido neste caso o input ficará vermelho.
         * Caso sejá valido ou inexistente ficará verde.
         */
        $('#inputEMail').on('focusout', function () {
            if ($(this).val() != '') {
                $.ajax({
                    url: '<?= base_url() . '/Usuarios/validaEmail' ?>',
                    data: 'email=' + $(this).val() + '&id=' + $('#inputID').val(),
                    dataType: 'json',
                    type: 'POST',
                    success: function (values) {

                        if (values == true) {
                            $("#divEMail").removeClass('has-success');
                            $("#divEMail").addClass('has-error');
                        } else {
                            $("#divEMail").removeClass('has-error');
                            $("#divEMail").addClass('has-success');
                        }
                    }
                });
            } else {
                $("#divEMail").removeClass('has-success');
                $("#divEMail").addClass('has-error');
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

        $('#inputTelefone').mask(MascaraNonoDigito, NonoDigitoOpcoes);

        /*
         * Oculta o select com as empresas
         */

        $('#empresa').hide();

        /*
         * Ao selecionar o perfil cliente mostra as empresas cadastradas
         */

        $('#selectPerfil').on('change', function () {
            if ($(this).val() == 1) {
                $('#empresa').show();
            } else {
                $('#empresa').hide();
            }
        });

        $('#formUsuario').tabs();

    });

</script>

<div id="formulario_cadastro" class="hidden" title="">
    <form method="post" name="formUsuario" id="formUsuario" class="form-horizontal">

        <ul>
            <li><a href="#dadosUsuario">Dados Usuário</a></li>
            <li><a href="#projetos">Projetos</a></li>
        </ul>

        <div id="dadosUsuario">
            <input type="hidden" name="inputID" id="inputID" value="0" />

            <div class="form-group">
                <label class="col-md-4 control-label" for="inputNome">Nome</label>  
                <div class="col-md-8">
                    <input id="inputNome" name="inputNome" placeholder="Nome" class="form-control input-md" type="text">    
                </div>
            </div>

            <div class="form-group" id="divUsuario">
                <label for="inputUsuario" class="col-md-4 control-label">Usuário</label>
                <div class="col-md-8">
                    <input type="text" class="form-control input-md" id="inputUsuario" name="inputUsuario" placeholder="Usuário">
                </div>
            </div>

            <div class="form-group">
                <label for="inputSenha" class="col-md-4 control-label">Senha</label>
                <div class="col-md-4">
                    <input type="password" class="form-control input-md" id="inputSenha" required name="inputSenha" placeholder="Senha">
                </div>
                <div class="col-md-4">
                    <label class="checkbox-inline" for="inputChangeme">
                        <input name="inputChangeme" id="inputChangeme" value="changeme" type="checkbox"> Senha temporária
                    </label>
                </div>
            </div>

            <div class="form-group" id="divEMail">
                <label for="inputEMail" class="col-md-4 control-label">E-Mail</label>
                <div class="col-md-8">
                    <input type="text" class="form-control input-md" id="inputEMail" name="inputEMail" placeholder="E-Mail">
                </div>
            </div>

            <div class="form-group" id="divTelefone">
                <label for="inputTelefone" class="col-md-4 control-label">Telefone</label>
                <div class="col-md-8">
                    <input type="text" class="form-control input-md" id="inputTelefone" name="inputTelefone" placeholder="Telefone">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="selectPerfil">Perfil</label>
                <div class="col-md-4">
                    <select id="selectPerfil" name="selectPerfil" class="selectpicker">
                        <option disabled selected>Selecione um perfil</option>
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
                <label class="col-md-4 control-label" for="selectEmpresa">Empresa</label>
                <div class="col-md-4">
                    <select id="selectEmpresa" name="selectEmpresa" class="selectpicker">
                        <option disabled selected>Selecione uma empresa</option>
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
            <div id="selectProjeto"></div>
        </div>

    </form>
</div>