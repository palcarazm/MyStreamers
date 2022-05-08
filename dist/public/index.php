<?php
if (is_file(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    require_once __DIR__ . '/../config/config-template.php';
}

use Router\Router;
use Controllers\PublicController;
use Controllers\AdminController;
use Apis\ConfigurationApi;
use Apis\ComunicationApi;
use Apis\AuthentificationApi;
use Apis\UserApi;
use Model\ActiveRecord;
use Model\Rol;

$router = new Router;

/* AÑADIR RUTAS */
// Configuración
if(!IS_CONFIG_SITE){$router->add('GET', '/config', [PublicController::class, 'config']);}
$router->add('POST', '/api/config/v1/database', [ConfigurationApi::class, 'postDatabase']);
$router->add('POST', '/api/config/v1/admin', [ConfigurationApi::class, 'postAdmin']);
$router->add('POST', '/api/config/v1/email', [ConfigurationApi::class, 'postEmail']);
$router->add('PUT', '/api/config/v1/email', [ConfigurationApi::class, 'putEmail']);
$router->add('POST', '/api/config/v1/site', [ConfigurationApi::class, 'postSite']);
$router->add('PUT', '/api/config/v1/site', [ConfigurationApi::class, 'putSite']);

// Comunicación
$router->add('POST','/api/com/v1/email',[ComunicationApi::class,'postEmail']);

// Autentificación
$router->add('GET','/login',[PublicController::class, 'login']);
$router->add('GET','/create-otp',[PublicController::class, 'createOTP']);
$router->add('GET','/invalidate-otp',[PublicController::class, 'invalidateOTP']);
$router->add('GET','/new-password',[PublicController::class, 'newPassword']);
$router->add('POST','/api/auth/v1/otp',[AuthentificationApi::class,'postOTP']);
$router->add('DELETE','/api/auth/v1/otp',[AuthentificationApi::class,'deleteOTP']);
$router->add('PATCH','/api/auth/v1/otp',[AuthentificationApi::class,'patchOTP']);
$router->add('POST','/api/auth/v1/auth',[AuthentificationApi::class,'postAuth']);
$router->add('DELETE','/api/auth/v1/auth',[AuthentificationApi::class,'deleteAuth']);

// API Usuario
$router->add('POST','/api/user/v1/user',[UserApi::class,'postUser']);
$router->add('PUT','/api/user/v1/user',[UserApi::class,'putUser']);
$router->add('DELETE','/api/user/v1/user',[UserApi::class,'deleteUser']);
$router->add('PATCH','/api/user/v1/user/lock',[UserApi::class,'lockUser']);
$router->add('PATCH','/api/user/v1/user/unlock',[UserApi::class,'unlockUser']);
$router->add('POST','/api/user/v1/link',[UserApi::class,'postLink']);
$router->add('PUT','/api/user/v1/link',[UserApi::class,'putLink']);
$router->add('DELETE','/api/user/v1/link',[UserApi::class,'deleteLink']);
$router->add('PUT','/api/user/v1/profile/links',[UserApi::class,'putProfileLinks']);
$router->add('POST','/api/user/v1/profile',[UserApi::class,'postProfile']);
$router->add('PATCH','/api/user/v1/profile/lock',[UserApi::class,'lockProfile']);
$router->add('PATCH','/api/user/v1/profile/unlock',[UserApi::class,'unlockProfile']);

// Administración
/// Perfil
$router->add('GET','/admin/miperfil',[AdminController::class,'perfil'],Rol::PERMS_BASIC);
/// Configuración
$router->add('GET','/admin/config/sitio',[AdminController::class,'configSitio'],Rol::PERMS_CONFIG);
/// Usuarios
$router->add('GET','/admin/usuarios/crear',[AdminController::class,'userAdd'],Rol::PERMS_USUARIOS);
$router->add('GET','/admin/usuarios/editar',[AdminController::class,'userEdit'],Rol::PERMS_USUARIOS);
$router->add('GET','/admin/usuarios/listar',[AdminController::class,'userList'],Rol::PERMS_USUARIOS);
$router->add('GET','/admin/usuarios/enlaces/crear',[AdminController::class,'linkAdd'],Rol::PERMS_USUARIOS);
$router->add('GET','/admin/usuarios/enlaces/editar',[AdminController::class,'linkEdit'],Rol::PERMS_USUARIOS);
$router->add('GET','/admin/usuarios/enlaces/listar',[AdminController::class,'linkList'],Rol::PERMS_USUARIOS);

// Publicas
$router->add('GET','/',[PublicController::class, 'index']);
$router->add('GET','/participantes',[PublicController::class, 'participantes']);
$router->add('GET','/participantes/ficha',[PublicController::class, 'participante']);

/* ENROUTAR */
if (is_file(__DIR__ . '/../config/config.php')) {
    ActiveRecord::connect(getDB());
    $router->route();
    ActiveRecord::close();
} else {
    $router->route();
}
