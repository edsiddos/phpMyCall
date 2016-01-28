
<div class="row text-center ">
    <div class="col-md-12">
        <br /><br />
        <h2>
            <?= $phpmycall_auth ?>
        </h2>
        <br />
    </div>
</div>
<div class="row ">

    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong> <?= $help_view ?> </strong>  
            </div>
            <div class="panel-body">
                <form role="form" action="<?= site_url('login/autenticar') ?>" method="post">
                    <br />
                    <div class="form-group input-group">
                        <span class="input-group-addon"><i class="fa fa-user"  ></i></span>
                        <input type="text" name="usuario" class="form-control" placeholder="<?= $user ?>" />
                    </div>
                    <div class="form-group input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
                        <input type="password" name="senha" class="form-control"  placeholder="<?= $password ?>" />
                    </div>

                    <div class="col-md-offset-4">
                        <button type="submit" class="btn btn-primary "><?= $sign_in ?></button>
                    </div>
                    <hr />
                </form>
            </div>

        </div>
    </div>

</div>
