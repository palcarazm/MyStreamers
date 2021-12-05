<?php
if (is_file(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    require_once __DIR__ . '/../config/config-template.php';
}

use Route\Router;
use Controllers\PublicController;
use Apis\ConfigurationApi;
use Model\ActiveRecord;

$router = new Router;

/* AÑADIR RUTAS */
// Configuración
if(!IS_INIT){$router->add('GET', '/config', [PublicController::class, 'config']);}
$router->add('POST', '/api/config/v1/database', [ConfigurationApi::class, 'postDatabase']);
$router->add('POST', '/api/config/v1/admin', [ConfigurationApi::class, 'postAdmin']);
$router->add('POST', '/api/config/v1/email', [ConfigurationApi::class, 'postEmail']);
$router->add('PUT', '/api/config/v1/email', [ConfigurationApi::class, 'putEmail']);
$router->add('POST', '/api/config/v1/site', [ConfigurationApi::class, 'postSite']);

/* ENROUTAR */
if (is_file(__DIR__ . '/../config/config.php')) {
    ActiveRecord::connect(getDB());
    $router->route();
    ActiveRecord::close();
} else {
    $router->route();
}
