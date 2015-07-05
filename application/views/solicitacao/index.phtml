
<script type="text/javascript" src="<?= HTTP_JS . '/tinymce/tinymce.min.js' ?>"></script>

<script type="text/javascript">

    tinymce.init({
        selector: '#textareaDescricao',
        language: 'pt_BR'
    });

    $(document).ready(function () {

        $("select[name='selectProjeto']").change(function () {
            if ($(this).val() !== 0) {
                $.ajax({
                    url: "<?= HTTP . '/Solicitacao/getSolicitantes' ?>",
                    data: "projeto=" + $(this).val(),
                    dataType: "json",
                    type: "post",
                    success: function (data) {
                        var cliente = $("select[name='selectSolicitante'] option:selected").val();
                        var tecnico = $("select[name='selectTecnico'] option:selected").val();

                        $("select[name='selectSolicitante'] > option").remove();
                        $("select[name='selectTecnico'] > option").remove();
                        $("select[name='selectSolicitante']").append('<option value="0" disabled selected>Solicitante</option>');
                        $("select[name='selectTecnico']").append('<option value="0" disabled selected>Técnico</option>');

                        $.each(data, function (key, value) {
                            $("select[name='selectSolicitante']").append('<option value="' + value.id + '" ' + (cliente == value.id ? "selected" : "") + '>' + value.nome + '</option>');

                            if (value.tecnico) {
                                $("select[name='selectTecnico']").append('<option value="' + value.id + '" ' + (tecnico == value.id ? "selected" : "") + '>' + value.nome + '</option>');
                            }
                        });
                    }
                });
            }
        });

        /**
         * Exibe informa dos arquivos selecionados que serão
         * anexado a solicitação
         */
        $('#inputArquivos').change(function () {
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

            $("#arquivos-novos").html(dados);
        });

        $('button[name=submit-dados]').button({
            icons: {
                primary: 'ui-icon-circle-check'
            }
        });

    });
</script>

<style type="text/css">
    div.browse-wrap {
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

    span.upload-path {
        text-align: center;
        margin:20px;
        display:block;
        font-size: 80%;
        color:#3b5998;
        font-weight:bold;
    }

    button[type='submit']{
        margin: auto 45%;
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

    <form action="<?= $link ?>"  method="post" enctype="multipart/form-data">
        <input type="hidden" name="inputID" id="inputID" value="0" />
        <input type="hidden" name="solicitacaoOrigem" id="solicitacaoOrigem" value="<?= $solicitacaoOrigem; ?>" />

        <div class="row">
            <div class="six columns">
                <label for="selectProjeto" class="four columns">
                    Projeto:
                </label>
                <select name="selectProjeto" required class="eight columns" id="selectProjeto">
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
            <div class="six columns">
                <label for="selectSolicitante" class="four columns">
                    Solicitante:
                </label>
                <select name="selectSolicitante" required class="eight columns" id="selectSolicitante">
                    <option value="0" disabled selected>Solicitante</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="six columns">
                <label for="selectPrioridade" class="four columns">
                    Prioridade: <span class="text-danger">*</span>
                </label>
                <select name="selectPrioridade" required id="selectPrioridade" class="eight columns">
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
            <div class="six columns">
                <label for="selectTecnico" class="four columns">Técnico:</label>
                <select name="selectTecnico" class="eight columns" id="selectTecnico">
                    <option value="0" disabled selected>Técnico</option>
                </select>
            </div>
        </div>

        <div class="row">
            <label for="textareaDescricao" class="twelve columns">Descrição:</label>
            <div class="twelve columns">
                <textarea name="textareaDescricao" id="textareaDescricao"></textarea>
            </div>
        </div>

        <div class="row">
            <div class="twelve columns">
                <div class="browse-wrap">
                    <div class="title">Anexar arquivos a esta solicitação</div>
                    <input type="file" name="inputArquivos[]" class="upload form-control" id="inputArquivos"
                           title="Anexar arquivos a esta solicitação" multiple>
                </div>
                <span id="arquivos-novos" class="upload-path"></span>
            </div>
        </div>

        <div class="row">
            <div class="twelve columns" id="arquivos-antigos">
            </div>
        </div>

        <div class="row">
            <div class="twelve columns">
                <button type="submit" name="submit-dados">
                    Salvar
                </button>
            </div>
        </div>

    </form>
</div>