

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

        <table class="table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 40%;">Função</th>
                    <th colspan="<?= count($perfis) ?>">Níveis de acesso</th>
                </tr>
                <tr>
                    <?php
                    $width = 60 / count($perfis);

                    foreach ($perfis as $perfil):
                        ?>
                        <th style="width: <?= $width . '%' ?>">
                            <?= $perfil['perfil'] ?>
                        </th>
                        <?php
                    endforeach;
                    ?>
                </tr>
            </thead>
            <tbody>

                <?php
                foreach ($config_solicitacoes as $config):
                    $autorizado = explode(', ', $config['texto']);
                    ?>
                    <tr>
                        <td>
                            <?= $config['comentario'] ?>
                        </td>

                        <?php
                        foreach ($perfis as $perfil):
                            ?>
                            <td>
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox"
                                           name="config_solicitacao"
                                           perfil="<?= $perfil['id'] ?>"
                                           config="<?= $config['parametro'] ?>"
                                           <?= array_search($perfil['id'], $autorizado) !== FALSE ? 'checked' : ''; ?>>
                                    <label></label>
                                </div>
                            </td>
                            <?php
                        endforeach;
                        ?>

                    </tr>
                    <?php
                endforeach;
                ?>

            </tbody>
        </table>

    </div>

    <!---------------------------->
    <!-- Edição das prioridades -->
    <!---------------------------->

    <div id="prioridades">

        <table class="table">
            <thead>
                <tr>
                    <th>Default</th>
                    <th>Nome</th>
                    <th>Nivel</th>
                    <th>Cor</th>
                </tr>
            </thead>
        </table>

    </div>

    <!----------------------------------->
    <!-- Liberação de acesso aos menus -->
    <!----------------------------------->

    <div id="menu">

    </div>
</div>