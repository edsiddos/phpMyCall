
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table-locale-all.min.js') ?>"></script>

<link href="<?= base_url('static/bootstrap-table/css/bootstrap-table.min.css') ?>" rel="stylesheet">

<script type="text/javascript">
    $(document).ready(function () {

        $('#accordion').accordion({
            collapsible: true
        });
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

    function actionFormatter() {
        return [
            '<button type="button" class="btn btn-default btn-sm visualizar" title="<?= $visualize_request ?>">',
            '<i class="fa fa-eye"></i>',
            '</button>'
        ].join('');
    }

    window.actionEvents = {
        'click .visualizar': function (e, value, row, index) {
            $(location).attr('href', '<?= base_url("solicitacao/visualizar") ?>/' + row.solicitacao);
        }
    };

</script>

<style type="text/css">
    td:first-child {
        text-align: center;
    }
</style>

<div class="container">

    <div id="accordion">
        <h3>
            <?= $open_requests ?>
        </h3>
        <div>

            <table
                id="solicitacoes_abertas"
                data-toggle="table"
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
                        <th data-field="action" data-formatter="actionFormatter" data-events="actionEvents"></th>
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

        <h3>
            <?= $request_for_service ?>
        </h3>
        <div>

            <div style="height: 350px;">

                <table
                    id="solicitacoes_atendimentos"
                    data-toggle="table"
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
                            <th data-field="action" data-formatter="actionFormatter" data-events="actionEvents"></th>
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

</div>