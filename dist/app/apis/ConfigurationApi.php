<?php

namespace Apis;

use Route\Router;
use Model\Usuario;
use Notihnio\RequestParser\RequestParser;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
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
        $DB_HOST = htmlspecialchars(trim($_POST['dbhost']));
        $DB_USER = htmlspecialchars(trim($_POST['dbuser']));
        $DB_PASS = htmlspecialchars(trim($_POST['dbpass']));
        $DB_NAME = htmlspecialchars(trim($_POST['dbname']));

        if (self::verifyDB($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)) { //Conexión verificada
            try { // Crear fichero de configuración
                $data = file_get_contents(__DIR__ . '/../../config/config-template.php');
                $data = preg_replace('/define\(\'SECRET\',\'\S+\'\)/', "define('SECRET','".strtoupper(bin2hex(random_bytes(20)))."')", $data);
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
        $username = htmlspecialchars(trim($_POST['user']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $pass = htmlspecialchars(trim($_POST['pass']));

        if (!checkPasswordStrength($pass)) { // Validación contraseña
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'La contraseña indicada no cumple los estándares de seguridad',
                'content' => array()
            )));
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //validación email
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
     * Configura el servidor de e-mail
     *
     * @param Router $router
     */
    public static function postEmail(Router $router): void
    {
        RequestParser::parse();
        if (empty($_POST)) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }

        // Valida configuración inicial
        if (IS_CONFIG_EMAIL) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 403,
                'message' => 'La configuración inicial del servidor de correo ya ha sido efectuada y no puede volver a ejecutarse.',
                'content' => array()
            )));
            return;
        }

        // Valida campos requeridos
        if (!isset($_POST['hostSMTP']) || !isset($_POST['portSMTP']) || !isset($_POST['userEmail']) || !isset($_POST['passEmail']) || !isset($_POST['adminEmail']) || !isset($_POST['fromEmail']) || !isset($_POST['fromName'])) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'Debe incluirse todos los valores requeridos.',
                'content' => array()
            )));
            return;
        }

        // Filtrar las variables
        $hostSMTP = htmlspecialchars(trim($_POST['hostSMTP']));
        $portSMTP = filter_var(trim($_POST['portSMTP']), FILTER_SANITIZE_NUMBER_INT);
        $userEmail = htmlspecialchars(trim($_POST['userEmail']));
        $passEmail = htmlspecialchars(trim($_POST['passEmail']));
        $adminEmail = filter_var(trim($_POST['adminEmail']), FILTER_SANITIZE_EMAIL);
        $fromEmail = filter_var(trim($_POST['fromEmail']), FILTER_SANITIZE_EMAIL);
        $fromName = htmlspecialchars(trim($_POST['fromName']));

        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL) || !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) { //validación email
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 400,
                'message' => 'El e-mail indicado es inválido',
                'content' => array()
            )));
            return;
        }
       
        try {// Intento de conexión
           self::verifySMTP($hostSMTP, $portSMTP, $userEmail, $passEmail);
        } catch (Exception $e) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Se ha producido un error al intentar conectar con el servidor SMTP:<br>' . $e->getMessage(),
                'content' => array()
            )));
            return;
        }

        try { // Guardar datos en el fichero de configuración
            $data = file_get_contents(__DIR__ . '/../../config/config.php');
            $data = preg_replace('/define\(\'SMTP_HOST\',\'\S+\'\)/', "define('SMTP_HOST','{$hostSMTP}')", $data);
            $data = preg_replace('/define\(\'SMTP_PORT\',\'\S+\'\)/', "define('SMTP_PORT','{$portSMTP}')", $data);
            $data = preg_replace('/define\(\'SMTP_USER\',\'\S+\'\)/', "define('SMTP_USER','{$userEmail}')", $data);
            $data = preg_replace('/define\(\'SMTP_PASS\',\'\S+\'\)/', "define('SMTP_PASS','{$passEmail}')", $data);
            $data = preg_replace('/define\(\'SMTP_EMAIL\',\'\S+\'\)/', "define('SMTP_EMAIL','{$fromEmail}')", $data);
            $data = preg_replace('/define\(\'SMTP_NAME\',\'\S+\'\)/', "define('SMTP_NAME','{$fromName}')", $data);
            file_put_contents(__DIR__ . '/../../config/config.php', $data);
        } catch (\Exception $e) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Se ha producido un error al guardar los datos de configuración',
                'content' => array()
            )));
            return;
        }

        try { // Envio de mensaje al administrador
            $mail = new PHPMailer(true);
            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $hostSMTP;                             //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $userEmail;                             //SMTP username
            $mail->Password   = $passEmail;                             //SMTP password
            $mail->Port       = $portSMTP;                              //TCP port to connect to
            $mail->setFrom($fromEmail, $fromName);
            $mail->CharSet= 'UTF-8';
        
            //Recipients
            $mail->addAddress($adminEmail);

            //Content
            $mail->isHTML(true);
            $mail->Subject = '[MyStreamers] Verificación del servidor';
            $mail->Body    = 'Correo de prueba del <b>servidor SMTP</b>!';
            $mail->AltBody = 'Correo de prueba del servidor SMTP!';
        
            $mail->send();
        } catch (Exception $e) {
            $router->render('api/api', 'layout-api', array('response' => array(
                'status' => 500,
                'message' => 'Se ha producido un error al enviar el correo de prueba:<br>' . $mail->ErrorInfo,
                'content' => array()
            )));
            return;
        }

        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 201,
            'message' => 'Configuración del servidor de email registrada',
            'content' => array()
        )));
        return;
    }

    /**
     * Actualiza la configuración del servidor de e-mail
     *
     * @param Router $router
     */
    public static function putEmail(Router $router): void
    {
        RequestParser::parse();
        if (empty($_PUT)) {
            $_PUT = json_decode(file_get_contents("php://input"), true);
        }

        if (isset($_GET['confirm'])) { // Metodo de confirmación
            if ($_GET['confirm']== 'true') {
                if (IS_CONFIG_EMAIL) {
                    $router->render('api/api', 'layout-api', array('response' => array(
                        'status' => 403,
                        'message' => 'La configuración inicial del servidor de correo ya ha sido efectuada y no puede volver a ejecutarse.',
                        'content' => array()
                    )));
                    return;
                }

                try { // Alcualiza fichero de configuración
                    $data = file_get_contents(__DIR__ . '/../../config/config.php');
                    $data = preg_replace('/define\(\'IS_CONFIG_EMAIL\',\S+\)/', "define('IS_CONFIG_EMAIL',true)", $data);
                    file_put_contents(__DIR__ . '/../../config/config.php', $data);
                } catch (\Exception $e) {
                    $router->render('api/api', 'layout-api', array('response' => array(
                        'status' => 202,
                        'message' => 'No se ha conseguido bloquear el acceso a la API de configuración del servidor de email. Realice un bloqueo manual',
                        'content' => array()
                    )));
                    return;
                }
    
                $router->render('api/api', 'layout-api', array('response' => array(
                    'status' => 201,
                    'message' => 'Validación del servidor de email registrada',
                    'content' => array()
                )));
                return;

            }
        }

        // Método no encontrado
        $router->render('api/api', 'layout-api', array('response' => array(
            'status' => 405,
            'message' => 'Método o parámetros no soportados',
            'content' => array()
        )));
        return;
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
        $titulo = htmlspecialchars(trim($_POST['titulo']));
        $tema = htmlspecialchars(trim($_POST['tema']));
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
    private static function verifyDB(String $DB_HOST, String $DB_USER, String $DB_PASS, String $DB_NAME): bool
    {
        $db = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
        if ($db->connect_error) {
            return false;
        }
        return true;
    }

    /**
     * Verifica conexión con el servidor SMTP
     *
     * @param String $hostSMTP Host del servidor SMTP
     * @param integer $portSMTP Puerto del servidor SMTP
     * @param String $userEmail Usuario SMTP
     * @param String $passEmail Contraseña SMTP
     * @return void Lanza una exception en caso de error
     */
    private static function verifySMTP(String $hostSMTP, int $portSMTP, String $userEmail, String $passEmail): void
    {
        $smtp = new SMTP();
        //Connect to an SMTP server
        if (!$smtp->connect($hostSMTP, $portSMTP)) {
            throw new Exception('Fallo en la conexión con el servidor');
        }
        //Say hello
        if (!$smtp->hello(gethostname())) {
            throw new Exception('El servidor no responde: ' . $smtp->getError()['error']);
        }
        //Get the list of ESMTP services the server offers
        $e = $smtp->getServerExtList();
        //If server can do TLS encryption, use it
        if (is_array($e) && array_key_exists('STARTTLS', $e)) {
            $tlsok = $smtp->startTLS();
            if (!$tlsok) {
                throw new Exception('Allo al iniciar la encriptación: ' . $smtp->getError()['error']);
            }
            //Repeat EHLO after STARTTLS
            if (!$smtp->hello(gethostname())) {
                throw new Exception('El servidor no responde: ' . $smtp->getError()['error']);
            }
            //Get new capabilities list, which will usually now include AUTH if it didn't before
            $e = $smtp->getServerExtList();
        }
        //If server supports authentication, do it (even if no encryption)
        if (is_array($e) && array_key_exists('AUTH', $e)) {
            if (!$smtp->authenticate($userEmail, $passEmail)) {
                throw new Exception('Fallo en la autenticación de usuario: ' . $smtp->getError()['error']);
            }
        }
    }
}
