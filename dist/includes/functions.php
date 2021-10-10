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
