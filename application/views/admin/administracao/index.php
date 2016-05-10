

<script type="text/javascript">

    $(document).ready(function () {
        $table_configuracoes = $('#tab_configuracoes');
        $checkbox_config_solicitacao = $('input[type=checkbox][name=config_solicitacao]');
        $dialog_alert = $('#dialog_alert');
        $solicitacao_prioridade = $('input[type=radio][name=prioridade]');
        $input_cor_prioridade = $('input[type=color][prioridade]');
        $config_menu = $('input[type=checkbox][name=config_menu]');

        $table_configuracoes.tabs();

        $checkbox_config_solicitacao.on('change', function () {
            var config = $(this).attr('config');
            var perfil = $(this).attr('perfil');
            var checked = $(this).prop('checked');

            $.ajax({
                url: '<?= site_url('administracao/grava_config_solicitacao') ?>',
                data: {
                    config: config,
                    perfil: perfil,
                    checked: checked
                },
                dataType: 'json',
                type: 'post'
            }).always(function (data, status) {
                if (status === 'success') {
                    if (data.status === true) {
                        $dialog_alert.dialog('Alteração feita com sucesso.');
                    } else {
                    }
                } else {
                }
            });
        });

        /**********************************************/
        /*        Configuração das prioridades        */
        /**********************************************/

        $solicitacao_prioridade.on('change', function () {
            var value = $(this).val();

            $.ajax({
                url: '<?= site_url('administracao/grava_prioridade_solicitacao') ?>',
                data: {
                    prioridade: value
                },
                dataType: 'json',
                type: 'post'
            }).always(function (data, status) {
                if (status === 'success') {
                    if (data.status === true) {
                        $dialog_alert.dialog('Alteração feita com sucesso.');
                    } else {
                    }
                } else {
                }
            });
        });

        $input_cor_prioridade.on('change', function () {
            var prioridade = $(this).attr('prioridade');
            var cor = $(this).val();

            $.ajax({
                url: '<?= site_url('administracao/altera_cor_prioridade') ?>',
                data: {
                    prioridade: prioridade,
                    cor: cor
                },
                dataType: 'json',
                type: 'post'
            });
        });

        /**********************************************/
        /*      Configuração de acesso aos menus      */
        /**********************************************/

        $config_menu.on('click', function () {
            var menu = $(this).attr('menu');
            var perfil = $(this).attr('perfil');
            var checked = $(this).prop('checked');

            $.ajax({
                url: '<?= site_url('administracao/altera_acesso_menus') ?>',
                data: {
                    menu: menu,
                    perfil: perfil,
                    checked: checked
                },
                dataType: 'json',
                type: 'post'
            });
        });

        /**********************************************/
        /*                Dialog alert                */
        /**********************************************/

        $dialog_alert.dialog({
            autoOpen: false,
            modal: true,
            closeOnEscape: false,
            button: [
                {
                    title: 'OK',
                    icons: {
                        primary: 'ui-icon-check'
                    },
                    click: function () {
                        $dialog_alert.dialog('close');
                    }
                }
            ]
        });
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
            <tbody>
                <?php
                foreach ($prioridades as $prioridade):
                    ?>
                    <tr>
                        <td>
                            <div class="radio radio-primary">
                                <input type="radio" name="prioridade" value="<?= $prioridade['id'] ?>" <?= $prioridade['padrao'] === TRUE ? 'checked' : '' ?> />
                                <label></label>
                            </div>
                        </td>
                        <td>
                            <?= $prioridade['nome'] ?>
                        </td>
                        <td>
                            <?= $prioridade['nivel'] ?>
                        </td>
                        <td>
                            <input type="color" value="<?= $prioridade['cor'] ?>" prioridade="<?= $prioridade['id'] ?>" class="form-control cor" />
                        </td>
                    </tr>
                    <?php
                endforeach;
                ?>
            </tbody>
        </table>

    </div>

    <!----------------------------------->
    <!-- Liberação de acesso aos menus -->
    <!----------------------------------->

    <div id="menu">

        <table class="table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 20%;">Menu</th>
                    <th rowspan="2" style="width: 20%;">Sub - Menu</th>
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
                foreach ($menus['menus'] as $cod_menu => $menu):
                    $autorizado = explode(', ', $config['texto']);

                    $count_sub_menus = count($menu['sub_menus']);
                    ?>
                    <tr>
                        <td rowspan="<?= $count_sub_menus ?>">
                            <?= $menu['nome'] ?>
                        </td>

                        <?php
                        $cod_anterior = 0;
                        foreach ($menu['sub_menus'] as $cod_sub_menu => $submenu):

                            if ($cod_anterior != 0):
                                ?>
                            <tr>
                                <?php
                            endif;
                            ?>
                            <td>
                                <?= $submenu['nome'] ?>
                            </td>
                            <?php
                            foreach ($perfis as $perfil):
                                ?>
                                <td>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox"
                                               name="config_menu"
                                               perfil="<?= $perfil['id'] ?>"
                                               menu="<?= $cod_sub_menu ?>"
                                               <?= isset($menus['configuracao'][$cod_sub_menu][$perfil['id']]) && $menus['configuracao'][$cod_sub_menu][$perfil['id']] ? 'checked' : ''; ?>>
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

                    </tr>
                    <?php
                endforeach;
                ?>

            </tbody>
        </table>

    </div>
</div>


<div id="dialog_alert" title="Atenção"></div>