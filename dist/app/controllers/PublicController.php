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

    /**
     * Controlador de la página de inicio
     *
     * @param Router $router
     * @return void
     */
    public static function index(Router $router)
    {
        $router->render('public/index','layout-public',array('title'=>'Inicio'));
    }

    /**
     * Controlador de la página de login
     *
     * @param Router $router
     * @return void
     */
    public static function login(Router $router)
    {
        $router->render('auth/login','layout-admin-headerless',array(
            'title'=>'Login',
            'destino' => $_GET['dest'] ?? '/admin'
        ));
    }
}