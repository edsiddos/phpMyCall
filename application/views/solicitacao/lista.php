

<script type="text/javascript">
    $(document).ready(function () {

        $('tbody > tr').click(function () {
            $(location).attr('href', '<?= HTTP . "/Solicitacao/visualizar" ?>/' + $(this).attr('solicitacao'));
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

    <div>
        <table class="table-solicitacoes u-full-width">
            <thead>
                <tr>
                    <th class="col3 text-center">Projeto</th>
                    <th class="col2 text-center">Problema</th>
                    <th class="col1 text-center">Prioridade</th>
                    <th class="text-center">Solicitante</th>
                    <th class="text-center">Atendente</th>
                    <th class="text-center">Abertura</th>
                    <th class="text-center">Q. Arquivos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($solicitacoes as $values) {
                    ?>
                    <tr style="background-color: <?= $prioridades[$values['prioridade']] ?>" solicitacao="<?= $values['solicitacao']; ?>">
                        <td>
                            <?= $values['projeto'] ?>
                        </td>
                        <td>
                            <?= $values['problema'] ?>
                        </td>
                        <td>
                            <?= $values['prioridade'] ?>
                        </td>
                        <td>
                            <?= $values['solicitante'] ?>
                        </td>
                        <td>
                            <?= $values['atendente'] ?>
                        </td>
                        <td>
                            <?= $values['abertura'] ?>
                        </td>
                        <td>
                            <?= $values['arquivos'] ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>