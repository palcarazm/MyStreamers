<?php

namespace Controllers;

use Model\Sitio;
use Model\Usuario;
use Router\Router;
use Model\TipoEnlace;
use GuzzleHttp\Client;
//use GuzzleHttp\TransferStats;

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

    /**
     * Pagina de asociación de video
     *
     * @param Router $router
     * @return void
     */
    public static function videoAdd(Router $router)
    {
        $usuario = getAuthUser();
        if (!$usuario->hasProfile()) {
            $router->render('public/403', 'layout-admin', array('title' => 'Acceso no autorizado'));
            return;
        }
        $channels = $usuario->getYoutubeChannels();
        $canalesAlert = empty($channels);

        // Importar videos de Youtube
        $youtubeAlert = false;
        $videos = [];
        if (!$canalesAlert) {
            $userID = $usuario->getID();
            $channelsID = [];
            foreach ($channels as $channel) {
                $channelsID[] = $channel->getID();
            }
            $channelsID = join("&channelId=", $channelsID);
            try {
                $url = '';
                $client = new Client(['headers' => array(
                    'Accept'     => 'application/json'
                )]);
                $res = $client->request(
                    "GET",
                    "https://www.googleapis.com/youtube/v3/search",
                    [
                        "query" => 'key=' . YOUTUBE_APIKEY . '&part=snippet&order=date&maxResults=50&channelId=' . $channelsID,
                        /*'on_stats' => function (TransferStats $stats) use (&$url) {
                            $url = $stats->getEffectiveUri();
                        },*/
                        'http_errors' => false
                    ]
                );
                //debug($url);

                if ($res->getStatusCode() != 200) {
                    $youtubeAlert = true;
                } else {
                    $bodyout = json_decode($res->getBody()->getContents());
                    //debug($bodyout);
                    $videosUsuarioID = [];
                    foreach ($usuario->getYoutubeVideos() as $video) {
                        $videosUsuarioID[] = $video->getID();
                    }
                    foreach ($bodyout->items as $item) {
                        if ($item->id->kind == "youtube#video") {
                            $videos[] = array(
                                'userID' => $userID,
                                'id' => $item->id->videoId,
                                'titulo' => $item->snippet->title,
                                'fecha' => date('c', strtotime($item->snippet->publishedAt)),
                                'added' => in_array($item->id->videoId, $videosUsuarioID)
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                $youtubeAlert = true;
            }
        }

        $router->render('lists/video-add-list', 'layout-admin', array(
            'title' => 'Asociar videos',
            'videos' => $videos,
            'canalesAlert' => $canalesAlert,
            'youtubeAlert' => $youtubeAlert
        ));
    }

    /**
     * Pagina de listado de video
     *
     * @param Router $router
     * @return void
     */
    public static function videoList(Router $router)
    {
        $usuario = getAuthUser();
        if (!$usuario->hasProfile()) {
            $router->render('public/403', 'layout-admin', array('title' => 'Acceso no autorizado'));
            return;
        }
      
        $router->render('lists/admin-list', 'layout-admin', array(
            'title' => 'Mis videos',
            'obj_type' => array(
                'singular' => 'video',
                'plural' => 'videos'
            ),
            'header_list' => ['Imagen', 'Titulo', 'Fecha' , 'Acciones'],
            'obj_list' => $usuario->getYoutubeVideos()
        ));
    }
}
