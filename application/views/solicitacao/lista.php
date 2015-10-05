
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/jquery.dataTables.min.js' ?>"></script>
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/dataTables.jqueryui.min.js' ?>"></script>
<script type="text/javascript" src="<?= base_url() . 'static/js/datatable/dataTables.responsive.min.js' ?>"></script>

<link href="<?= base_url() . 'static/css/datatable/dataTables.jqueryui.min.css' ?>" rel="stylesheet">
<link href="<?= base_url() . 'static/css/datatable/responsive.jqueryui.min.css' ?>" rel="stylesheet">

<script type="text/javascript">
    $(document).ready(function () {

        var datatable = $('#table_solicitacoes').DataTable({
            ordering: true,
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url() . 'solicitacao/lista_solicitacoes' ?>",
                type: "POST",
                data: function (data) {
                    data.situacao = $('select[name=situacao]').val();
                    data.prioridade = $('select[name=prioridade]').val();
                }
            },
            language: {
                url: "<?= base_url() . 'static/js/datatable/pt_br.json' ?>"
            },
            columns: [
                {"data": "abertura"},
                {"data": "projeto"},
                {"data": "problema"},
                {"data": "prioridade"},
                {"data": "solicitante"},
                {"data": "atendente"},
                {"data": "num_arquivos"}
            ]
        }).on('click', 'tr', function () {
            var data = datatable.row(this).data();

            $(location).attr('href', '<?= base_url() . "solicitacao/visualizar" ?>/' + data.solicitacao);
        });

        $('select').on('change', function () {
            datatable.ajax.reload();
        });

    });
</script>


<div class="container">

    <?php
    if ((!empty($_SESSION ['msg_erro'])) || (!empty($_SESSION ['msg_sucesso']))) {
        ?>
        <div
            class="alert <?= empty($_SESSION['msg_erro']) ? 'alert-success' : 'alert-danger'; ?> alert-error text-center">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?= empty($_SESSION['msg_erro']) ? $_SESSION['msg_sucesso'] : $_SESSION['msg_erro']; ?>
        </div>
        <?php
        unset($_SESSION ['msg_erro']);
        unset($_SESSION ['msg_sucesso']);
    }
    ?>

    <div class="form-horizontal">
        <div class="row">
            <div class="form-group col-md-6">
                <label class="col-md-4 control-label">Situação</label>
                <div class="col-md-8">
                    <select name="situacao" class="selectpicker">
                        <option value="">Todas</option>
                        <option value="1">Aberta</option>
                        <option value="2">Atendimento</option>
                        <option value="3">Encerrada</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label class="col-md-4 control-label">Prioridade</label>
                <div class="col-md-8">
                    <select name="prioridade" class="selectpicker">
                        <option value="">Todas</option>
                        <?php
                        foreach ($prioridades as $values) {
                            ?>
                            <option value="<?= $values['id'] ?>">
                                <?= $values['nome'] ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <table id="table_solicitacoes" class="display responsive nowrap" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Projeto</th>
                    <th>Problema</th>
                    <th>Prioridade</th>
                    <th>Solicitante</th>
                    <th>Atendente</th>
                    <th>Abertura</th>
                    <th>Q. Arquivos</th>
                </tr>
            </thead>
        </table>
    </div>
</div>