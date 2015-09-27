

<div id="dialog_feedback">

    <?php
    $hidden = array('input_id' => '0');
    $attr_from = array('class' => 'form-horizontal', 'name' => 'formulario', 'id' => 'formulario');

    echo form_open(array(), $attr_from, $hidden);

    $class_label = array('class' => 'col-md-4 control-label');
    $class_input = array('class' => 'form-control input-md');
    ?>

    <div class="form-group">
        <?= form_label('Nome:', 'input_nome', $class_label); ?>
        <div class="col-md-8">
            <?= form_input(array('name' => 'input_nome', 'id' => 'input_nome', 'placeholder' => 'Nome', 'maxlength' => '50'), '', $class_input) ?>
        </div>
    </div>

    <div class="form-group">
        <?= form_label('Abreviatura:', 'input_abreviatura', $class_label); ?>
        <div class="col-md-8">
            <?= form_input(array('name' => 'input_abreviatura', 'id' => 'input_abreviatura', 'placeholder' => 'Abreviatura', 'maxlenght' => '10'), '', $class_input) ?>
        </div>
    </div>

    <div class="form-group">
        <?= form_label('Descontar do tempo total de solução?', 'input_descontar', $class_label); ?>
        <div class="col-md-8">
            <?= form_checkbox(array('name' => 'input_descontar', 'id' => 'input_descontar', 'placeholder' => 'Tempo para solução', 'value' => 'descontar')) ?>
        </div>
    </div>

    <div class="form-group">
        <?= form_label('Descrição:', 'text_descricao', $class_label); ?>
        <div class="col-md-8">
            <?= form_textarea(array('name' => 'text_descricao', 'id' => 'text_descricao', 'placeholder' => 'Descrição do feedback', 'maxlength' => '1000'), '', $class_input) ?>
        </div>
    </div>

    <?php
    echo form_close();
    ?>
</div>