<?php
if (is_file(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    require_once __DIR__ . '/../config/config-template.php';
}

use Route\Router;
use Controllers\PublicController;
use Apis\ConfigurationApi;
use Apis\ComunicationApi;
use Apis\AuthentificationApi;
use Model\ActiveRecord;

$router = new Router;

/* AÑADIR RUTAS */
// Configuración
if(!IS_CONFIG_SITE){$router->add('GET', '/config', [PublicController::class, 'config']);}
$router->add('POST', '/api/config/v1/database', [ConfigurationApi::class, 'postDatabase']);
$router->add('POST', '/api/config/v1/admin', [ConfigurationApi::class, 'postAdmin']);
$router->add('POST', '/api/config/v1/email', [ConfigurationApi::class, 'postEmail']);
$router->add('PUT', '/api/config/v1/email', [ConfigurationApi::class, 'putEmail']);
$router->add('POST', '/api/config/v1/site', [ConfigurationApi::class, 'postSite']);

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

// Publicas
$router->add('GET','/',[PublicController::class, 'index']);

/* ENROUTAR */
if (is_file(__DIR__ . '/../config/config.php')) {
    ActiveRecord::connect(getDB());
    $router->route();
    ActiveRecord::close();
} else {
    $router->route();
}
