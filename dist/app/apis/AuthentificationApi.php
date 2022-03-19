<?php

namespace Apis;

use DateTime;
use Route\Router;
use Model\Usuario;
use GuzzleHttp\Client;
use Model\Sitio;
use Notihnio\RequestParser\RequestParser;
use Route\Token;

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
        RequestParser::parse();
        if (empty($_POST)) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }

        // Valida campos requeridos
        if (!isset($_POST['usuario'])) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::findUser(htmlspecialchars(trim($_POST['usuario'])));
        if (is_null($usuario)) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Usuario no encontrado.',
                'content' => array()
            )));
            return;
        }
        // Crear OTP
        try {
            $OTP = $usuario->createOTP();
        } catch (\Exception $e) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Error en la creación de OTP.',
                'content' => array()
            )));
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
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Error en el envío del OTP.',
                'content' => array()
            )));
            return;
        }

        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 201,
            'message' => 'Notificación enviada por e-mail.',
            'content' => array(
                'email' => $usuario->email
            )
        )));
    }

    /**
     * API de invalidación de OTP
     *
     * @param Router $router
     * @return void
     */
    public static function deleteOTP(Router $router): void
    {
        RequestParser::parse();
        if (empty($_DELETE)) {
            $_DELETE = json_decode(file_get_contents("php://input"), true);
        }

        // Valida campos requeridos
        if (!isset($_DELETE['usuario'])) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::findUser(htmlspecialchars(trim($_DELETE['usuario'])));
        if (is_null($usuario)) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Usuario no encontrado.',
                'content' => array()
            )));
            return;
        }
        // Elimina OTP
        if(!$usuario->deleteOTP()){
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Error con la base de datos no permite completar la invalidación del OTP.',
                'content' => array()
            )));
            return;
        }

        // Respuesta
        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 200,
            'message' => 'OTP invalidado.',
            'content' => array()
        )));
    }

    /**
     * API de validación de OTP
     *
     * @param Router $router
     * @return void
     */
    public static function patchOTP(Router $router): void
    {
        RequestParser::parse();
        if (empty($_PATCH)) {
            $_PATCH = json_decode(file_get_contents("php://input"), true);
        }

        // Valida campos requeridos
        if (!isset($_PATCH['usuario']) || !isset($_PATCH['otp']) || !isset($_PATCH['clave'])) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }

        // Buscar usuario filtrando varaible
        $usuario = Usuario::findUser(htmlspecialchars(trim($_PATCH['usuario'])));
        if (is_null($usuario)) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Usuario no encontrado.',
                'content' => array()
            )));
            return;
        }

        // Valida el OTP
        if(!$usuario->validateOTP($_PATCH['otp'])){
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => getMessage($usuario->errors()),
                'content' => array()
            )));
            return;
        }
        // Cambia la contraseña
        if(!$usuario->setPass($_PATCH['clave'])){
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Error al actualizar la contraseña. '. getMessage($usuario->errors()),
                'content' => array()
            )));
            return;
        }

        // Elimina el OTP
        $otp_message = '';
        if(!$usuario->deleteOTP()){
            $otp_message = ' El OTP no ha posido ser eliminado por seguridad intenten eliminarlo.';
        }

        // Respuesta
        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 200,
            'message' => 'Contraseña actualizada.'.$otp_message,
            'content' => array()
        )));
    }

    /**
     * API de Apertura de sesión
     *
     * @param Router $router
     * @return void
     */
    public static function postAuth(Router $router): void
    {
        RequestParser::parse();
        if (empty($_POST)) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }

        // Valida campos requeridos
        if (!isset($_POST['usuario']) || !isset($_POST['clave'])) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }

        // Buscar usuario filtrando variable
        $usuario = Usuario::findUser(htmlspecialchars(trim($_POST['usuario'])));
        if (is_null($usuario)) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Usuario no encontrado.',
                'content' => array()
            )));
            return;
        }

        // Validar clave
        if(!$usuario->validatePass($_POST['clave'])){
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Clave incorrecta.',
                'content' => array()
            )));
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
        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 200,
            'message' => 'Usuario identificado.',
            'content' => array()
        )));
    }

    /**
     * Elimina la sesión de autentificación de usuario
     *
     * @param Router $router
     * @return void
     */
    public static function deleteAuth(Router $router): void
    {
        RequestParser::parse();
        if (empty($_DELETE)) {
            $_DELETE = json_decode(file_get_contents("php://input"), true);
        }

        loadSession();
        if(isset($_SESSION['auth'])){unset($_SESSION['auth']);}

        // Respuesta
        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 200,
            'message' => 'Sesión cerrada.',
            'content' => array()
        )));
    }
}
