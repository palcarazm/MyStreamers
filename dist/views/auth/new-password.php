<h1 class="card-header">Restablecimiento de contraseña</h1>
<div class="card-body">
    <form action="/api/auth/v1/otp" method="patch" class="border-secondary step-body" id="new-password-form">
        <div class="card-body pt-2 pb-2">
            <div class="form-group row">
                <label for="usuario" class="col-sm-3 col-md-2 col-form-label">Usuario</label>
                <div class="col-sm-9 col-md-10">
                    <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Nombre de usuario o e-mail" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="otp" class="col-sm-3 col-md-2 col-form-label">Código OTP</label>
                <div class="col-sm-9 col-md-10">
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Código OTP enviado por e-mail" required value="<?php echo $otp;?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="clave" class="col-sm-3 col-md-2 col-form-label">Nueva Contraseña</label>
                <div class="col-sm-9 col-md-10 input-group">
                    <input type="password" class="form-control" id="clave" name="clave" placeholder="Contraseña" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary toggle-password" type="button"><i class="far fa-eye"></i></button>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="clave2" class="col-sm-3 col-md-2 col-form-label">Repetir Contraseña</label>
                <div class="col-sm-9 col-md-10 input-group">
                    <input type="password" class="form-control" id="clave2" name="clave2" placeholder="Contraseña" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary toggle-password" type="button"><i class="far fa-eye"></i></button>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary d-block ml-auto mr-0" disabled>Acceder</button>
            <p class="text-right mt-5"><a href="/login">Iniciar sesión</a></p>
        </div>
    </form>
</div>