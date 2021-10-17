<?php

/**
 * Debugear
 * @param mixed $var Variable a debugear
 * @return void
 */
function debug($var): void
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

/**
 * Carga la plantilla
 * @param String $template Plantilla a cargar
 * @param array $options Opciones de la platilla
 * @return void
 */
function getTemplate(String $template, array $options = []): void
{
    include __DIR__ . '/templates/' . $template . '.php';
}

/**
 * Obtiene el listado de temas existentes
 *
 * @return array Temas existentes
 */
function getThemes():array
{
    $themes = [];
    $themeFolders = array_diff(scandir(THEMES_DIR), array('..', '.'));
    foreach ($themeFolders as $themeFolder) {
        if(!is_dir(THEMES_DIR . "/". $themeFolder)) continue;
        if(!file_exists(THEMES_DIR."/".$themeFolder."/theme.json")) continue;
        $themes[] = array('folder'=>$themeFolder) + json_decode(file_get_contents(THEMES_DIR."/".$themeFolder."/theme.json"),true);
    }
    return $themes;
}

/**
 * Conectar la Base de datos
 * @return mysqli Base de datos
 */
function getDB(): mysqli
{
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset('utf8');
    $db->query("SET lc_messages = 'es_ES';");
    if ($db->connect_error) {
        echo 'Error de conexion con la base de datos: ' + $db->connect_error;
        exit;
    }
    return $db;
}

/**
 * Verificar conexion con la base de datos
 * @param String $DB_HOST Host de conexion
 * @param String $DB_USER Usuario de conexion
 * @param String $DB_PASS Contraseña de conexion
 * @param String $DB_NAME Base de datos de conexion
 * @return bool Se establece conexión (SI/NO)
 */
function verifyDB(String $DB_HOST, String $DB_USER, String $DB_PASS, String $DB_NAME): bool
{
    $db = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($db->connect_error) {
        return false;
    }
    return true;
}

/**
 * Verifica que la contraseña introducida respeta el estandar de seguridad
 * @return bool Respeta el estandar de seguridad (SI/NO)
 */
function checkPasswordStrength(string $password): bool
{
    if (!preg_match('@[A-Z]@', $password) || !preg_match('@[a-z]@', $password) || !preg_match('@[0-9]@', $password) || !preg_match('@[^\w]@', $password) || strlen($password) < 8) {
        return false;
    } else {
        return true;
    }
}

/**
 * Genera el mensage de respuesta de la API en base a un arreglo de mensajes
 *
 * @param array $messages arreglo de mensajes
 * @return String mensaje único
 */
function getMessage(array $messages): String
{
    return join("<br/>", array_values($messages));
}
