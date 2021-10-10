<?php
require_once __DIR__ . '/../config/config-template.php';

use Route\Router;
use Controllers\PublicController;

$router = new Router;

/* AÑADIR RUTAS */
// Configuración
$router->add('GET','/config', [PublicController::class,'config']);
//debug($router->routes);
//debug($_SERVER['REQUEST_METHOD']);

// /* ADMINISTRACIÓN */
// $router->addRouteGET('/admin' , [PropiedadController::class,'index'],false);

// /* PROPIEDADES */
// $router->addRouteGET('/admin/propiedades/crear' , [PropiedadController::class,'create'],false);
// $router->addRoutePOST('/admin/propiedades/crear' , [PropiedadController::class,'create'],false);
// $router->addRouteGET('/admin/propiedades/actualizar' , [PropiedadController::class,'update'],false);
// $router->addRoutePOST('/admin/propiedades/actualizar' , [PropiedadController::class,'update'],false);
// $router->addRoutePOST('/admin/propiedades/eliminar' , [PropiedadController::class,'delete'],false);

// /* VENDEDRORES */
// $router->addRouteGET('/admin/vendedores/crear' , [VendedorController::class,'create'],false);
// $router->addRoutePOST('/admin/vendedores/crear' , [VendedorController::class,'create'],false);
// $router->addRouteGET('/admin/vendedores/actualizar' , [VendedorController::class,'update'],false);
// $router->addRoutePOST('/admin/vendedores/actualizar' , [VendedorController::class,'update'],false);
// $router->addRoutePOST('/admin/vendedores/eliminar' , [VendedorController::class,'delete'],false);

// /* PUBLICAS */
// $router->addRouteGET('/', [PublicController::class,'index']);
// $router->addRouteGET('/nosotros', [PublicController::class,'nosotros']);
// $router->addRouteGET('/propiedades', [PublicController::class,'archivePropiedad']);
// $router->addRouteGET('/propiedad', [PublicController::class,'propiedad']);
// $router->addRouteGET('/blog', [PublicController::class,'archiveEntry']);
// $router->addRouteGET('/entrada', [PublicController::class,'entry']);
// $router->addRouteGET('/contacto', [PublicController::class,'contacto']);
// $router->addRoutePOST('/contacto', [PublicController::class,'contacto']);

// /* LOGIN */
// $router->addRouteGET('/login', [UserController::class,'login']);
// $router->addRoutePOST('/login', [UserController::class,'login']);
// $router->addRouteGET('/logout', [UserController::class,'logout']);

$router->route();
//$router->render('config/index','layout-admin-headerless');