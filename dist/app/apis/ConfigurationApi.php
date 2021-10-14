<?php

namespace Apis;

use Route\Router;
use Model\Usuario;
use Notihnio\RequestParser\RequestParser;
use mysqli;

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
                case 'adminuser':
                    $response = ConfigurationApi::configAdminuser();
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
    protected static function configDatabase(): array
    {
        //Filtrar las variables
        $DB_HOST = filter_var(trim($_POST['dbhost']), FILTER_SANITIZE_STRING);
        $DB_USER = filter_var(trim($_POST['dbuser']), FILTER_SANITIZE_STRING);
        $DB_PASS = filter_var(trim($_POST['dbpass']), FILTER_SANITIZE_STRING);
        $DB_NAME = filter_var(trim($_POST['dbname']), FILTER_SANITIZE_STRING);

        if (verifyDB($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)) { //Conexión verificada
            // Crear fichero de configuración
            $data = file_get_contents(__DIR__ . '/../../config/config-template.php');
            $data = preg_replace('/#DB_HOST/', $DB_HOST, $data);
            $data = preg_replace('/#DB_NAME/', $DB_NAME, $data);
            $data = preg_replace('/#DB_USER/', $DB_USER, $data);
            $data = preg_replace('/#DB_PASS/', $DB_PASS, $data);
            file_put_contents(__DIR__ . '/../../config/config.php', $data);

            // Crear tablas
            $fileSQL = file_get_contents(__DIR__ . '/../../config/database-init.min.sql');

            $db = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
            $querynum = 0;
            if ($db->multi_query($fileSQL)) {
                do {
                    if ($db->more_results()) {
                        $querynum++;
                    }
                } while ($db->next_result());
            }
            if ($db->errno) {
                return array(
                    'status' => '500',
                    'message' => 'Error en la sentencia #' . ($querynum + 1) . '<br/><br/><span style="color:red;">' . $db->error . '</span>',
                    'content' => array()
                );
            }
            $db->close();

            return array(
                'status' => '200',
                'message' => 'Datos actualizados',
                'content' => array()
            );
        } else { // Conexión fallida
            return array(
                'status' => '500',
                'message' => 'No se ha podido conectar con la base de datos',
                'content' => array()
            );
        }
    }

    /**
     * Configura el administrador principal del sistema
     *
     * @return array Respuesta de la API
     */
    protected static function configAdminuser(): array
    {
        //Filtrar las variables
        $username = filter_var(trim($_POST['user']), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $pass = filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && checkPasswordStrength($pass)) { //validación de datos
            $user = new Usuario(array(
                'username' => $username,
                'email' => $email,
                'pass' => password_hash($pass, PASSWORD_BCRYPT, array('cost' => 12)),
                'FK_id_rol' => 1
            ));

            if ($user->validate()) { // validación de usuario
                if ($user->save()) { // guargar usuario
                    return array(
                        'status' => '200',
                        'message' => 'Datos actualizados',
                        'content' => array($user)
                    );
                } else { // error al guardar usuario
                    return array(
                        'status' => '500',
                        'message' => getMessage($user->errors()),
                        'content' => array()
                    );
                }
            } else { // validación de usuario no superada
                return array(
                    'status' => '500',
                    'message' => getMessage($user->errors()),
                    'content' => array()
                );
            }
        } else { // Validación de datos no superada
            return array(
                'status' => '500',
                'message' => 'No se ha superado la validación de email y contraseña',
                'content' => array()
            );
        }
    }
}
