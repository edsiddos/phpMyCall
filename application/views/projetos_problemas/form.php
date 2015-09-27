

<script type="text/javascript">
    $(document).ready(function () {

        /*
         * Mascára para hora
         */
        $('#input_resposta, #input_solucao').mask('0HHH:M0', {
            translation: {
                'H': {pattern: /[0-9]/, optional: true},
                'M': {pattern: /[0-5]/}
            }
        }).attr('pattern', '[0-9]{1,4}:[0-5][0-9]');

        /*
         * Gera abas para inserção de dados do projeto
         * e para seleção dos participantes.
         */
        $('#projetos_problemas').tabs();

        /*
         * Gera autocomplete do nome do projeto
         * e busca dados do projeto ao perder o foco do input projeto
         */
        $("input[name=input_nome_projeto]").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: '<?= base_url() . 'projetos_problemas/get_projetos' ?>',
                    data: request,
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        response(data);
                    }
                });
            }
        }).on('focusout', function () {
            $.ajax({
                url: '<?= base_url() . 'projetos_problemas/get_dados_projeto' ?>',
                data: 'nome=' + $(this).val(),
                dataType: 'json',
                type: 'post',
                success: function (data) {
                    $('textarea[name=text_projeto]').val(data.descricao_projeto);
                    multi.setOrigin(data.usuarios);
                    multi.setDestiny(data.participantes);
                }
            });
        });

        /*
         * Gera autocomplete do tipo de problema
         */
        $("input[name='input_nome_problema']").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: '<?= site_url() . 'projetos_problemas/get_problemas' ?>',
                    data: request,
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        response(data);
                    }
                });
            }
        });

    });
</script>


<div id="dialog_projetos_problemas" class="hidden">
    <?php
    $hidden = array('input_projeto' => '0', 'input_problema' => '0', 'input_projeto_problema' => '0');
    $attr_from = array('class' => 'form-horizontal', 'name' => 'projetos_problemas', 'id' => 'projetos_problemas');

    echo form_open(array(), $attr_from, $hidden);

    $class_label = array('class' => 'col-md-4 control-label');
    $class_input = array('class' => 'form-control input-md');
    ?>

    <ul>
        <li><a href="#cadastro_projeto">Projeto tipo problema</a></li>
        <li><a href="#usuarios_projeto">Participantes</a></li>
    </ul>

    <div id="cadastro_projeto">

        <?= form_fieldset('Projeto') ?>

        <div class="form-group">
            <?= form_label('Nome do Projeto:', 'input_nome_projeto', $class_label); ?>
            <div class="col-md-8">
                <?= form_input(array('name' => 'input_nome_projeto', 'id' => 'input_nome_projeto', 'placeholder' => 'Projeto', 'maxlength' => '100'), '', $class_input) ?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Descrição do projeto (Opcional):', 'text_projeto', $class_label); ?>
            <div class="col-md-8">
                <?= form_textarea(array('name' => 'text_projeto', 'id' => 'text_projeto', 'placeholder' => 'Descrição do projeto', 'maxlength' => '500'), '', $class_input) ?>
            </div>
        </div>

        <?php
        echo form_fieldset_close();

        echo form_fieldset('Problema');
        ?>

        <div class="form-group">
            <?= form_label('Tipo de Problema:', 'input_nome_problema', $class_label); ?>
            <div class="col-md-8">
                <?= form_input(array('name' => 'input_nome_problema', 'id' => 'input_nome_problema', 'placeholder' => 'Tipo de problema', 'maxlength' => '100'), '', $class_input) ?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Descrição do tipo de problemas:', 'text_descricao', $class_label); ?>
            <div class="col-md-8">
                <?= form_textarea(array('name' => 'text_descricao', 'id' => 'text_descricao', 'placeholder' => 'Descrição do tipo de problema', 'maxlength' => '1000'), '', $class_input) ?>
            </div>
        </div>

        <?php
        echo form_fieldset_close();

        echo form_fieldset('Prazos');
        ?>


        <div class="row">
            <div class="col-xs-6 form-group">
                <?= form_label('Resposta:', 'input_resposta', $class_label); ?>
                <div class="col-md-8">
                    <?= form_input(array('name' => 'input_resposta', 'id' => 'input_resposta', 'placeholder' => 'Tempo de resposta'), '', $class_input) ?>
                </div>
            </div>
            <div class="col-xs-6 form-group">
                <?= form_label('Solução:', 'input_solucao', $class_label); ?>
                <div class="col-md-8">
                    <?= form_input(array('name' => 'input_solucao', 'id' => 'input_solucao', 'placeholder' => 'Tempo para solução'), '', $class_input) ?>
                </div>
            </div>
        </div>

        <?= form_fieldset_close(); ?>
    </div>

    <div id="usuarios_projeto">
        <div id="relacao_usuarios"></div>
    </div>

</form>
</div>