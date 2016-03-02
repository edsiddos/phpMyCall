
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('static/bootstrap-table/js/bootstrap-table-locale-all.min.js') ?>"></script>

<link href="<?= base_url('static/bootstrap-table/css/bootstrap-table.min.css') ?>" rel="stylesheet">

<script type="text/javascript">

    $(document).ready(function () {
        $table_solicitacoes = $('#solicitacoes');

        $('select').on('change', function () {
            $table_solicitacoes.bootstrapTable('refresh', {silent: true});
        });
    });

    var buscaSolicitacoes = function (params) {
        var data = JSON.parse(params.data);
        data.situacao = $('select[name=situacao]').val();
        data.prioridade = $('select[name=prioridade]').val();

        $.ajax({
            url: '<?= base_url('solicitacao/lista_solicitacoes') ?>',
            data: data,
            type: 'post',
            dataType: 'json'
        }).done(function (data) {
            params.success(data);
        });
    };

    function actionFormatter() {
        return [
            '<button type="button" class="btn btn-default btn-sm visualizar" title="<?= $label_visualize_request ?>">',
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
                <label class="col-md-4 control-label">
                    <?= $label_status_request ?>
                </label>
                <div class="col-md-8">
                    <select name="situacao" class="selectpicker form-control">
                        <option value=""><?= $label_option_all_status_request ?></option>
                        <option value="1"><?= $label_option_open_status_request ?></option>
                        <option value="2"><?= $label_option_in_service_request ?></option>
                        <option value="3"><?= $label_option_closed_request ?></option>
                    </select>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label class="col-md-4 control-label">
                    <?= $label_priority_request ?>
                </label>
                <div class="col-md-8">
                    <select name="prioridade" class="selectpicker form-control">
                        <option value="">
                            <?= $label_option_all_priority_request ?>
                        </option>
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

        <table
            id="solicitacoes"
            data-toggle="table"
            data-ajax="buscaSolicitacoes"
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
                    <th data-field="abertura" data-sortable="true"><?= $label_start_time_column_table_request ?></th>
                    <th data-field="projeto" data-sortable="true"><?= $label_project_name_column_table_request ?></th>
                    <th data-field="problema" data-sortable="true"><?= $label_problem_name_column_table_request ?></th>
                    <th data-field="prioridade" data-sortable="true"><?= $label_priority_name_column_table_request ?></th>
                    <th data-field="solicitante" data-sortable="true"><?= $label_requester_name_column_table_request ?></th>
                    <th data-field="atendente" data-sortable="true"><?= $label_technician_name_column_table_request ?></th>
                    <th data-field="num_arquivos" data-sortable="true"><?= $label_files_column_table_request ?></th>
                </tr>
            </thead>
        </table>

    </div>
</div>