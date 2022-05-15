<?php

namespace Apis;

use mysqli;
use stdClass;
use Model\Api;
use Model\Usuario;
use Router\Router;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class StreamsApi
{

    /**
     * Api de recuperar el estado de emisión
     *
     * @param Router $router
     * @return void
     */
    public static function getStatus(Router $router): void
    {
        $api = new Api($router, 'GET');

        $online = [];
        $offline = [];

        // Usuarios con perfil activo y Twitch
        $usuarios = Usuario::findActiveStreamProfiles();
        $twitch_users = [];
        $twitch_usersXid = [];
        foreach ($usuarios as $usuario) {
            $twitch_users[] = $usuario->twitch_user;
            $twitch_usersXid[$usuario->twitch_user] = $usuario->getID();
        }
        
        // Envio por lotes
        while (sizeof($twitch_users) != 0) {
            $num = min(100, sizeof($twitch_users));
            $twitch_usersQuery = array_slice($twitch_users, 0, $num);
            $twitch_users = array_slice($twitch_users, $num);

            // General un token de Twitch
            try {
                $client = new Client(['headers' => array(
                    'Content-Type'     => 'application/json',
                    'Accept'     => 'application/json'
                )]);
                $res = $client->request(
                    "POST",
                    "https://id.twitch.tv/oauth2/token",
                    [
                        "json" => array(
                            'client_id' => TWITCH_CLIENT_ID,
                            'client_secret' => TWITCH_CLIENT_SECRET,
                            'grant_type' => 'client_credentials'
                        ),
                        'http_errors' => false
                    ]
                );

                if ($res->getStatusCode() != 200) {
                    $api->send(500, 'Se produjo un error al contactar con Twitch', new stdClass());
                    return;
                } else {
                    $bodyout = json_decode($res->getBody()->getContents());

                    // Verificar si los usuarios del lote estan online
                    try {
                        //$url = '';
                        $client = new Client(['headers' => array(
                            'Content-Type'     => 'application/json',
                            'Accept'     => 'application/json',
                            'Client-id' => TWITCH_CLIENT_ID,
                            'Authorization' => 'Bearer ' . $bodyout->access_token

                        )]);
                        $res = $client->request(
                            "GET",
                            "https://api.twitch.tv/helix/streams",
                            [
                                "query" => array(
                                    'first' => 100,
                                    'user_login' => $twitch_usersQuery
                                ),
                                //'on_stats' => function (TransferStats $stats) use (&$url) {
                                //    $url = $stats->getEffectiveUri();
                                //},
                                'http_errors' => false
                            ]
                        );
                        //debug($url);
                        if ($res->getStatusCode() != 200) {
                            $api->send(500, 'Se produjo un error al contactar con Twitch', new stdClass());
                            return;
                        } else {
                            $bodyout = json_decode($res->getBody()->getContents());
                            $streams=[];
                            foreach ($bodyout->data as $stream) {
                                $streams[] = $stream->user_login;
                            }
                            foreach ($twitch_usersQuery as $twitch_user) {
                                if(in_array($twitch_user,$streams)){
                                    $online[] = $twitch_usersXid[$twitch_user];
                                }else{
                                    $offline[] = $twitch_usersXid[$twitch_user];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $api->send(500, 'Se produjo un error al contactar con Twitch', new stdClass());
                        return;
                    }
                }
            } catch (\Exception $e) {
                $api->send(500, 'Se produjo un error al contactar con Twitch', new stdClass());
                return;
            }
        }

        // Mensajes
        $api->send(200, 'Estado de emisión recuperada con éxito.', array(
            'online' => $online,
            'offline' => $offline
        ));
    }
}
