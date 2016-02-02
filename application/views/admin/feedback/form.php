

<div id="dialog_feedback">

    <?php
    $hidden = array('input_id' => '0');
    $attr_from = array('class' => 'form-horizontal', 'name' => 'formulario', 'id' => 'formulario');

    echo form_open(array(), $attr_from, $hidden);

    $class_label = array('class' => 'col-md-4 control-label');
    $class_input = array('class' => 'form-control input-md');
    ?>

    <div class="form-group">
        <?= form_label($label_name_feedback . ':', 'input_nome', $class_label); ?>
        <div class="col-md-8">
            <?= form_input(array('name' => 'input_nome', 'id' => 'input_nome', 'placeholder' => $label_name_feedback, 'maxlength' => '50'), '', $class_input) ?>
        </div>
    </div>

    <div class="form-group">
        <?= form_label($label_abbreviation_feedback . ':', 'input_abreviatura', $class_label); ?>
        <div class="col-md-4">
            <?= form_input(array('name' => 'input_abreviatura', 'id' => 'input_abreviatura', 'placeholder' => $label_abbreviation_feedback, 'maxlenght' => '10'), '', $class_input) ?>
        </div>

        <div class="col-md-4">
            <div class="checkbox checkbox-primary">
                <?= form_checkbox(array('name' => 'input_descontar', 'id' => 'input_descontar'), 'descontar') ?>
                <?= form_label($label_discount_total_time_feedback, 'input_descontar'); ?>
            </div>
        </div>

    </div>

    <div class="form-group">
        <?= form_label($label_description_feedback . ':', 'text_descricao', $class_label); ?>
        <div class="col-md-8">
            <?= form_textarea(array('name' => 'text_descricao', 'id' => 'text_descricao', 'placeholder' => $label_description_feedback, 'maxlength' => '1000'), '', $class_input) ?>
        </div>
    </div>

    <?php
    echo form_close();
    ?>
</div>