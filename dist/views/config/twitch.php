<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Configuración de la conexión con Twitch</h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<main class="content">

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de configuración de la conexión con Twitch</h3>

            <!-- <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div> -->
        </div>
        <form action="/api/config/v1/twitch" method="put" class="border-secondary step-body" id="updateTwitch-form">
            <div class="card-body">
                <div class="alert alert-warning" role="alert">
                    Dado que MyStreamers no requiere acceder a la información privada de su perfil, se recomiendo registrar la aplicación en una cuenta de twitch secundaria para que en caso de que ocurra un compromiso de seguridad sus datos no sean expuestos.
                </div>
                <div class="form-group row">
                    <label for="client_id" class="col-sm-3 col-md-2 col-form-label">Client ID</label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" class="form-control" id="client_id" name="client_id" placeholder="Client ID de la aplicación registrada en Twitch" value="<?php echo TWITCH_CLIENT_ID; ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="client_secret" class="col-sm-3 col-md-2 col-form-label">Client Secret</label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" class="form-control" secret="client_secret" name="client_secret" placeholder="Client Secret de la aplicación registrada en Twitch" value="<?php echo TWITCH_CLIENT_SECRET; ?>" required>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Registrar información de la conexión</button>
            </div>
            <!-- /.card-footer-->
        </form>
    </div>
    <!-- /.card -->

</main>