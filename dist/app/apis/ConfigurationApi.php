<?php

namespace Apis;

use Route\Router;
use Notihnio\RequestParser\RequestParser;

class ConfigurationApi
{
    /**
     * Controlador de la página de configuración
     *
     * @param Router $router
     * @return void
     */
    public static function api(Router $router)
    {
        RequestParser::parse();
        if (!isset($_POST['action'])) {
            $response = array(
                'status' => '400',
                'message' => 'Se debe especificar la action a realizar',
                'content' => array()
            );
        } else {
            switch ($_POST['action']) {
                case 'database':
                    $response = ConfigurationApi::configDatabase();
                    break;
                default:
                    $response = array(
                        'status' => '501',
                        'message' => 'Acción no implementada',
                        'content' => array()
                    );
                    break;
            }
        }
        $router->render('api/api', 'layout-api', array('response' => $response));
    }

    /**
     * Valida los datos de conexión de la base de datos.
     * Si son correctos lanza la configuración de la misma.
     *
     * @return array Respuesta de la API
     */
    public static function configDatabase(): array
    {
        $DB_HOST = filter_var(trim($_POST['dbhost']), FILTER_SANITIZE_STRING);
        $DB_USER = filter_var(trim($_POST['dbuser']), FILTER_SANITIZE_STRING);
        $DB_PASS = filter_var(trim($_POST['dbpass']), FILTER_SANITIZE_STRING);
        $DB_NAME = filter_var(trim($_POST['dbname']), FILTER_SANITIZE_STRING);
        if (verifyDB($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)) {
            $data = file_get_contents(__DIR__ . '/../../config/config-template.php');
            $data = preg_replace('/#DB_HOST/', $DB_HOST, $data);
            $data = preg_replace('/#DB_NAME/', $DB_NAME, $data);
            $data = preg_replace('/#DB_USER/', $DB_USER, $data);
            $data = preg_replace('/#DB_PASS/', $DB_PASS, $data);
            file_put_contents(__DIR__ . '/../../config/config.php', $data);
            $response = array(
                'status' => '200',
                'message' => 'Datos actualizados',
                'content' => array()
            );
        } else {
            $response = array(
                'status' => '500',
                'message' => 'No se ha podido conectar con la base de datos',
                'content' => array()
            );
        }
        return $response;
    }
}
