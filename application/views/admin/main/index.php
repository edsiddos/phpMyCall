
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table-locale-all.min.js') ?>"></script>

<link href="<?= base_url('static/bootstrap-table/css/bootstrap-table.min.css') ?>" rel="stylesheet">

<script type="text/javascript">
    $(document).ready(function () {

        $('#accordion').collapse({});

    });

    var buscaSolicitacoesAbertas = function (params) {
        var data = JSON.parse(params.data);
        data.situacao = 1; //Prioridade
        data.prioridade = 0; //Normal

        $.ajax({
            url: '<?= base_url('solicitacao/lista_solicitacoes') ?>',
            data: data,
            type: 'post',
            dataType: 'json'
        }).done(function (data) {
            params.success(data);
            params.complete();
        });
    };

    var buscaSolicitacoesAtendimentos = function (params) {
        var data = JSON.parse(params.data);
        data.situacao = 2; //Prioridade
        data.prioridade = 0; //Normal

        $.ajax({
            url: '<?= base_url('solicitacao/lista_solicitacoes') ?>',
            data: data,
            type: 'post',
            dataType: 'json'
        }).done(function (data) {
            params.success(data);
            params.complete();
        });
    };
</script>


<div class="container">

    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingAbertos">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#abertos" aria-expanded="true" aria-controls="abertos">
                        <?= $open_requests ?>
                    </a>
                </h4>
            </div>
            <div id="abertos" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingAbertos">
                <div class="panel-body">

                    <table
                        id="solicitacoes_abertas"
                        data-toggle="table"
                        data-height="400"
                        data-ajax="buscaSolicitacoesAbertas"
                        data-side-pagination="server"
                        data-pagination="true"
                        data-method="post"
                        data-page-list="[5, 10, 20, 50, 100, 200]"
                        data-locale="pt-BR"
                        data-search="true"
                        data-sort-name="abertura"
                        data-sort-order="asc">
                        <thead>
                            <tr>
                                <th data-field="abertura" data-sortable="true"><?= $open ?></th>
                                <th data-field="projeto" data-sortable="true"><?= $product ?></th>
                                <th data-field="problema" data-sortable="true"><?= $label ?></th>
                                <th data-field="prioridade" data-sortable="true"><?= $priority ?></th>
                                <th data-field="solicitante" data-sortable="true"><?= $requester ?></th>
                                <th data-field="atendente" data-sortable="true"><?= $attendant ?></th>
                                <th data-field="num_arquivos" data-sortable="true"><?= $n_files ?></th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingAtendimentos">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#atendimentos" aria-expanded="false" aria-controls="atendimentos">
                        <?= $request_for_service ?>
                    </a>
                </h4>
            </div>
            <div id="atendimentos" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingAtendimentos">
                <div class="panel-body">
                    <table
                        id="solicitacoes_atendimentos"
                        data-toggle="table"
                        data-height="400"
                        data-ajax="buscaSolicitacoesAtendimentos"
                        data-side-pagination="server"
                        data-pagination="true"
                        data-method="post"
                        data-page-list="[5, 10, 20, 50, 100, 200]"
                        data-locale="pt-BR"
                        data-search="true"
                        data-sort-name="abertura"
                        data-sort-order="asc">
                        <thead>
                            <tr>
                                <th data-field="abertura"><?= $open ?></th>
                                <th data-field="projeto"><?= $product ?></th>
                                <th data-field="problema"><?= $label ?></th>
                                <th data-field="prioridade"><?= $priority ?></th>
                                <th data-field="solicitante"><?= $requester ?></th>
                                <th data-field="atendente"><?= $attendant ?></th>
                                <th data-field="num_arquivos"><?= $n_files ?></th>
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>