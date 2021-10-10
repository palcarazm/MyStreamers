<?php
namespace Controllers;

use Route\Router;

class PublicController{
    /**
     * Controlador de la página de configuración
     *
     * @param Router $router
     * @return void
     */
    public static function config(Router $router)
    {
        $router->render('config/config','layout-admin-headerless');
    }
}