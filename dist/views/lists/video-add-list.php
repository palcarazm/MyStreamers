<?php
use Model\Video;
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Añadir videos de YouTube</h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<main class="content">

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de mis videos de YouTube</h3>
        </div>
        <div class="card-body table-responsive">
            <?php if($youtubeAlert):?>
                <div class="alert alert-warning" role="alert">
                Se produjo un error en la recuperación de vídeo de YouTube.
                </div>
            <?php endif;
            if($canalesAlert):?>
                <div class="alert alert-danger" role="alert">
                Debe asociar sus canales de YouTube en su <a href="/admin/miperfil">perfil</a> antes de emplear esta sección.
                </div>
            <?php endif;
            if(!$youtubeAlert & !$canalesAlert):?>
            <table class="data table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $video) {
                        Video::printRowTemp($video);
                    } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </tfoot>
            </table>
            <?php endif;?>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

</main>