<?php

use Model\Usuario;

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

/**
 * Carga la session
 *
 * @return void
 */
function loadSession(): void
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Comprueba si hay un usuario autenticado
 *
 * @return boolean
 */
function isAuth(): bool
{
    loadSession();
    return isset($_SESSION['auth']);
}

/**
 * Devuelve el usuario autenticado
 *
 * @return Usuario|null
 */
function getAuthUser():Usuario|null
{
    if(!isAuth()){
        return null;
    }else{
        return $_SESSION['auth']['usuario'];
    }
}