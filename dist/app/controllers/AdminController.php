<?php

namespace Controllers;

use Model\Sitio;
use Model\TipoEnlace;
use Model\Usuario;
use Router\Router;

class AdminController
{
    /**
     * Página de información de My Streamers
     *
     * @param Router $router
     * @return void
     */
    public static function mystreamers(Router $router)
    {
        $router->render('mystreamers/mystreamers', 'layout-admin', array(
            'title' => 'Sobre My Streamers'
        ));
    }

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
     * Página de configuración de la conexión con Twitch
     *
     * @param Router $router
     * @return void
     */
    public static function configTwitch(Router $router)
    {
        $router->render('config/twitch', 'layout-admin', array(
            'title' => 'Configuración de Twitch'
        ));
    }

    /**
     * Página de configuración de la conexión con YouTube
     *
     * @param Router $router
     * @return void
     */
    public static function configYoutube(Router $router)
    {
        $router->render('config/youtube', 'layout-admin', array(
            'title' => 'Configuración de YouTube'
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
        $router->render('forms/mi-perfil', 'layout-admin', array(
            'title' => 'Mi perfil',
            'usuario' => getAuthUser()
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

    /**
     * Página de Listado usuarios
     *
     * @param Router $router
     * @return void
     */
    public static function userList(Router $router)
    {
        $router->render('lists/admin-list', 'layout-admin', array(
            'title' => 'Listar Usuarios',
            'obj_type' => array(
                'singular' => 'usuario',
                'plural' => 'usuarios'
            ),
            'header_list' => ['Usuario', 'E-Mail', 'Rol', 'Perfil', 'Gestión de perfil', 'Gestión de Usuario'],
            'obj_list' => Usuario::all()
        ));
    }

    /**
     * Pagina de creación de tipo de enlace
     *
     * @param Router $router
     * @return void
     */
    public static function linkAdd(Router $router)
    {
        $router->render('forms/link', 'layout-admin', array(
            'title' => 'Crear Enlace',
            'enlace' => null
        ));
    }

    /**
     * Pagina de edición de tipo de enlace
     *
     * @param Router $router
     * @return void
     */
    public static function linkEdit(Router $router)
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
        $enlace = TipoEnlace::find($_GET['id']);
        if (is_null($enlace)) {
            $router->renderError();
            return;
        }
        $router->render('forms/link', 'layout-admin', array(
            'title' => 'Editar enlace',
            'enlace' => $enlace
        ));
    }

    /**
     * Página de listado de tipos de enlaces
     *
     * @param Router $router
     * @return void
     */
    public static function linkList(Router $router)
    {
        $router->render('lists/admin-list', 'layout-admin', array(
            'title' => 'Listar tipo de enlaces del perfil público de usuario',
            'obj_type' => array(
                'singular' => 'tipo de enlaces',
                'plural' => 'tipos de enlaces'
            ),
            'header_list' => ['Tipo de enlaces', 'Acciones'],
            'obj_list' => TipoEnlace::all()
        ));
    }
}
