<?php

namespace Apis;

use mysqli;
use stdClass;
use Model\Api;
use Model\Rol;
use Route\Router;
use Model\Usuario;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class ConfigurationApi
{
    const SCOPE = Rol::PERMS_CONFIG;
    /**
     * Valida los datos de conexión de la base de datos.
     * Si son correctos lanza la configuración de la misma.
     *
     * @param Router $router
     */
    public static function postDatabase(Router $router): void
    {
        $api = new Api($router, 'POST', array(
            array(
                'name' => 'dbhost',
                'required' => true,
                'type' => 'string'
            ),
            array(
                'name' => 'dbname',
                'required' => true,
                'type' => 'string'
            ),
            array(
                'name' => 'dbuser',
                'required' => true,
                'type' => 'string'
            ),
            array(
                'name' => 'dbpass',
                'required' => true,
                'type' => 'string'
            )
        ));

        // Valida configuración inicial
        if (IS_CONFIG_DATABASE) {
            $api->send(403, 'La configuración inicial de la base de datos ya ha sido efectuada y no puede volver a ejecutarse.', new stdClass());
            return;
        }

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        //Filtrar las variables
        $DB_HOST = htmlspecialchars(trim($api->in['dbhost']));
        $DB_USER = htmlspecialchars(trim($api->in['dbuser']));
        $DB_PASS = htmlspecialchars(trim($api->in['dbpass']));
        $DB_NAME = htmlspecialchars(trim($api->in['dbname']));

        if (self::verifyDB($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)) { //Conexión verificada
            try { // Crear fichero de configuración
                $data = file_get_contents(__DIR__ . '/../../config/config-template.php');
                $data = preg_replace('/define\(\'SECRET\',\'\S+\'\)/', "define('SECRET','" . strtoupper(bin2hex(random_bytes(20))) . "')", $data);
                $data = preg_replace('/define\(\'DB_HOST\',\'\S+\'\)/', "define('DB_HOST','{$DB_HOST}')", $data);
                $data = preg_replace('/define\(\'DB_NAME\',\'\S+\'\)/', "define('DB_NAME','{$DB_NAME}')", $data);
                $data = preg_replace('/define\(\'DB_USER\',\'\S+\'\)/', "define('DB_USER','{$DB_USER}')", $data);
                $data = preg_replace('/define\(\'DB_PASS\',\'\S+\'\)/', "define('DB_PASS','{$DB_PASS}')", $data);
                file_put_contents(__DIR__ . '/../../config/config.php', $data);
            } catch (\Exception $e) {
                $api->send(500, 'No se ha conseguido guardar los datos de conexión con la base de datos en el fichero de configuración', new stdClass());
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
                    $api->send(500, 'Error en la sentencia #' . ($querynum + 1) . '<br/><br/><span style="color:red;">' . $db->error . '</span>', new stdClass());
                    return;
                }
                $db->close();
            } catch (\Exception $e) {
                $api->send(500, 'No se ha conseguido cargar la configuración inicial en la base de datos', new stdClass());
                return;
            }

            try { // Alcualiza fichero de configuración
                $data = file_get_contents(__DIR__ . '/../../config/config.php');
                $data = preg_replace('/define\(\'IS_CONFIG_DATABASE\',\S+\)/', "define('IS_CONFIG_DATABASE',true)", $data);
                file_put_contents(__DIR__ . '/../../config/config.php', $data);
            } catch (\Exception $e) {
                $api->send(202, 'No se ha conseguido bloquear el acceso a la API de configuración de la base de datos. Realice un bloqueo manual', new stdClass());
                return;
            }

            $api->send(201, 'Configuración de la base de datos completada', new stdClass());
            return;
        } else { // Conexión fallida
            $api->send(500, 'No se ha conseguido establecer conexión con la base de datos', new stdClass());
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
        $api = new Api($router, 'POST', array(
            array(
                'name' => 'user',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 50
            ),
            array(
                'name' => 'email',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 320,
                'filter' => FILTER_VALIDATE_EMAIL
            ),
            array(
                'name' => 'pass',
                'required' => true,
                'type' => 'string',
                'min' => 8
            )
        ));

        // Valida configuración inicial
        if (IS_CONFIG_ADMIN) {
            $api->send(403, 'La configuración inicial del administrador ya ha sido efectuada y no puede volver a ejecutarse.', new stdClass());
            return;
        }

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Filtrar las variables
        $username = htmlspecialchars(trim($api->in['user']));
        $email = filter_var(trim($api->in['email']), FILTER_SANITIZE_EMAIL);
        $pass = htmlspecialchars(trim($api->in['pass']));

        if (!checkPasswordStrength($pass)) { // Validación contraseña
            $api->send(500, 'La contraseña indicada no cumple los estándares de seguridad', new stdClass());
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //validación email
            $api->send(400, 'El e-mail indicado es inválido', new stdClass());
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
                    $api->send(202, 'No se ha conseguido bloquear el acceso a la API de configuración del administrador. Realice un bloqueo manual', new stdClass());
                    return;
                }

                $api->send(201, 'Configuración del administrador completada', new stdClass());
                return;
            } else { // error al guardar usuario
                $api->send(500, 'Se ha producido un error al crear el usuario:<br>' . getMessage($user->errors()), new stdClass());
                return;
            }
        } else { // validación de usuario no superada
            $api->send(500, 'Se ha producido un error al crear el usuario:<br>' . getMessage($user->errors()), new stdClass());
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
        $api = new Api($router, 'POST', array(
            array(
                'name' => 'hostSMTP',
                'required' => true,
                'type' => 'string',
                'min' => 1
            ),
            array(
                'name' => 'portSMTP',
                'required' => true,
                'type' => 'string',
                'min' => 1
            ),
            array(
                'name' => 'userEmail',
                'required' => true,
                'type' => 'string',
                'min' => 1
            ),
            array(
                'name' => 'passEmail',
                'required' => true,
                'type' => 'string',
                'min' => 1
            ),
            array(
                'name' => 'adminEmail',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 320,
                'filter' => FILTER_VALIDATE_EMAIL
            ),
            array(
                'name' => 'fromEmail',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 320,
                'filter' => FILTER_VALIDATE_EMAIL
            ),
            array(
                'name' => 'fromName',
                'required' => true,
                'type' => 'string',
                'min' => 1
            )
        ));

        // Valida configuración inicial
        if (IS_CONFIG_EMAIL) {
            $api->send(403, 'La configuración inicial del servidor de correo ya ha sido efectuada y no puede volver a ejecutarse.', new stdClass());
            return;
        }

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        // Filtrar las variables
        $hostSMTP = htmlspecialchars(trim($api->in['hostSMTP']));
        $portSMTP = filter_var(trim($api->in['portSMTP']), FILTER_SANITIZE_NUMBER_INT);
        $userEmail = htmlspecialchars(trim($api->in['userEmail']));
        $passEmail = htmlspecialchars(trim($api->in['passEmail']));
        $adminEmail = filter_var(trim($api->in['adminEmail']), FILTER_SANITIZE_EMAIL);
        $fromEmail = filter_var(trim($api->in['fromEmail']), FILTER_SANITIZE_EMAIL);
        $fromName = htmlspecialchars(trim($api->in['fromName']));

        try { // Intento de conexión
            self::verifySMTP($hostSMTP, $portSMTP, $userEmail, $passEmail);
        } catch (Exception $e) {
            $api->send(500, 'Se ha producido un error al intentar conectar con el servidor SMTP:<br>' . $e->getMessage(), new stdClass());
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
            $api->send(500, 'Se ha producido un error al guardar los datos de configuración', new stdClass());
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
            $mail->CharSet = 'UTF-8';

            //Recipients
            $mail->addAddress($adminEmail);

            //Content
            $mail->isHTML(true);
            $mail->Subject = '[MyStreamers] Verificación del servidor';
            $mail->Body    = 'Correo de prueba del <b>servidor SMTP</b>!';
            $mail->AltBody = 'Correo de prueba del servidor SMTP!';

            $mail->send();
        } catch (Exception $e) {
            $api->send(500, 'Se ha producido un error al enviar el correo de prueba:<br>' . $mail->ErrorInfo, new stdClass());
            return;
        }

        $api->send(201, 'Configuración del servidor de email registrada', new stdClass());
        return;
    }

    /**
     * Actualiza la configuración del servidor de e-mail
     *
     * @param Router $router
     */
    public static function putEmail(Router $router): void
    {
        $api = new Api($router, 'PUT', array());

        if (isset($_GET['confirm'])) { // Metodo de confirmación
            if ($_GET['confirm'] == 'true') {
                if (IS_CONFIG_EMAIL) {
                    $api->send(403, 'La configuración inicial del servidor de correo ya ha sido efectuada y no puede volver a ejecutarse.', new stdClass());
                    return;
                }

                try { // Alcualiza fichero de configuración
                    $data = file_get_contents(__DIR__ . '/../../config/config.php');
                    $data = preg_replace('/define\(\'IS_CONFIG_EMAIL\',\S+\)/', "define('IS_CONFIG_EMAIL',true)", $data);
                    file_put_contents(__DIR__ . '/../../config/config.php', $data);
                } catch (\Exception $e) {
                    $api->send(202, 'No se ha conseguido bloquear el acceso a la API de configuración del servidor de email. Realice un bloqueo manual', new stdClass());
                    return;
                }

                $api->send(201, 'Validación del servidor de email registrada', new stdClass());
                return;
            }
        }

        // Método no encontrado
        $api->send(405, 'Método o parámetros no soportados', new stdClass());
        return;
    }

    /**
     * Actualiza la información del sitio
     *
     * @param Router $router
     */
    public static function putSite(Router $router): void
    {
        $api = new Api($router, 'PUT', array(
            array(
                'name' => 'titulo',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 2 ^ 32 - 1
            ),
            array(
                'name' => 'tema',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 2 ^ 32 - 1
            ),
            array(
                'name' => 'descripcion',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 2 ^ 32 - 1
            ),
            array(
                'name' => 'eventos',
                'required' => false,
                'type' => 'boolean'
            ),
            array(
                'name' => 'noticias',
                'required' => false,
                'type' => 'boolean'
            ),
            array(
                'name' => 'normas',
                'required' => false,
                'type' => 'boolean'
            ),
            array(
                'name' => 'enlaces',
                'required' => false,
                'type' => 'boolean'
            )
        ), array('SESSION', 'TOKEN'));

        // Valida la autentificación
        if (!$api->auth(self::SCOPE)) {
            return;
        }

        self::configSite($api);
    }
    /**
     * Configura la información del sitio
     *
     * @param Router $router
     */
    public static function postSite(Router $router): void
    {
        $api = new Api($router, 'POST', array(
            array(
                'name' => 'titulo',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 2 ^ 32 - 1
            ),
            array(
                'name' => 'tema',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 2 ^ 32 - 1
            ),
            array(
                'name' => 'descripcion',
                'required' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 2 ^ 32 - 1
            ),
            array(
                'name' => 'eventos',
                'required' => false,
                'type' => 'boolean'
            ),
            array(
                'name' => 'noticias',
                'required' => false,
                'type' => 'boolean'
            ),
            array(
                'name' => 'normas',
                'required' => false,
                'type' => 'boolean'
            ),
            array(
                'name' => 'enlaces',
                'required' => false,
                'type' => 'boolean'
            )
        ));

        // Valida configuración inicial
        if (IS_CONFIG_SITE) {
            $api->send(403, 'La configuración inicial del sitio ya ha sido efectuada y no puede volver a ejecutarse.', new stdClass());
            return;
        }

        self::configSite($api);
    }

    /**
     * Configura la información del sitio
     * @param array $body argumentos de configuración
     * @param Router $router
     */
    private static function configSite(Api $api): void
    {

        // Valida campos requeridos
        if (!$api->validate()) {
            return;
        }

        //Filtrar las variables
        $titulo = htmlspecialchars(trim($api->in['titulo']));
        $tema = htmlspecialchars(trim($api->in['tema']));
        $descripcion = trim($api->in['descripcion']);
        $eventos = isset($api->in['eventos']) ? 1 : 0;
        $noticias = isset($api->in['noticias']) ? 1 : 0;
        $normas = isset($api->in['normas']) ? 1 : 0;
        $enlaces = isset($api->in['enlaces']) ? 1 : 0;

        if (!in_array($tema, array_column(getThemes(), 'folder'))) { // Validar tema
            $api->send(500, 'El tema seleccionado no se encuentra disponible', new stdClass());
            return;
        }

        try { // Registrar los datos
            $db = getDB();
            $stmt = $db->prepare("REPLACE INTO opciones (opcion , valor) VALUES ('titulo' , ?),('tema' , ?),('descripcion' , ?);");
            $stmt->bind_param('sss', $titulo, $tema, $descripcion);
            if (!$stmt->execute()) { // Registro datos principales en error
                $stmt->close();
                $db->close();
                $api->send(500, 'Se ha producido un error al registrar la configuración en la base de datos', new stdClass());
                return;
            }
            $stmt->close();

            $stmt = $db->prepare("REPLACE INTO modulos (modulo , activo) VALUES ('eventos' , ?),('noticias' , ?),('normas' , ?),('enlaces' , ?);");
            $stmt->bind_param('iiii', $eventos, $noticias, $normas, $enlaces);
            if (!$stmt->execute()) { // Registro datos de módulos en error
                $stmt->close();
                $db->close();
                $api->send(500, 'Se ha producido un error al registrar la configuración en la base de datos', new stdClass());
                return;
            }
            $stmt->close();
            $db->close();
        } catch (\Exception $e) {
            $api->send(500, 'Se ha producido un error al registrar la configuración en la base de datos', new stdClass());
            return;
        }

        try { // Alcualiza fichero de configuración
            $data = file_get_contents(__DIR__ . '/../../config/config.php');
            $data = preg_replace('/define\(\'IS_CONFIG_SITE\',\S+\)/', "define('IS_CONFIG_SITE',true)", $data);
            file_put_contents(__DIR__ . '/../../config/config.php', $data);
        } catch (\Exception $e) {
            $api->send(202, 'No se ha conseguido bloquear el acceso a la API de configuración del sitio. Realice un bloqueo manual', new stdClass());
            return;
        }

        $api->send(201, 'Configuración del sitio completada', new stdClass());
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
