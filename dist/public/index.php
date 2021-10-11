<?php
if(is_file(__DIR__ . '/../config/config.php')){
    require_once __DIR__ . '/../config/config.php';
}else{
    require_once __DIR__ . '/../config/config-template.php';
}

use Route\Router;
use Controllers\PublicController;
use Apis\ConfigurationApi;

$router = new Router;

/* AÑADIR RUTAS */
// Configuración
$router->add('GET','/config', [PublicController::class,'config']);
$router->add('POST','/api/config', [ConfigurationApi::class,'api']);


$router->route();