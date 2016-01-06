<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?= $title ?></title>

        <!-- BOOTSTRAP STYLES-->
        <link href="<?= base_url('static/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" />
        <link href="<?= base_url('static/bootstrap-select/css/bootstrap-select.min.css') ?>" rel="stylesheet" />
        <link href="<?= base_url('static/awesome-bootstrap-checkbox/css/awesome-bootstrap-checkbox.css') ?>" rel="stylesheet" />
        <!-- FONTAWESOME STYLES-->
        <link href="<?= base_url('static/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet" />
        <!-- JQuery UI -->
        <link href="<?= base_url('static/jquery-ui/css/jquery-ui.min.css') ?>" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
        <link href="<?= base_url('static/custom.css') ?>" rel="stylesheet" />
        <link href="<?= base_url('static/mycallstyle/css/jquery.ui.icons.theme.css') ?>" rel="stylesheet" />
        <link href="<?= base_url('static/mycallstyle/css/jquery.ui.theme.css') ?>" rel="stylesheet" />
        <link href="<?= base_url('static/aguarde/css/aguarde.css') ?>" rel="stylesheet" />

        <!-- JQUERY SCRIPTS -->
        <script src="<?= base_url('static/jquery/js/jquery.js') ?>"></script>
        <!-- BOOTSTRAP SCRIPTS -->
        <script src="<?= base_url('static/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?= base_url('static/bootstrap-select/js/bootstrap-select.min.js') ?>"></script>
        <script src="<?= base_url('static/bootstrap-select/js/defaults-pt_BR.min.js') ?>"></script>
        <!-- JQUERY UI -->
        <script src="<?= base_url('static/jquery-ui/js/jquery-ui.min.js') ?>"></script>
        <!-- CUSTOM SCRIPTS -->
        <script src="<?= base_url('static/aguarde/js/aguarde.js') ?>"></script>

    </head>
    <body>

        <!-- Fixed navbar -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">phpMyCall</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?= base_url('main/index') ?>">
                        phpMyCall
                    </a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">

                    <ul class="nav navbar-nav">
                        <li>
                            <a href="<?= base_url('main/index') ?>">Home</a>
                        </li>

                        <?php
                        $result = $this->cache->apc->get('menu');

                        if ($result === false) {
                            $result = Menu::gera_menu_por_perfil();
                            $this->cache->apc->save('menu', $result, TTL_CACHE);
                        }

                        foreach ($result [$_SESSION ['perfil']] as $nome_menu => $menu) {
                            if (is_array($menu)) {
                                ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <?= $nome_menu ?>
                                        <span class="fa arrow"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php
                                        foreach ($menu as $nome_submenu => $submenu) {
                                            if (is_array($submenu)) {
                                                ?>
                                                <li class="dropdown">
                                                    <a href="#"class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                        <?= $nome_submenu ?> <span class="caret-right"></span>
                                                        <span class="fa arrow"></span>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <?php
                                                        foreach ($submenu as $nome_opcao => $opcao) {
                                                            ?>
                                                            <li>
                                                                <a href="<?= base_url($opcao) ?>"><?= $nome_opcao ?></a>
                                                            </li>
                                                            <?php
                                                        }
                                                        ?>
                                                    </ul>
                                                </li>
                                                <?php
                                            } else {
                                                ?>
                                                <li>
                                                    <a href="<?= base_url($submenu) ?>"><?= $nome_submenu ?></a>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </li>
                                <?php
                            } else {
                                ?>
                                <li>
                                    <a href="<?= base_url($menu) ?>"><?= $nome_menu ?></a>
                                </li>
                                <?php
                            }
                        }
                        ?>

                        <li>
                            <a href="<?= base_url('login/logout') ?>">Logout</a>
                        </li>
                    </ul>

                </div><!--/.nav-collapse -->
            </div>
        </nav>

        <!-- Begin page content -->
        <div class="container">
