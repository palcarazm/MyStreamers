<h1 class="card-header">Invalidar código OTP</h1>
<div class="card-body">
        <form action="/api/auth/v1/otp" method="delete" class="border-secondary" id="invalidate-otp-form">
            <div class="card-body pt-2 pb-2">
                <div class="form-group row">
                    <label for="usuario" class="col-sm-3 col-md-2 col-form-label">Usuario</label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Nombre de usuario o e-mail" required value="<?php echo $usuario;?>"> 
                    </div>
                </div>
                <button type="submit" class="btn btn-danger d-block ml-auto mr-0">Invalidar OTP</button>
            </div>
        </form>
</div>