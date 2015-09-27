

<script type="text/javascript">
    $(document).ready(function () {

        /*
         * Mascára para hora
         */
        $('#inputResposta, #inputSolucao').mask('0HHH:M0', {
            translation: {
                'H': {pattern: /[0-9]/, optional: true},
                'M': {pattern: /[0-5]/}
            }
        }).attr('pattern', '[0-9]{1,4}:[0-5][0-9]');

        /*
         * Gera dialog para inserção dos dados do projeto
         * e participantes do projeto
         */
        $('#dialogProjetosProblemas').dialog({
            autoOpen: false,
            closeOnEscape: false,
            modal: true,
            width: '80%',
            height: $(window).height() * 0.95,
            buttons: [
                {
                    text: 'Salvar',
                    icons: {
                        primary: 'ui-icon-disk'
                    },
                    click: function () {
                        $(this).dialog('close');
                        projeto.submitFormulario();
                    }
                },
                {
                    text: 'Cancelar',
                    icons: {
                        primary: 'ui-icon-close'
                    },
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ],
            position: {my: 'center', at: 'center', of: window}
        });

        /*
         * Gera abas para inserção de dados do projeto
         * e para seleção dos participantes.
         */
        $('#projetoProblemas').tabs();

        /*
         * Gera autocomplete do nome do projeto
         * e busca dados do projeto ao perder o foco do input projeto
         */
        $("input[name=inputNomeProjeto]").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: '<?= HTTP . '/ProjetosProblemas/getProjetos' ?>',
                    data: request,
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        response(data);
                    }
                });
            }
        }).on('focusout', function () {
            $.ajax({
                url: '<?= HTTP . '/ProjetosProblemas/getDadosProjeto' ?>',
                data: 'nome=' + $(this).val(),
                dataType: 'json',
                type: 'post',
                success: function (data) {
                    $('textarea[name=textProjeto]').val(data.descricao_projeto);
                    multi.setOrigin(data.usuarios);
                    multi.setDestiny(data.participantes);
                }
            });
        });

        /*
         * Gera autocomplete do tipo de problema
         */
        $("input[name='inputNomeProblema']").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: '<?= HTTP . '/ProjetosProblemas/getProblemas' ?>',
                    data: request,
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        response(data);
                    }
                });
            }
        });

        $('#dialogProjetosProblemas').removeClass('hide');

    });
</script>


<div id="dialogProjetosProblemas" class="hide">
    <form name="projetoProblemas" id="projetoProblemas">

        <ul>
            <li><a href="#cadastroProjeto">Projeto tipo problema</a></li>
            <li><a href="#usuariosProjeto">Participantes</a></li>
        </ul>

        <div id="cadastroProjeto">
            <input type="hidden" name="inputProjeto" id="inputProjeto" value="0" />
            <input type="hidden" name="inputProblema" id="inputProblema" value="0" />
            <input type="hidden" name="inputProjetoProblema" id="inputProjetoProblema" value="0" />

            <fieldset>
                <legend>Projeto</legend>
                <div class="row">
                    <label for="inputNomeProjeto" class="columns four">Nome do projeto:</label>
                    <input type="text" class="columns eight" id="inputNomeProjeto" name="inputNomeProjeto"
                           placeholder="Projeto" maxlength="100">
                </div>

                <div class="row">
                    <label for="textProjeto" class="columns four">Descrição do projeto (Opcional):</label>
                    <textarea class="columns eight" id="textProjeto" name="textProjeto"
                              placeholder="Descrição do projeto" maxlength="500"></textarea>
                </div>
            </fieldset>

            <fieldset>
                <legend>Problema</legend>
                <div class="row">
                    <label for="inputNomeProblema" class="columns four">Tipo de Problema:</label>
                    <input type="text" class="columns eight" id="inputNomeProblema" name="inputNomeProblema" placeholder="Tipo de problema" maxlength="100">
                </div>

                <div class="row">
                    <label for="textDescricao" class="columns four">Descrição do tipo de problemas:</label>
                    <textarea id="textDescricao" class="columns eight" maxlength="1000" name="textDescricao" placeholder="Descrição do tipo de problema"></textarea>
                </div>
            </fieldset>

            <fieldset>
                <legend>Prazos</legend>
                <div class="row">
                    <label for="inputResposta" class="columns two">Resposta:</label>
                    <input type="text" class="columns four" id="inputResposta" name="inputResposta" placeholder="Tempo de resposta">

                    <label for="inputSolucao" class="columns two">Solução:</label>
                    <input type="text" class="columns four" id="inputSolucao" name="inputSolucao" placeholder="Tempo para solução">
                </div>
            </fieldset>
        </div>

        <div id="usuariosProjeto">
            <div id="relacaoUsuarios"></div>
        </div>

    </form>
</div>