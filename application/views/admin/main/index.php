
<script type="text/javascript" src="<?= base_url('static/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/datatables/js/dataTables.jqueryui.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/datatables-responsive/js/dataTables.responsive.js') ?>"></script>

<link href="<?= base_url('static/datatables/css/dataTables.jqueryui.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('static/datatables-responsive/css/responsive.jqueryui.css') ?>" rel="stylesheet">

<script type="text/javascript">
    $(document).ready(function () {

        $('#accordion').accordion({
            collapsible: true
        });

        /****************************************************************/

        var solicitacoes_abertas = $('#solicitacoes_abertas').DataTable({
            ordering: true,
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('solicitacao/lista_solicitacoes') ?>",
                type: "POST",
                data: function (data) {
                    data.situacao = 1;
                    data.prioridade = 0;
                }
            },
            language: {
                url: "<?= base_url('static/datatables/js/pt_br.json') ?>"
            },
            columns: [
                {data: "abertura"},
                {data: "projeto"},
                {data: "problema"},
                {data: "prioridade"},
                {data: "solicitante"},
                {data: "atendente"},
                {data: "num_arquivos"}
            ]
        }).on('click', 'tr', function () {
            var data = solicitacoes_abertas.row(this).data();

            $(location).attr('href', '<?= base_url('solicitacao/visualizar') ?>/' + data.solicitacao);
        });

        /****************************************************************/

        var solicitacoes_atendimentos = $('#solicitacoes_atendimentos').DataTable({
            ordering: true,
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "<?= base_url('solicitacao/lista_solicitacoes') ?>",
                type: "POST",
                data: function (data) {
                    data.situacao = 2;
                    data.prioridade = 0;
                }
            },
            language: {
                url: "<?= base_url('static/datatables/js/pt_br.json') ?>"
            },
            columns: [
                {data: "abertura"},
                {data: "projeto"},
                {data: "problema"},
                {data: "prioridade"},
                {data: "solicitante"},
                {data: "atendente"},
                {data: "num_arquivos"}
            ]
        }).on('click', 'tr', function () {
            var data = solicitacoes_atendimentos.row(this).data();

            $(location).attr('href', '<?= base_url('solicitacao/visualizar') ?>/' + data.solicitacao);
        });

    });
</script>


<div class="container">

    <div id="accordion">
        <h3>
            <?= $open_requests ?>
        </h3>
        <div>

            <table id="solicitacoes_abertas" class="display responsive nowrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th><?= $open ?></th>
                        <th><?= $product ?></th>
                        <th><?= $label ?></th>
                        <th><?= $priority ?></th>
                        <th><?= $requester ?></th>
                        <th><?= $attendant ?></th>
                        <th><?= $n_files ?></th>
                    </tr>
                </thead>
            </table>

        </div>

        <h3>
            <?= $request_for_service ?>
        </h3>
        <div>

            <div style="height: 350px;">
                <table id="solicitacoes_atendimentos" class="display responsive nowrap" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= $open ?></th>
                            <th><?= $product ?></th>
                            <th><?= $label ?></th>
                            <th><?= $priority ?></th>
                            <th><?= $requester ?></th>
                            <th><?= $attendant ?></th>
                            <th><?= $n_files ?></th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>

    </div>

</div>