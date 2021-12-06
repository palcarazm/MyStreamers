<?php

namespace Apis;

use Route\Router;
use Model\Usuario;
use Notihnio\RequestParser\RequestParser;
use mysqli;

class ConfigurationApi
{
    /**
     * Valida los datos de conexión de la base de datos.
     * Si son correctos lanza la configuración de la misma.
     *
     * @param Router $router
     */
    public static function postDatabase(Router $router): void
    {
        RequestParser::parse();
        if (empty($_POST)) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }

        // Valida configuración inicial
        if (IS_CONFIG_DATABASE) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 403,
                'message' => 'La configuración inicial de la base de datos ya ha sido efectuada y no puede volver a ejecutarse.',
                'content' => array()
            )));
            return;
        }

        // Valida campos requeridos
        if (!isset($_POST['dbhost']) || !isset($_POST['dbuser']) || !isset($_POST['dbpass']) || !isset($_POST['dbname'])) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }

        //Filtrar las variables
        $DB_HOST = filter_var(trim($_POST['dbhost']), FILTER_SANITIZE_STRING);
        $DB_USER = filter_var(trim($_POST['dbuser']), FILTER_SANITIZE_STRING);
        $DB_PASS = filter_var(trim($_POST['dbpass']), FILTER_SANITIZE_STRING);
        $DB_NAME = filter_var(trim($_POST['dbname']), FILTER_SANITIZE_STRING);

        if (self::verifyDB($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)) { //Conexión verificada
            try { // Crear fichero de configuración
                $data = file_get_contents(__DIR__ . '/../../config/config-template.php');
                $data = preg_replace('/define\(\'DB_HOST\',\'\S+\'\)/', "define('DB_HOST','{$DB_HOST}')", $data);
                $data = preg_replace('/define\(\'DB_NAME\',\'\S+\'\)/', "define('DB_NAME','{$DB_NAME}')", $data);
                $data = preg_replace('/define\(\'DB_USER\',\'\S+\'\)/', "define('DB_USER','{$DB_USER}')", $data);
                $data = preg_replace('/define\(\'DB_PASS\',\'\S+\'\)/', "define('DB_PASS','{$DB_PASS}')", $data);
                file_put_contents(__DIR__ . '/../../config/config.php', $data);
            } catch (\Exception $e) {
                $router->render('api/api', 'layout-api', array('response' => array(
                    'status' => 500,
                    'message' => 'No se ha conseguido guardar los datos de conexión con la base de datos en el fichero de configuración',
                    'content' => array()
                )));
                return;
            }

            try { // Crear tablas
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
                    $router->render('api/api', 'layout-api', array('response' => array(
                        'status' => 500,
                        'message' => 'Error en la sentencia #' . ($querynum + 1) . '<br/><br/><span style="color:red;">' . $db->error . '</span>',
                        'content' => array()
                    )));
                    return;
                }
                $db->close();
            } catch (\Exception $e) {
                $router->render('api/api', 'layout-api', array('response' => array(
                    'status' => 500,
                    'message' => 'No se ha conseguido cargar la configuración inicial en la base de datos',
                    'content' => array()
                )));
                return;
            }

            try { // Alcualiza fichero de configuración
                $data = file_get_contents(__DIR__ . '/../../config/config.php');
                $data = preg_replace('/define\(\'IS_CONFIG_DATABASE\',\S+\)/', "define('IS_CONFIG_DATABASE',true)", $data);
                file_put_contents(__DIR__ . '/../../config/config.php', $data);
            } catch (\Exception $e) {
                $router->render('api/api', 'layout-api', array('response' => array(
                    'status' => 202,
                    'message' => 'No se ha conseguido bloquear el acceso a la API de configuración de la base de datos. Realice un bloqueo manual',
                    'content' => array()
                )));
                return;
            }

            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 201,
                'message' => 'Configuración de la base de datos completada',
                'content' => array()
            )));
            return;
        } else { // Conexión fallida
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'No se ha conseguido establecer conexión con la base de datos',
                'content' => array()
            )));
            return;
        }
    }

    /**
     * Configura el administrador principal del sistema
     *
     * @param Router $router
     */
    public static function postAdmin(Router $router): void
    {
        RequestParser::parse();
        if (empty($_POST)) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }

        // Valida configuración inicial
        if (IS_CONFIG_ADMIN) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 403,
                'message' => 'La configuración inicial del administrador ya ha sido efectuada y no puede volver a ejecutarse.',
                'content' => array()
            )));
            return;
        }

        // Valida campos requeridos
        if (!isset($_POST['user']) || !isset($_POST['email']) || !isset($_POST['pass'])) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }

        // Filtrar las variables
        $username = filter_var(trim($_POST['user']), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $pass = filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING);

        if (!checkPasswordStrength($pass)) { // Validación contraseña
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'La contraseña indicada no cumple los estándares de seguridad',
                'content' => array()
            )));
            return;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) { //validación email
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'El e-mail indicado es inválido',
                'content' => array()
            )));
            return;
        }

        // Cargar modelo de usuario
        $user = new Usuario(array(
            'username' => $username,
            'email' => $email,
            'pass' => password_hash($pass, PASSWORD_BCRYPT, array('cost' => 12)),
            'FK_id_rol' => 1
        ));

        if ($user->validate()) { // validación de usuario
            if ($user->save()) { // guargar usuario
                try { // Alcualiza fichero de configuración
                    $data = file_get_contents(__DIR__ . '/../../config/config.php');
                    $data = preg_replace('/define\(\'IS_CONFIG_ADMIN\',\S+\)/', "define('IS_CONFIG_ADMIN',true)", $data);
                    file_put_contents(__DIR__ . '/../../config/config.php', $data);
                } catch (\Exception $e) {
                    $router->render('api/api', 'layout-api', array('response' => array(
                        'status' => 202,
                        'message' => 'No se ha conseguido bloquear el acceso a la API de configuración del administrador. Realice un bloqueo manual',
                        'content' => array()
                    )));
                    return;
                }

                $router->render('api/api', 'layout-api', array('response' => array(
                    'status' => 201,
                    'message' => 'Configuración del administrador completada',
                    'content' => array()
                )));
                return;
            } else { // error al guardar usuario
                $router->render('api/api', 'layout-api', array('response' => array(
                    'status' => '500',
                    'message' => 'Se ha producido un error al crear el usuario:<br>' . getMessage($user->errors()),
                    'content' => array()
                )));
                return;
            }
        } else { // validación de usuario no superada
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => '500',
                'message' => 'Se ha producido un error al crear el usuario:<br>' . getMessage($user->errors()),
                'content' => array()
            )));
            return;
        }
    }

    /**
     * Configura la información del sitio
     *
     * @param Router $router
     */
    public static function postSite(Router $router): void
    {
        RequestParser::parse();
        if (empty($_POST)) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }

        // Valida configuración inicial
        if (IS_CONFIG_SITE) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 403,
                'message' => 'La configuración inicial del sitio ya ha sido efectuada y no puede volver a ejecutarse.',
                'content' => array()
            )));
            return;
        }

        // Valida campos requeridos
        if (!isset($_POST['titulo']) || !isset($_POST['tema']) || !isset($_POST['descripcion'])) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }

        //Filtrar las variables
        $titulo = filter_var(trim($_POST['titulo']), FILTER_SANITIZE_STRING);
        $tema = filter_var(trim($_POST['tema']), FILTER_SANITIZE_STRING);
        $descripcion = trim($_POST['descripcion']);
        $eventos = isset($_POST['eventos']) ? 1 : 0;
        $noticias = isset($_POST['noticias']) ? 1 : 0;
        $normas = isset($_POST['normas']) ? 1 : 0;
        $enlaces = isset($_POST['enlaces']) ? 1 : 0;

        if (!in_array($tema, array_column(getThemes(), 'folder'))) { // Validar tema
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'El tema seleccionado no se encuentra disponible',
                'content' => array()
            )));
            return;
        }

        if ($titulo == '' || $descripcion == '') { // Validar datos obligatorios
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Debe proveerse un título y descripción',
                'content' => array()
            )));
            return;
        }

        try { // Registrar los datos
            $db = getDB();
            $stmt = $db->prepare("REPLACE INTO opciones (opcion , valor) VALUES ('titulo' , ?),('tema' , ?),('descripcion' , ?);");
            $stmt->bind_param('sss', $titulo, $tema, $descripcion);
            if (!$stmt->execute()) { // Registro datos principales en error
                $stmt->close();
                $db->close();
                $router->render('api/api', 'layout-api', array('response' => array(
                    'status' => 500,
                    'message' => 'Se ha producido un error al registrar la configuración en la base de datos',
                    'content' => array()
                )));
                return;
            }
            $stmt->close();

            $stmt = $db->prepare("REPLACE INTO modulos (modulo , activo) VALUES ('eventos' , ?),('noticias' , ?),('normas' , ?),('enlaces' , ?);");
            $stmt->bind_param('iiii', $eventos, $noticias, $normas, $enlaces);
            if (!$stmt->execute()) { // Registro datos de módulos en error
                $stmt->close();
                $db->close();
                $router->render('api/api', 'layout-api', array('response' => array(
                    'status' => 500,
                    'message' => 'Se ha producido un error al registrar la configuración en la base de datos',
                    'content' => array()
                )));
                return;
            }
            $stmt->close();
            $db->close();
        } catch (\Exception $e) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Se ha producido un error al registrar la configuración en la base de datos',
                'content' => array()
            )));
            return;
        }

        try { // Alcualiza fichero de configuración
            $data = file_get_contents(__DIR__ . '/../../config/config.php');
            $data = preg_replace('/define\(\'IS_CONFIG_SITE\',\S+\)/', "define('IS_CONFIG_SITE',true)", $data);
            file_put_contents(__DIR__ . '/../../config/config.php', $data);
        } catch (\Exception $e) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 202,
                'message' => 'No se ha conseguido bloquear el acceso a la API de configuración del sitio. Realice un bloqueo manual',
                'content' => array()
            )));
            return;
        }

        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 201,
            'message' => 'Configuración del sitio completada',
            'content' => array()
        )));
        return;
    }

    /**
     * Verificar conexion con la base de datos
     * @param String $DB_HOST Host de conexion
     * @param String $DB_USER Usuario de conexion
     * @param String $DB_PASS Contraseña de conexion
     * @param String $DB_NAME Base de datos de conexion
     * @return bool Se establece conexión (SI/NO)
     */
    private function verifyDB(String $DB_HOST, String $DB_USER, String $DB_PASS, String $DB_NAME): bool
    {
        $db = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
        if ($db->connect_error) {
            return false;
        }
        return true;
    }
}
