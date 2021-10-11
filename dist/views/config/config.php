<h1 class="card-header">Configuración</h1>
<div class="card-body">
    <div class="progress mb-3">
        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" id="progreso"></div>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item step" id="verifyDB">
            <h2 class="card-subtitle"><i class="far fa-circle text-muted"></i><span>Configiuración de la base de datos</span></h2>
            <form action="/api/config" method="post" class="border-secondary">
                <div class="card-body pt-2 pb-2">
                    <p class="card-text">Vamos a iniciar la configuración autoguiada del sitio.</p>
                    <div class="form-group row">
                        <label for="dbhost" class="col-sm-3 col-md-2 col-form-label">Host</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="dbhost" name="dbhost" value="localhost" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="dbname" class="col-sm-3 col-md-2 col-form-label">Base de datos</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="dbname" name="dbname" placeholder="Nombre de la base de datos" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="dbuser" class="col-sm-3 col-md-2 col-form-label">Usuario</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="dbuser" name="dbuser" placeholder="Nombre de usuario" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="dbpass" class="col-sm-3 col-md-2 col-form-label">Contraseña</label>
                        <div class="col-sm-9 col-md-10 input-group">
                            <input type="password" class="form-control" id="dbpass" name="dbpass" placeholder="Contrseña" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="database">
                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Comprobar conexión</button>
                </div>
            </form>
        </li>
        <!--verifyDB-->
        <li class="list-group-item step" id="createAdmin">
            <h2 class="card-subtitle"><i class="far fa-circle text-muted"></i><span>Configuración del administrador</span></h2>
            <form action="/api/config" method="post" class="border-secondary" style="display:none;">
                <div class="card-body pt-2 pb-2">
                    <p class="card-text">Continuamos con la creación del administrador</p>
                    <div class="form-group row">
                        <label for="user" class="col-sm-3 col-md-2 col-form-label">Usuario</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="user" name="user" placeholder="Nombre de usuario" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-3 col-md-2 col-form-label">E-Mail</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Correo Electrónico" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pass" class="col-sm-3 col-md-2 col-form-label">Contraseña</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="pass" name="pass" placeholder="Contraseña" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pass2" class="col-sm-3 col-md-2 col-form-label">Repetir Contraseña</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="password" class="form-control" id="pass2" placeholder="Contraseña" required>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="adminuser">
                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Crear administrador</button>
                </div>
            </form>
        </li>
        <!--createAdmin-->
    </ul>

</div>