<?php

namespace Apis;

use stdClass;
use Model\Api;
use Router\Router;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class ComunicationApi
{
    protected static string $EMAIL_TEMPLATES_DIR = TEMPLATES_DIR . '/COM/email';
    const SCOPE = "COM";

    /**
     * API de envio de email conforme a la plantilla específicada
     *
     * @param Router $router
     * @return void
     */
    public static function postEmail(Router $router): void
    {
        $api = new Api(
            $router,
            'POST',
            array(
                array(
                    'name' => 'email',
                    'required' => true,
                    'type' => 'string',
                    'max' =>  320,
                    'filter' => FILTER_VALIDATE_EMAIL
                ),
                array(
                    'name' => 'plantilla',
                    'required' => true,
                    'type' => 'string'
                ),
                array(
                    'name' => 'variables',
                    'required' => true,
                    'type' => 'object',
                    'schema' => array()
                )
            ),
            array(),
            array('TOKEN')
        );

        // Valida la autentificación
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Filtrar las variables
        $email     = filter_var(trim($api->in['email']), FILTER_SANITIZE_EMAIL);
        $plantilla = htmlspecialchars(trim($api->in['plantilla']));

        // Valida la plantilla
        $html   = self::$EMAIL_TEMPLATES_DIR . '/' . $plantilla . '_body.html';
        $text   = self::$EMAIL_TEMPLATES_DIR . '/' . $plantilla . '_body.txt';
        $header = self::$EMAIL_TEMPLATES_DIR . '/' . $plantilla . '_header.txt';
        if (!is_file($html) || !is_file($text) || !is_file($header)) {
            $api->send(500, 'Plantilla de comunicación desconocida.', new stdClass());
            return;
        }

        //Cargar la plantilla
        $html   = file_get_contents($html);
        $text   = file_get_contents($text);
        $header = file_get_contents($header);

        // Remplazar variables
        foreach ($api->in['variables'] as $variable => $valor) {
            $html   = preg_replace('/\$\{' . $variable . '\}/', $valor, $html);
            $text   = preg_replace('/\$\{' . $variable . '\}/', $valor, $text);
            $header = preg_replace('/\$\{' . $variable . '\}/', $valor, $header);
        }

        // Valida todas las variables sustituidas
        if (preg_match('/\$\{\S+\}/', $html) || preg_match('/\$\{\S+\}/', $text) || preg_match('/\$\{\S+\}/', $header)) {
            $api->send(400, 'Deben de indicarse todas las variables de la plantilla.', new stdClass());
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
            $api->send(500, 'Error al enviar el e-mail.', new stdClass());
            return;
        }

        $api->send(200, 'Notificación enviada por e-mail.', new stdClass());
    }
}
