<?php

namespace Apis;

use stdClass;
use Model\Api;
use Model\Sitio;
use Router\Token;
use Router\Router;
use Model\Usuario;
use GuzzleHttp\Client;

class AuthentificationApi
{
    /**
     * API de creación de OTP
     *
     * @param Router $router
     * @return void
     */
    public static function postOTP(Router $router): void
    {
        $api = new Api(
            $router,
            'POST',
            array(
                array(
                    'name' => 'usuario',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  1
                )
            )
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::findUser(htmlspecialchars(trim($api->in['usuario'])));
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }
        // Crear OTP
        try {
            $OTP = $usuario->createOTP();
        } catch (\Exception $e) {
            $api->send(400, 'Error en la creación de OTP.', new stdClass());
            return;
        }


        // Envio del OTP
        $client = new Client(['headers' => array(
            'Authorization' => Token::SECURITY . ' ' . Token::create([ComunicationApi::SCOPE]),
            'Accept'     => 'application/json'
        )]);
        $res = $client->request(
            "POST",
            $_SERVER['HTTP_HOST'] . "/api/com/v1/email",
            [
                "json" => array(
                    'email' => $usuario->email,
                    'plantilla' => 'otp-send',
                    'variables' => array(
                        'user' => $usuario->username,
                        'otp' => $OTP,
                        'sitio' => Sitio::getTitulo(),
                        'host' => $_SERVER['SERVER_NAME']
                    )
                ),
                'http_errors' => false
            ]
        );
        if ($res->getStatusCode() != 200) {
            $api->send(500, 'Error en el envío del OTP.', new stdClass());
            return;
        }

        $api->send(201, 'Notificación enviada por e-mail.', array(
            'email' => $usuario->email
        ));
    }

    /**
     * API de invalidación de OTP
     *
     * @param Router $router
     * @return void
     */
    public static function deleteOTP(Router $router): void
    {
        $api = new Api(
            $router,
            'DELETE',
            array(
                array(
                    'name' => 'usuario',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  1
                )
            )
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::findUser(htmlspecialchars(trim($api->in['usuario'])));
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }
        // Elimina OTP
        if (!$usuario->deleteOTP()) {
            $api->send(500, 'Error con la base de datos no permite completar la invalidación del OTP.', new stdClass());
            return;
        }

        // Respuesta
        $api->send(200, 'OTP invalidado.', new stdClass());
    }

    /**
     * API de validación de OTP
     *
     * @param Router $router
     * @return void
     */
    public static function patchOTP(Router $router): void
    {
        $api = new Api(
            $router,
            'PATCH',
            array(
                array(
                    'name' => 'usuario',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  1
                ),
                array(
                    'name' => 'otp',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  16,
                    'max' => 16,
                ),
                array(),
                array(
                    'name' => 'clave',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  8
                )
            )
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::findUser(htmlspecialchars(trim($api->in['usuario'])));
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Valida el OTP
        if (!$usuario->validateOTP($api->in['otp'])) {
            $api->send(500, getMessage($usuario->errors()), new stdClass());
            return;
        }
        // Cambia la contraseña
        if (!$usuario->setPass($api->in['clave'])) {
            $api->send(500, 'Error al actualizar la contraseña. ' . getMessage($usuario->errors()), new stdClass());
            return;
        }

        // Elimina el OTP
        $otp_message = '';
        if (!$usuario->deleteOTP()) {
            $otp_message = ' El OTP no ha posido ser eliminado por seguridad intente eliminarlo.';
        }

        // Respuesta
        $api->send(200, 'Contraseña actualizada.' . $otp_message, new stdClass());
    }

    /**
     * API de Apertura de sesión
     *
     * @param Router $router
     * @return void
     */
    public static function postAuth(Router $router): void
    {
        $api = new Api(
            $router,
            'POST',
            array(
                array(
                    'name' => 'usuario',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  1
                ),
                array(
                    'name' => 'clave',
                    'required' => true,
                    'type' => 'string',
                    'min' =>  8
                )
            )
        );

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Buscar usuario filtrando variable
        $usuario = Usuario::findUser(htmlspecialchars(trim($api->in['usuario'])));
        if (is_null($usuario)) {
            $api->send(500, 'Usuario no encontrado.', new stdClass());
            return;
        }

        // Verifica que no está bloqueado
        if ($usuario->isBlocked()) {
            $api->send(500, 'Usuario bloqueado por algún administrador del sistema.', new stdClass());
            return;
        }

        // Validar clave
        if (!$usuario->validatePass($api->in['clave'])) {
            $api->send(500, 'Clave incorrecta.', new stdClass());
            return;
        }

        // Iniciar la sesion
        loadSession();
        $_SESSION['auth'] = array(
            'usuario' => $usuario,
            'iat' => time(),                        // inicio de la session
            'exp' => time() + (24 * 60 * 60),       // validez (24 h)
        );

        // Respuesta
        $api->send(200, 'Usuario identificado.', new stdClass());
    }

    /**
     * Elimina la sesión de autentificación de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function deleteAuth(Router $router): void
    {
        $api = new Api(
            $router,
            'DELETE',
            array()
        );

        loadSession();
        if (isset($_SESSION['auth'])) {
            unset($_SESSION['auth']);
        }

        // Respuesta
        $api->send(200, 'Sesión cerrada.', new stdClass());
    }
}
