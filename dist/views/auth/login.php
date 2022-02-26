<h1 class="card-header">Iniciar Sesión</h1>
<div class="card-body">
    <form action="/api/auth/v1/auth" method="post" class="border-secondary step-body" id="login-form">
        <div class="card-body pt-2 pb-2">
            <div class="form-group row">
                <label for="usuario" class="col-sm-3 col-md-2 col-form-label">Usuario</label>
                <div class="col-sm-9 col-md-10">
                    <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Nombre de usuario o e-mail" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="clave" class="col-sm-3 col-md-2 col-form-label">Contraseña</label>
                <div class="col-sm-9 col-md-10 input-group">
                    <input type="password" class="form-control" id="clave" name="clave" placeholder="Contraseña" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary toggle-password" type="button"><i class="far fa-eye"></i></button>
                    </div>
                </div>
            </div>
            <p class="text-right"><a href="/create-otp">Restablecer Contraseña</a></p>
            <input type="hidden" name="destino" id="destino" value="<?php echo $destino;?>">
            <button type="submit" class="btn btn-primary d-block ml-auto mr-0" >Acceder</button>
        </div>
    </form>
</div>