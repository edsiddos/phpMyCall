

<div class="container">

    <?php
    if ((!empty($_SESSION ['msg_erro'])) || (!empty($_SESSION ['msg_sucesso']))) {
        ?>
        <div class="alert <?= empty($_SESSION['msg_erro']) ? 'alert-success' : 'alert-danger'; ?> text-center">
            <?= empty($_SESSION['msg_erro']) ? $_SESSION['msg_sucesso'] : $_SESSION['msg_erro']; ?>
        </div>
        <?php
        unset($_SESSION ['msg_erro']);
        unset($_SESSION ['msg_sucesso']);
    }
    ?>

    <?php
    echo form_open('login/novaSenha', array('method' => 'post', 'name' => 'alterar'));
    echo form_fieldset('Alterar Senha');

    $label_attr = array('class' => 'control-label col-md-4');
    $input_attr = array('class' => 'form-control', 'required' => TRUE, 'pattern' => '([0-9]|[a-z]|[A-Z]){5,50}');
    ?>

    <div class="row">
        <div class="form-group col-md-12">
            <?= form_label('Nova Senha:', 'novaSenha', $label_attr) ?>

            <div class="col-md-8">
                <?php
                $field_senha = array('id' => 'novaSenha', 'name' => 'novaSenha', 'placeholder' => 'Nova Senha');
                echo form_password($field_senha, '', $input_attr);
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-12">
            <?= form_label('Redigite a nova senha:', 'redigite', $label_attr) ?>

            <div class="col-md-8">
                <?php
                $field_redigite = array('id' => 'redigite', 'name' => 'redigite', 'placeholder' => 'Digite novamente a nova senha');
                echo form_password($field_redigite, '', $input_attr);
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-offset-4 col-md-4">
            <?= form_button(array('type' => 'submit', 'class' => 'btn btn-default col-md-12'), 'Alterar') ?>
        </div>
    </div>

    <?= form_close() ?>
</div>