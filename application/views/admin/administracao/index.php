

<script type="text/javascript">

    $(document).ready(function () {
        $table_configuracoes = $('#tab_configuracoes');

        $table_configuracoes.tabs();
    });

</script>

<div id="tab_configuracoes" title="">

    <ul>
        <li>
            <a href="#config_solicitacoes">
                <?= $title_tab_edit_config_request ?>
            </a>
        </li>
        <li>
            <a href="#prioridades">
                <?= $title_tab_edit_priority ?>
            </a>
        </li>
        <li>
            <a href="#menu">
                <?= $title_tab_access_menu ?>
            </a>
        </li>
    </ul>

    <!----------------------------------->
    <!-- Configuração das solicitações -->
    <!----------------------------------->

    <div id="config_solicitacoes">

    </div>

    <!---------------------------->
    <!-- Edição das prioridades -->
    <!---------------------------->

    <div id="prioridades">

    </div>

    <!----------------------------------->
    <!-- Liberação de acesso aos menus -->
    <!----------------------------------->

    <div id="menu">

    </div>
</div>