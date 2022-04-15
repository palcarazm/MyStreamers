<?php

namespace Apis;

use stdClass;
use Model\Api;
use Model\Rol;
use Model\Usuario;
use Router\Router;
use GuzzleHttp\Client;
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
            'FK_id_rol' => $api->in['id_Rol']
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
            $api->send(202, 'Usuario ha sido creado. Pero no se ha podido mandar el código de inicio de contraseña.', new stdClass());
            return;
        }

        // Mensaje de respuesta
        $api->send(201, 'Usuario ha sido creado. Se ha mandado un código de inicio de contraseña por e-mail.', new stdClass());
        return;
    }

    /**
     * Api de modificación de usuario
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
        if (!$usuario->save()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }
        if (isset($api->in['imagen'])) {
            $imagen->save(IMG_DIR .  $nombreimagen);
        }

        // Mensaje de respuesta
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
        if(!$usuario->delete()) {
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
        if($usuario->isBlocked()) {
            $api->send(202, 'El usuario ya se encuentra bloqueado.', new stdClass());
            return;
        }

        // Bloquea al usuario
        if(!$usuario->bloquear()) {
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
        if(!$usuario->isBlocked()) {
            $api->send(202, 'El usuario ya se encuentra desbloqueado.', new stdClass());
            return;
        }

        // Desbloquea al usuario
        if(!$usuario->desbloquear()) {
            $api->sendErrorDB($usuario->errors());
            return;
        }

        // Mensajes
        $api->send(200, 'Usuario ha sido desbloqueado.', new stdClass());
    }
}
