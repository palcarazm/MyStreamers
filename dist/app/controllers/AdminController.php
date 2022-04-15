<?php

namespace Controllers;

use Model\Sitio;
use Model\Usuario;
use Router\Router;

class AdminController
{
    /**
     * Página de configuración del sitio
     *
     * @param Router $router
     * @return void
     */
    public static function configSitio(Router $router)
    {
        $router->render('config/sitio', 'layout-admin', array(
            'title' => 'Configuración del Sitio',
            'titulo' => Sitio::getTitulo(),
            'tema' => Sitio::getTema(),
            'descripcion' => Sitio::getDescripcion(),
            'eventos' => Sitio::isEnabled("eventos"),
            'noticias' => Sitio::isEnabled("noticias"),
            'normas' => Sitio::isEnabled("normas"),
            'enlaces' => Sitio::isEnabled("enlaces")
        ));
    }

    /**
     * Página de perfil de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function perfil(Router $router)
    {
        $router->render('forms/user', 'layout-admin', array(
            'title' => 'Mi perfil',
            'usuario' => getAuthUser(),
            'rol_edit' => false
        ));
    }

    /**
     * Pagina de creación de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function userAdd(Router $router)
    {
        $router->render('forms/user', 'layout-admin', array(
            'title' => 'Crear Usuario',
            'usuario' => null,
            'rol_edit' => true
        ));
    }

    /**
     * Pagina de edición de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function userEdit(Router $router)
    {
        $querySchema = array(
            array(
                'name' => 'id',
                'required' => true,
                'type' => 'integer'
            )
        );
        if (!$router->validate($_GET, $querySchema)) {
            $router->renderError();
            return;
        }
        $usuario = Usuario::find($_GET['id']);
        if (is_null($usuario)) {
            $router->renderError();
            return;
        }
        $router->render('forms/user', 'layout-admin', array(
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
            'rol_edit' => true
        ));
    }
}
