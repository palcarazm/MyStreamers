<?php

namespace Apis;

use stdClass;
use Model\Api;
use Model\Rol;
use Model\Usuario;
use Router\Router;
use Model\TipoEnlace;
use GuzzleHttp\Client;
//use GuzzleHttp\TransferStats;
use Intervention\Image\ImageManagerStatic;

class UserApi
{
    const SCOPE = Rol::PERMS_USUARIOS;

    public static function postUser(Router $router): void
    {
        $api = new Api(
            $router,
            'POST',
            array(
                array(
                    'name' => 'username',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  1,
                    'max' => 50,
                ),
                array(
                    'name' => 'email',
                    'required' => true,
                    'type' => 'string',
                    'filter' => FILTER_VALIDATE_EMAIL,
                    'min' =>  1,
                    'max' => 320,
                ),
                array(
                    'name' => 'id_Rol',
                    'required' => true,
                    'type' => 'integer'
                ),
                array(
                    'name' => 'descripcion',
                    'required' => false,
                    'type' => 'string',
                    'max' => 2 ** 32 - 1,
                ),
                array(
                    'name' => 'imagen',
                    'required' => false,
                    'type' => 'object',
                    'schema' => array(
                        array(
                            'name' => 'name',
                            'required' => true,
                            'type' => 'string'
                        ),
                        array(
                            'name' => 'type',
                            'required' => true,
                            'type' => 'string'
                        ),
                        array(
                            'name' => 'size',
                            'required' => true,
                            'type' => 'integer'
                        ),
                        array(
                            'name' => 'content',
                            'required' => true,
                            'type' => 'string'
                        ),
                    )
                )
            ),
            array(),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Validar el rol
        $rol = Rol::find($api->in['id_Rol']);
        if (is_null($rol)) {
            $api->send(500, 'Rol solicitado no existe.', new stdClass());
            return;
        }

        // Crea al usuario
        $usuario = new Usuario(array(
            'username' => $api->in['username'],
            'email' => $api->in['email'],
            'pass' => password_hash(strtoupper(bin2hex(random_bytes(12))), PASSWORD_BCRYPT, array('cost' => 12)),
            'FK_id_rol' => $api->in['id_Rol'],
            'descripcion' => $api->in['descripcion'] ?? null
        ));

        // Subir imagen
        if (isset($api->in['imagen'])) {
            $type = $api->in['imagen']['type'];
            $extension = explode("/", $type);
            $nombreimagen = "/uploads/" . md5(uniqid(rand(), true)) . '.' . end($extension);
            $imagen = ImageManagerStatic::make($api->in['imagen']['content'])->fit(400, 400);
            $usuario->setImage($nombreimagen);
        }

        // Guardar Usuario
        if (!$usuario->save()) {
            debug($usuario->errors());
            $api->sendErrorDB($usuario->errors());
            return;
        }
        if (isset($api->in['imagen'])) {
            $imagen->save(IMG_DIR .  $nombreimagen);
        }

        // Enviar OTP
        $client = new Client(['headers' => array(
            'Accept'     => 'application/json'
        )]);
        $res = $client->request(
            "POST",
            $_SERVER['HTTP_HOST'] . "/api/auth/v1/otp",
            [
                "json" => array('usuario' => $usuario->username),
                'http_errors' => false
            ]
        );
        if ($res->getStatusCode() != 201) {
            $api->send(202, 'Usuario ha sido creado. Pero no se ha podido mandar el c??digo de inicio de contrase??a.', new stdClass());
            return;
        }

        // Mensaje de respuesta
        $api->send(201, 'Usuario ha sido creado. Se ha mandado un c??digo de inicio de contrase??a por e-mail.', new stdClass());
        return;
    }

    /**
     * Api de modificaci??n de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function putUser(Router $router): void
    {
        $api = new Api(
            $router,
            'PUT',
            array(
                array(
                    'name' => 'username',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  1,
                    'max' => 50,
                ),
                array(
                    'name' => 'email',
                    'required' => true,
                    'type' => 'string',
                    'filter' => FILTER_VALIDATE_EMAIL,
                    'min' =>  1,
                    'max' => 320,
                ),
                array(
                    'name' => 'id_Rol',
                    'required' => true,
                    'type' => 'integer'
                ),
                array(
                    'name' => 'descripcion',
                    'required' => false,
                    'type' => 'string',
                    'max' => 2 ** 32 - 1,
                ),
                array(
                    'name' => 'imagen',
                    'required' => false,
                    'type' => 'object',
                    'schema' => array(
                        array(
                            'name' => 'name',
                            'required' => true,
                            'type' => 'string'
                        ),
                        array(
                            'name' => 'type',
                            'required' => true,
                            'type' => 'string'
                        ),
                        array(
                            'name' => 'size',
                            'required' => true,
                            'type' => 'integer'
                        ),
                        array(
                            'name' => 'content',
                            'required' => true,
                            'type' => 'string'
                        ),
                    )
                )
            ),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN, Api::AUTH_SELF)
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

        // Validar el rol
        $rol = Rol::find($api->in['id_Rol']);
        if (is_null($rol)) {
            $api->send(500, 'Rol solicitado no existe.', new stdClass());
            return;
        }

        // Subir imagen
        if (isset($api->in['imagen'])) {
            $type = $api->in['imagen']['type'];
            $extension = explode("/", $type);
            $nombreimagen = "/uploads/" . md5(uniqid(rand(), true)) . '.' . end($extension);
            $imagen = ImageManagerStatic::make($api->in['imagen']['content'])->fit(400, 400);
            $usuario->setImage($nombreimagen);
        }

        // Actualizar usuario
        $usuario->username = $api->in['username'];
        $usuario->email = $api->in['email'];
        if ($api->getAuthMethod() != Api::AUTH_SELF) {
            $usuario->FK_id_rol = $api->in['id_Rol'];
        }
        if (isset($api->in['descripcion'])) {
            $usuario->descripcion = $api->in['descripcion'];
        }
        if (!$usuario->save()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }
        if (isset($api->in['imagen'])) {
            $imagen->save(IMG_DIR .  $nombreimagen);
        }

        // Mensaje de respuesta
        if ($api->getAuthMethod() != Api::AUTH_SELF) {
            loadSession();
            $_SESSION['auth']['usuario'] = Usuario::find(getAuthUser()->getID());
        }
        $api->send(200, 'Usuario ha sido actualizado.', new stdClass());
        return;
    }

    /**
     * Api de borrado de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function deleteUser(Router $router): void
    {
        $api = new Api(
            $router,
            'DELETE',
            array(),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::find($api->query['id']);
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Borra al usuario
        if (!$usuario->delete()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'Usuario ha sido borrado.', new stdClass());
    }

    /**
     * Api de bloqueo de usuarios
     *
     * @param Router $router
     * @return void
     */
    public static function lockUser(Router $router): void
    {
        $api = new Api(
            $router,
            'PATCH',
            array(),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::find($api->query['id']);
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Verifica estado de usuario
        if ($usuario->isBlocked()) {
            $api->send(202, 'El usuario ya se encuentra bloqueado.', new stdClass());
            return;
        }

        // Bloquea al usuario
        if (!$usuario->bloquear()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'Usuario ha sido bloqueado.', new stdClass());
    }

    /**
     * Api de desbloqueo de usuarios
     *
     * @param Router $router
     * @return void
     */
    public static function unlockUser(Router $router): void
    {
        $api = new Api(
            $router,
            'PATCH',
            array(),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::find($api->query['id']);
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Verifica estado de usuario
        if (!$usuario->isBlocked()) {
            $api->send(202, 'El usuario ya se encuentra desbloqueado.', new stdClass());
            return;
        }

        // Desbloquea al usuario
        if (!$usuario->desbloquear()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'Usuario ha sido desbloqueado.', new stdClass());
    }

    /**
     * Api de creaci??n de tipos de enlaces
     *
     * @param Router $router
     * @return void
     */
    public static function postLink(Router $router): void
    {
        $api = new Api(
            $router,
            'POST',
            array(
                array(
                    'name' => 'icono',
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 40
                ),
                array(
                    'name' => 'tipo',
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 50
                )
            ),
            array(),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Crea el tipo de enlace
        $tipo = new TipoEnlace(array(
            'icono' => $api->in['icono'],
            'tipo' => $api->in['tipo'],
        ));

        // Guardar el tipo de enlace
        if (!$tipo->save()) {
            $api->sendErrorDB($tipo->errors());
            return;
        }
        // Mensajes
        $api->send(201, 'El tipo de enlace ha sido creado.', new stdClass());
    }

    /**
     * Api de edici??n de tipos de enlaces
     *
     * @param Router $router
     * @return void
     */
    public static function putLink(Router $router): void
    {
        $api = new Api(
            $router,
            'PUT',
            array(
                array(
                    'name' => 'icono',
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 40
                ),
                array(
                    'name' => 'tipo',
                    'required' => true,
                    'type' => 'string',
                    'min' => 1,
                    'max' => 50
                )
            ),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Carga el tipo de enlace
        $tipo = TipoEnlace::find($api->query['id']);
        if (is_null($tipo)) {
            $api->send(500, 'Tipo de enlace no encontrado.', new stdClass());
            return;
        }

        // Actualiza el tipo de enlace
        $tipo->icono = $api->in['icono'];
        $tipo->tipo = $api->in['tipo'];
        if (!$tipo->save()) {
            $api->sendErrorDB($tipo->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'El tipo de enlace ha sido actualizado.', new stdClass());
    }

    /**
     * Api de supresi??n de tipos de enlaces
     *
     * @param Router $router
     * @return void
     */
    public static function deleteLink(Router $router): void
    {
        $api = new Api(
            $router,
            'DELETE',
            array(),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Carga el tipo de enlace
        $tipo = TipoEnlace::find($api->query['id']);
        if (is_null($tipo)) {
            $api->send(500, 'Tipo de enlace no encontrado.', new stdClass());
            return;
        }

        // Borrar el tipo de enlace
        if (!$tipo->delete()) {
            $api->sendErrorDB($tipo->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'El tipo de enlace ha sido borrado.', new stdClass());
    }

    /**
     * Api de establecimiento de los enlaces del perfil p??blico de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function putProfileLinks(Router $router): void
    {
        $api = new Api(
            $router,
            'PUT',
            array(
                array(
                    'name' => 'enlaces',
                    'required' => true,
                    'type' => 'array',
                    'schema' => array(
                        array(
                            'name' => 'id',
                            'required' => true,
                            'type' => 'integer',
                        ),
                        array(
                            'name' => 'enlace',
                            'required' => true,
                            'type' => 'string',
                        )
                    )
                )
            ),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_TOKEN, Api::AUTH_SELF)
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

        // Filtrado de enlaces
        $enlaces = [];
        $error_tipo = [];
        $error_enlace = [];
        foreach ($api->in['enlaces'] as $enlace) {
            if (is_null(TipoEnlace::find($enlace['id']))) {
                $error_tipo[] = $enlace['id'];
                continue;
            }
            if (!filter_var($enlace['enlace'], FILTER_VALIDATE_URL)) {
                $error_enlace[] = $enlace['enlace'];
                continue;
            }
            $enlaces[] = array('FK_id_enlace' => $enlace['id'], 'enlace' => $enlace['enlace']);
        }

        // Establece los enlaces
        if (!$usuario->setEnlaces($enlaces)) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        if (!empty($error_tipo) || !empty($error_enlace)) {
            $api->send(202, 'Enlaces establecidos pero algunos enlaces no superar??n la validaci??n.', array(
                'tipo_en_error' => $error_tipo,
                'enlace_en_error' => $error_enlace
            ));
        } else {
            $api->send(200, 'Enlaces estabecidos.', new stdClass());
        }
    }

    /**
     * Api de creaci??n de perfiles de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function postProfile(Router $router): void
    {
        $api = new Api(
            $router,
            'POST',
            array(),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::find($api->query['id']);
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Verifica estado de usuario
        if ($usuario->hasProfile()) {
            $api->send(202, 'El usuario ya dispone de perfil p??blico.', new stdClass());
            return;
        }

        // Crea el perfil p??blico de usuario en oculto
        if (!$usuario->ocultar()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'Se ha asociado un perfil p??blico al usuario.', new stdClass());
    }

    /**
     * Api de bloqueo de perfiles de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function lockProfile(Router $router): void
    {
        $api = new Api(
            $router,
            'PATCH',
            array(),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::find($api->query['id']);
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Verifica estado de usuario
        if (!$usuario->hasProfile()) {
            $api->send(500, 'El usuario no dispone de perfil p??blico.', new stdClass());
            return;
        }
        if (!$usuario->isPublished()) {
            $api->send(202, 'El usuario ya tiene el perfil p??blico bloqueado.', new stdClass());
            return;
        }

        // Oculta el perfil p??blico
        if (!$usuario->ocultar()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'Se ha bloqueado el perfil p??blico al usuario.', new stdClass());
    }

    /**
     * Api de desbloqueo de perfiles de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function unlockProfile(Router $router): void
    {
        $api = new Api(
            $router,
            'PATCH',
            array(),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SESSION, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::find($api->query['id']);
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Verifica estado de usuario
        if (!$usuario->hasProfile()) {
            $api->send(500, 'El usuario no dispone de perfil p??blico.', new stdClass());
            return;
        }
        if ($usuario->isPublished()) {
            $api->send(202, 'El usuario ya tiene el perfil p??blico desbloqueado.', new stdClass());
            return;
        }

        // Oculta el perfil p??blico
        if (!$usuario->publicar()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'Se ha desbloqueado el perfil p??blico al usuario.', new stdClass());
    }

    /**
     * Api de establecer fuentes de emisi??n en directo
     *
     * @param Router $router
     * @return void
     */
    public static function putProfileStreams(Router $router): void
    {
        $api = new Api(
            $router,
            'PUT',
            array(
                array(
                    'name' => 'twitch',
                    'required' => true,
                    'type' => 'string',
                    'min' => 4,
                    'max' => 25
                )
            ),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SELF, Api::AUTH_TOKEN)
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Autentifica al usuario
        if (!$api->auth(self::SCOPE, $api->query['id'])) {
            return;
        }

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

                // Verificar si el usaurio existe en Twitch
                try {
                    $client = new Client(['headers' => array(
                        'Content-Type'     => 'application/json',
                        'Accept'     => 'application/json',
                        'Client-id' => TWITCH_CLIENT_ID,
                        'Authorization' => 'Bearer ' . $bodyout->access_token

                    )]);
                    $res = $client->request(
                        "GET",
                        "https://api.twitch.tv/helix/users",
                        [
                            "query" => array(
                                'login' => $api->in['twitch']
                            ),
                            'http_errors' => false
                        ]
                    );

                    if ($res->getStatusCode() != 200) {


                        $api->send(500, 'Se produjo un error al contactar con Twitch', new stdClass());
                        return;
                    } else {
                        $bodyout = json_decode($res->getBody()->getContents());
                        if (count($bodyout->data) != 1) {
                            $api->send(500, 'Usuario no encontrado en Twitch.', new stdClass());
                            return;
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

        // Buscar usuario filtrando varaible
        $usuario = Usuario::find($api->query['id']);
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Graudar informaci??n del perfil
        if (!$usuario->setTwitch($api->in['twitch'])) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'Fuentes de emisi??n en directo establecidas.', new stdClass());
    }

    /**
     * Api de establecer canales de YouTube
     *
     * @param Router $router
     * @return void
     */
    public static function putProfileChannels(Router $router): void
    {
        $api = new Api(
            $router,
            'PUT',
            array(
                array(
                    'name' => 'channels',
                    'required' => true,
                    'type' => 'array',
                    'schema' => array(
                        'type' => 'string',
                        'required' => false,
                        'min' => 24,
                        'max' => 24
                    )
                )
            ),
            array(
                array(
                    'name' => 'id',
                    'required' => true,
                    'type' => 'integer'
                )
            ),
            array(Api::AUTH_SELF, Api::AUTH_TOKEN)
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

        // Verificar canales de YouTube
        $successChannels = [];
        $errorChannels = [];
        $twitchChannels = [];
        if (!empty($api->in['channels'])) {
            $channels = join("&id=", $api->in['channels']);
            try {
                $url = '';
                $client = new Client(['headers' => array(
                    'Accept'     => 'application/json'
                )]);
                $res = $client->request(
                    "GET",
                    "https://www.googleapis.com/youtube/v3/channels",
                    [
                        "query" => 'key=' . YOUTUBE_APIKEY . '&part=id&id=' . $channels,
                        /*'on_stats' => function (TransferStats $stats) use (&$url) {
                            $url = $stats->getEffectiveUri();
                        },*/
                        'http_errors' => false
                    ]
                );
                //debug($url);

                if ($res->getStatusCode() != 200) {
                    $api->send(500, 'Se produjo un error al contactar con YouTube', new stdClass());
                    return;
                } else {
                    $bodyout = json_decode($res->getBody()->getContents());
                    //debug($bodyout);
                    foreach ($bodyout->items as $item) {
                        $twitchChannels[] = $item->id;
                    }
                    foreach ($api->in['channels'] as $channel) {
                        if (in_array($channel, $twitchChannels)) {
                            $successChannels[] = $channel;
                        } else {
                            $errorChannels[] = $channel;
                        }
                    }
                }
            } catch (\Exception $e) {
                $api->send(500, 'Se produjo un error al contactar con YouTube', new stdClass());
                return;
            }
        }

        // Eliminar canales previos
        foreach ($usuario->getYoutubeChannels() as $channel) {
            if (!$channel->delete()) {
                $api->sendErrorDB($usuario->errors());
                return;
            }
        }
        // Guardar informaci??n de los canales
        foreach ($successChannels as $channel) {
            if (!$usuario->setYoutubeChannel($channel)) {
                $api->sendErrorDB($usuario->errors());
                return;
            }
        }

        // Mensajes
        if (empty($errorChannels)) {
            $api->send(200, 'Los canales de YouTube han sido guardados.', new stdClass());
        } else {
            $api->send(202, 'Los canales de YouTube han sido guardados, pero algunos son desconocidos por YouTube.', array(
                'successChannels' => $successChannels,
                'errorChannels' => $errorChannels
            ));
        }
    }
}
