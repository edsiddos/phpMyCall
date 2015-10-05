
<script type="text/javascript" src="<?= base_url() . 'static/js/tinymce/tinymce.min.js' ?>"></script>

<script type="text/javascript">

    tinymce.init({
        selector: '#textarea_descricao',
        language: 'pt_BR'
    });

    $(document).ready(function () {

        $("select[name='select_projeto']").change(function () {
            if ($(this).val() !== 0) {
                $.ajax({
                    url: "<?= base_url() . 'solicitacao/get_solicitantes' ?>",
                    data: "projeto=" + $(this).val(),
                    dataType: "json",
                    type: "post",
                    success: function (data) {
                        var cliente = $("select[name='select_solicitante'] option:selected").val();
                        var tecnico = $("select[name='select_tecnico'] option:selected").val();

                        $("select[name='select_solicitante'] > option").remove();
                        $("select[name='select_tecnico'] > option").remove();
                        $("select[name='select_solicitante']").append('<option value="0" disabled selected>Solicitante</option>');
                        $("select[name='select_tecnico']").append('<option value="0" disabled selected>Técnico</option>');

                        $.each(data, function (key, value) {
                            $("select[name='select_solicitante']").append('<option value="' + value.id + '" ' + (cliente == value.id ? "selected" : "") + '>' + value.nome + '</option>');

                            if (value.tecnico) {
                                $("select[name='select_tecnico']").append('<option value="' + value.id + '" ' + (tecnico == value.id ? "selected" : "") + '>' + value.nome + '</option>');
                            }
                        });

                        $('select').selectpicker('refresh');
                    }
                });
            }
        });

        /**
         * Exibe informa dos arquivos selecionados que serão
         * anexado a solicitação
         */
        $('#input_arquivos').change(function () {
            var dados = '';
            var tamanho = '';
            $.each(this.files, function (key, file) {

                var size = file.size / 1024;
                if (size / 1024 > 1) {
                    if (((size / 1024) / 1024) > 1) {
                        size = (Math.round(((size / 1024) / 1024) * 100) / 100);
                        tamanho = size + "Gb";
                    }
                    else {
                        size = (Math.round((size / 1024) * 100) / 100);
                        tamanho = size + "Mb";
                    }
                }
                else {
                    size = (Math.round(size * 100) / 100);
                    tamanho = size + "kb";
                }

                dados += "Nome: " + file.name + " - Tamanho: " + tamanho + "<br/>";
            });

            $("#arquivos_novos").html(dados);
        });

    });
</script>

<style type="text/css">
    div.browse_wrap {
        top:0;
        left:0;
        margin:20px;
        cursor:pointer;
        overflow:hidden;
        padding:20px 60px;
        text-align:center;
        position:relative;
        background-color:#f6f7f8;
        border:solid 1px #d2d2d7;
        border-radius: 5px;
    }

    div.title {
        color:#3b5998;
        font-size:14px;
        font-weight:bold;
    }

    input.upload {
        right:0;
        margin:0;
        bottom:0;
        padding:0;
        opacity:0;
        height:300px;
        outline:none;
        cursor:inherit;
        position:absolute;
        font-size:1000px !important;
    }

    span.upload_path {
        text-align: center;
        margin:20px;
        display:block;
        font-size: 80%;
        color:#3b5998;
        font-weight:bold;
    }
</style>

<div class="container">

    <?php
    if ((!empty($_SESSION ['msg_erro'])) || (!empty($_SESSION ['msg_sucesso']))) {
        ?>
        <div class="alert <?= empty($_SESSION['msg_erro']) ? 'alert-success' : 'alert-danger'; ?> alert-error text-center">
            <?= empty($_SESSION['msg_erro']) ? $_SESSION['msg_sucesso'] : $_SESSION['msg_erro']; ?>
        </div>
        <?php
        unset($_SESSION ['msg_erro']);
        unset($_SESSION ['msg_sucesso']);
    }
    ?>

    <form action="<?= $link ?>" class="form-horizontal"  method="post" enctype="multipart/form-data">
        <input type="hidden" name="input_id" id="input_id" value="0" />
        <input type="hidden" name="solicitacao_origem" id="solicitacao_origem" value="<?= $solicitacao_origem; ?>" />

        <div class="row">
            <div class="col-xs-6 form-group">
                <label for="select_projeto" class="col-md-4 control-label">
                    Projeto:
                </label>
                <div class="col-md-8">
                    <select name="select_projeto" required class="selectpicker" id="select_projeto">
                        <option value="0" disabled selected>Projeto</option>
                        <?php
                        $id = 0;
                        foreach ($projetos as $values) {
                            if ($id !== $values['id_projeto']) {
                                if ($id !== 0) {
                                    echo '</optgroup>';
                                }
                                echo '<optgroup label="' . $values['projeto'] . '">';
                                $id = $values['id_projeto'];
                            }
                            ?>
                            <option value="<?= $values['id'] ?>">
                                <?= $values['problema'] ?>
                            </option>
                            <?php
                        }
                        echo '</optgroup>';
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-xs-6 form-group">
                <label for="select_solicitante" class="col-md-4 control-label">
                    Solicitante:
                </label>
                <div class="col-md-8">
                    <select name="select_solicitante" required class="selectpicker" id="select_solicitante">
                        <option value="0" disabled selected>Solicitante</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6 form-group">
                <label for="select_prioridade" class="col-md-4 control-label">
                    Prioridade: <span class="text-danger">*</span>
                </label>
                <div class="col-md-8">
                    <select name="select_prioridade" required id="select_prioridade" class="selectpicker">
                        <option value="0"></option>
                        <?php
                        foreach ($prioridade as $values) {
                            ?>
                            <option value="<?= $values['id'] ?>" <?= $values['padrao'] ? "selected" : ""; ?>>
                                <?= $values['nome'] ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-xs-6 form-group">
                <label for="select_tecnico" class="col-md-4 control-label">Técnico:</label>
                <div class="col-md-8">
                    <select name="select_tecnico" class="selectpicker" id="select_tecnico">
                        <option value="0" disabled selected>Técnico</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label for="textarea_descricao" class="col-md-12 text-left">Descrição:</label>
                <div class="col-md-12">
                    <textarea name="textarea_descricao" id="textarea_descricao"></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="browse_wrap">
                    <div class="title">Anexar arquivos a esta solicitação</div>
                    <input type="file" name="input_arquivos[]" class="upload form-control" id="input_arquivos"
                           title="Anexar arquivos a esta solicitação" multiple>
                </div>
                <span id="arquivos_novos" class="upload_path"></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12" id="arquivos_antigos"></div>
        </div>

        <div class="row">
            <div class="col-md-offset-4 col-md-4">
                <button type="submit" class="col-md-12 btn btn-default" name="submit_dados">
                    <samp class="fa fa-check"></samp>
                    Salvar
                </button>
            </div>
        </div>

    </form>
</div>