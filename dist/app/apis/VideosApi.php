<?php

namespace Apis;

use mysqli;
use stdClass;
use Model\Api;
use Model\Rol;
use Model\Usuario;
use Router\Router;
use GuzzleHttp\Client;
//use GuzzleHttp\TransferStats;

class VideosApi
{
    const SCOPE = Rol::PERMS_SELF;

    /**
     * Api de asociación de video de YouTube
     *
     * @param Router $router
     * @return void
     */
    public static function postVideo(Router $router): void
    {
        $api = new Api(
            $router,
            'POST',
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'string',
                    'max' => 11
                ),
                array(
                    'name' => 'titulo',
                    'required' => true,
                    'type' => 'string',
                    'max' => 100
                ),
                array(
                    'name' => 'fecha',
                    'required' => true,
                    'type' => 'string'
                ),
            ),
            array(
                array(
                'name' => 'id',
                'required' => true,
                'type' => 'integer',
                )
            ),
            array(API::AUTH_SELF, API::AUTH_TOKEN)
        );

          // Valida campos requeridos
          if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE, $api->query['id'])) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::find($api->query['id']);
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        //Guardar video
        if (!$usuario->setYoutubeVideo($api->in['id'],$api->in['titulo'],$api->in['fecha'])) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(201, 'El vídeo ha sido guardado.', new stdClass());
    }
}
