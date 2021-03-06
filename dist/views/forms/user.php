<?php

use Model\Rol;
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1><?php echo is_null($usuario) ? 'Crear' : 'Editar'; ?> usuario</h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<main class="content">

    <!-- Default box -->
    <?php if (is_null($usuario)) : ?>
        <form enctype="multipart/form-data" action="/api/user/v1/user" method="POST" class="border-secondary" id="user-form" form-success="reset">
        <?php else : ?>
            <form enctype="multipart/form-data" action="/api/user/v1/user<?php echo '?id=' . $usuario->getID(); ?>" method="PUT" class="border-secondary" id="user-form" form-success="redirect" destino="/admin/usuarios/listar">
            <?php endif; ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Formulario de usuario</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="username" class="col-sm-3 col-md-2 col-form-label">Usuario*</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Nombre de usuario" required value="<?php echo $usuario->username ?? ''; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-md-2 col-form-label">E-Mail*</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Dirección de e-mail" required value="<?php echo $usuario->email ?? ''; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id_Rol" class="col-sm-3 col-md-2 col-form-label">Rol*</label>
                        <div class="col-sm-9 col-md-10">
                            <select name="id_Rol" id="id_Rol" class="custom-select form-control" required>
                                <option value="" disabled <?php echo is_null($usuario) ? "selected" : ""; ?>>--Selecione</option>
                                <?php if (is_null($usuario)) : ?>
                                    <?php foreach (Rol::all() as $rol) : ?>
                                        <option value="<?php echo $rol->getID(); ?>">
                                            <?php echo ucfirst($rol->rol); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <?php foreach (Rol::all() as $rol) : ?>
                                        <option value="<?php echo $rol->getID(); ?>" <?php echo $rol->getID() == $usuario->rol->getID() ? "selected" : ""; ?>>
                                            <?php echo ucfirst($rol->rol); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="imagen" class="col-sm-3 col-md-2 col-form-label">Imagen</label>
                        <?php if (!is_null($usuario)) : ?>
                            <div class="d-flex flex-column flex-sm-row align-items-center col-sm-9 col-md-10">
                                <div class="col-form-label pr-4">
                                    <img src="<?php $usuario->printImageURL(); ?>" alt="Imagen de usuario" class='img-circle img-thumbnail img-size-thumbnail'>
                                </div>
                                <div class="flex-grow-1">
                                <?php else : ?>
                                    <div class="col-sm-9 col-md-10">
                                    <?php endif; ?>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*">
                                        <label class="custom-file-label" for="imagen" data-browse="Selecionar">Selecciona una imagen</label>
                                        <small id="id_RolHelpBlock" class="form-text text-muted">
                                            Se recomienda una imagen cuadrada de al menos 400 x 400 px.
                                        </small>
                                    </div>
                                    <?php if (!is_null($usuario)) : ?>
                                    </div>
                                <?php endif; ?>
                                </div>
                            </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary d-block ml-auto mr-0"><i class="fas fa-save"></i> Guardar</button>
                    </div>
                    <!-- /.card-footer-->
                </div>
                <!-- /.card -->
            </form>
</main>