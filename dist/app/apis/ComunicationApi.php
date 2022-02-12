<?php

namespace Apis;

use Route\Router;
use Route\Token;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Notihnio\RequestParser\RequestParser;

class ComunicationApi
{
    protected static string $EMAIL_TEMPLATES_DIR = TEMPLATES_DIR . '/COM/email';
    const SCOPE ="COM";

    /**
     * API de envio de email conforme a la plantilla específicada
     *
     * @param Router $router
     * @return void
     */
    public static function postEmail(Router $router): void
    {
        RequestParser::parse();
        if (empty($_POST)) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }
        
        // Credito para envio de email
        $token = Token::validate(self::SCOPE);
        if($token->getStatus()!=Token::SUCCESS_CODE){
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => $token->getStatus(),
                'message' => $token->getMessage(),
                'content' => array()
            )));
            return;
        }

        // Valida campos requeridos
        if (!isset($_POST['email']) || !isset($_POST['plantilla']) || !isset($_POST['variables']) || !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }
        // Filtrar las variables
        $email     = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $plantilla = filter_var(trim($_POST['plantilla']), FILTER_SANITIZE_STRING);

        // Valida la plantilla
        $html   = self::$EMAIL_TEMPLATES_DIR . '/' . $plantilla . '_body.html';
        $text   = self::$EMAIL_TEMPLATES_DIR . '/' . $plantilla . '_body.txt';
        $header = self::$EMAIL_TEMPLATES_DIR . '/' . $plantilla . '_header.txt';
        if (!is_file($html) || !is_file($text) || !is_file($header)) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Plantilla de comunicación desconocida.',
                'content' => array()
            )));
            return;
        }

        //Cargar la plantilla
        $html   = file_get_contents($html);
        $text   = file_get_contents($text);
        $header = file_get_contents($header);

        // Remplazar variables
        foreach($_POST['variables'] as $variable => $valor){
            $html   = preg_replace('/\$\{'.$variable.'\}/', $valor, $html);
            $text   = preg_replace('/\$\{'.$variable.'\}/', $valor, $text);
            $header = preg_replace('/\$\{'.$variable.'\}/', $valor, $header);
        }

        // Valida todas las variables sustituidas
        if (preg_match('/\$\{\S+\}/',$html) || preg_match('/\$\{\S+\}/',$text) || preg_match('/\$\{\S+\}/',$header)) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Deben de indicarse todas las variables de la plantilla.',
                'content' => array()
            )));
            return;
        }

        // Envio del e-mail
        try {
            $mail = new PHPMailer(true);
            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = SMTP_HOST;                             //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = SMTP_USER;                             //SMTP username
            $mail->Password   = SMTP_PASS;                             //SMTP password
            $mail->Port       = SMTP_PORT;                              //TCP port to connect to
            $mail->setFrom(SMTP_EMAIL, SMTP_NAME);
            $mail->CharSet    = 'UTF-8';
        
            //Recipients
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $header;
            $mail->Body    = $html;
            $mail->AltBody = $text;
        
            $mail->send();
        } catch (Exception $e) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Error al enviar el e-mail.',
                'content' => array()
            )));
            return;
        }

        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 200,
            'message' => 'Notificación enviada por e-mail.',
            'content' => array()
        )));

    }
}
