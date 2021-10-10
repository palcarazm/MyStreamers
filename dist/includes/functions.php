<?php
/**
 * Debugear
 * @param mixed $var Variable a debugear
 * @return void
 */
function debug ($var):void
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
 * Conectar la Base de datos
 * @return mysqli Base de datos
 */
function getDB() : mysqli
{
    $db = new mysqli(DB_HOST , DB_USER , DB_PASS , DB_NAME);
    $db->set_charset('utf8');
    $db->query("SET lc_messages = 'es_ES';");
    if($db->connect_error){
        echo 'Error de conexion con la base de datos: '+$db->connect_error;
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
function verifyDB(String $DB_HOST , String $DB_USER , String $DB_PASS , String $DB_NAME) : bool
{
    $db = new mysqli($DB_HOST , $DB_USER , $DB_PASS , $DB_NAME);
    if($db->connect_error){
        return false;
    }
    return true;
}