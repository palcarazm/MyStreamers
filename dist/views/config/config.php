<h1 class="card-header">Configuración</h1>
<div class="card-body">
    <div class="progress mb-3">
        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" id="progreso"></div>
    </div>
    <ul class="list-group list-group-flush">
        <!-- Inicio configDatabase -->
        <li class="list-group-item step current" id="configDatabase">
            <h2 class="card-subtitle"><i></i><span>Configiuración de la base de datos</span></h2>
            <form action="/api/config/v1/database" method="post" class="border-secondary step-body">
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
                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Comprobar conexión</button>
                </div>
            </form>
        </li>
        <!-- Fin configDatabase -->
        <!-- Inicio configAdmin -->
        <li class="list-group-item step todo" id="configAdmin">
            <h2 class="card-subtitle"><i></i><span>Configuración del administrador</span></h2>
            <form action="/api/config/v1/admin" method="post" class="border-secondary step-body">
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
                        <div class="col-sm-9 col-md-10 input-group">
                            <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pass2" class="col-sm-3 col-md-2 col-form-label">Repetir Contraseña</label>
                        <div class="col-sm-9 col-md-10 input-group">
                            <input type="password" class="form-control" id="pass2" name="pass2" placeholder="Contraseña" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0" disabled>Crear administrador</button>
                </div>
            </form>
        </li>
        <!-- Fin configAdmin -->
        <!-- Inicio emailConfig -->
        <li class="list-group-item step todo" id="configEmail">
            <h2 class="card-subtitle"><i></i><span>Configuración del servidor de e-mail</span></h2>
            <form action="/api/config/v1/email" method="post" class="border-secondary step-body">
                <div class="card-body pt-2 pb-2">
                    <p class="card-text">Continuamos con la configuración del servidor de e-mail</p>
                    <div class="form-group row">
                        <label for="hostSMTP" class="col-sm-3 col-md-2 col-form-label">Host SMTP</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="hostSMTP" name="hostSMTP" placeholder="host" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="portSMTP" class="col-sm-3 col-md-2 col-form-label">Puerto</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="number" min="0" class="form-control" id="portSMTP" name="portSMTP" placeholder="puerto" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="userEmail" class="col-sm-3 col-md-2 col-form-label">Usuario</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="userEmail" name="userEmail" placeholder="Usuario" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="passEmail" class="col-sm-3 col-md-2 col-form-label">Contraseña</label>
                        <div class="col-sm-9 col-md-10 input-group">
                            <input type="password" class="form-control" id="passEmail" name="passEmail" placeholder="Contraseña" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button"><i class="far fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="fromEmail" class="col-sm-3 col-md-2 col-form-label">E-mail de envío de correos</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="fromEmail" name="fromEmail" placeholder="E-mail que mostrar en el envío de correos" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="fromName" class="col-sm-3 col-md-2 col-form-label">Nombre de envío de correos</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="fromName" name="fromName" placeholder="Nombre que mostrar en el envío  envío de correos" required>
                        </div>
                    </div>
                    <input type="hidden" name="adminEmail" id="adminEmail" value="">
                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Verificar servidor de e-mail</button>
                </div>
            </form>
        </li>
        <!-- Fin emailConfig -->
        <!-- Inicio siteConfig -->
        <li class="list-group-item step todo" id="configSite">
            <h2 class="card-subtitle"><i></i><span>Configuración del sitio</span></h2>
            <form action="/api/config/v1/site" method="post" class="border-secondary step-body">
                <div class="card-body pt-2 pb-2">
                    <p class="card-text">Continuamos con la configuración básica del sitio</p>
                    <div class="form-group row">
                        <label for="titulo" class="col-sm-3 col-md-2 col-form-label">Título</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Título del sitio" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tema" class="col-sm-3 col-md-2 col-form-label">Tema</label>
                        <div class="col-sm-9 col-md-10">
                            <select name="tema" id="tema" class="custom-select form-control" required>
                                <option value="" disabled selected>--Selecione</option>
                                <?php foreach (getThemes() as $theme) : ?>
                                    <option value="<?php echo $theme['folder']; ?>"><?php echo $theme['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                    <div class="form-group row">
                        <label for="descripcion" class="form-label col-12">Descripción del sitio</label>
                        <textarea name="descripcion" id="descripcion" class="col-12 custom-textarea form-control" required></textarea>
                    </div>
                    <fieldset class="mb-3">
                        <legend>Módulos</legend>
                        <div class="form-row">
                            <div class=" col-md-6 col-lg-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input form-control" id="eventos" name="eventos" checked>
                                    <label class="custom-control-label" for="eventos">Eventos</label>
                                </div>
                            </div>
                            <div class=" col-md-6 col-lg-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input form-control" id="noticias" name="noticias" checked>
                                    <label class="custom-control-label" for="noticias">Noticias</label>
                                </div>
                            </div>
                            <div class=" col-md-6 col-lg-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input form-control" id="normas" name="normas" checked>
                                    <label class="custom-control-label" for="normas">Normas</label>
                                </div>
                            </div>
                            <div class=" col-md-6 col-lg-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input form-control" id="enlaces" name="enlaces" checked>
                                    <label class="custom-control-label" for="enlaces">Enlaces personalizados</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Registrar información del sitio</button>
                </div>
            </form>
        </li>
        <!-- Fin siteConfig -->
        <!-- Inicio twitchConfig -->
        <li class="list-group-item step todo" id="configTwitch">
            <h2 class="card-subtitle"><i></i><span>Configuración de la conexión con Twitch</span></h2>
            <form action="/api/config/v1/twitch" method="post" class="border-secondary step-body">
                <div class="card-body pt-2 pb-2">
                    <p class="card-text">Ahora con la conexión con twitch. Debes registrar una aplicación en la consola de desarrolladores de Twitch. <a href="https://dev.twitch.tv/docs/authentication/register-app" target="_blank" rel="noopener noreferrer">Aquí tienes la guía de Twitch</a>.</p>
                    <div class="alert alert-warning" role="alert">
                        Dado que MyStreamers no requiere acceder a la información privada de su perfil, se recomiendo registrar la aplicación en una cuenta de twitch secundaria para que en caso de que ocurra un compromiso de seguridad sus datos no sean expuestos.
                    </div>
                    <div class="form-group row">
                        <label for="client_id" class="col-sm-3 col-md-2 col-form-label">Client ID</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" id="client_id" name="client_id" placeholder="Client ID de la aplicación registrada en Twitch" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="client_secret" class="col-sm-3 col-md-2 col-form-label">Client Secret</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" class="form-control" secret="client_secret" name="client_secret" placeholder="Client Secret de la aplicación registrada en Twitch" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary d-block ml-auto mr-0">Registrar información de conexión</button>
                </div>
            </form>
        </li>
        <!-- Fin twitchConfig -->
        <!-- Inicio finalMessage -->
        <li class="list-group-item step todo" id="finalMessage">
            <div class="step-body">
                <div class="alert alert-success step-body" role="alert">
                    <h4 class="alert-heading">Bien hecho!</h4>
                    <p>Has finalizado la configuración inicial de My Streamers. Ahora ya tu web y base de datos se encuentran conectadas, has creado el administrador del sistema y configurado los datos básicos de tu sitio.</p>
                    <p class="mb-0">¿Por qué no te pasas por el <a href="/login" class="alert-link text-white">panel de administración</a> para añadir información a tu sitio?</p>
                </div>
                <p><span class="badge badge-warning"><i class="fas fa-exclamation-circle  mr-1"></i>Por seguridad, vamos a bloquear el acceso a esta sección para que nadie pueda modificar la configuración del sitio sin estar autenticado.</span></p>
            </div>
        </li>
        <!-- Fin finalMessage -->
    </ul>

</div>