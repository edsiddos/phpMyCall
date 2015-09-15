

<script type="text/javascript">
    $(document).ready(function () {

        $('tbody > tr').click(function () {
            $(location).attr('href', '<?= site_url() . "solicitacao/visualizar" ?>/' + $(this).attr('solicitacao'));
        });

        $('#accordion').accordion({
            collapsible: true
        });

    });
</script>


<div class="container">

    <div id="accordion">
        <h3>
            Solicitações em aberto
        </h3>
        <div>

            <table class="table col-lg-12">
                <thead>
                    <tr>
                        <th class="text-center col-lg-2">Projeto</th>
                        <th class="text-center col-lg-2">Problema</th>
                        <th class="text-center col-lg-2">Prioridade</th>
                        <th class="text-center col-lg-1">Solicitante</th>
                        <th class="text-center col-lg-2">Atendente</th>
                        <th class="text-center col-lg-2">Abertura</th>
                        <th class="text-center col-lg-1">Q. Arquivos</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>

        <h3>
            Solicitações em atendimento
        </h3>
        <div>

            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center col-lg-2">Projeto</th>
                        <th class="text-center col-lg-2">Problema</th>
                        <th class="text-center col-lg-2">Prioridade</th>
                        <th class="text-center col-lg-1">Solicitante</th>
                        <th class="text-center col-lg-2">Atendente</th>
                        <th class="text-center col-lg-2">Abertura</th>
                        <th class="text-center col-lg-1">Q. Arquivos</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>

    </div>

</div>