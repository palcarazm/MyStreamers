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
        <form action="/api/config/v1/youtube" method="put" class="border-secondary" id="updateYoutube-form">
            <div class="card-body">
                <p class="card-text">Ahora con la conexión con YouTube. Debes registrar una aplicación en la consola de desarrolladores de Google. <a href="https://developers.google.com/youtube/v3/getting-started?hl=es" target="_blank" rel="noopener noreferrer">Aquí tienes la guía de Google</a>.</p>
                <div class="alert alert-danger" role="alert">
                    No olvide activar el servicio YouTube Data API v3 en el proyecto de la consola de desarrolladores de Google desde el menú API y servicios > Biblioteca.
                </div>
                <div class="alert alert-warning" role="alert">
                    Dado que MyStreamers no requiere acceder a la información privada de su perfil, se recomiendo registrar la aplicación en una cuenta de Google secundaria para que en caso de que ocurra un compromiso de seguridad sus datos no sean exuestos
                </div>
                <div class="form-group row">
                    <label for="apiKey" class="col-sm-3 col-md-2 col-form-label">API Key *</label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" class="form-control" id="apiKey" name="apiKey" placeholder="API Key de la aplicación registrada en Google" required value="<?php echo YOUTUBE_APIKEY; ?>">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Registrar información de la conexión</button>
            </div>
        </form>
    </div>
    <!-- /.card -->

</main>