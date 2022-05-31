<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1><?php echo is_null($enlace) ? 'Crear' : 'Editar'; ?> enlace</h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<main class="content">

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de enlace</h3>
        </div>
        <?php if (is_null($enlace)) : ?>
            <form enctype="multipart/form-data" action="/api/user/v1/link" method="POST" class="border-secondary" id="link-form" form-success="reset">
            <?php else : ?>
                <form enctype="multipart/form-data" action="/api/user/v1/link<?php echo '?id=' . $enlace->getID(); ?>" method="PUT" class="border-secondary" id="link-form" form-success="redirect" destino="/admin/usuarios/enlaces/listar">
                <?php endif; ?>
                <div class="card-body row">
                    <div class="form-group row col-sm-12 col-md-8">
                        <label for="tipo" class="col-sm-3 col-md-auto col-form-label">Tipo de enlace*</label>
                        <div class="col-sm-9 col-md">
                            <input type="text" class="form-control" id="tipo" name="tipo" placeholder="Descripción del enlace" required value="<?php echo $enlace->tipo ?? ''; ?>">
                        </div>
                    </div>
                    <div class="form-group row col-sm-12 col-md-4">
                        <label for="icono" class="col-sm-3 col-md-auto col-form-label">Icono*</label>
                        <div class="btn-group col-sm-9 col-md">
                            <button id="icono" data-selected="graduation-cap" type="button" class="icp icp-dd btn btn-default iconpicker-component dropdown-toggle" data-toggle="dropdown">
                                <i class="<?php echo $enlace->icono ?? ''; ?>"></i>
                                <span class="caret"><?php echo is_null($enlace) ? '--Selecionar--' : ''; ?></span>
                            </button>
                            <div class="dropdown-menu"></div>
                            <input type="hidden" name="icono" required value="<?php echo $enlace->icono ?? ''; ?>">
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0"><i class="fas fa-save"></i> Guardar</button>
                </div>
                <!-- /.card-footer-->
                </form>
    </div>
    <!-- /.card -->
</main>