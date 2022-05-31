<?php

namespace Controllers;

use Model\Rol;
use Model\Sitio;
use Model\Usuario;
use Router\Router;

class PublicController
{
    /**
     * Página de información de My Streamers
     *
     * @param Router $router
     * @return void
     */
    public static function mystreamers(Router $router)
    {
        $router->render('mystreamers/mystreamers', 'layout-public', array(
            'title' => 'Sobre My Streamers'
        ));
    }

    /**
     * Controlador de la página de configuración
     *
     * @param Router $router
     * @return void
     */
    public static function config(Router $router)
    {
        $router->render('config/config', 'layout-admin-headerless', array('title' => 'Configuración'));
    }

    /**
     * Controlador de la página de inicio
     *
     * @param Router $router
     * @return void
     */
    public static function index(Router $router)
    {
        $router->render('public/index', 'layout-public', array(
            'title' => 'Inicio',
            'participantes' => Usuario::findActiveStreamProfiles()
        ));
    }

    /**
     * Controlador de la página de busca
     *
     * @param Router $router
     * @return void
     */
    public static function buscar(Router $router)
    {
        $querySchema = array(
            array(
                'name' => 'search',
                'required' => true,
                'type' => 'string'
            )
        );
        if (!$router->validate($_GET, $querySchema)) {
            $router->renderError();
            return;
        }
        $resultados = Sitio::search($_GET['search']);
        $desc = empty($resultados) ? 'No se han encontrado resultados en esta búsqueda' : null;
        $router->render('public/archivo', 'layout-public', array(
            'title' => 'Busqueda',
            'archivo_titulo' => 'Resultados de busqueda',
            'archivo_descripcion' => $desc,
            'archivo_item' => 'registro',
            'archivo' => $resultados
        ));
    }

    /**
     * Controlador de la ficha de participante
     *
     * @param Router $router
     * @return void
     */
    public static function participante(Router $router)
    {
        $querySchema = array(
            array(
                'name' => 'participante',
                'required' => true,
                'type' => 'string'
            )
        );
        if (!$router->validate($_GET, $querySchema)) {
            $router->renderError();
            return;
        }
        $usuario = Usuario::findUser($_GET['participante']);
        if (is_null($usuario) || !$usuario->hasProfile()) {
            $router->renderError();
            return;
        }
        if (!$usuario->isPublished()) {
            if (isAuth()) {
                if (!getAuthUser()->can(Rol::PERMS_USUARIOS)) {
                    $router->renderError();
                    return;
                }
            } else {
                $router->renderError();
                return;
            }
        }
        $router->render('public/participante', 'layout-public', array(
            'title' => $usuario->username,
            'participante' => $usuario
        ));
    }

    /**
     * Controlador del archivo de participantes
     *
     * @param Router $router
     * @return void
     */
    public static function participantes(Router $router)
    {
        $router->render('public/archivo', 'layout-public', array(
            'title' => 'Participantes',
            'archivo_titulo' => 'Participantes',
            'archivo_descripcion' => null,
            'archivo_item' => 'participante',
            'archivo' => Usuario::findActiveProfiles()
        ));
    }

    /**
     * Controlador de la página de login
     *
     * @param Router $router
     * @return void
     */
    public static function login(Router $router)
    {
        $router->render('auth/login', 'layout-admin-headerless', array(
            'title' => 'Login',
            'destino' => $_GET['dest'] ?? '/admin'
        ));
    }

    /**
     * Controlador de la página de creación de OTP
     *
     * @param Router $router
     * @return void
     */
    public static function createOTP(Router $router)
    {
        $router->render('auth/create-otp', 'layout-admin-headerless', array('title' => 'Restablecer Contraseña'));
    }

    /**
     * Controlador de la página de invalidación del OTP
     *
     * @param Router $router
     * @return void
     */
    public static function invalidateOTP(Router $router)
    {
        $router->render('auth/invalidate-otp', 'layout-admin-headerless', array(
            'title' => 'Invalidar OTP',
            'usuario' => $_GET['user'] ?? ''
        ));
    }

    /**
     * Controlador de la página de restablecimiento de contraseña
     *
     * @param Router $router
     * @return void
     */
    public static function newPassword(Router $router)
    {
        $router->render('auth/new-password', 'layout-admin-headerless', array(
            'title' => 'Restablecimiento de contraseña ',
            'otp' => $_GET['otp'] ?? ''
        ));
    }
}
