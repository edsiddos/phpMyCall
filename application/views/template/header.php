<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?= $title ?></title>

        <!-- BOOTSTRAP STYLES-->
        <link href="<?= site_url() . 'static/css/bootstrap.min.css' ?>" rel="stylesheet" />
        <link href="<?= site_url() . 'static/css/bootstrap-theme.min.css' ?>" rel="stylesheet" />
        <!-- FONTAWESOME STYLES-->
        <link href="<?= site_url() . 'static/css/font-awesome.min.css' ?>" rel="stylesheet" />
        <!-- MORRIS CHART STYLES-->
        <link href="<?= site_url() . 'static/css/metisMenu.min.css' ?>" rel="stylesheet" />
        <!-- JQuery UI -->
        <link href="<?= site_url() . 'static/css/jquery-ui.min.css' ?>" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
        <link href="<?= site_url() . 'static/css/custom.css' ?>" rel="stylesheet" />

        <!-- JQUERY SCRIPTS -->
        <script src="<?= site_url() . 'static/js/jquery.min.js' ?>"></script>
        <!-- BOOTSTRAP SCRIPTS -->
        <script src="<?= site_url() . 'static/js/bootstrap.min.js' ?>"></script>
        <!-- METISMENU SCRIPTS -->
        <script src="<?= site_url() . 'static/js/metisMenu.min.js' ?>"></script>
        <!-- JQUERY UI -->
        <script src="<?= site_url() . 'static/js/jquery-ui.min.js' ?>"></script>
        <!-- CUSTOM SCRIPTS -->
        <script src="<?= site_url() . 'static/js/custom.js' ?>"></script>

    </head>
    <body>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div style="color: white; padding: 15px 50px 5px 50px; float: right; font-size: 16px;">
                    <a href="<?= site_url() . 'login/logout' ?>" class="btn btn-default square-btn-adjust">Logout</a>
                </div>
            </nav>
            <!-- /. NAV TOP  -->

            <nav class="navbar-default navbar-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav" id="main-menu">
                        <li class="text-center">
                            <img src="<?= site_url() . 'static/img/logo.png' ?>" class="user-image img-responsive"/>
                        </li>



                        <?php
                        $result = $this->cache->apc->get('menu');

                        if ($result === false) {
                            $result = Menu::geraMenuPorPerfil();
                            $this->cache->apc->save('menu', $result);
                        }

                        foreach ($result [$_SESSION ['perfil']] as $nome_menu => $menu) {
                            if (is_array($menu)) {
                                ?>
                                <li>
                                    <a href="#">
                                        <?= $nome_menu ?>
                                        <span class="fa arrow"></span>
                                    </a>
                                    <ul class="nav nav-second-level">
                                        <?php
                                        foreach ($menu as $nome_submenu => $submenu) {
                                            if (is_array($submenu)) {
                                                ?>
                                                <li>
                                                    <a href="#">
                                                        <?= $nome_submenu ?> <span class="caret-right"></span>
                                                        <span class="fa arrow"></span>
                                                    </a>
                                                    <ul class="nav nav-third-level">
                                                        <?php
                                                        foreach ($submenu as $nome_opcao => $opcao) {
                                                            ?>
                                                            <li>
                                                                <a href="<?= site_url() . $opcao ?>"><?= $nome_opcao ?></a>
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
                                                    <a href="<?= site_url() . $submenu ?>"><?= $nome_submenu ?></a>
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
                                    <a href="<?= site_url() . $menu ?>"><?= $nome_menu ?></a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>

                </div>

            </nav>
            <!-- /. NAV SIDE  -->

            <div id="page-wrapper" >
                <div id="page-inner">