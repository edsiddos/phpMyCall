<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>Login</title>

        <!-- BOOTSTRAP STYLES-->
        <link href="<?= site_url('static/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet" />
        <!-- FONTAWESOME STYLES-->
        <link href="<?= site_url('static/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
        <link href="<?= site_url('static/custom.css') ?>" rel="stylesheet" />

    </head>
    <body>
        <div class="container">
            <div class="row text-center ">
                <div class="col-md-12">
                    <br /><br />
                    <h2>phpMyCall - Autenticação</h2>
                    <br />
                </div>
            </div>
            <div class="row ">

                <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong> Informe usuário e senha </strong>  
                        </div>
                        <div class="panel-body">
                            <form role="form" action="<?= site_url() . 'login/autenticar' ?>" method="post">
                                <br />
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"  ></i></span>
                                    <input type="text" name="usuario" class="form-control" placeholder="Usuário" />
                                </div>
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
                                    <input type="password" name="senha" class="form-control"  placeholder="Senha" />
                                </div>

                                <div class="col-md-offset-4">
                                    <button type="submit" class="btn btn-primary ">Efetuar login</button>
                                </div>
                                <hr />
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>


        <!-- JQUERY SCRIPTS -->
        <script src="<?= site_url('static/jquery/js/jquery.min.js') ?>"></script>
        <!-- BOOTSTRAP SCRIPTS -->
        <script src="<?= site_url('static/bootstrap/js/bootstrap.min.js') ?>"></script>

    </body>
</html>