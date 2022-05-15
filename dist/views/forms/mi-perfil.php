<?php

use Model\Rol;
use Model\TipoEnlace;

$enlacesUser = $usuario->getEnlaces();
$enlacesUserTipo = [];
foreach($enlacesUser as $enlace){
    $enlacesUserTipo[$enlace->tipo->tipo] = $enlace->enlace;
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Mi perfil de usuario</h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Formulario de usuario</h3>
                    </div>
                    <form enctype="multipart/form-data" action="/api/user/v1/user<?php echo '?id=' . $usuario->getID(); ?>" method="PUT" class="border-secondary" id="user-form" form-success="redirect" destino="/admin/miperfil">
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
                                        <option value="" disabled>--Selecione</option>
                                        <?php foreach (Rol::all() as $rol) : ?>
                                            <option value="<?php echo $rol->getID(); ?>" <?php echo $rol->getID() == $usuario->rol->getID() ? "selected" : ""; ?> <?php echo ($rol->getID() == $usuario->rol->getID()) ? "" : "disabled"; ?>>
                                                <?php echo ucfirst($rol->rol); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small id="id_RolHelpBlock" class="form-text text-muted">
                                        No puedes modificar tu propio rol.
                                    </small>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label for="imagen" class="col-sm-3 col-md-2 col-form-label">Imagen</label>
                                <div class="d-flex flex-column flex-sm-row align-items-center col-sm-9 col-md-10">
                                    <div class="col-form-label pr-4">
                                        <img src="<?php $usuario->printImageURL(); ?>" alt="Imagen de usuario" class='img-circle img-thumbnail img-size-thumbnail'>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*">
                                            <label class="custom-file-label" for="imagen" data-browse="Selecionar">Selecciona una imagen</label>
                                            <small id="id_RolHelpBlock" class="form-text text-muted">
                                                Se recomienda una imagen cuadrada de al menos 400 x 400 px.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($usuario->hasProfile()) : ?>
                                <div class="form-group row">
                                    <label for="descripcion" class="form-label col-12">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="col-12 custom-textarea form-control" required><?php echo $usuario->descripcion ?? ''; ?></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary d-block ml-auto mr-0"><i class="fas fa-save"></i> Guardar</button>
                        </div>
                        <!-- /.card-footer-->
                    </form>
                </div>
            </div>
            <?php if ($usuario->hasProfile()) : ?>
                <div class="col-lg-5 col-xl-4">
                    <?php if ($usuario->hasProfile()) : ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Enlaces Públicos</h3>
                            </div>
                            <form enctype="multipart/form-data" action="/api/user/v1/profile/links<?php echo '?id=' . $usuario->getID(); ?>" method="PUT" class="border-secondary" id="profile-links-form">
                                <div class="card-body">
                                    <?php foreach(TipoEnlace::all() as $tipoEnlace):?>
                                        <div class="form-group row">
                                        <label for="tipoenlace-<?php echo $tipoEnlace->getID();?>" class="col-auto col-form-label" title="<?php echo $tipoEnlace->tipo?>"><?php $tipoEnlace->print();?></label>
                                        <div class="col">
                                            <input type="url" class="form-control" id="tipoenlace-<?php echo $tipoEnlace->getID();?>" name="<?php echo $tipoEnlace->getID();?>" placeholder="Enlace a tu perfil de <?php echo $tipoEnlace->tipo;?>" value="<?php echo $enlacesUserTipo[$tipoEnlace->tipo] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0"><i class="fas fa-save"></i> Guardar</button>
                                </div>
                                <!-- /.card-footer-->
                            </form>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Emisiones en directo</h3>
                            </div>
                            <form action="/api/user/v1/profile/streams<?php echo '?id=' . $usuario->getID(); ?>" method="PUT" class="border-secondary" id="profile-streams-form">
                                <div class="card-body">
                                        <div class="form-group row">
                                        <label for="twitch" class="col-auto col-form-label">Ususarios de Twitch*</label>
                                        <div class="col">
                                            <input type="text" class="form-control" id="twitch" name="twitch" placeholder="Nombre de usuario" value="<?php echo $usuario->twitch_user ?? ''; ?>" minlength="4" maxlength="10" required>
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
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>