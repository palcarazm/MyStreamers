<h1 class="card-header">Restablecer Contraseña</h1>
<div class="card-body">
    <div id="create-otp">
        <form action="/api/auth/v1/otp" method="post" class="border-secondary">
            <div class="card-body pt-2 pb-2">
                <div class="form-group row">
                    <label for="usuario" class="col-sm-3 col-md-2 col-form-label">Usuario</label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Nombre de usuario o e-mail" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Enviar código por e-mail</button>
                <p class="text-right mt-5"><a href="/login">Iniciar sesión</a></p>
            </div>
        </form>
    </div>
    <div class="d-none" id="finalMessage">
        <div class="step-body">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Código enviado!</h4>
                <p>Te hemos mandado un e-mail con un codigo para restablecer la contraseña.</p>
            </div>
            <p><span class="badge badge-warning"><i class="fas fa-exclamation-circle  mr-1"></i>Puede tardar unos minutos en ser enviado. Si no lo encuentras verifica tu bandeja de SPAM.</span></p>
            <div class="d-flex flex-row-reverse"><a href="/new-password" class="btn btn-primary">Establecer una nueva contraseña</a></div>
        </div>
    </div>
</div>